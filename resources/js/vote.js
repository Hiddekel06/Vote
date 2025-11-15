// c:\xampp\htdocs\Laravel\GovAthon\resources\js\vote.js

console.log('Le fichier vote.js est chargé.');

// On s'assure que le DOM est chargé avant d'exécuter le code.
// L'événement 'alpine:init' est parfait car il garantit aussi que Alpine.js est prêt.
document.addEventListener('alpine:init', () => {
    // On vérifie si on est bien sur la page de vote avant d'exécuter le code
    // pour éviter des erreurs sur d'autres pages.
    const voteContainer = document.querySelector('[x-data*="showVoteModal"]');
    if (!voteContainer) {
        return; // On n'est pas sur la page de vote, on ne fait rien.
    }

    console.log('✅ Alpine prêt sur la page de vote.');

    const alpineScope = Alpine.$data(voteContainer);
    const otpRequestForm = document.getElementById('otp-request-form');
    const submitVoteBtn = document.getElementById('submit-vote-btn');
    const otpVerifyForm = document.getElementById('otp-verify-form');
    const submitOtpBtn = document.getElementById('submit-otp-btn');

    // Si les éléments ne sont pas trouvés, on arrête pour éviter des erreurs.
    if (!otpRequestForm || !submitVoteBtn || !otpVerifyForm || !submitOtpBtn) {
        console.error("Un ou plusieurs éléments du formulaire de vote n'ont pas été trouvés.");
        return;
    }

    // Envoi OTP
    submitVoteBtn.addEventListener('click', async () => {
        console.log('Clic détecté sur le bouton "Recevoir le code"');

        alpineScope.isLoading = true;
        alpineScope.errorMessage = '';

        const countryCode = document.getElementById('country_code').value;
        const phoneDisplay = document.getElementById('telephone_display').value;
        document.getElementById('telephone_full').value = countryCode + phoneDisplay;

        grecaptcha.ready(function() {
            grecaptcha.execute(otpRequestForm.dataset.recaptchaKey, { action: 'vote' })
                .then(async function(token) {
                    document.getElementById('recaptcha-token').value = token;

                    const formData = new FormData(otpRequestForm);
                    const url = otpRequestForm.dataset.sendOtpUrl;
                    console.log('Numéro complet:', document.getElementById('telephone_full').value);

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Erreur serveur.');

                        if (data.success) {
                            alpineScope.voteStep = 2;
                        }
                    } catch (error) {
                        alpineScope.errorMessage = error.message;
                    } finally {
                        alpineScope.isLoading = false;
                    }
                });
        });
    });

    // Validation OTP
    submitOtpBtn.addEventListener('click', async () => {
        alpineScope.isLoading = true;
        alpineScope.errorMessage = '';

        const formData = new FormData(otpVerifyForm);
        const url = otpVerifyForm.dataset.verifyOtpUrl;
        console.log('Numéro complet:', document.getElementById('telephone_full').value);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Erreur lors de la vérification.');

            if (data.success) {
                alpineScope.successMessage = data.message;
                alpineScope.voteStep = 3;
            }
        } catch (error) {
            alpineScope.errorMessage = error.message;
            if (error.message.includes('expiré') || error.message.includes('invalide')) {
                alpineScope.voteStep = 3;
            }
        } finally {
            alpineScope.isLoading = false;
        }
    });
});
