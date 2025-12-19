<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckQrToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si le token QR existe en session
        $qrToken = session('qr_token_jour_j');
        $qrTokenExpiresAt = session('qr_token_expires_at');

        // Si pas de token, bloquer l'accès
        if (!$qrToken) {
            return response()->view('errors.403-qr', [
                'message' => 'Accès refusé. Vous devez scanner le QR Code pour accéder au vote Jour J.'
            ], 403);
        }

        // Vérifier l'expiration du token (10 minutes)
        if ($qrTokenExpiresAt && Carbon::parse($qrTokenExpiresAt)->isPast()) {
            // Token expiré, nettoyer la session
            session()->forget(['qr_token_jour_j', 'qr_token_expires_at', 'vote_event_id']);
            
            return response()->view('errors.403-qr', [
                'message' => 'Votre accès a expiré. Veuillez scanner à nouveau le QR Code.'
            ], 403);
        }

        return $next($request);
    }
}
