<?php
include '../backend/config.php';

// Fetch donneurs (copropriétaire, agent)
$donneurs = $pdo->query("SELECT id, nom FROM personne WHERE type IN ('copropriétaire', 'agent')")->fetchAll(PDO::FETCH_ASSOC);

// Fetch receveurs (copropriétaire, locataire, squatteur)
$receveurs = $pdo->query("SELECT id, nom FROM personne WHERE type IN ('copropriétaire', 'locataire', 'squatteur')")->fetchAll(PDO::FETCH_ASSOC);

// Fetch lots from the immeuble table
$lots = $pdo->query("SELECT id, nom FROM immeuble")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title  >Gestion de Remise de Clé</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Ajout de Font Awesome -->
</head>

<body>
    <div class="container">
        <header>
            <h1 style="color: grey;">Gestion de Remise de Clé</h1>
        </header>

        <div id="alertMessage" style="display: none;"></div> <!-- Alerte de message -->

        <section class="list-section">
           
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2 >Liste des Remises de Clé</h2> <div class="search-container" style="margin-bottom: 20px;">
                        <input type="text" id="searchInput" placeholder="Rechercher ..." style="padding: 10px; width: 100%; max-width: 600px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <button id="ajouterRemiseBtn" class="btn btn-add" style="background-color: #c00000; color: white;">
                        <i class="fas fa-plus"></i> Ajouter une Remise de Clé
                    </button>

                </div>
            <table id="remisesTable" class="table">
                <thead>
                    <tr>
                        <th>Donneur</th>
                        <th>Receveur</th>
                        <th>Nom du Lot</th>
                        <th>Date de Remise</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
               
                </tbody>
            </table>
        </section>

        <!-- Overlay -->
        <div id="overlay" style="display: none;"></div>

        <!-- Popup pour enregistrer une nouvelle remise -->
       <div id="remisePopup" class="popup" style="display: none;">
    <div class="popup-content" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f9f9f9; max-width: 500px; margin: 20px auto;">
        <span id="closePopup" class="close" style="cursor: pointer; font-size: 24px; color: #c00000;">&times;</span>
        <h2 style="color: #333; border-bottom: 2px solid #c00000; padding-bottom: 10px; margin-bottom: 20px;">Enregistrer une Remise de Clé</h2>
        <form id="remiseForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="donneur_id">Donneur :</label>
                <select name="donneur_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">-- Sélectionnez un donneur --</option>
                    <?php foreach ($donneurs as $donneur) { ?>
                        <option value="<?= $donneur['id'] ?>"><?= htmlspecialchars($donneur['nom']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="receveur_id">Receveur :</label>
                <select name="receveur_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">-- Sélectionnez un receveur --</option>
                    <?php foreach ($receveurs as $receveur) { ?>
                        <option value="<?= $receveur['id'] ?>"><?= htmlspecialchars($receveur['nom']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="lot_id">Lot :</label>
                <select name="lot_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">-- Sélectionnez un lot --</option>
                    <?php foreach ($lots as $lot) { ?>
                        <option value="<?= $lot['id'] ?>"><?= htmlspecialchars($lot['nom']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date_remise">Date de Remise :</label>
                <input type="date" name="date_remise" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div class="form-group">
                <label for="photo_video">Photo/Video :</label>
                <input type="file" name="photo_video" accept="image/*,video/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            <div class="form-group">
                <label for="commentaire">Commentaire :</label>
                <textarea name="commentaire" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
            </div>
            <div class="form-group">
                <label for="signature">Signature :</label>
                <input type="file" name="signature" accept="image/*" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            <button type="submit"style="background-color: #c00000; color: white; padding: 10px 20px; border: none; border-radius: 5px;">Soumettre</button>
        </form>
    </div>
</div>


<!-- Popup pour afficher les détails d'une remise -->
<div id="remiseDetailsPopup" class="popup" style="display: none;">
    <div class="popup-content">
        <span class="close" id="closePopupDetails" style="cursor: pointer; font-size: 24px; color: #c00000;">&times;</span>
        <section class="form-section">
          

            <p id="remiseDetails"></p> <!-- Détails de la remise affichés ici -->
        </section>
    </div>
</div>


<!-- Popup pour modifier une remise -->
<div id="modifierPopup" class="popup" style="display: none;">
    <div class="popup-content" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f0f0f0; max-width: 500px; max-height: 80vh; margin: 20px auto; overflow-y: auto;">
        <span id="closeModifierPopup" class="close" style="cursor: pointer; font-size: 24px; color: #c00000;">&times;</span>
        <h2 style="color: #333; border-bottom: 2px solid #c00000; padding-bottom: 10px; margin-bottom: 20px;">Modifier une Remise de Clé</h2>
        <form id="modifierForm" enctype="multipart/form-data">
            <input type="hidden" name="id" id="modifier_id"> <!-- Champ caché pour l'ID -->
            <div class="form-group">
                <label for="modifier_donneur_id"><strong>Donneur :</strong></label>
                <select name="donneur_id" id="modifier_donneur_id" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                    <option value="">-- Sélectionnez un donneur --</option>
                    <?php foreach ($donneurs as $donneur) { ?>
                        <option value="<?= $donneur['id'] ?>"><?= htmlspecialchars($donneur['nom']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="modifier_receveur_id"><strong>Receveur :</strong></label>
                <select name="receveur_id" id="modifier_receveur_id" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                    <option value="">-- Sélectionnez un receveur --</option>
                    <?php foreach ($receveurs as $receveur) { ?>
                        <option value="<?= $receveur['id'] ?>"><?= htmlspecialchars($receveur['nom']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="modifier_lot_id"><strong>Lot :</strong></label>
                <select name="lot_id" id="modifier_lot_id" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                    <option value="">-- Sélectionnez un lot --</option>
                    <?php foreach ($lots as $lot) { ?>
                        <option value="<?= $lot['id'] ?>"><?= htmlspecialchars($lot['nom']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="modifier_date_remise"><strong>Date de Remise :</strong></label>
                <input type="date" name="date_remise" id="modifier_date_remise" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
            </div>
            <div class="form-group">
                <label for="modifier_commentaire"><strong>Commentaire :</strong></label>
                <textarea name="commentaire" id="modifier_commentaire" style="width: 100%; padding: 8px; margin-bottom: 10px;"></textarea>
            </div>

            <!-- Afficher le média actuel -->
            <div class="form-group">
                <label><strong>Média actuel :</strong></label>
                <div id="current_photo_video" style="margin-bottom: 10px;"></div>
            </div>

            <!-- Champ pour télécharger un nouveau média -->
            <div class="form-group">
                <label for="modifier_photo_video"><strong>Modifier Photo/Video :</strong></label>
                <input type="file" id="modifier_photo_video" accept="image/*,video/mp4" style="margin-bottom: 10px;" />
            </div>

            <button type="submit" style="background-color: #c00000; color: white; padding: 10px 20px; border: none; border-radius: 5px;">Modifier</button>
        </form>
    </div>
</div>


    </div>

    <script src="script.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const remisesTableBody = document.querySelector("#remisesTable tbody");
        const alertMessage = document.getElementById('alertMessage');
        const remiseDetailsPopup = document.getElementById("remiseDetailsPopup");
        const remiseDetails = document.getElementById("remiseDetails");
        const closePopupDetails = document.getElementById("closePopupDetails");

        const modifierPopup = document.getElementById("modifierPopup");
        const closeModifierPopup = document.getElementById("closeModifierPopup");
        const modifierForm = document.getElementById("modifierForm");
        
        const overlay = document.getElementById('overlay');

        // Fetch remises data from get_remises.php
        fetch('../backend/get_remises.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alertMessage.innerText = data.error;
                    alertMessage.style.display = 'block';
                } else {
                    populateTable(data);
                }
            })
            .catch(error => {
                alertMessage.innerText = 'Erreur lors de la récupération des remises.';
                alertMessage.style.display = 'block';
            });

        // Function to populate the table with remises data
        function populateTable(remises) {
            remisesTableBody.innerHTML = ''; // Clear existing rows

            remises.forEach(remise => {
                const tr = document.createElement("tr");

                tr.innerHTML = `
                    <td>${remise.donneur_nom}</td>
                    <td>${remise.receveur_nom}</td>
                    <td>${remise.lot_nom}</td>
                    <td>${formatDate(remise.date_remise)}</td>
                   <td>
                        <button class="btn btn-details " data-id="${remise.id}" title="Détails" style="background-color: #ff4d4d; color: white;">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="btn btn-edit" data-id="${remise.id}" title="Modifier" style="background-color: #ff4d4d; color: white;">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-delete" data-id="${remise.id}" title="Supprimer" style="background-color: #ff4d4d; color: white;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>

                `;

                remisesTableBody.appendChild(tr);
            });

            // Add event listeners for the "Modifier" buttons
            const editButtons = document.querySelectorAll(".btn-edit");
            editButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const remiseId = this.getAttribute("data-id");
                    openModifierPopup(remiseId);
                });
            });

            // Add event listeners for the "Détails" buttons
            const detailButtons = document.querySelectorAll(".btn-details");
            detailButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const remiseId = this.getAttribute("data-id");
                    showRemiseDetails(remiseId);
                });
            });

            // Add event listeners for the "Supprimer" buttons
            const deleteButtons = document.querySelectorAll(".btn-delete");
            deleteButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const remiseId = this.getAttribute("data-id");
                    confirmDelete(remiseId);
                });
            });
        }
