   <nav class="navbar navbar-vertical navbar-expand-lg">
      <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
      <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
         <div class="navbar-vertical-content scrollbar">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
               <li class="nav-item">
                  <a class="nav-link" href="{{ route('admin.dashboard') }}">
                     <div class="d-flex align-items-center">
                        <span class="nav-link-icon"><span class="fas fa-chart-line"></span></span>
                        <span class="nav-link-text">Dashboard</span>
                     </div>
                  </a>
               </li>
               <li class="nav-item">
                  <a class="nav-link collapsed" href="#navbarVerticalStats" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbarVerticalStats">
                     <div class="d-flex align-items-center">
                        <span class="nav-link-icon"><span class="fas fa-chart-bar"></span></span>
                        <span class="nav-link-text">Statistiques</span>
                     </div>
                  </a>
                  <ul class="nav collapse" id="navbarVerticalStats">
                     <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.statistiques') }}">
                           <span class="nav-link-text">📊 Vote Public</span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.statistiques.jour-j') }}">
                           <span class="nav-link-text">📍 Vote Jour J</span>
                        </a>
                     </li>
                  </ul>
               </li>
               <li class="nav-item">
                  <a class="nav-link collapsed" href="#navbarVerticalVote" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbarVerticalVote">
                     <div class="d-flex align-items-center">
                        <span class="nav-link-icon"><span class="fas fa-vote-yea"></span></span>
                        <span class="nav-link-text">Gestion des Votes</span>
                     </div>
                  </a>
                  <ul class="nav collapse" id="navbarVerticalVote">
                     <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.vote-events.index') }}">
                           <span class="nav-link-text">📋 Tous les événements</span>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.vote-events.create') }}">
                           <span class="nav-link-text">➕ Créer un événement</span>
                        </a>
                     </li>
                  </ul>
               </li>
            </ul>
         </div>
      </div>
   </nav>