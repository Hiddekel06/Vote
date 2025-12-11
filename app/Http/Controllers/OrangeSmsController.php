<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrangeSmsController extends Controller
{
    /**
     * Récupérer le token d'accès Orange
     */
    public function getAccessToken()
    {
        try {
            // Ajout d'un timeout de 10 secondes pour éviter que la requête ne bloque indéfiniment
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => env('ORANGE_AUTH_HEADER'),
            ])->withoutVerifying() // uniquement pour dev local
              ->asForm()
              ->post(env('ORANGE_API_URL').'/oauth/v3/token', [
                  'grant_type' => 'client_credentials',
              ]);

            if ($response->failed()) {
                // Log diagnostic (corps tronqué) pour comprendre les 401/4xx
                $body = $response->body();
                Log::error('Échec récupération token Orange', [
                    'status' => $response->status(),
                    'body_length' => strlen($body ?? ''),
                    'body_preview' => mb_substr($body ?? '', 0, 2000),
                    'headers' => $response->headers(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['access_token'] ?? null;

        } catch (\Exception $e) {
            Log::error('Exception récupération token Orange: '.$e->getMessage());
            return null;
        }
    }

    /**
     * Envoyer un OTP par SMS
     */
    public function sendOtp(Request $request)
    {
        $phone = $request->phone; // Numéro en format international +221771234567
        if (!$phone) {
            return response()->json(['error' => 'Le numéro de téléphone est requis'], 422);
        }

        $otp = $request->input('otp', rand(100000, 999999));
        $message = $request->input('message', "Votre code OTP est : $otp");

        $token = $this->getAccessToken();
        if (!$token) {
            return response()->json(['error' => 'Impossible d’obtenir le token'], 500);
        }

        try {
         $sender = 'tel:' . env('ORANGE_SENDER'); // ex: tel:SMS132230

         // Log de la requête pour debug (sans l'OTP)
         Log::info('Préparation envoi SMS Orange', [
             'sender' => $sender,
             'destination' => 'tel:' . $phone,
             'message_length' => strlen($message),
         ]);

         $response = Http::timeout(10)
       ->withToken($token)
       ->withoutVerifying() // uniquement pour dev local
       ->post(env('ORANGE_API_URL')."/smsmessaging/v1/outbound/$sender/requests", [
         'outboundSMSMessageRequest' => [
              'address' => ['tel:' . $phone],   // destinataire
             'senderAddress' => $sender,        // sender officiel avec tel:
             'outboundSMSTextMessage' => [
                 'message' => $message
             ],
             'receiptRequest' => [               // facultatif, pour les DLR
                'callbackData' => 'optional info'
            ]
        ]
    ]);


            // Ne pas logger ou exposer la réponse complète d'Orange ni l'OTP.
            try {
                $digitsOnly = preg_replace('/\D+/', '', $phone);
                $last4 = substr($digitsOnly, -4);
            } catch (\Throwable $e) {
                $last4 = null;
            }
            if ($response->failed()) {
                $body = $response->body();
                Log::error('Échec envoi SMS Orange', [
                    'status' => $response->status(),
                    'phone_last4' => $last4,
                    'body_length' => strlen($body ?? ''),
                    'body_preview' => mb_substr($body ?? '', 0, 2000),
                    'headers' => $response->headers(),
                ]);
            } else {
                $responseBody = $response->json();
                Log::info('Requête SMS envoyée à Orange', [
                    'status' => $response->status(),
                    'phone_last4' => $last4,
                    'body_length' => strlen($response->body() ?? ''),
                    'resourceURL' => $responseBody['outboundSMSMessageRequest']['resourceURL'] ?? null,
                    'deliveryStatus' => $responseBody['outboundSMSMessageRequest']['deliveryInfoList']['deliveryInfo'][0]['deliveryStatus'] ?? null,
                ]);
            }

            return response()->json([
                'success' => $response->successful(), // true si HTTP 2xx
                'message' => $response->successful() ? 'SMS envoyé' : 'Échec envoi SMS',
                // Ne pas renvoyer l'OTP ni la réponse brute d'Orange dans l'API publique
            ]);

        } catch (\Exception $e) {
            // Logger l'exception pour investigation sans exposer les détails côté client
            Log::error('Exception envoi SMS Orange', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de l\'envoi du SMS'], 500);
        }
    }
}