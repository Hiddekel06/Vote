@extends('layouts.app')

@section('title', 'Gestion des Événements Vote Jour J')

@section('content')

<div class="content">
    <div class="pb-5">
        <h2 class="text-bold text-body-emphasis">📍 Gestion des Événements Vote Jour J</h2>
        <p class="text-body-tertiary">Gérez les événements GPS pour le vote sur place</p>
    </div>

    <!-- Bouton Créer un événement -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Événements GPS</h3>
        <a href="{{ route('admin.vote-events.create') }}" class="btn btn-primary">
            <span class="fas fa-plus me-2"></span>
            Créer un événement
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Liste des événements -->
    <div class="card">
        <div class="card-body">
            @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Dates</th>
                                <th>Localisation</th>
                                <th>Rayon</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $item)
                                @php $event = $item['event']; @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $event->nom }}</div>
                                        <small class="text-muted">#{{ $event->id }}</small>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div><i class="fas fa-calendar me-1"></i> {{ $event->date_debut->format('d/m/Y H:i') }}</div>
                                            <div><i class="fas fa-calendar me-1"></i> {{ $event->date_fin->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small font-monospace">
                                            <div>📍 {{ number_format($event->latitude, 6) }}° N</div>
                                            <div>📍 {{ number_format($event->longitude, 6) }}° E</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $event->rayon_metres }}m</span>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.vote-events.toggle', $event->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $event->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $event->is_active ? '✓ Actif' : '✗ Inactif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.vote-events.qr-code', $event->id) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Voir QR Code">
                                            <i class="fas fa-qrcode"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $event->id }}"
                                                title="Supprimer l'événement">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <span class="badge bg-secondary ms-2">{{ $item['total_votes'] ?? 0 }} votes</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modales de suppression -->
                @foreach($events as $item)
                    @php $event = $item['event']; @endphp
                    <div class="modal fade" id="deleteModal{{ $event->id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $event->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="deleteLabel{{ $event->id }}">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Supprimer l'événement
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Êtes-vous sûr de vouloir supprimer cet événement ?</strong></p>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <p class="mb-1"><strong>{{ $event->nom }}</strong></p>
                                            <small class="text-muted">Du {{ $event->date_debut->format('d/m/Y H:i') }} au {{ $event->date_fin->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </div>
                                    @if($item['total_votes'] > 0)
                                        <div class="alert alert-warning mt-3" role="alert">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Attention :</strong> Cet événement contient {{ $item['total_votes'] }} vote(s). 
                                            Cette action supprimera aussi tous les votes enregistrés.
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <form action="{{ route('admin.vote-events.destroy', $event->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash me-2"></i>
                                            Oui, supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <h4>Aucun événement</h4>
                    <p class="text-muted">Commencez par créer votre premier événement GPS pour le Vote Jour J.</p>
                    <a href="{{ route('admin.vote-events.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>
                        Créer un événement
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
