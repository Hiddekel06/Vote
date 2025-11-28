

@extends('layouts.app')


{{--
 Stock Dashboard Page
 Re-écriture propre pour s'intégrer dans le layout "layoutsAdmin.app".
 Le header, le footer et la sidebar sont fournis par le layout.
--}}


@section('title', 'Stock Dashboard')


@section('content')


<div class="content">
       <h2 class="mb-4 text-body-emphasis"> Dashboard</h2>

       {{-- NOUVEAU : Cartes de statistiques --}}
       <div class="row g-3 mb-4">
           <!-- Carte Total Projets -->
           <div class="col-12 col-md-6 col-lg-3">
               <div class="card h-100">
                   <div class="card-body">
                       <div class="d-flex flex-between-center">
                           <div class="flex-1">
                               <h5 class="mb-1 text-body-tertiary text-nowrap">Projets Validés</h5>
                               <h4 class="mb-0">{{ $totalProjets }}</h4>
                           </div>
                           <div class="fs-4 text-body-tertiary"><span class="fas fa-folder-open"></span></div>
                       </div>
                   </div>
               </div>
           </div>
           <!-- Carte Total Votes -->
           <div class="col-12 col-md-6 col-lg-3">
               <div class="card h-100">
                   <div class="card-body">
                       <div class="d-flex flex-between-center">
                           <div class="flex-1">
                               <h5 class="mb-1 text-body-tertiary text-nowrap">Total des Votes</h5>
                               <h4 class="mb-0">{{ $totalVotes }}</h4>
                           </div>
                           <div class="fs-4 text-body-tertiary"><span class="fas fa-vote-yea"></span></div>
                       </div>
                   </div>
               </div>
           </div>
           <!-- Carte Votants Uniques -->
           <div class="col-12 col-md-6 col-lg-3">
               <div class="card h-100">
                   <div class="card-body">
                       <div class="d-flex flex-between-center">
                           <div class="flex-1">
                               <h5 class="mb-1 text-body-tertiary text-nowrap">Votants Uniques</h5>
                               <h4 class="mb-0">{{ $totalVotants }}</h4>
                           </div>
                           <div class="fs-4 text-body-tertiary"><span class="fas fa-users"></span></div>
                       </div>
                   </div>
               </div>
           </div>
           <!-- Carte Projet en Tête -->
           <div class="col-12 col-md-6 col-lg-3">
               <div class="card h-100">
                   <div class="card-body">
                       <div class="d-flex flex-between-center">
                           <div class="flex-1">
                               <h5 class="mb-1 text-body-tertiary text-nowrap">Projet en Tête</h5>
                               <h6 class="mb-0 text-truncate" title="{{ $projetEnTete?->nom_projet }}">{{ $projetEnTete?->nom_projet ?? 'N/A' }}</h6>
                               <small class="text-success fw-bold">{{ $projetEnTete?->votes_count ?? 0 }} votes</small>
                           </div>
                           <div class="fs-4 text-body-tertiary"><span class="fas fa-trophy"></span></div>
                       </div>
                   </div>
               </div>
           </div>
       </div>

       {{-- NOUVEAU : Contrôle du système de vote --}}
       <div class="row g-3 mb-4">
           <div class="col-12">
               <div class="card">
                   <div class="card-header">
                       <h5 class="mb-0">Contrôle du système de vote</h5>
                   </div>
                   <div class="card-body">
                       <div class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" id="voteStatusToggle"
                               data-url="{{ route('admin.vote.status.update') }}"
                               {{ $currentStatus === 'active' ? 'checked' : '' }}>
                           <label class="form-check-label" for="voteStatusToggle">
                               <span id="voteStatusLabel">{{ $currentStatus === 'active' ? 'Vote Actif' : 'Vote Inactif' }}</span>
                           </label>
                       </div>
                       <p class="text-muted mt-2" id="voteStatusMessage">
                           Le système de vote est actuellement {{ $currentStatus === 'active' ? 'ouvert' : 'fermé' }}.
                       </p>
                   </div>
               </div>
           </div>
       </div>

       <script>
           document.addEventListener('DOMContentLoaded', function() {
               const voteStatusToggle = document.getElementById('voteStatusToggle');
               const voteStatusLabel = document.getElementById('voteStatusLabel');
               const voteStatusMessage = document.getElementById('voteStatusMessage');
                   console.log("Toggle trouvé :", voteStatusToggle);
    console.log("URL initiale :", voteStatusToggle.dataset.url);


               voteStatusToggle.addEventListener('change', function() {
                   const newStatus = this.checked ? 'active' : 'inactive';
                   const url = this.dataset.url;
                   console.log("Nouvel état choisi :", newStatus);
                       console.log('URL utilisée pour le PATCH :', url); 

                   fetch(url, {
                       method: 'PATCH',
                       credentials: 'same-origin', // include session cookie
                       headers: {
                           'Content-Type': 'application/json',
                           'X-CSRF-TOKEN': '{{ csrf_token() }}', // Laravel CSRF token
                           'Accept': 'application/json'
                       },
                       body: JSON.stringify({ vote_status: newStatus })
                   })
                   .then(async response => {
                       // If server returned non-2xx, try to parse JSON error, else text
                       let payload;
                       const text = await response.text();
                       try {
                           payload = JSON.parse(text || '{}');
                       } catch (e) {
                           payload = { message: text };
                       }

                       if (!response.ok) {
                           // Revert toggle and show error
                           this.checked = !this.checked;
                           console.error('Server error:', response.status, payload);
                           alert('Erreur lors de la mise à jour du statut du vote: ' + (payload.message || 'Erreur serveur'));
                           return;
                       }

                       // Success path
                       if (payload.success || response.status === 204) {
                           voteStatusMessage.textContent = `Le système de vote est actuellement ${newStatus === 'active' ? 'ouvert' : 'fermé'}.`;
                           voteStatusLabel.textContent = newStatus === 'active' ? 'Vote Actif' : 'Vote Inactif';
                       } else {
                           // Unexpected payload, revert toggle
                           this.checked = !this.checked;
                           console.warn('Unexpected response payload:', payload);
                           alert('La mise à jour a échoué: ' + (payload.message || 'Réponse inattendue'));
                       }
                   })
                   .catch(error => {
                       console.error('Network or parsing error:', error);
                       this.checked = !this.checked; // Revert on network error
                       alert('Une erreur est survenue lors de la communication avec le serveur.');
                   });
               });
           });
       </script>

       <div class="row g-3 mb-4">
           <div class="col-12">
               <div class="card">
    <div class="card-header">
        <h5 class="mb-0">Répartition des votes par catégorie — par profil</h5>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <label class="form-check-label me-2">Affichage</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="compactModeToggle">
                    <label class="form-check-label ms-2" for="compactModeToggle">Mode compact</label>
                </div>
            </div>
            <div id="profileButtons" class="btn-group" role="group" aria-label="Choisir profile" style="display:none;">
                <button type="button" class="btn btn-sm btn-outline-primary profile-btn active" data-profile="student">Étudiants</button>
                <button type="button" class="btn btn-sm btn-outline-success profile-btn" data-profile="startup">Startups</button>
                <button type="button" class="btn btn-sm btn-outline-warning profile-btn" data-profile="other">Citoyens</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="chartsContainer">
            <div class="chart-col" data-profile="student">
                <h6 class="text-muted">Étudiants</h6>
                <div id="chartVotesCategorieStudent" style="min-height: 250px;"></div>
            </div>
            <div class="chart-col" data-profile="startup">
                <h6 class="text-muted">Startups</h6>
                <div id="chartVotesCategorieStartup" style="min-height: 250px;"></div>
            </div>
            <div class="chart-col" data-profile="other">
                <h6 class="text-muted">Citoyens</h6>
                <div id="chartVotesCategorieOther" style="min-height: 250px;"></div>
            </div>
        </div>

        <div id="chartsLegend" class="mt-3" style="display:none;">
            <strong>Légende :</strong>
            <span class="badge bg-primary ms-2" style="background-color:#60a5fa;">Étudiants</span>
            <span class="badge bg-success ms-2" style="background-color:#34d399;">Startups</span>
            <span class="badge bg-warning ms-2" style="background-color:#f59e0b;">Citoyens</span>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var labels = {!! json_encode($secteurLabels ?? []) !!};
        var studentData = {!! json_encode($studentData ?? []) !!};
        var startupData = {!! json_encode($startupData ?? []) !!};
        var otherData = {!! json_encode($otherData ?? []) !!};

        function renderBar(id, title, data, color) {
            var dom = document.getElementById(id);
            if (!dom) return;
            var chart = echarts.init(dom);
            var option = {
                tooltip: { trigger: 'axis' },
                xAxis: { type: 'category', data: labels, axisLabel: { color: '#666' } },
                yAxis: { type: 'value', axisLabel: { color: '#666' } },
                series: [{ data: data, type: 'bar', itemStyle: { color: color } }]
            };
            chart.setOption(option);
            window.addEventListener('resize', chart.resize);
        }

        renderBar('chartVotesCategorieStudent', 'Étudiants', studentData, '#60a5fa'); // bleu
        renderBar('chartVotesCategorieStartup', 'Startups', startupData, '#34d399'); // vert
        renderBar('chartVotesCategorieOther', 'Citoyens', otherData, '#f59e0b'); // jaune/orange

        // Compact mode toggle behaviour
        var compactToggle = document.getElementById('compactModeToggle');
        var profileButtons = document.getElementById('profileButtons');
        var chartsLegend = document.getElementById('chartsLegend');
        var chartsContainer = document.getElementById('chartsContainer');
        var activeProfile = 'student';

        function setCompactMode(enabled) {
            if (enabled) {
                profileButtons.style.display = 'inline-flex';
                chartsLegend.style.display = 'block';
                // Show only active profile column
                Array.from(document.querySelectorAll('.chart-col')).forEach(function(col){
                    col.style.display = (col.dataset.profile === activeProfile) ? 'block' : 'none';
                });
                // make container single column
                chartsContainer.classList.remove('md:grid-cols-3');
                chartsContainer.classList.add('md:grid-cols-1');
            } else {
                profileButtons.style.display = 'none';
                chartsLegend.style.display = 'none';
                Array.from(document.querySelectorAll('.chart-col')).forEach(function(col){ col.style.display = 'block'; });
                chartsContainer.classList.remove('md:grid-cols-1');
                chartsContainer.classList.add('md:grid-cols-3');
            }
        }

        compactToggle.addEventListener('change', function(){
            setCompactMode(this.checked);
        });

        Array.from(document.querySelectorAll('.profile-btn')).forEach(function(btn){
            btn.addEventListener('click', function(){
                Array.from(document.querySelectorAll('.profile-btn')).forEach(function(b){ b.classList.remove('active'); });
                this.classList.add('active');
                activeProfile = this.dataset.profile;
                if (compactToggle.checked) setCompactMode(true);
            });
        });

        // Initialize compact mode off
        setCompactMode(false);
    });
