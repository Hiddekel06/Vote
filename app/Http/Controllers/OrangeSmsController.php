<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class OrangeSmsController extends Controller
{
    /**
     * Récupère un token d'accès Orange
     * à partir du Basic stocké dans ORANGE_AUTH_HEADER.
     */
    private function getAccessToken(): ?string
    {
        $baseUrl = rtrim(env('ORANGE_API_URL', 'https://api.orange.com'), '/');

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => env('ORANGE_AUTH_HEADER'), // Basic xxxx
                ])
                ->asForm()
                ->withoutVerifying() // dev local
                ->post($baseUrl . '/oauth/v3/token', [
                    'grant_type' => 'client_credentials',
                ]);

            Log::info('Orange - token response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->failed()) {
                Log::error('Orange - token failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            return $response->json('access_token');
        } catch (\Throwable $e) {
            Log::error('Orange - token exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Normalise un numéro type "77 379 27 37" => "+221773792737"
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone ?? '');

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (! str_starts_with($digits, '221')) {
            $digits = '221' . $digits;
        }

        return '+' . $digits; // ex : +221773792737
    }

    /**
     * Rate limiting "maison" par IP et par téléphone
     * pour contrer les bots / brute-force.
     */
    private function checkRateLimit(string $normalizedPhone, string $ip): ?\Illuminate\Http\JsonResponse
    {
        // 1) Limite par IP (ex: 20 OTP max sur 1h)
        $ipKey = 'otp_ip:' . $ip;
        $ipCount = Cache::add($ipKey, 0, now()->addHour()) ? 0 : Cache::increment($ipKey);

        if ($ipCount > 20) {
            Log::warning('OTP rate-limit IP', ['ip' => $ip]);
            return response()->json([
                'success' => false,
                'message' => 'Trop de tentatives depuis cette adresse IP. Réessayez plus tard.',
            ], 429);
        }

        // 2) Limite par téléphone (ex: 5 OTP max sur 1h)
        $phoneKey = 'otp_phone:' . $normalizedPhone;
        $phoneCount = Cache::add($phoneKey, 0, now()->addHour()) ? 0 : Cache::increment($phoneKey);

        if ($phoneCount > 5) {
            Log::warning('OTP rate-limit phone', ['phone_last4' => substr($normalizedPhone, -4)]);
            return response()->json([
                'success' => false,
                'message' => 'Trop de demandes de code pour ce numéro. Réessayez plus tard.',
            ], 429);
        }

        return null;
    }

    /**
     * Envoi d’un SMS via Orange (couche bas niveau)
     */
    public function sendSmsInternal(string $phone, string $message): array

    {
        $token = $this->getAccessToken();
        if (! $token) {
            return [
                'ok'     => false,
                'status' => 500,
                'body'   => 'Impossible de récupérer le token Orange',
                'raw'    => null,
            ];
        }

        $normalizedPhone = $this->normalizePhone($phone);  // +2217737...
        $destAddress     = 'tel:' . $normalizedPhone;      // tel:+2217737...

        $shortCode     = env('ORANGE_COUNTRY_SENDER', '2210000'); // donné par Orange
        $senderAddress = 'tel:+' . $shortCode;                     // tel:+2210000
        $senderName    = env('ORANGE_SENDER', 'govathon');         // sender whiteliste

        $baseUrl = rtrim(env('ORANGE_API_URL', 'https://api.orange.com'), '/');
        $url     = $baseUrl . '/smsmessaging/v1/outbound/' . urlencode($senderAddress) . '/requests';

        $payload = [
            'outboundSMSMessageRequest' => [
                'address'       => $destAddress,
                'senderAddress' => $senderAddress,
                'senderName'    => $senderName,
                'outboundSMSTextMessage' => [
                    'message' => $message,
                ],
                'receiptRequest' => [
                    'callbackData' => 'govathon-otp',
                ],
            ],
        ];

        Log::info('Orange - SMS payload', [
            'url'     => $url,
            'phone'   => substr($normalizedPhone, -4),
        ]);

        $response = Http::timeout(10)
            ->withToken($token) // Bearer <token>
            ->withoutVerifying()
            ->post($url, $payload);

        Log::info('Orange - SMS response', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        return [
            'ok'     => $response->successful(),
            'status' => $response->status(),
            'body'   => $response->json(),
            'raw'    => $response,
        ];
    }

    /**
     * ENVOI OTP
     * ---------------
     * body: { "phone": "...", "projet_id": 123 }
     * - génère un OTP,
     * - le stocke hashé dans otp_codes,
     * - refuse si le numéro a déjà voté (table votes),
     * - applique du rate-limiting.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'telephone' => ['required', 'string', 'min:6', 'max:20'],
            'projet_id' => ['required', 'integer'],
        ], [
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.string'   => 'Le numéro de téléphone est invalide.',
            'telephone.min'      => 'Le numéro de téléphone doit contenir au moins 6 caractères.',
            'telephone.max'      => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
            'projet_id.required' => 'Le projet est obligatoire.',
            'projet_id.integer'  => 'Le projet sélectionné est invalide.',
        ]);

        $rawPhone        = $request->input('telephone');
        $normalizedPhone = $this->normalizePhone($rawPhone);
        $projetId        = $request->input('projet_id');
        $ip              = $request->ip();

        // 1) Vérifier si ce numéro a déjà voté (quel que soit le projet)
        $alreadyVoted = DB::table('vote_publics')
            ->where('telephone', $normalizedPhone)
            ->where('est_verifie', 1)
            ->exists();

        if ($alreadyVoted) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà voté. Un seul vote est autorisé.',
            ], 403);
        }

        // 2) Rate limiting anti-bot (IP + phone)
        if ($resp = $this->checkRateLimit($normalizedPhone, $ip)) {
            return $resp; // JSON 429
        }

        // 3) Générer un OTP et le stocker hashé
        $otp = random_int(100000, 999999);

        // On supprime un éventuel ancien OTP pour ce couple (phone, projet)
        DB::table('otp_codes')
            ->where('phone', $normalizedPhone)
            ->where('projet_id', $projetId)
            ->delete();

        DB::table('otp_codes')->insert([
            'phone'       => $normalizedPhone,
            'projet_id'   => $projetId,
            'code_hash'   => Hash::make((string) $otp),
            'expires_at'  => now()->addMinutes(5),
            'attempts'    => 0,
            'consumed_at' => null,
            'ip_address'  => $ip,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // 4) Envoyer le SMS avec le message demandé
        $message = "Votre code OTP est de : {$otp}";

        $result = $this->sendSmsInternal($normalizedPhone, $message);

        if (! $result['ok']) {
            return response()->json([
                'success' => false,
                'message' => 'Échec de l’envoi du SMS.',
                'status'  => $result['status'],
                'body'    => $result['body'],
            ], 500);
        }

        // 🔒 On NE renvoie PAS l’OTP côté client (sécurité).
        return response()->json([
            'success' => true,
            'message' => 'Code OTP envoyé par SMS.',
        ]);
    }

    /**
     * VÉRIFICATION OTP
     * -----------------
     * body: { "phone": "...", "projet_id": 123, "otp": "123456" }
     * - vérifie le code saisi,
     * - limite le nombre d’essais,
     * - marque l’OTP comme consommé,
     * - enregistre le vote (table votes),
     * - bloque définitivement ce numéro.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'telephone' => ['required', 'string', 'min:6', 'max:20'],
            'projet_id' => ['required', 'integer'],
            'otp'       => ['required', 'digits:6'],
        ], [
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.string'   => 'Le numéro de téléphone est invalide.',
            'telephone.min'      => 'Le numéro de téléphone doit contenir au moins 6 caractères.',
            'telephone.max'      => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
            'projet_id.required' => 'Le projet est obligatoire.',
            'projet_id.integer'  => 'Le projet sélectionné est invalide.',
            'otp.required'       => 'Le code OTP est obligatoire.',
            'otp.digits'         => 'Le code OTP doit contenir exactement 6 chiffres.',
        ]);

        $rawPhone        = $request->input('telephone');
        $normalizedPhone = $this->normalizePhone($rawPhone);

        $projetId        = $request->input('projet_id');
        $otpInput        = $request->input('otp');
        $ip              = $request->ip();

        // 1) Si le numéro a déjà voté (est_verifie = 1 sur n'importe quel projet), on bloque
        $alreadyVoted = DB::table('vote_publics')
            ->where('telephone', $normalizedPhone)
            ->where('est_verifie', 1)
            ->exists();

        if ($alreadyVoted) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà voté. Un seul vote est autorisé.',
            ], 403);
        }

        // 2) Récupérer l’OTP actif pour ce couple (phone, projet)
        $otpRow = DB::table('otp_codes')
            ->where('phone', $normalizedPhone)
            ->where('projet_id', $projetId)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if (! $otpRow) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun code OTP actif pour ce numéro et ce projet.',
            ], 400);
        }

        // 3) Vérifier expiration
        if (now()->greaterThan($otpRow->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Le code OTP a expiré. Merci de redemander un nouveau code.',
            ], 400);
        }

        // 4) Limiter le nombre d’essais (ex: 5 max)
        if ($otpRow->attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Nombre d’essais dépassé. Merci de redemander un nouveau code.',
            ], 429);
        }

        // 5) Vérifier l’OTP (hash)
        $isValid = Hash::check($otpInput, $otpRow->code_hash);

        if (! $isValid) {
            DB::table('otp_codes')
                ->where('id', $otpRow->id)
                ->update([
                    'attempts'   => $otpRow->attempts + 1,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => false,
                'message' => 'Code OTP incorrect.',
            ], 400);
        }

        // 6) OTP valide : on le marque consommé et on enregistre le vote
      // 6) OTP valide : on le marque consommé et on enregistre le vote
DB::transaction(function () use ($otpRow, $normalizedPhone, $projetId, $ip, $request) {

    // 6.1 Marquer l’OTP comme consommé
    DB::table('otp_codes')
        ->where('id', $otpRow->id)
        ->update([
            'consumed_at' => now(),
            'updated_at'  => now(),
        ]);

    // 6.2 Récupérer les infos techniques
    $userAgent = substr($request->userAgent() ?? 'unknown', 0, 1000);

    $geoCountry = null;
    $geoCity    = null;

    try {
        // Si tu as installé un package GeoIP (ex: torann/geoip)
        if (function_exists('geoip')) {
            $geo = geoip()->getLocation($ip);
            $geoCountry = $geo->iso_code ?? $geo->country ?? null; // SN
            $geoCity    = $geo->city ?? null;
        }
    } catch (\Throwable $e) {
        Log::warning('GeoIP lookup failed', [
            'ip'      => $ip,
            'message' => $e->getMessage(),
        ]);
    }

    // 6.3 Enregistrer le vote vérifié
    DB::table('vote_publics')->updateOrInsert(
        [
            'telephone' => $normalizedPhone,
            'projet_id' => $projetId,
        ],
        [
            'email'       => null,
            'token'       => null,
            'est_verifie' => 1,
            'ip_address'  => $ip,
            'user_agent'  => $userAgent,
            'geo_country' => $geoCountry,
            'geo_city'    => $geoCity,
            'updated_at'  => now(),
            'created_at'  => now(),
        ]
    );
});


        return response()->json([
            'success' => true,
            'message' => 'Vote validé avec succès.',
        ]);
    }

    /**
     * Test manuel de SMS simple (sans OTP) si besoin
     * GET /test-orange-sms
     */
    public function testSimple()
    {
        $result = $this->sendSmsInternal(
            '773792737',
            'Test Govathon (Laravel) - ' . now()->toDateTimeString()
        );

        return response()->json([
            'success' => $result['ok'],
            'status'  => $result['status'],
            'body'    => $result['body'],
        ], $result['ok'] ? 200 : 500);
    }
}
