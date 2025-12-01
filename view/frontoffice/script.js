script.js
// View/frontoffice/script.js - VERSION CORRIGÉE AVEC VALIDATION COMPLÈTE
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('donForm');

    if (form) {
        // Validation en temps réel
        form.addEventListener('input', function(e) {
            validateField(e.target.name);
        });

        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Valider TOUS les champs requis
            const isValid = validateAllFields();
            
            if (isValid) {
                this.submit();
            } else {
                // Scroll vers la première erreur
                const firstError = document.querySelector('.error-message');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Valider tous les champs au chargement (pour voir l'état initial)
        setTimeout(() => {
            validateAllFields(true); // true = mode silencieux (pas d'affichage d'erreur)
        }, 100);
    }
});

function validateAllFields(initialLoad = false) {
    const requiredFields = ['montant', 'dateDon', 'typeDon'];
    const orgField = document.querySelector('[name="organisationId"]');
    if (orgField && orgField.offsetParent !== null) {
        requiredFields.push('organisationId');
    }
    
    let allValid = true;
    
    requiredFields.forEach(field => {
        const isValid = validateField(field, initialLoad);
        if (!isValid) {
            allValid = false;
        }
    });
    
    return allValid;
}

function validateField(fieldName, initialLoad = false) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (!field) return true;
    
    // Ignorer les champs optionnels vides
    const optionalFields = ['nom_donateur', 'prenom_donateur'];
    if (optionalFields.includes(fieldName) && field.value.trim() === '') {
        removeError(field);
        resetFieldStyle(field);
        return true;
    }
    
    const value = field.value.trim();
    
    // En mode initialLoad, on ne supprime pas les erreurs existantes
    if (!initialLoad) {
        removeError(field);
    }
    
    let isValid = true;
    let message = '';
    
    switch(fieldName) {
        case 'montant':
            const montant = parseFloat(value.replace(',', '.'));
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
            } else if ((value.split('.')[1] && value.split('.')[1].length > 2) || 
                      (value.split(',')[1] && value.split(',')[1].length > 2)) {
                message = "❌ Maximum 2 décimales autorisées";
                isValid = false;
            }
            break;
            
        case 'dateDon':
            if (!value) {
                message = "❌ La date est obligatoire";
                isValid = false;
            } else {
                const selectedDate = new Date(value + 'T00:00:00');
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate > today) {
                    message = "❌ La date ne peut pas être dans le futur";
                    isValid = false;
                } else if (selectedDate < new Date('2000-01-01')) {
                    message = "❌ La date est trop ancienne";
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
    
    // En mode initialLoad, on n'affiche pas les erreurs
    if (!isValid && !initialLoad) {
        showError(field, message);
    } else if (isValid && !initialLoad) {
        showSuccess(field);
    }
    
    return isValid;
}

function showError(field, message) {
    field.classList.remove('valid');
    field.classList.add('invalid');
    
    let errorElement = field.parentNode.querySelector('.error-message');
    if (!errorElement) {
        errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        field.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
    errorElement.style.display = 'block';
}

function showSuccess(field) {
    field.classList.remove('invalid');
    field.classList.add('valid');
    removeError(field);
}

function removeError(field) {
    const errorElement = field.parentNode.querySelector('.error-message');
    if (errorElement) {
        errorElement.remove();
    }
}

function resetFieldStyle(field) {
    field.classList.remove('invalid', 'valid');
}