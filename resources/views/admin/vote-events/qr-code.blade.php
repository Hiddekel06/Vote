@extends('layouts.app')

@section('title', 'QR Code - ' . $event->nom)

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
            <!-- En-tête -->
            <div class="text-center mb-4">
                <h3 class="text-warning fw-bold mb-2">{{ $event->nom }}</h3>
                <p class="text-muted">QR Code pour accès rapide à l'événement</p>
            </div>

            <!-- Informations de l'événement -->
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted">📅 Début :</small>
                            <div class="fw-semibold">{{ $event->date_debut->format('d/m/Y à H:i') }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">📅 Fin :</small>
                            <div class="fw-semibold">{{ $event->date_fin->format('d/m/Y à H:i') }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">📍 Latitude :</small>
                            <div class="font-monospace">{{ number_format($event->latitude, 6) }}°</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">📍 Longitude :</small>
                            <div class="font-monospace">{{ number_format($event->longitude, 6) }}°</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">🎯 Rayon :</small>
                            <div class="fw-semibold">{{ $event->rayon_metres }} mètres</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">🔐 Statut :</small>
                            <span class="badge {{ $event->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $event->is_active ? '✓ Actif' : '✗ Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>
            <!-- QR Code -->
            <div class="text-center py-4">
                <div class="d-inline-block bg-white p-4 rounded shadow">
                    <div id="qrcode"></div>
                </div>
                
                <div class="mt-3">
                    <p class="text-muted small mb-2">Scanner ce code pour accéder rapidement au vote</p>
                    <div class="bg-light rounded p-2 d-inline-block">
                        <code class="small">{{ $qrUrl }}</code>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i>
                    Imprimer
                </button>
                
                <button onclick="downloadQRCode()" class="btn btn-success">
                    <i class="fas fa-download me-2"></i>
                    Télécharger PNG
                </button>
                
                <button onclick="copyToClipboard('{{ $qrUrl }}')" class="btn btn-info">
                    <i class="fas fa-copy me-2"></i>
                    Copier le lien
                </button>
            </div>

            <!-- Statistiques -->
            <div class="mt-4 pt-4 border-top">
                <h5 class="mb-3">📊 Statistiques</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card bg-light text-center">
                            <div class="card-body">
                                <h3 class="text-primary mb-0">{{ $event->voteJourJ->count() }}</h3>
                                <small class="text-muted">Votes total</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light text-center">
                            <div class="card-body">
                                <h3 class="text-success mb-0">{{ $event->voteJourJ->where('validation_status', 'success')->count() }}</h3>
                                <small class="text-muted">Votes validés</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light text-center">
                            <div class="card-body">
                                <h3 class="text-danger mb-0">{{ $event->voteJourJ->where('validation_status', 'outside_zone')->count() }}</h3>
                                <small class="text-muted">Hors zone</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
        </div>
<!-- QRCode.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    // Générer le QR Code
    const qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "{{ $qrUrl }}",
        width: 300,
        height: 300,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    // Fonction pour télécharger le QR Code
    function downloadQRCode() {
        const canvas = document.querySelector('#qrcode canvas');
        if (canvas) {
            const url = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = 'qrcode-{{ Str::slug($event->nom) }}.png';
            link.href = url;
            link.click();
        }
    }

    // Fonction pour copier le lien
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('✓ Lien copié dans le presse-papiers !');
        }, function(err) {
            console.error('Erreur lors de la copie:', err);
        });
    }
</script>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #qrcode, #qrcode * {
            visibility: visible;
        }
        #qrcode {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
    }
</style>

@endsection
