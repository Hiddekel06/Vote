

@extends('layouts.app')


{{--
 Stock Dashboard Page
 Re-écriture propre pour s'intégrer dans le layout "layoutsAdmin.app".
 Le header, le footer et la sidebar sont fournis par le layout.
--}}


@section('title', 'Stock Dashboard')


@section('content')

<html>
    <h1>Gov'Athon 2025 Vote</h1>
</html>
<div class="content admin-dashboard">
    <style>
        .admin-dashboard .admin-hero {
            background: linear-gradient(135deg, rgba(34,197,94,0.06), rgba(59,130,246,0.05));
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 1rem;
        }
        .admin-dashboard .stat-pill { min-width:110px; padding:10px 14px; border-radius:10px; background:rgba(255,255,255,0.02); }
        .admin-dashboard .stat-pill .label { font-size:12px; color: #9CA3AF; }
        .admin-dashboard .stat-pill .value { font-size:18px; font-weight:700; }
        .admin-dashboard .card { border-radius:12px; box-shadow: 0 6px 18px rgba(2,6,23,0.06); }
        /* Stat card specific */
        .admin-dashboard .stat-card { padding:1rem; overflow:hidden; }
        .admin-dashboard .stat-card .stat-icon { width:56px; height:56px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; }
        .admin-dashboard .stat-label { font-size:13px; color:#6B7280; margin-bottom:6px; }
        .admin-dashboard .stat-value { font-size:20px; font-weight:700; }
        .admin-dashboard .stat-sub { font-size:12px; color:#9CA3AF; }
        @media (max-width:767px) { .admin-dashboard .admin-hero { padding:12px; } .admin-dashboard .stat-pill { min-width:90px; padding:8px 10px; } }
    </style>

    <div class="admin-hero d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('images/logoGov.jpeg') }}" alt="GovAthon Logo" style="width:68px; height:auto; border-radius:8px;" />
            <div>
                <h1 class="h4 mb-0">Admin Dashboard</h1>
                <p class="mb-0 text-muted small">Vue d'ensemble — statistiques et contrôles rapides</p>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3 mt-md-0">
            <div class="stat-pill text-center">
                <div class="label">Projets</div>
                <div class="value">{{ $totalProjets }}</div>
            </div>
            <div class="stat-pill text-center">
                <div class="label">Votes</div>
                <div class="value">{{ $totalVotes }}</div>
            </div>
            <div class="stat-pill text-center">
                <div class="label">Votants</div>
                <div class="value">{{ $totalVotants }}</div>
            </div>
        </div>
    </div>

       {{-- NOUVEAU : Cartes de statistiques --}}
       <div class="row g-3 mb-4">
           <!-- Carte Total Projets -->
           <div class="col-12 col-md-6 col-lg-3">
               <div class="card h-100 stat-card">
                   <div class="d-flex align-items-center">
                       <div class="stat-icon me-3" style="background:#ECFDF5;color:#059669;">
                           <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7"></path><path d="M16 3v4"></path><path d="M8 3v4"></path><path d="M3 11h18"></path></svg>
                       </div>
                       <div class="flex-1">
                           <div class="stat-label">Projets Validés</div>
                           <div class="stat-value">{{ $totalProjets }}</div>
                           <div class="stat-sub">Depuis la dernière semaine</div>
                       </div>
                   </div>
               </div>
           </div>
           <!-- Carte Total Votes -->
           <div class="col-12 col-md-6 col-lg-3">
               <div class="card h-100 stat-card">
                   <div class="d-flex align-items-center">
                       <div class="stat-icon me-3" style="background:#EFF6FF;color:#2563EB;">
                           <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10h-6l-2 9-2-5-4 5H3"></path></svg>
                       </div>
                       <div class="flex-1">
                           <div class="stat-label">Total des Votes</div>
                           <div class="stat-value">{{ $totalVotes }}</div>
                           <div class="stat-sub">Depuis la dernière semaine</div>
                       </div>
                   </div>
               </div>
           </div>
           <!-- Carte Votants Uniques -->
           <div class="col-12 col-md-6 col-lg-3">
               <div class="card h-100 stat-card">
                   <div class="d-flex align-items-center">
                       <div class="stat-icon me-3" style="background:#FFFBEB;color:#D97706;">
                           <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path><circle cx="11" cy="7" r="4"></circle></svg>
                       </div>
                       <div class="flex-1">
                           <div class="stat-label">Votants Uniques</div>
                           <div class="stat-value">{{ $totalVotants }}</div>
                           <div class="stat-sub">Depuis la dernière semaine</div>
                       </div>
                   </div>
               </div>
           </div>
           <!-- Carte Projet en Tête -->
           <div class="col-12 col-md-6 col-lg-3">
               <div class="card h-100 stat-card">
                   <div class="d-flex align-items-center">
                       <div class="stat-icon me-3" style="background:#F0F9FF;color:#0EA5E9;">
                           <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3 7h7l-5.5 4 2 7L12 16l-6.5 4 2-7L2 9h7z"></path></svg>
                       </div>
                       <div class="flex-1">
                           <div class="stat-label">Projet en Tête</div>
                           <div class="stat-value">{{ $projetEnTete?->nom_projet ?? 'N/A' }}</div>
                           <div class="stat-sub text-success">{{ $projetEnTete?->votes_count ?? 0 }} votes</div>
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
        // Labels spécifiques pour chaque profil
        var studentLabels = {!! json_encode($studentLabels ?? []) !!};
        var startupLabels = {!! json_encode($startupLabels ?? []) !!};
        var otherLabels = {!! json_encode($otherLabels ?? []) !!};
        
        // Données de votes pour chaque profil
        var studentData = {!! json_encode($studentData ?? []) !!};
        var startupData = {!! json_encode($startupData ?? []) !!};
        var otherData = {!! json_encode($otherData ?? []) !!};

        function renderBar(id, title, data, color, labels) {
            var dom = document.getElementById(id);
            if (!dom) return;
            var chart = echarts.init(dom);

            // create a subtle gradient from the provided color
            var gradientFill = new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                { offset: 0, color: color },
                { offset: 1, color: 'rgba(255,255,255,0.06)' }
            ]);

            var option = {
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: 'rgba(0,0,0,0.75)',
                    textStyle: { color: '#fff' }
                },
                grid: { left: '3%', right: '3%', bottom: '3%', containLabel: true },
                xAxis: {
                    type: 'category',
                    data: labels,
                    axisLabel: { color: '#9CA3AF' },
                    axisLine: { lineStyle: { color: 'rgba(148,163,184,0.18)' } },
                    axisTick: { show: false }
                },
                yAxis: {
                    type: 'value',
                    axisLabel: { color: '#9CA3AF' },
                    axisLine: { show: false },
                    splitLine: { lineStyle: { color: 'rgba(148,163,184,0.06)' } }
                },
                series: [{
                    name: title,
                    data: data,
                    type: 'bar',
                    barWidth: '42%',
                    itemStyle: {
                        color: gradientFill,
                        borderRadius: [6,6,4,4]
                    },
                    emphasis: { itemStyle: { opacity: 0.9 } }
                }]
            };
            chart.setOption(option);
            window.addEventListener('resize', chart.resize);
        }

        // use a modern palette and render three bar charts with their specific labels
        renderBar('chartVotesCategorieStudent', 'Étudiants', studentData, '#60a5fa', studentLabels); // bleu
        renderBar('chartVotesCategorieStartup', 'Startups', startupData, '#34d399', startupLabels); // vert
        renderBar('chartVotesCategorieOther', 'Citoyens', otherData, '#f59e0b', otherLabels); // jaune/orange

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
                   backgroundColor: 'transparent',
                   color: ['#60a5fa', '#34d399', '#f59e0b', '#A78BFA', '#FB7185'],
                   tooltip: { trigger: 'item', formatter: '{b}: {c} ({d}%)', backgroundColor: 'rgba(0,0,0,0.75)', textStyle: { color: '#fff' } },
                   legend: {
                       orient: 'vertical',
                       left: 'left',
                       textStyle: { color: '#9CA3AF' }
                   },
                   series: [
                       {
                           name: 'Votes par profil',
                           type: 'pie',
                           radius: ['40%', '64%'],
                           center: ['65%', '50%'],
                           roseType: false,
                           avoidLabelOverlap: true,
                           label: {
                               show: true,
                               position: 'outside',
                               formatter: '{b}\n{d}%',
                               color: '#111827'
                           },
                           labelLine: { length: 12, length2: 8, lineStyle: { color: 'rgba(34,34,34,0.08)' } },
                           itemStyle: {
                               borderColor: '#fff',
                               borderWidth: 2,
                               shadowBlur: 6,
                               shadowColor: 'rgba(0,0,0,0.08)'
                           },
                           emphasis: { itemStyle: { shadowBlur: 12, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.25)' } },
                           data: seriesData
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

               var palette = ['#60a5fa','#34d399','#f59e0b','#A78BFA','#FB7185'];

               var option = {
                   tooltip: {
                       trigger: 'axis',
                       backgroundColor: 'rgba(0,0,0,0.75)',
                       textStyle: { color: '#fff' },
                       axisPointer: { type: 'cross', label: { backgroundColor: '#6a7985' } }
                   },
                   legend: {
                       data: allLegendNames,
                       textStyle: { color: '#374151' }
                   },
                   grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                   xAxis: [{ type: 'category', boundaryGap: false, data: dailyVoteLabels, axisLine: { lineStyle: { color: 'rgba(15,23,42,0.06)' } }, axisLabel: { color: '#6B7280' } }],
                   yAxis: [{ type: 'value', axisLine: { show: false }, axisLabel: { color: '#6B7280' }, splitLine: { lineStyle: { color: 'rgba(15,23,42,0.04)' } } }],
                   series: allSeriesData.map(function(s, idx) {
                       var color = palette[idx % palette.length];
                       return Object.assign({}, s, {
                           smooth: true,
                           symbol: 'circle',
                           symbolSize: 6,
                           lineStyle: { width: 2, color: color },
                           areaStyle: {
                               color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                   { offset: 0, color: color + '33' },
                                   { offset: 1, color: 'rgba(255,255,255,0.02)' }
                               ])
                           }
                       });
                   })
               };

               myChart.setOption(option);
               window.addEventListener('resize', myChart.resize);
           });
       </script>

       {{-- Carte chaleur horaire des votes (jour x heure) --}}
       <div class="row g-3 mb-4">
           <div class="col-12">
               <div class="card">
                   <div class="card-header">
                       <h5 class="mb-0">Carte chaleur horaire des votes</h5>
                       <small class="text-muted">Visualise les pics de vote par jour et par heure</small>
                   </div>
                   <div class="card-body">
                       <div id="chartVotesHeatmap" style="min-height: 360px;"></div>
                   </div>
               </div>
           </div>
       </div>

       <script>
           document.addEventListener('DOMContentLoaded', function () {
               var dom = document.getElementById('chartVotesHeatmap');
               if (!dom) return;
               var chart = echarts.init(dom);

               var heatmapHours = {!! json_encode($heatmapHours ?? []) !!};
               var heatmapDays = {!! json_encode($heatmapDays ?? []) !!};
               var rawHeatmapData = {!! json_encode($heatmapData ?? []) !!};
               var heatmapMax = {!! json_encode($heatmapMax ?? 0) !!};

               var formattedData = (rawHeatmapData || []).map(function (item) {
                   return [item[0], item[1], item[2] || 0];
               });

               var maxValue = Math.max(heatmapMax || 0, 1);

               var option = {
                   tooltip: {
                       position: 'top',
                       backgroundColor: 'rgba(0,0,0,0.75)',
                       textStyle: { color: '#fff' },
                       formatter: function (params) {
                           var day = heatmapDays[params.value[1]] || '';
                           var hour = heatmapHours[params.value[0]] || '';
                           var val = params.value[2] || 0;
                           return day + ' ' + hour + '<br/>Votes: ' + val;
                       }
                   },
                   grid: { left: 50, right: 20, bottom: 40, top: 30 },
                   xAxis: {
                       type: 'category',
                       data: heatmapHours,
                       splitArea: { show: true },
                       axisLabel: { rotate: 45, color: '#6B7280' },
                       axisLine: { lineStyle: { color: 'rgba(148,163,184,0.3)' } }
                   },
                   yAxis: {
                       type: 'category',
                       data: heatmapDays,
                       splitArea: { show: true },
                       axisLabel: { color: '#6B7280' },
                       axisLine: { lineStyle: { color: 'rgba(148,163,184,0.3)' } }
                   },
                   visualMap: {
                       min: 0,
                       max: maxValue,
                       calculable: true,
                       orient: 'vertical',
                       right: 10,
                       bottom: 10,
                       inRange: { color: ['#fee2e2', '#f87171', '#b91c1c'] },
                       textStyle: { color: '#6B7280' }
                   },
                   series: [{
                       name: 'Votes',
                       type: 'heatmap',
                       data: formattedData,
                       emphasis: {
                           itemStyle: {
                               shadowBlur: 10,
                               shadowColor: 'rgba(0, 0, 0, 0.3)'
                           }
                       }
                   }]
               };

               chart.setOption(option);
               window.addEventListener('resize', chart.resize);
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