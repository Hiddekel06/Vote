<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoteController; 
use App\Http\Controllers\Admin\DashboardController; 
use App\Http\Controllers\Admin\VoteStatusController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ClassementController;
use App\Helpers\FileChecker;
use App\Http\Controllers\OrangeSmsController;

// 🔹 Page d’accueil (simple page de présentation)
Route::get('/', [VoteController::class, 'choixCategorie'])->name('vote.index');

// 🔹 Page de vote et de recherche (avec paramètre projet_id optionnel pour le partage)
Route::get('/vote/projet/{id}', [VoteController::class, 'afficherProjet'])->name('vote.afficherProjet');

Route::get('/vote/categorie/{profile_type}', [VoteController::class, 'index'])->name('vote.secteurs')
    ->whereIn('profile_type', ['student', 'startup', 'other']);

// Endpoint léger pour récupérer les détails d'un projet (JSON limité)
Route::get('/vote/project/{id}/data', [VoteController::class, 'projectData'])->name('vote.project.data');

// Route pour la recherche dynamique (AJAX)
Route::get('/vote/recherche-ajax', [VoteController::class, 'rechercheAjax'])->name('vote.rechercheAjax');

// --- Processus de vote ---
// L'utilisateur soumet le formulaire de la modale pour recevoir son code OTP
Route::post('/vote/envoyer-otp', [VoteController::class, 'envoyerOtp'])
    ->middleware('throttle:3,10') // Limite à 3 demandes d'OTP toutes les 10 minutes par IP
    ->name('vote.envoyerOtp');

// L'utilisateur soumet le code OTP pour valider son vote
Route::post('/vote/verifier-otp', [VoteController::class, 'verifierOtp'])
    ->middleware('throttle:5,10') // Limite à 5 tentatives de vérification toutes les 10 minutes par IP
    ->name('vote.verifierOtp');

// 🔹 Page de classement des projets (Général et par catégorie)
Route::get('/classement', [ClassementController::class, 'index'])->name('projets.classement');

// Route pour le partage d'un projet
// --- Section Administrateur ---
// On ajoute 'role.admin' pour s'assurer que seul un admin peut accéder à ces routes.
Route::middleware(['auth', 'verified', 'role.admin:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Page des statistiques
    Route::get('/statistiques', [DashboardController::class, 'statistiques'])->name('statistiques');
    // Route pour mettre à jour le statut et la période du vote
    Route::patch('/vote-status', [VoteStatusController::class, 'update'])->name('vote.status.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/admin/statistiques/export/pdf', [\App\Http\Controllers\Admin\DashboardController::class, 'exportStatistiquesPDF'])->name('admin.statistiques.export.pdf');
Route::get('/admin/statistiques/export/csv', [\App\Http\Controllers\Admin\DashboardController::class, 'exportStatistiquesCSV'])->name('admin.statistiques.export.csv');



Route::get('/check-assets', function() {
    $files = [
        'vendors/simplebar/simplebar.min.css',
        'assets/css/theme-rtl.min.css',
        'assets/css/theme.min.css',
        'assets/css/user-rtl.min.css',
        'assets/css/user.min.css',
        'vendors/popper/popper.min.js',
        'vendors/bootstrap/bootstrap.min.js',
        'vendors/anchorjs/anchor.min.js',
        'vendors/is/is.min.js',
        'vendors/fontawesome/all.min.js',
        'vendors/lodash/lodash.min.js',
        'vendors/list.js/list.min.js',
        'vendors/feather-icons/feather.min.js',
        'vendors/dayjs/dayjs.min.js',
        'vendors/leaflet/leaflet.js',
        'vendors/leaflet.markercluster/leaflet.markercluster.js',
        'vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js',
        'assets/js/phoenix.js',
        'vendors/echarts/echarts.min.js',
        'assets/js/dashboards/ecommerce-dashboard.js', // optionnel, décommente si tu veux tester
    ];

    foreach($files as $file) {
        FileChecker::checkAsset($file);
    }
});
Route::post('/send-otp', [OrangeSmsController::class, 'sendOtp']);

Route::get('/apropos', [App\Http\Controllers\PageController::class, 'apropos'])->name('apropos');

require __DIR__.'/auth.php';
