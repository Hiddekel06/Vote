

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
       <div class="swiper-theme-container w-100">
         <div class="swiper theme-slider" data-swiper='{"spaceBetween":24,"loop":true,"centeredSlides":true,"slidesPerView":"auto","autoplay":{"delay":0},"freeMode":true,"speed":6500,"grabCursor":true}'>
           <div class="swiper-wrapper swiper-continuous-autoplay">


             <!-- Carte 1 : Agents Théoriques -->
           <div class="swiper-slide stock-overview-card">
               <div class="card">
               <div class="card-body">
                   <div class="d-flex flex-between-center gap-2 gap-lg-3">
                   <div class="flex-1">
                       <div class="d-flex align-items-center gap-2 mb-2">
                       <h5 class="mb-0 text-body-tertiary text-nowrap">Agents théoriques</h5>
                       <div class="badge badge-phoenix fs-10 d-flex align-items-center badge-phoenix-success">
                           Total<span class="ms-1 fas fa-users"></span>
                       </div>
                       </div>
                       <h4 class="mb-0">44</h4>
                   </div>
                   <div class="overview-echart echart-stock-overview-chart" data-echarts='{"data":[100,120,110,130,125,135,120,140]}'></div>


                   </div>
               </div>
               </div>
           </div>


           <!-- Carte 2 : Agents attestés -->
           <div class="swiper-slide stock-overview-card">
               <div class="card">
               <div class="card-body">
                   <div class="d-flex flex-between-center gap-2 gap-lg-3">
                   <div class="flex-1">
                       <div class="d-flex align-items-center gap-2 mb-2">
                       <h5 class="mb-0 text-body-tertiary text-nowrap">Agents certifiés</h5>
                       <div class="badge badge-phoenix fs-10 d-flex align-items-center badge-phoenix-success">
                           Validés<span class="ms-1 fas fa-certificate"></span>
                       </div>
                       </div>
                       <h4 class="mb-0">48</h4>
                   </div>
                   <div class="overview-echart echart-stock-overview-chart" data-echarts='{"data":[100,120,110,130,125,135,120,140]}'></div>


                   </div>
               </div>
               </div>
           </div>


           <!-- Carte 3 : Agents suspects -->
           <div class="swiper-slide stock-overview-card">
               <div class="card">
               <div class="card-body">
                   <div class="d-flex flex-between-center gap-2 gap-lg-3">
                   <div class="flex-1">
                       <div class="d-flex align-items-center gap-2 mb-2">
                       <h5 class="mb-0 text-body-tertiary text-nowrap">Écarts d’effectif</h5>
                       <div class="badge badge-phoenix fs-10 d-flex align-items-center badge-phoenix-danger">
                           <span class="ms-1 fas fa-exclamation-triangle"></span>
                       </div>
                       </div>
                       <h4 class="mb-0">908</h4>
                   </div>
                   <div class="overview-echart echart-stock-overview-inverted-chart" data-echarts='{"data":[-500,-300,-250,-280,-150,-250,-300,-180,-145,-250,-46,-250,-90,-80,-85,-150,-250,-180,-175,-50]}'></div>


                   </div>
               </div>
               </div>
           </div>
           <div class="swiper-slide stock-overview-card">
               <div class="card">
                   <div class="card-body">
                   <div class="d-flex flex-between-center gap-2 gap-lg-3">
                       <div class="flex-1">
                       <div class="d-flex align-items-center gap-2 mb-2">
                           <h5 class="mb-0 text-body-tertiary text-nowrap">Attestations en cours</h5>
                           <div class="badge badge-phoenix fs-10 d-flex align-items-center badge-phoenix-warning">
                           En attente<span class="ms-1 fas fa-hourglass-half"></span>
                           </div>
                       </div>
                       <h4 class="mb-0">98</h4>
                       </div>
                       <div class="overview-echart echart-stock-overview-chart" data-echarts='{"data":[60,80,70,90,85,100,110,95]}'></div>
                   </div>
                   </div>
               </div>
               </div>
               <div class="swiper-slide stock-overview-card">
                   <div class="card">
                       <div class="card-body">
                       <div class="d-flex flex-between-center gap-2 gap-lg-3">
                           <div class="flex-1">
                           <div class="d-flex align-items-center gap-2 mb-2">
                               <h5 class="mb-0 text-body-tertiary text-nowrap">Attestations validées</h5>
                               <div class="badge badge-phoenix fs-10 d-flex align-items-center badge-phoenix-success">
                               Validées<span class="ms-1 fas fa-check-circle"></span>
                               </div>
                           </div>
                           <h4 class="mb-0">41</h4>
                           </div>
                           <div class="overview-echart echart-stock-overview-chart" data-echarts='{"data":[100,120,140,130,150,160,170,180]}'></div>
                       </div>
                       </div>
                   </div>
                   </div>


                   <div class="swiper-slide stock-overview-card">
                   <div class="card">
                       <div class="card-body">
                       <div class="d-flex flex-between-center gap-2 gap-lg-3">
                           <div class="flex-1">
                           <div class="d-flex align-items-center gap-2 mb-2">
                               <h5 class="mb-0 text-body-tertiary text-nowrap">Agents en attente</h5>
                               <div class="badge badge-phoenix fs-10 d-flex align-items-center badge-phoenix-danger">
                               En cours<span class="ms-1 fas fa-user-clock"></span>
                               </div>
                           </div>
                           <h4 class="mb-0">789</h4>
                           </div>
                           <div class="overview-echart echart-stock-overview-inverted-chart" data-echarts='{"data":[-400,-300,-250,-200,-150,-100,-80,-60,-40,-20,-10,-30,-50,-70,-90,-110,-130,-150,-170,-190]}'></div>
                       </div>
                       </div>
                   </div>


           </div>
         </div>
       </div>
       <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 py-5 border-top mt-4">
       <h3 class="mb-4 text-body-emphasis">Répartition par structure</h3>


<div class="row gx-5 align-items-stretch">
 {{-- ◀️ Liste verticale des structures parentes --}}
 <div class="col-12 col-xl-5 col-xxl-4 mb-4 mb-xl-0 d-flex flex-column" style="max-height: 420px; overflow-y: auto;">
   <div class="search-box w-100 mb-3 pe-xl-3">
     <form class="position-relative">
       <input class="form-control search-input search" type="search" placeholder="Rechercher une structure…" aria-label="Search" />
       <span class="fas fa-search search-box-icon"></span>
     </form>
   </div>


   <div class="scrollbar flex-grow-1 pe-xl-3">
     <ul class="nav flex-column gap-3" id="structureTab" role="tablist">
      <li>Test</li>
     </ul>
   </div>
 </div>



</div>


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


<h3 class="mb-4 text-body-emphasis">Suivi des attestations critiques</h3>


<div class="row gx-5">
 {{-- ◀️ Liste des structures ayant validé toutes leurs attestations (ancien Top Gainers) --}}
 <div class="col-12 col-xl-5 col-xxl-4 mb-4 mb-xl-0 overflow-auto" style="max-height: 420px;">
   <div class="search-box w-100 mb-3 pe-xl-3">
     <form class="position-relative">
       <input class="form-control search-input search" type="search" placeholder="Rechercher une structure…" aria-label="Search" />
       <span class="fas fa-search search-box-icon"></span>
     </form>
   </div>


   <div class="scrollbar top-stock-tab w-100 pe-xl-3">
     <ul class="nav gap-3 flex-nowrap flex-xl-column" id="validatedStructureTab" role="tablist" style="overflow-y: auto; max-height: 340px;">

         <li class="nav-item">
           <div class="nav-link card company-card">
             <div class="card-body p-2">
               <div class="d-flex gap-3 align-items-center">
                 <span class="uil uil-check-circle fs-3 text-success"></span>
                 <div>
                   <h6 class="fw-semibold text-body-secondary mb-1 lh-sm text-nowrap"></h6>
                   <h5 class="mb-0 text-success">Toutes les attestations validées</h5>
                 </div>
               </div>
             </div>
           </div>
         </li>

     </ul>
   </div>
 </div>


 {{-- ▶️ Liste des attestations critiques (ancien Top Losers) --}}
 <div class="col-12 col-xl-7 col-xxl-8 ps-xl-0">
   <div class="rounded p-3 shadow-sm bg-body" style="max-height: 420px; overflow-y: auto;">
     <h5 class="text-body-emphasis mb-3">Attestations en attente depuis plus de 7 jours</h5>
     <table class="table table-sm table-hover align-middle mb-0">
       <thead class="table-light">
         <tr>
           <th>Référence</th>
           <th>Structure</th>
           <th>Date création</th>
           <th>Jours en attente</th>
         </tr>
       </thead>
       <tbody>
           <tr><td colspan="4" class="text-muted text-center">Aucune attestation critique actuellement.</td></tr>
      
       </tbody>
     </table>
   </div>
 </div>
</div>




     </div>
       <!-- ===============================================-->
   <!--    JavaScripts-->
   <!-- ===============================================-->


@endsection


@push('scripts')
 {{-- Chargez ici vos scripts ECharts/Chart.js ou autre --}}
 <script defer src="{{ asset('js/charts/stock-dashboard.js') }}"></script>
@endpush