</script>

           </div>
       </div>

       {{-- NOUVEAU : Répartition des votes par profil (camembert) --}}
       <div class="row g-3 mb-4">
           <div class="col-12">
               <div class="card">
                   <div class="card-header">
                       <h5 class="mb-0">Répartition des votes par profil</h5>
                   </div>
                   <div class="card-body">
                       <div id="chartVotesProfiles" style="min-height: 300px;"></div>
                   </div>
               </div>
           </div>
       </div>

       <script>
           document.addEventListener('DOMContentLoaded', function () {
               var dom = document.getElementById('chartVotesProfiles');
               if (!dom) return;
               var chart = echarts.init(dom);

               // Données dynamiques fournies par DashboardController
               var profileLabels = {!! json_encode($profileTypeLabels ?? []) !!};
               var profileData = {!! json_encode($profileTypeData ?? []) !!};

               // Construire le tableau attendu par ECharts
               var seriesData = profileLabels.map(function(label, idx) {
                   return { name: label, value: profileData[idx] ?? 0 };
               });

               var option = {
                   tooltip: { trigger: 'item', formatter: '{b}: {c} ({d}%)' },
                   legend: { orient: 'vertical', left: 'left' },
                   series: [
                       {
                           name: 'Votes par profil',
                           type: 'pie',
                           radius: '60%',
                           data: seriesData,
                           emphasis: { itemStyle: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.5)' } }
                       }
                   ]
               };

               chart.setOption(option);
               window.addEventListener('resize', chart.resize);
           });
       </script>

       {{-- NOUVEAU : Évolution des votes par jour --}}
       <div class="row g-3 mb-4">
           <div class="col-12">
               <div class="card">
                   <div class="card-header">
                       <h5 class="mb-0">Évolution des votes par jour</h5>
                   </div>
                   <div class="card-body">
                       <div id="chartDailyVoteEvolution" style="min-height: 300px;"></div>
                   </div>
               </div>
           </div>
       </div>

       <script>
           document.addEventListener("DOMContentLoaded", function () {
               var chartDom = document.getElementById('chartDailyVoteEvolution');
               var myChart = echarts.init(chartDom);

               var dailyVoteLabels = {!! json_encode($dailyVoteLabels) !!};
               var allSeriesData = {!! json_encode($allSeriesData) !!};
               var allLegendNames = {!! json_encode($allLegendNames) !!};

               var option = {
                   tooltip: {
                       trigger: 'axis',
                       axisPointer: {
                           type: 'cross',
                           label: {
                               backgroundColor: '#6a7985'
                           }
                       }
                   },
                   legend: {
                       data: allLegendNames,
                       textStyle: {
                           color: '#ccc' // Adjust legend text color for dark theme
                       }
                   },
                   grid: {
                       left: '3%',
                       right: '4%',
                       bottom: '3%',
                       containLabel: true
                   },
                   xAxis: [
                       {
                           type: 'category',
                           boundaryGap: false,
                           data: dailyVoteLabels,
                           axisLabel: { color: '#ccc' }
                       }
                   ],
                   yAxis: [
                       {
                           type: 'value',
                           axisLabel: { color: '#ccc' }
                       }
                   ],
                   series: allSeriesData
               };

               myChart.setOption(option);
               window.addEventListener('resize', myChart.resize);
           });
       </script>

       {{-- NOUVEAU : Top 3 Projets --}}
       <div class="row g-3 mb-4">
           <div class="col-12">
               <div class="card">
                   <div class="card-header">
                       <h5 class="mb-0">Top 3 Projets</h5>
                   </div>
                   <div class="card-body">
                       @if($top3Projects->isNotEmpty())
                           <ul class="list-group">
                               @foreach($top3Projects as $index => $project)
                                   <li class="list-group-item d-flex justify-content-between align-items-center">
                                       <span class="badge bg-primary rounded-pill me-2">{{ $index + 1 }}</span>
                                       {{ $project->nom_projet }} ({{ $project->votes_count }} votes)
                                       <span class="badge bg-success rounded-pill">{{ $project->secteur->nom ?? 'N/A' }}</span>
                                   </li>
                               @endforeach
                           </ul>
                       @else
                           <p class="text-muted">Aucun projet dans le top 3 pour le moment.</p>
                       @endif
                   </div>
               </div>
           </div>
       </div>
         <div class="mb-4">


