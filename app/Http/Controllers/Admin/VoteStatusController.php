<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoteStatusController extends Controller
{
    /**
     * Met à jour le statut du système de vote.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vote_status' => 'required|string|in:active,inactive',
        ]);

        Configuration::updateOrCreate(
            ['cle' => 'vote_status'],
            ['valeur' => $validated['vote_status']]
        );

        return redirect()->route('admin.dashboard')->with('success', 'Le statut du système de vote a été mis à jour avec succès.');
    }
}
