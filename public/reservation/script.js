// ========== COMPTE À REBOURS ==========
function updateCountdown() {
    // Date cible du GOVATHON (à modifier selon votre date)
    const targetDate = new Date('2025-12-31T23:59:59').getTime();
    
    const interval = setInterval(() => {
        const now = new Date().getTime();
        const distance = targetDate - now;
        
        if (distance < 0) {
            clearInterval(interval);
            document.getElementById('countdown-days').textContent = '00';
            document.getElementById('countdown-hours').textContent = '00';
            document.getElementById('countdown-minutes').textContent = '00';
            document.getElementById('countdown-seconds').textContent = '00';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('countdown-days').textContent = String(days).padStart(2, '0');
        document.getElementById('countdown-hours').textContent = String(hours).padStart(2, '0');
        document.getElementById('countdown-minutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('countdown-seconds').textContent = String(seconds).padStart(2, '0');
    }, 1000);
}

// ========== GESTION DE LA MODAL ==========
const modal = document.getElementById('reservationModal');
const closeBtn = document.querySelector('.close-modal');

function showFormModal() {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeFormModal() {
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    resetForm();
}

// Fermer la modal en cliquant en dehors
window.onclick = function(event) {
    if (event.target === modal) {
        closeFormModal();
    }
}

// ========== GESTION DU FORMULAIRE ==========
let isSending = false;
let showOtpInput = false;
let hideSubmitButton = false;
let loading = false;
let otpMessage = '';
let generatedOtp = '';

function resetForm() {
    document.getElementById('userForm').reset();
    showOtpInput = false;
    hideSubmitButton = false;
    otpMessage = '';
    document.getElementById('otpSection').style.display = 'none';
    document.getElementById('submitSection').style.display = 'block';
    document.getElementById('otpVerifySection').style.display = 'none';
    document.getElementById('otpError').textContent = '';
}

async function submitForm(e) {
    e.preventDefault();
    
    if (isSending) return;
    
    const form = document.getElementById('userForm');
    const formData = {
        nom: form.nom.value,
        prenom: form.prenom.value,
        telephone: form.telephone.value,
        email: form.email.value,
        fonction: form.fonction.value
    };
    
    // Validation
    if (!formData.nom || !formData.prenom || !formData.telephone || !formData.email || !formData.fonction) {
        alert('Veuillez remplir tous les champs !');
        return;
    }
    
    // Validation email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        alert('Email invalide !');
        return;
    }
    
    isSending = true;
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtnText').textContent = 'Envoi...';
    
    try {
        // Générer un code OTP (6 chiffres)
        generatedOtp = Math.floor(100000 + Math.random() * 900000).toString();
        
        // Simuler l'envoi d'OTP (à remplacer par votre API)
        await sendOtp(formData, generatedOtp);
        
        // Afficher la section OTP
        showOtpInput = true;
        hideSubmitButton = true;
        document.getElementById('otpSection').style.display = 'block';
        document.getElementById('submitSection').style.display = 'none';
        document.getElementById('otpVerifySection').style.display = 'block';
        
        console.log('OTP généré:', generatedOtp); // Pour tester
        
    } catch (error) {
        alert('Erreur lors de l\'envoi. Veuillez réessayer.');
        console.error(error);
    } finally {
        isSending = false;
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('submitBtnText').textContent = 'Générer un OTP';
    }
}

async function sendOtp(formData, otp) {
    // Simuler un appel API (remplacez par votre vrai endpoint)
    return new Promise((resolve) => {
        setTimeout(() => {
            console.log('Envoi OTP à:', formData.email, 'et', formData.telephone);
            console.log('OTP:', otp);
            resolve();
        }, 2000);
    });
    
    /* Exemple avec fetch vers votre API :
    const response = await fetch('https://votre-api.com/send-otp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ...formData, otp })
    });
    
    if (!response.ok) {
        throw new Error('Erreur lors de l\'envoi');
    }
    
    return response.json();
    */
}

function onOtpChange(event) {
    const value = event.target.value;
    if (value.length > 6) {
        event.target.value = value.slice(0, 6);
    }
}

async function verifyOtp() {
    const otpInput = document.getElementById('otpInput').value;
    
    if (!otpInput || otpInput.length !== 6) {
        otpMessage = 'Veuillez entrer un code OTP à 6 chiffres';
        document.getElementById('otpError').textContent = otpMessage;
        return;
    }
    
    loading = true;
    document.getElementById('verifyBtn').disabled = true;
    document.getElementById('verifyBtn').innerHTML = '<i class="loading-icon"></i> Vérification...';
    
    try {
        // Vérifier l'OTP (à remplacer par votre API)
        await new Promise((resolve) => setTimeout(resolve, 1500));
        
        if (otpInput === generatedOtp) {
            alert('✅ Réservation confirmée avec succès !');
            closeFormModal();
        } else {
            otpMessage = '❌ Code OTP incorrect. Veuillez réessayer.';
            document.getElementById('otpError').textContent = otpMessage;
        }
        
    } catch (error) {
        otpMessage = 'Erreur lors de la vérification';
        document.getElementById('otpError').textContent = otpMessage;
    } finally {
        loading = false;
        document.getElementById('verifyBtn').disabled = false;
        document.getElementById('verifyBtn').textContent = 'Valider';
    }
}

// ========== BOUTON SCROLL TO TOP ==========
const scrollTopBtn = document.getElementById('scrollTopBtn');

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        scrollTopBtn.style.display = 'flex';
    } else {
        scrollTopBtn.style.display = 'none';
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// ========== INITIALISATION ==========
document.addEventListener('DOMContentLoaded', () => {
    updateCountdown();
    
    // Event listeners
    document.getElementById('reserveBtn').addEventListener('click', showFormModal);
    closeBtn.addEventListener('click', closeFormModal);
    document.getElementById('userForm').addEventListener('submit', submitForm);
    document.getElementById('verifyBtn').addEventListener('click', verifyOtp);
    document.getElementById('otpInput').addEventListener('input', onOtpChange);
    scrollTopBtn.addEventListener('click', scrollToTop);
});
