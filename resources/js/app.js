import './bootstrap';
import '../css/app.css';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// On importe notre logique de vote spécifique
import './vote.js';

// AJAX pagination for classement
import './pagination';

/**
 * Gère le partage d'un projet.
 * Utilise l'API Web Share si disponible, sinon copie le lien dans le presse-papiers.
 * @param {string} url L'URL du projet à partager.
 */
window.shareProject = async function(url) {
    const shareData = {
        title: 'Découvrez ce projet sur GovAthon !',
        text: 'J\'ai trouvé ce projet intéressant sur GovAthon, jetez-y un œil !',
        url: url,
    };

    if (navigator.share) {
        try {
            await navigator.share(shareData);
            console.log('Projet partagé avec succès !');
        } catch (err) {
            console.error('Erreur lors du partage :', err);
        }
    } else {
        // Fallback pour les navigateurs qui ne supportent pas l'API Web Share
        try {
            await navigator.clipboard.writeText(url);
            alert('Lien du projet copié dans le presse-papiers !');
        } catch (err) {
            console.error('Impossible de copier le lien :', err);
            alert('Impossible de copier le lien.');
        }
    }
}