// Function to show details of a remise
  // Fonction pour formater la date
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0
    const year = String(date.getFullYear()).slice(-2); // Obtenir les deux derniers chiffres de l'année
    return `${day}/${month}/${year}`;
}
function showRemiseDetails(remiseId) {
    fetch(`../backend/get_remise.php?id=${remiseId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alertMessage.innerText = data.error;
                alertMessage.style.display = 'block';
            } else {
                // Populate the details popup with remise information
                remiseDetails.innerHTML = `
    <div class="remise-details-container" style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f0f0f0; max-width: 500px; margin: 20px auto;">
        <h3 style="color: #333; border-bottom: 2px solid #c00000; padding-bottom: 10px; margin-bottom: 20px;">Détails de la remise</h3>
        <p style="margin: 10px 0;"><strong>Donneur:</strong> ${data.donneur_nom}</p>
        <p style="margin: 10px 0;"><strong>Receveur:</strong> ${data.receveur_nom}</p>
        <p style="margin: 10px 0;"><strong>Lot:</strong> ${data.lot_nom}</p>
        <p style="margin: 10px 0;"><strong>Date de remise:</strong> ${formatDate(data.date_remise)}</p>
                <p style="margin: 10px 0;"><strong>Commentaire:</strong> ${data.commentaire || 'Aucun commentaire'}</p>
        `;
        if (data.photo_video) {
            // Base URL for accessing media files
            const baseURL = 'http://localhost/gestion_remise_cle/backend/';

            remiseDetails.innerHTML += `
                <p style="margin: 10px 0;"><strong>Photo/Video:</strong></p>
                <div style="margin-bottom: 10px;">
                    ${data.photo_video.endsWith('.mp4') ? `
                        <video controls style="width: 100%; border: 1px solid #ddd; border-radius: 5px;">
                            <source src="${baseURL}${data.photo_video}" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture de vidéos.
                        </video>
                    ` : `
                        <img src="${baseURL}${data.photo_video}" alt="Photo de remise" style="width: 50%; border: 1px solid #ddd; border-radius: 5px;">
                    `}
                </div>
            `;
    }



            
    // Close the details container
    remiseDetails.innerHTML += `</div>`;


                    // Show the details popup
                    remiseDetailsPopup.style.display = 'block';
                    overlay.style.display = 'block';
                }
            })
            .catch(error => {
                alertMessage.innerText = 'Erreur lors de la récupération des détails de la remise.';
                alertMessage.style.display = 'block';
            });
}


    // Close the details modal
    closePopupDetails.addEventListener("click", function() {
            remiseDetailsPopup.style.display = "none";
            document.getElementById('overlay').style.display = 'none';
        });

        // Close the modal if user clicks outside of it
        document.getElementById('overlay').addEventListener("click", function() {
            remiseDetailsPopup.style.display = "none";
            this.style.display = 'none';
        });

        // Function to confirm and delete a remise
        function confirmDelete(remiseId) {

            if (confirm("Êtes-vous sûr de vouloir supprimer cette remise ?")) {
                fetch(`../backend/delete_remise.php?id=${remiseId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alertMessage.innerText = 'Remise supprimée avec succès.';
                        alertMessage.style.display = 'block';
                        window.location.reload(); // Refresh the remises table
                    } else {
                        alertMessage.innerText = data.error || 'Erreur lors de la suppression.';
                        alertMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    alertMessage.innerText = 'Erreur lors de la suppression de la remise.';
                    alertMessage.style.display = 'block';
                });
            }
        }

        // Function to open and populate the modifier popup
     // Function to open and populate the modifier popup
