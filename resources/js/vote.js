// c:\xampp\htdocs\Laravel\GovAthon\resources\js\vote.js

console.log('Le fichier vote.js est chargé.');

// Avoid double initialization
if (!window.__voteHandlersInit) {
    window.__voteHandlersInit = true;
}

function initVoteHandlers() {
    // Avoid running the init multiple times
    if (initVoteHandlers._done) return;
    initVoteHandlers._done = true;

    // On vérifie si on est bien sur la page de vote avant d'exécuter le code
    const voteContainer = document.querySelector('[x-data*="showVoteModal"]');
    if (!voteContainer) {
        console.log('vote.js: pas de container de vote trouvé — abort.');
        return; // Not on vote page
    }

    console.log('✅ Alpine prêt ou détecté sur la page de vote.');

    let alpineScope = null;
    try {
        if (typeof Alpine?.$data === 'function') {
            alpineScope = Alpine.$data(voteContainer);
        } else {
            console.warn('Alpine.$data is not a function on this Alpine instance');
        }
    } catch (e) {
        console.error('Erreur lors de la récupération du scope Alpine:', e);
    }

    console.log('Debug: alpineScope ->', alpineScope);

    const otpRequestForm = document.getElementById('otp-request-form');
    const submitVoteBtn = document.getElementById('submit-vote-btn');
    const otpVerifyForm = document.getElementById('otp-verify-form');
    const submitOtpBtn = document.getElementById('submit-otp-btn');

    if (!otpRequestForm || !submitVoteBtn || !otpVerifyForm || !submitOtpBtn) {
        console.error('Un ou plusieurs éléments du formulaire de vote n\'ont pas été trouvés.', {
            otpRequestForm, submitVoteBtn, otpVerifyForm, submitOtpBtn
        });
        return;
    }

    // Helper to safely set Alpine properties with logging and fallback
    const safeSet = (obj, key, value) => {
        try {
            if (obj) obj[key] = value;
        } catch (e) {
            console.error(`Échec assign ${key}`, e, { obj, key, value });
        }
    };

    // Envoi OTP
    submitVoteBtn.addEventListener('click', async () => {
        console.log('Clic détecté sur le bouton "Recevoir le code"');

        if (!alpineScope) {
            console.warn('Alpine scope introuvable — tentative fallback via event dispatch.');
        }

        safeSet(alpineScope, 'isLoading', true);
        safeSet(alpineScope, 'errorMessage', '');

        const countryCodeEl = document.getElementById('country_code');
        const phoneDisplayEl = document.getElementById('telephone_display');
        const telephoneFullEl = document.getElementById('telephone_full');

        if (!countryCodeEl || !phoneDisplayEl || !telephoneFullEl) {
            console.error('Éléments téléphone manquants', { countryCodeEl, phoneDisplayEl, telephoneFullEl });
            safeSet(alpineScope, 'isLoading', false);
            return;
        }

        telephoneFullEl.value = countryCodeEl.value + phoneDisplayEl.value;

        console.log('Debug: recaptchaKey/data-send-url ->', otpRequestForm.dataset.recaptchaKey, otpRequestForm.dataset.sendOtpUrl);

        try {
            await new Promise((resolve) => {
                if (typeof grecaptcha === 'undefined' || !grecaptcha?.ready) {
                    console.warn('grecaptcha not available');
                    return resolve();
                }
                grecaptcha.ready(function() {
                    grecaptcha.execute(otpRequestForm.dataset.recaptchaKey, { action: 'vote' })
                        .then(function(token) {
                            const tEl = document.getElementById('recaptcha-token');
                            if (tEl) tEl.value = token;
                            resolve();
                        }).catch(err => {
                            console.error('grecaptcha.execute failed', err);
                            resolve();
                        });
                });
            });

            const formData = new FormData(otpRequestForm);
            const url = otpRequestForm.dataset.sendOtpUrl;
            console.log('Numéro complet:', document.getElementById('telephone_full').value, 'POST ->', url);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                    'Accept': 'application/json',
                },
                body: formData
            });

            let data = null;
            try { data = await response.json(); } catch(e) { console.warn('Invalid JSON response', e); }
            console.log('OTP send response', response.status, data);

            if (!response.ok) {
                const msg = data?.message || ('Erreur serveur: ' + response.status);
                safeSet(alpineScope, 'errorMessage', msg);
                safeSet(alpineScope, 'isLoading', false);
                return;
            }

            if (data?.success) {
                // If Alpine scope is available, update it; otherwise, emit an event
                if (alpineScope) {
                    safeSet(alpineScope, 'successMessage', data?.message || 'Code envoyé !');
                    safeSet(alpineScope, 'voteStep', 2);

                } else {
                    window.dispatchEvent(new CustomEvent('otp-sent', { detail: data }));
                }
            } else {
                safeSet(alpineScope, 'errorMessage', data?.message || 'Erreur inattendue');
            }

        } catch (error) {
            console.error('Erreur lors de l\'envoi OTP:', error);
            safeSet(alpineScope, 'errorMessage', error.message || String(error));
        } finally {
            safeSet(alpineScope, 'isLoading', false);
        }
    });

    // Validation OTP
    submitOtpBtn.addEventListener('click', async () => {
        safeSet(alpineScope, 'isLoading', true);
        safeSet(alpineScope, 'errorMessage', '');

        const formData = new FormData(otpVerifyForm);
        const url = otpVerifyForm.dataset.verifyOtpUrl;
        console.log('Numéro complet:', document.getElementById('telephone_full').value);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Erreur lors de la vérification.');

            if (data.success) {
                safeSet(alpineScope, 'successMessage', data.message);
                safeSet(alpineScope, 'voteStep', 3);
            }
        } catch (error) {
            safeSet(alpineScope, 'errorMessage', error.message);
            if (error.message.includes('expiré') || error.message.includes('invalide')) {
                safeSet(alpineScope, 'voteStep', 3);
            }
        } finally {
            safeSet(alpineScope, 'isLoading', false);
        }
    });
}

// Bind to alpine:init (when imported before Alpine starts)
document.addEventListener('alpine:init', () => {
    try { initVoteHandlers(); } catch (e) { console.error('initVoteHandlers error on alpine:init', e); }
});

// If Alpine is already present and the DOM is ready, initialize immediately
if (document.readyState !== 'loading' && typeof Alpine !== 'undefined') {
    try { initVoteHandlers(); } catch (e) { console.error('initVoteHandlers error immediate', e); }
} else {
    // Also try to initialize after DOMContentLoaded as a fallback
    document.addEventListener('DOMContentLoaded', () => {
        try { initVoteHandlers(); } catch (e) { console.error('initVoteHandlers error on DOMContentLoaded', e); }
    });
}
