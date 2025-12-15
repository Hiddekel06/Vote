<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SntSmsController extends Controller
{
    /**
     * Normalise un numéro vers le format attendu par Dexchange (ex: 221773792737)
     */
    private function normalizeNumber(string $phone): string
    {
        // On enlève tout sauf les chiffres
        $digits = preg_replace('/\D+/', '', $phone ?? '');

        // Enlève un éventuel 00 au début
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // Si ça commence par 0 (ex: 0773792737) => on enlève le 0
        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // Si ça ne commence pas par 221 => on préfixe
        if (! str_starts_with($digits, '221')) {
            $digits = '221' . $digits;
        }

        return $digits;
    }

    /**
     * Appel générique à l'API Dexchange / SendText
     */
    private function callApi(array $payload)
    {
        $apiKey  = env('SNT_SMS_API_KEY');
        $baseUrl = rtrim(env('SNT_SMS_BASE_URL', 'https://api-v2.dexchange-sms.com'), '/');

        if (! $apiKey) {
            return response()->json(['error' => 'SNT_SMS_API_KEY manquant dans .env'], 500);
        }

        Log::info('SNT SMS - callApi', [
            'url'     => "$baseUrl/api/v1/send/sms",
            'payload' => $payload,
        ]);

        $response = Http::timeout(15)
            ->acceptJson()
            ->withHeaders([
                // D’après l’OpenAPI: bearerAuth
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])
            ->post("$baseUrl/api/v1/send/sms", $payload);

        Log::info('SNT SMS - réponse brute', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        return $response;
    }

    /**
     * Petit test simple : envoie un SMS sur TON numéro
     * GET /test-sms
     */
    public function testSms()
    {
        $signature = env('SNT_SMS_SIGNATURE', 'SENDTEXT');

        // 👉 Mets ici ton numéro (sans indicatif ou avec, comme tu veux,
        // on normalise derrière)
        $rawPhone  = '773792737';
        $phone     = $this->normalizeNumber($rawPhone);

        $payload = [
            'signature' => $signature, // doit correspondre à un sendername “Disponible”
            'content'   => 'Test Govathon / SendText - ' . now()->format('d/m/Y H:i:s'),
            'number'    => [$phone],   // tableau de numéros
        ];

        $response = $this->callApi($payload);

        return response()->json([
            'status'   => $response->status(),
            'response' => json_decode($response->body(), true),
        ], $response->status());
    }

    /**
     * Envoi d’un OTP générique (pour ton vote, login, etc.)
     * body attendu : { phone: "..."}
     * POST /send-otp
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $signature = env('SNT_SMS_SIGNATURE', 'SENDTEXT');

        $phone = $this->normalizeNumber($request->phone);
        $otp   = random_int(100000, 999999);

        $message = "Votre code OTP Govathon est : $otp";

        $payload = [
            'signature' => $signature,
            'content'   => $message,
            'number'    => [$phone],
        ];

        Log::info('SNT OTP - envoi', [
            'phone_last4' => substr($phone, -4),
            'otp'         => $otp,
        ]);

        $response = $this->callApi($payload);

        if ($response->failed()) {
            Log::error('SNT OTP - échec', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Échec envoi SMS',
            ], 500);
        }

        // Pour les tests uniquement : renvoi l’OTP
        return response()->json([
            'success'   => true,
            'message'   => 'SMS OTP envoyé',
            'otp_debug' => $otp, // ⚠️ à enlever en prod
        ]);
    }
}
