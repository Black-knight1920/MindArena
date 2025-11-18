// View/frontoffice/script.js - VERSION AMÉLIORÉE
// Validation du formulaire de don
const form = document.getElementById('donForm');

// Validation en temps réel
form.addEventListener('input', function(e) {
    validateField(e.target.name);
});

// Validation à la soumission
form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fields = ['montant', 'dateDon', 'typeDon', 'organisationId'];
    let isValid = true;
    
    fields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    if (isValid) {
        this.submit();
    } else {
        // Scroll vers la première erreur
        const firstError = document.querySelector('.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

function validateField(fieldName) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    const value = field.value.trim();
    
    // Supprimer l'erreur précédente
    removeError(field);
    
    let isValid = true;
    let message = '';
    
    switch(fieldName) {
        case 'montant':
            const montant = parseFloat(value);
            if (!value) {
                message = "❌ Le montant est obligatoire";
                isValid = false;
            } else if (isNaN(montant)) {
                message = "❌ Veuillez entrer un montant valide";
                isValid = false;
            } else if (montant <= 0) {
                message = "❌ Le montant doit être supérieur à 0€";
                isValid = false;
            } else if (montant > 1000000) {
                message = "❌ Le montant ne peut pas dépasser 1,000,000€";
                isValid = false;
            } else if (value.split('.')[1] && value.split('.')[1].length > 2) {
                message = "❌ Maximum 2 décimales autorisées";
                isValid = false;
            }
            break;
            
        case 'dateDon':
            if (!value) {
                message = "❌ La date est obligatoire";
                isValid = false;
            } else {
                const selectedDate = new Date(value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate > today) {
                    message = "❌ La date ne peut pas être dans le futur";
                    isValid = false;
                }
            }
            break;
            
        case 'typeDon':
            if (!value) {
                message = "❌ Veuillez sélectionner un type de don";
                isValid = false;
            }
            break;
            
        case 'organisationId':
            if (!value) {
                message = "❌ Veuillez sélectionner une organisation";
                isValid = false;
            }
            break;
    }
    
    if (!isValid) {
        showError(field, message);
    } else {
        showSuccess(field);
    }
    
    return isValid;
}

function showError(field, message) {
    field.classList.remove('valid');
    field.classList.add('invalid');
    
    let errorElement = field.parentNode.querySelector('.error');
    if (!errorElement) {
        errorElement = document.createElement('span');
        errorElement.className = 'error';
        field.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
}

function showSuccess(field) {
    field.classList.remove('invalid');
    field.classList.add('valid');
    removeError(field);
}

function removeError(field) {
    const errorElement = field.parentNode.querySelector('.error');
    if (errorElement) {
        errorElement.remove();
    }
}

// Animation au scroll
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll pour les liens d'ancrage
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Animation des cartes d'organisations
    const orgCards = document.querySelectorAll('#organisations > div > div');
    orgCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
});