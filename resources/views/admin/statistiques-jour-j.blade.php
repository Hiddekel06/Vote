@extends('layouts.app')

@section('title', 'Statistiques Vote Jour J')

@section('content')
<div class="content admin-stats-jour-j">
    <style>
        .admin-stats-jour-j .stat-card { 
            padding: 1.5rem; 
            border-radius: 12px; 
            box-shadow: 0 6px 18px rgba(2,6,23,0.06); 
        }
        .admin-stats-jour-j .stat-icon { 
            width: 56px; 
            height: 56px; 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 20px; 
        }
        .admin-stats-jour-j .stat-label { 
            font-size: 13px; 
            color: #6B7280; 
            margin-bottom: 6px; 
        }
        .admin-stats-jour-j .stat-value { 
            font-size: 24px; 
            font-weight: 700; 
        }
        .admin-stats-jour-j .stat-sub { 
            font-size: 12px; 
            color: #9CA3AF; 
            margin-top: 4px;
        }
    </style>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Statistiques Vote Jour J</h1>
            <p class="text-muted mb-0">Analyse détaillée des votes sur site avec validation GPS</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour au Dashboard
        </a>
    </div>

    <!-- Cartes de statistiques globales -->
    <div class="row g-3 mb-4">
        <!-- Total Votes Jour J -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100 stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background:#F0F9FF;color:#0EA5E9;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="stat-label">Total Votes Jour J</div>
                        <div class="stat-value">{{ $totalVotesJourJ }}</div>
                        <div class="stat-sub">Votes sur site</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Votants Uniques -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100 stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background:#FFFBEB;color:#D97706;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path>
                            <circle cx="11" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="stat-label">Votants Uniques</div>
                        <div class="stat-value">{{ $totalVotantsJourJ }}</div>
                        <div class="stat-sub">Numéros distincts</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Votes Validés GPS -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100 stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background:#ECFDF5;color:#059669;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="stat-label">GPS Validés</div>
                        <div class="stat-value text-success">{{ $votesJourJValides }}</div>
                        <div class="stat-sub">Dans la zone</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Votes Hors Zone -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card h-100 stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="background:#FEF2F2;color:#DC2626;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="stat-label">Hors Zone</div>
                        <div class="stat-value text-danger">{{ $votesJourJHorsZone }}</div>
                        <div class="stat-sub">GPS non conforme</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques détaillés -->
    <div class="row g-3 mb-4">
        <!-- Top 5 Projets Jour J -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top 5 Projets - Vote Jour J</h5>
                </div>
                <div class="card-body">
                    @if($projetsTopJourJ->isNotEmpty())
                        <div id="chartTopJourJ" style="min-height: 350px;"></div>
                    @else
                        <p class="text-muted">Aucun vote Jour J pour le moment.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Votes par Événement GPS -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Votes par Événement GPS</h5>
                </div>
                <div class="card-body">
                    @if($votesParEvent->isNotEmpty())
                        <div id="chartEventBreakdown" style="min-height: 350px;"></div>
                    @else
                        <p class="text-muted">Aucun événement GPS configuré.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Validation GPS par Événement -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Taux de Validation GPS par Événement</h5>
                </div>
                <div class="card-body">
                    @if($votesParEvent->isNotEmpty())
                        <div id="chartGPSValidation" style="min-height: 400px;"></div>
                    @else
                        <p class="text-muted">Aucune donnée disponible.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau détaillé par événement -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Détails par Événement GPS</h5>
                </div>
                <div class="card-body">
                    @if($votesParEvent->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom de l'Événement</th>
                                        <th class="text-center">Total Votes</th>
                                        <th class="text-center text-success">Validés GPS</th>
                                        <th class="text-center text-danger">Hors Zone</th>
                                        <th class="text-center">Taux Validation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($votesParEvent as $event)
                                        <tr>
                                            <td>
                                                <strong>{{ $event['nom'] ?? 'Événement sans nom' }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $event['total'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $event['valides'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">{{ $event['hors_zone'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $taux = $event['total'] > 0 
                                                        ? round(($event['valides'] / $event['total']) * 100, 1) 
                                                        : 0;
                                                @endphp
                                                <span class="badge {{ $taux >= 80 ? 'bg-success' : ($taux >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $taux }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Aucun événement GPS configuré.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart 1: Top 5 Projets Jour J (Bar chart)
    @if($projetsTopJourJ->isNotEmpty())
    const chartTopJourJ = echarts.init(document.getElementById('chartTopJourJ'));
    const optionsTopJourJ = {
        tooltip: {
            trigger: 'axis',
            axisPointer: { type: 'shadow' }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            name: 'Nombre de votes'
        },
        yAxis: {
            type: 'category',
            data: {!! json_encode($projetLabelsJourJ) !!},
            axisLabel: {
                formatter: function(value) {
                    return value.length > 25 ? value.substring(0, 25) + '...' : value;
                }
            }
        },
        series: [{
            name: 'Votes',
            type: 'bar',
            data: {!! json_encode($projetDataJourJ) !!},
            itemStyle: {
                color: '#0EA5E9'
            },
            label: {
                show: true,
                position: 'right'
            }
        }]
    };
    chartTopJourJ.setOption(optionsTopJourJ);
    window.addEventListener('resize', () => chartTopJourJ.resize());
    @endif

    // Chart 2: Votes par Événement (Donut chart)
    @if($votesParEvent->isNotEmpty())
    const chartEventBreakdown = echarts.init(document.getElementById('chartEventBreakdown'));
    const eventData = {!! json_encode($votesParEvent->map(function($e) { 
        return ['name' => $e['nom'] ?? 'Sans nom', 'value' => $e['total']]; 
    })) !!};
    const optionsEventBreakdown = {
        tooltip: {
            trigger: 'item',
            formatter: '{b}: {c} votes ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left'
        },
        series: [{
            name: 'Votes par Événement',
            type: 'pie',
            radius: ['40%', '70%'],
            avoidLabelOverlap: false,
            itemStyle: {
                borderRadius: 10,
                borderColor: '#fff',
                borderWidth: 2
            },
            label: {
                show: true,
                formatter: '{b}: {c}'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: 16,
                    fontWeight: 'bold'
                }
            },
            data: eventData
        }]
    };
    chartEventBreakdown.setOption(optionsEventBreakdown);
    window.addEventListener('resize', () => chartEventBreakdown.resize());
    @endif

    // Chart 3: Taux de Validation GPS (Stacked bar chart)
    @if($votesParEvent->isNotEmpty())
    const chartGPSValidation = echarts.init(document.getElementById('chartGPSValidation'));
    const optionsGPSValidation = {
        tooltip: {
            trigger: 'axis',
            axisPointer: { type: 'shadow' }
        },
        legend: {
            data: ['Validés GPS', 'Hors Zone']
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: {!! json_encode($eventLabels) !!}
        },
        yAxis: {
            type: 'value',
            name: 'Nombre de votes'
        },
        series: [
            {
                name: 'Validés GPS',
                type: 'bar',
                stack: 'total',
                data: {!! json_encode($eventValidesData) !!},
                itemStyle: { color: '#10B981' },
                label: {
                    show: true,
                    position: 'inside'
                }
            },
            {
                name: 'Hors Zone',
                type: 'bar',
                stack: 'total',
                data: {!! json_encode($eventHorsZoneData) !!},
                itemStyle: { color: '#EF4444' },
                label: {
                    show: true,
                    position: 'inside'
                }
            }
        ]
    };
    chartGPSValidation.setOption(optionsGPSValidation);
    window.addEventListener('resize', () => chartGPSValidation.resize());
    @endif
});
</script>
@endpush

@endsection
