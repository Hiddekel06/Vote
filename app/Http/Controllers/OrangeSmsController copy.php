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
                Log::error('Échec récupération token Orange', [
                    'status' => $response->status(),
                    'body' => $response->body(),
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


            // Logs pour débogage
            Log::info('Réponse Orange SMS', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => $response->successful(), // true si HTTP 2xx
                'otp' => $otp,
                'response' => $response->json(),      // retourne la réponse brute d'Orange
            ]);

        } catch (\Exception $e) {
            Log::error('Exception envoi SMS Orange: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
