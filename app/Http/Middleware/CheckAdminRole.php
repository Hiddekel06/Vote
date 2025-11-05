<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Gère une requête entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles Les rôles autorisés passés en paramètre depuis le fichier de routes.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Le middleware 'auth' s'occupe déjà de vérifier si l'utilisateur est connecté.
        // On peut donc supposer que Auth::user() existe.
        $user = Auth::user();

        // On parcourt la liste des rôles autorisés par la route.
        // Si l'utilisateur a l'un de ces rôles, on le laisse passer.
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // Si l'utilisateur n'a aucun des rôles requis, on le redirige
        // avec un message d'erreur. abort(403) est aussi une bonne option.
        return redirect('/')->with('error', 'Accès non autorisé.');
    }
}
