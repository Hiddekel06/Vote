<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class VoteStatusController extends Controller
{
    /**
     * Met à jour le statut du système de vote.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'vote_status' => 'required|string|in:active,inactive',
        ]);

        // Récupérer l'ancienne valeur pour audit
        $old = Configuration::where('cle', 'vote_status')->value('valeur');

        Configuration::updateOrCreate(
            ['cle' => 'vote_status'],
            ['valeur' => $validated['vote_status']]
        );

        // Log simple d'audit
        try {
            $userId = Auth::id();
        } catch (\Exception $e) {
            $userId = null;
        }
        Log::info('Admin changed vote_status', [
            'user_id' => $userId,
            'from' => $old,
            'to' => $validated['vote_status'],
            'ip' => $request->ip(),
        ]);

        // Si la requête attend du JSON (par ex. fetch() avec Accept: application/json), renvoyer JSON.
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Le statut du système de vote a été mis à jour avec succès.',
                'old' => $old,
                'new' => $validated['vote_status'],
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Le statut du système de vote a été mis à jour avec succès.');
    }
}