{{-- Script Chart.js --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
 document.querySelectorAll(".structure-chart").forEach(canvas => {
   const labels = JSON.parse(canvas.dataset.labels || '[]');
   const values = JSON.parse(canvas.dataset.values || '[]');


   const ctx = canvas.getContext("2d");
   const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
   gradient.addColorStop(0, "rgba(79, 70, 229, 0.4)");
   gradient.addColorStop(1, "rgba(79, 70, 229, 0.05)");


   new Chart(canvas, {
     type: 'line',
     data: {
       labels: labels,
       datasets: [{
         label: "Évolution des agents",
         data: values,
         fill: true,
         tension: 0.4,
         borderWidth: 2,
         borderColor: "#10b981", // Vert émeraude (teal)
backgroundColor: "rgba(16, 185, 129, 0.1)",
pointBackgroundColor: "#10b981",


         pointRadius: 4,
         pointHoverRadius: 6,
       }]
     },
     options: {
       responsive: true,
       maintainAspectRatio: false,
       plugins: {
         legend: { display: false },
         tooltip: {
           callbacks: {
             label: ctx => `${ctx.parsed.y} agents`
           }
         }
       },
       scales: {
         y: {
           beginAtZero: true,
           ticks: {
             callback: val => val + " agents"
           }
         },
         x: {
           ticks: {
             maxRotation: 45,
             autoSkip: true
           }
         }
       }
     }
   });
 });
});
</script>
@endpush
</div>

     </div>
       <!-- ===============================================-->
   <!--    JavaScripts-->
   <!-- ===============================================-->