function openModifierPopup(remiseId) {
    fetch(`../backend/get_remise.php?id=${remiseId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alertMessage.innerText = data.error;
                alertMessage.style.display = 'block';
            } else {
                // Populate the modifier form fields
                document.getElementById("modifier_id").value = data.id;
                document.getElementById("modifier_donneur_id").value = data.donneur_id;
                document.getElementById("modifier_receveur_id").value = data.receveur_id;
                document.getElementById("modifier_lot_id").value = data.lot_id;
                document.getElementById("modifier_date_remise").value = data.date_remise;
                document.getElementById("modifier_commentaire").value = data.commentaire;

                // Base URL for accessing media files
                const baseURL = 'http://localhost/gestion_remise_cle/backend/';
                
                // Display current photo/video if available
                if (data.photo_video) {
                    const mediaElement = data.photo_video.endsWith('.mp4') ?
                        `<video controls style="width: 100%; border: 1px solid #ddd; border-radius: 5px;">
                            <source src="${baseURL}${data.photo_video}" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture de vidéos.
                        </video>` :
                        `<img src="${baseURL}${data.photo_video}" alt="Photo de remise" style="width:40%; border: 1px solid #ddd; border-radius: 5px;">`;

                    document.getElementById("current_photo_video").innerHTML = mediaElement;
                } else {
                    document.getElementById("current_photo_video").innerHTML = "Aucun média disponible.";
                }

                modifierPopup.style.display = 'block'; // Show the popup
                overlay.style.display = 'block'; // Show the overlay
            }
        })
        .catch(error => {
            alertMessage.innerText = 'Erreur lors de la récupération des données de la remise.';
            alertMessage.style.display = 'block';
        });
}


        // Event listener for form submission
        modifierForm.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(modifierForm);
            
            // Send the update request to the backend
            fetch('../backend/update_remise.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alertMessage.innerText = 'Remise de clé modifiée avec succès.';
                    alertMessage.style.display = 'block';

                    // Close the popup and refresh the table
                    modifierPopup.style.display = 'none';
                    overlay.style.display = 'none';
                    window.location.reload(); // Refresh the remises table
                } else {
                    alertMessage.innerText = data.error || 'Erreur lors de la modification.';
                    alertMessage.style.display = 'block';
                }
            })
            .catch(error => {
                alertMessage.innerText = 'Erreur lors de la soumission du formulaire.';
                alertMessage.style.display = 'block';
            });
        });

        // Close the modifier popup
        closeModifierPopup.addEventListener("click", function() {
            modifierPopup.style.display = "none";
            overlay.style.display = 'none';
        });

        // Close the modal if user clicks outside of it
        overlay.addEventListener("click", function() {
            modifierPopup.style.display = "none";
            remiseDetailsPopup.style.display = "none";
            this.style.display = 'none';
        });
    });
    document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase(); // Récupérer la valeur de recherche
    let table = document.getElementById('remisesTable');
    let tr = table.getElementsByTagName('tr'); // Récupérer toutes les lignes du tableau

    // Parcourir toutes les lignes du tableau
    for (let i = 1; i < tr.length; i++) { // Commencez à 1 pour ignorer l'en-tête
        let tdDonneur = tr[i].getElementsByTagName('td')[0]; // Donneur
        let tdReceveur = tr[i].getElementsByTagName('td')[1]; // Receveur
        let tdLot = tr[i].getElementsByTagName('td')[2]; // Nom du Lot

        // Vérifier si la valeur de recherche est présente dans les cellules
        if (tdDonneur && tdReceveur && tdLot) {
            let txtValueDonneur = tdDonneur.textContent || tdDonneur.innerText;
            let txtValueReceveur = tdReceveur.textContent || tdReceveur.innerText;
            let txtValueLot = tdLot.textContent || tdLot.innerText;

            // Afficher ou masquer la ligne en fonction de la recherche
            if (txtValueDonneur.toLowerCase().indexOf(filter) > -1 || 
                txtValueReceveur.toLowerCase().indexOf(filter) > -1 || 
                txtValueLot.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = ""; // Afficher la ligne
            } else {
                tr[i].style.display = "none"; // Masquer la ligne
            }
        }
    }
});

</script>



</body>
</html>
<script >
    