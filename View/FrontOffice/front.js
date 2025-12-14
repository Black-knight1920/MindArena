function validateForm() {
    let isValid = true;
    
    // Validation du titre
    const titre = document.getElementById('titre');
    const titreError = document.getElementById('titre-error');
    if (titre.value.trim() === '') {
        titreError.textContent = 'Le titre est obligatoire';
        isValid = false;
    } else if (titre.value.trim().length < 2) {
        titreError.textContent = 'Le titre doit contenir au moins 2 caractères';
        isValid = false;
    } else {
        titreError.textContent = '';
    }
    
    // Validation du type
    const type = document.getElementById('type');
    const typeError = document.getElementById('type-error');
    if (type.value.trim() === '') {
        typeError.textContent = 'Le type est obligatoire';
        isValid = false;
    } else {
        typeError.textContent = '';
    }
    
    // Validation de la difficulté
    const difficulte = document.getElementById('difficulte');
    const difficulteError = document.getElementById('difficulte-error');
    if (difficulte.value === '') {
        difficulteError.textContent = 'La difficulté est obligatoire';
        isValid = false;
    } else {
        difficulteError.textContent = '';
    }
    
    // Validation du thème
    const theme = document.getElementById('theme');
    const themeError = document.getElementById('theme-error');
    if (theme.value.trim() === '') {
        themeError.textContent = 'Le thème est obligatoire';
        isValid = false;
    } else {
        themeError.textContent = '';
    }
    
    // Validation de la catégorie
    const categorie = document.getElementById('categorie_id');
    const categorieError = document.getElementById('categorie_id-error');
    if (categorie.value === '') {
        categorieError.textContent = 'La catégorie est obligatoire';
        isValid = false;
    } else {
        categorieError.textContent = '';
    }
    
    return isValid;
}

// Validation en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    }
});

function validateField(field) {
    const errorElement = document.getElementById(field.id + '-error');
    
    switch(field.id) {
        case 'titre':
            if (field.value.trim() === '') {
                errorElement.textContent = 'Le titre est obligatoire';
            } else if (field.value.trim().length < 2) {
                errorElement.textContent = 'Le titre doit contenir au moins 2 caractères';
            } else {
                errorElement.textContent = '';
            }
            break;
            
        case 'type':
            if (field.value.trim() === '') {
                errorElement.textContent = 'Le type est obligatoire';
            } else {
                errorElement.textContent = '';
            }
            break;
            
        case 'difficulte':
            if (field.value === '') {
                errorElement.textContent = 'La difficulté est obligatoire';
            } else {
                errorElement.textContent = '';
            }
            break;
            
        case 'theme':
            if (field.value.trim() === '') {
                errorElement.textContent = 'Le thème est obligatoire';
            } else {
                errorElement.textContent = '';
            }
            break;
            
        case 'categorie_id':
            if (field.value === '') {
                errorElement.textContent = 'La catégorie est obligatoire';
            } else {
                errorElement.textContent = '';
            }
            break;
    }
}