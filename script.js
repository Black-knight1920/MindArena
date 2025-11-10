const form = document.getElementById('donForm');

form.addEventListener('submit', function(e) {
  e.preventDefault(); // Empêche l'envoi réel

  const montant = parseFloat(document.getElementById('montant').value);
  const dateDon = document.getElementById('dateDon').value;
  const typeDon = document.getElementById('typeDon').value;
  const organisation = document.getElementById('organisation').value;

  // Réinitialiser les messages d'erreur
  document.getElementById('montant-error').textContent = '';
  document.getElementById('date-error').textContent = '';
  document.getElementById('type-error').textContent = '';
  document.getElementById('org-error').textContent = '';
  document.getElementById('success-message').textContent = '';

  let valid = true;

  // Vérification du montant
  if (!(montant) || montant <= 0) {
    document.getElementById('montant-error').textContent = "Il faut un montant > 0";
    valid = false;
  }

  // Vérification de la date
  if (!dateDon) {
    document.getElementById('date-error').textContent = "Sélectionne une date";
    valid = false;
  }

  // Vérification du type
  if (!typeDon) {
    document.getElementById('type-error').textContent = "Sélectionne un type de don";
    valid = false;
  }

  // Vérification de l'organisation
  if (!organisation) {
    document.getElementById('org-error').textContent = "Sélectionne une organisation";
    valid = false;
  }

  // Si tout est correct
  if (valid) {
    document.getElementById('success-message').textContent = "Don enregistré avec succès !";
    
    form.reset(); // Réinitialise le formulaire
  }
});
