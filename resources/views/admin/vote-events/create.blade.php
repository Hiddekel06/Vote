@extends('layouts.app')

@section('title', 'Créer un Événement Vote Jour J')

@section('content')

<div class="content">
    <!-- Bouton Retour -->
    <div class="mb-4">
        <a href="{{ route('admin.vote-events.index') }}" class="btn btn-phoenix-secondary">
            <span class="fas fa-arrow-left me-2"></span>
            Retour à la liste
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title mb-4">📍 Nouvel Événement GPS</h3>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreur !</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.vote-events.store') }}" method="POST">
                @csrf

                <!-- Nom de l'événement -->
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom de l'événement <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}"
                           placeholder="Ex: Grande Finale GovAthon 2025" required>
                    <small class="form-text text-muted">Le nom de l'événement pour identification</small>
                </div>

                <!-- Dates -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_debut" class="form-label">Date et heure de début <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="date_debut" name="date_debut" 
                               value="{{ old('date_debut') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="date_fin" class="form-label">Date et heure de fin <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="date_fin" name="date_fin" 
                               value="{{ old('date_fin') }}" required>
                    </div>
                </div>

                <!-- Coordonnées GPS -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Localisation GPS de l'événement
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="latitude" name="latitude" 
                                       step="0.000001" value="{{ old('latitude') }}" placeholder="Ex: 6.131944" required>
                                <small class="form-text text-muted">Coordonnée Nord-Sud (degrés décimaux)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="longitude" name="longitude" 
                                       step="0.000001" value="{{ old('longitude') }}" placeholder="Ex: 1.222778" required>
                                <small class="form-text text-muted">Coordonnée Est-Ouest (degrés décimaux)</small>
                            </div>
                        </div>

                        <!-- Aide -->
                        <div class="alert alert-info mb-0">
                            <strong>💡 Comment trouver les coordonnées GPS ?</strong>
                            <ol class="mb-0 mt-2 small">
                                <li>Ouvrez <a href="https://www.google.com/maps" target="_blank">Google Maps</a></li>
                                <li>Faites un clic droit sur le lieu de l'événement</li>
                                <li>Cliquez sur les coordonnées pour les copier</li>
                                <li>Collez-les dans les champs ci-dessus</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Rayon autorisé -->
                <div class="mb-3">
                    <label for="rayon_metres" class="form-label">Rayon autorisé (mètres) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="rayon_metres" name="rayon_metres" 
                           value="{{ old('rayon_metres', 100) }}" min="10" max="5000" required>
                    <small class="form-text text-muted">
                        Distance maximale autorisée depuis le point GPS (recommandé : 50-200m pour une salle, 500-1000m pour un campus)
                    </small>
                </div>

                <!-- QR Code Token -->
                <div class="mb-3">
                    <label for="qr_token" class="form-label">QR Code Token (optionnel)</label>
                    <input type="text" class="form-control" id="qr_token" name="qr_token" 
                           value="{{ old('qr_token', \Illuminate\Support\Str::random(32)) }}" 
                           placeholder="Token généré automatiquement">
                    <small class="form-text text-muted">Token unique pour le QR code (généré automatiquement, peut être modifié)</small>
                </div>

                <!-- Expiration QR Code -->
                <div class="mb-3">
                    <label for="qr_expires_at" class="form-label">Expiration du QR Code (optionnel)</label>
                    <input type="datetime-local" class="form-control" id="qr_expires_at" name="qr_expires_at" 
                           value="{{ old('qr_expires_at') }}">
                    <small class="form-text text-muted">Date d'expiration du QR code (laissez vide pour pas d'expiration)</small>
                </div>

                <!-- Activer l'événement -->
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                           {{ old('is_active') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Activer l'événement immédiatement
                    </label>
                </div>

                <!-- Boutons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        Créer l'événement
                    </button>
                    <a href="{{ route('admin.vote-events.index') }}" class="btn btn-secondary flex-fill">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection