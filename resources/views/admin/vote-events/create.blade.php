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

                <!-- Coordonnées GPS + carte -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3 d-flex align-items-center justify-content-between">
                            <span>
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Localisation GPS de l'événement
                            </span>
                            <small class="text-muted ms-2">Cliquez sur la carte ou utilisez votre position</small>
                        </h5>

                        <div class="row">
                            <!-- Carte -->
                            <div class="col-md-7 mb-3">
                                <div id="gps-map"
                                     style="width:100%;height:350px;border-radius:0.75rem;border:1px solid #e5e7eb;overflow:hidden;">
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    📍 Vous pouvez cliquer sur la carte ou déplacer le marqueur pour ajuster la position.
                                </small>
                            </div>

                            <!-- Champs Lat / Lon + bouton GPS -->
                            <div class="col-md-5 mb-3">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('latitude') is-invalid @enderror"
                                           id="latitude"
                                           name="latitude"
                                           step="0.000001"
                                           value="{{ old('latitude') }}"
                                           placeholder="Ex: 14.693700" required>
                                    @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Coordonnée Nord-Sud (degrés décimaux)</small>
                                </div>
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('longitude') is-invalid @enderror"
                                           id="longitude"
                                           name="longitude"
                                           step="0.000001"
                                           value="{{ old('longitude') }}"
                                           placeholder="Ex: -17.444060" required>
                                    @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Coordonnée Est-Ouest (degrés décimaux)</small>
                                </div>

                                <!-- Rayon autorisé (lié au cercle sur la carte) -->
                                <div class="mb-3">
                                    <label for="rayon_metres" class="form-label">Rayon autorisé (mètres) <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('rayon_metres') is-invalid @enderror"
                                           id="rayon_metres"
                                           name="rayon_metres"
                                           value="{{ old('rayon_metres', 100) }}"
                                           min="10" max="5000" required>
                                    @error('rayon_metres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Le cercle bleu sur la carte représente ce rayon (ex : 50–200m pour une salle, 500–1000m pour un campus).
                                    </small>
                                </div>

                                <!-- Bouton "Utiliser ma position actuelle" -->
                                <div class="mb-2 d-grid gap-2">
                                    <button type="button"
                                            id="btn-use-my-location"
                                            class="btn btn-outline-primary btn-sm">
                                        Utiliser ma position actuelle
                                    </button>
                                    <small class="form-text text-muted">
                                        Si tu es sur le lieu de l'événement, ce bouton remplit automatiquement latitude et longitude.
                                    </small>
                                </div>

                                <!-- Aide Google Maps -->
                                <div class="alert alert-info mb-0">
                                    <strong>💡 Astuce :</strong>
                                    <ol class="mb-0 mt-2 small">
                                        <li>Ouvrez <a href="https://www.google.com/maps" target="_blank">Google Maps</a></li>
                                        <li>Clic droit sur le lieu de l'événement</li>
                                        <li>Cliquez sur les coordonnées pour les copier</li>
                                        <li>Collez-les dans les champs ci-dessus si besoin</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
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

@push('scripts')
    {{-- Script Google Maps JS : pense à mettre ta vraie clé dans config/services.php -> google.maps_key --}}
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=initVoteEventMap"
        async defer></script>

    <script>
        let map, marker, circle;
        let latInput, lonInput, radiusInput;

        function initVoteEventMap() {
            latInput    = document.getElementById('latitude');
            lonInput    = document.getElementById('longitude');
            radiusInput = document.getElementById('rayon_metres');

            // Valeurs par défaut : Dakar si rien
            const defaultLat = parseFloat(latInput.value) || 14.6937;
            const defaultLng = parseFloat(lonInput.value) || -17.44406;
            const center = { lat: defaultLat, lng: defaultLng };

            map = new google.maps.Map(document.getElementById('gps-map'), {
                center: center,
                zoom: 15,
                mapTypeId: 'roadmap',
            });

            marker = new google.maps.Marker({
                position: center,
                map: map,
                draggable: true,
                title: 'Position de l\'événement'
            });

            circle = new google.maps.Circle({
                strokeColor: '#00C2FF',
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: '#00C2FF',
                fillOpacity: 0.15,
                map: map,
                center: center,
                radius: parseInt(radiusInput.value) || 100
            });

            // Drag du marker -> met à jour lat/lon + cercle
            marker.addListener('dragend', (e) => {
                const lat = e.latLng.lat();
                const lng = e.latLng.lng();
                latInput.value = lat.toFixed(6);
                lonInput.value = lng.toFixed(6);
                circle.setCenter(e.latLng);
            });

            // Clic sur la carte -> déplace marker
            map.addListener('click', (e) => {
                marker.setPosition(e.latLng);
                circle.setCenter(e.latLng);
                latInput.value = e.latLng.lat().toFixed(6);
                lonInput.value = e.latLng.lng().toFixed(6);
            });

            // Changement du rayon -> change le cercle
            if (radiusInput) {
                radiusInput.addEventListener('input', () => {
                    const r = parseInt(radiusInput.value);
                    if (!isNaN(r) && circle) {
                        circle.setRadius(r);
                    }
                });
            }

            // Si l'admin tape à la main lat/lon -> recadre la carte
            const syncFromInputs = () => {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lonInput.value);
                if (isNaN(lat) || isNaN(lng)) return;
                const pos = new google.maps.LatLng(lat, lng);
                marker.setPosition(pos);
                circle.setCenter(pos);
                map.setCenter(pos);
            };

            ['change', 'blur'].forEach(evt => {
                latInput.addEventListener(evt, syncFromInputs);
                lonInput.addEventListener(evt, syncFromInputs);
            });
        }

        // Bouton "Utiliser ma position actuelle"
        document.addEventListener('DOMContentLoaded', function () {
            const btnLocation = document.getElementById('btn-use-my-location');
            if (!btnLocation) return;

            btnLocation.addEventListener('click', function (e) {
                e.preventDefault();

                if (!navigator.geolocation) {
                    alert('La géolocalisation n’est pas supportée par ce navigateur.');
                    return;
                }

                btnLocation.disabled = true;
                const originalText = btnLocation.innerText;
                btnLocation.innerText = 'Détection en cours...';

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        latInput.value = lat.toFixed(6);
                        lonInput.value = lng.toFixed(6);

                        if (map && marker && circle) {
                            const pos = new google.maps.LatLng(lat, lng);
                            marker.setPosition(pos);
                            circle.setCenter(pos);
                            map.setCenter(pos);
                        }

                        btnLocation.disabled = false;
                        btnLocation.innerText = originalText;
                    },
                    (error) => {
                        console.error('Erreur de géolocalisation admin:', error);
                        let msg = 'Impossible de récupérer votre position.';
                        if (error.code === error.PERMISSION_DENIED) {
                            msg = 'Permission de géolocalisation refusée. Autorisez-la dans votre navigateur puis réessayez.';
                        }
                        alert(msg);
                        btnLocation.disabled = false;
                        btnLocation.innerText = originalText;
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });
        });
    </script>
@endpush
