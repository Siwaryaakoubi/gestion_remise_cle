// Réinitialiser le formulaire
function resetForm() {
    document.getElementById('remiseForm').reset(); // Réinitialiser tous les champs du formulaire
}

document.addEventListener('DOMContentLoaded', function() {
    // Écouteur pour le bouton d'ajout de remise
    document.getElementById('ajouterRemiseBtn').addEventListener('click', function() {
        resetForm(); // Réinitialiser le formulaire avant d'afficher le popup
        togglePopup(true);
    });

    // Écouteur pour fermer le popup d'ajout
    document.getElementById('closePopup').addEventListener('click', function() {
        togglePopup(false);
    });

    // Ferme le popup si l'utilisateur clique sur l'overlay
    document.getElementById('overlay').addEventListener('click', function() {
        togglePopup(false);
    });

    // Gérer l'envoi du formulaire pour ajouter une remise
    document.getElementById('remiseForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Empêche l'envoi normal du formulaire

        const formData = new FormData(this); // Crée un objet FormData

        // Envoi de la requête au serveur
        fetch('../backend/create_remise.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur lors de l\'ajout de la remise');
            }
            return response.text();
        })
        .then(message => {
            alert('Remise ajoutée avec succès');
            window.location.reload();
        })
        .catch(error => {
            alert(`Une erreur s'est produite: ${error.message}`);
        });
    });

    // Charger les remises au chargement de la page
    // loadRemises();
});





// Fonction pour afficher ou masquer le popup
function togglePopup(show) {
    document.getElementById('remisePopup').style.display = show ? 'block' : 'none';
    document.getElementById('overlay').style.display = show ? 'block' : 'none';
}