@endsection


@push('scripts')
 {{-- Chargez ici vos scripts ECharts/Chart.js ou autre --}}
 <script defer src="{{ asset('js/charts/stock-dashboard.js') }}"></script>
 <script>
    document.addEventListener('DOMContentLoaded', () => {
        const {
            phoenix: {
                echarts: {
                    echartSetOption
                },
                colors
            }
        } = window;

        const chartEl = document.querySelector('.echart-basic-bar-chart-example');

        if (chartEl) {
            const userOptions = {
                // Remplacez ces données par les vôtres
                xAxis: {
                    data: ['Catégorie 1', 'Catégorie 2', 'Catégorie 3', 'Catégorie 4', 'Catégorie 5']
                },
                series: [{
                    name: 'Nombre de votes',
                    data: [120, 200, 150, 80, 70], // Données de vote pour chaque catégorie
                    itemStyle: {
                        color: colors.primary
                    }
                }],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    },
                    formatter: '{b}: {c} votes'
                }
            };

            echartSetOption(chartEl, userOptions, () => ({
                // Options de base d'ECharts ici si nécessaire
            }));
        }
    });
 </script>
     <script src="{{ asset('public/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('public/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('public/vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('public/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('public/vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('public/vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('public/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('public/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/phoenix.js') }}"></script>
    
@endpush