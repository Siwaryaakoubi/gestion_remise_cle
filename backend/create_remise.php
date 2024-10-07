<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donneur_id = $_POST['donneur_id'];
    $receveur_id = $_POST['receveur_id'];
    $lot_id = $_POST['lot_id'];
    $date_remise = $_POST['date_remise'];
    $commentaire = $_POST['commentaire'];

    // Handle the photo/video upload
    $photo_video = null;
    if (isset($_FILES['photo_video']) && $_FILES['photo_video']['error'] === UPLOAD_ERR_OK) {
        $photo_video = $_FILES['photo_video']['name'];
        $target_dir = "uploads/photos_videos/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
        }
        $target_file = $target_dir . basename($photo_video);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Move the uploaded file
        if (move_uploaded_file($_FILES['photo_video']['tmp_name'], $target_file)) {
            $photo_video = $target_file; // Save the file path in the database
        } else {
            echo "Erreur lors du téléchargement du fichier photo/vidéo.";
            exit;
        }
    }

    // Handle the signature file upload
    if (isset($_FILES['signature']) && $_FILES['signature']['error'] === UPLOAD_ERR_OK) {
        $signature = $_FILES['signature']['name'];
        $target_dir = "signatures/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
        }
        $target_file = $target_dir . basename($signature);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allowed file types for signature
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            // Move the uploaded file
            if (move_uploaded_file($_FILES['signature']['tmp_name'], $target_file)) {
                // Save the data into the database
                $stmt = $pdo->prepare("INSERT INTO RemiseCle (donneur_id, receveur_id, lot_id, date_remise, photo_video, commentaire, signature) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$donneur_id, $receveur_id, $lot_id, $date_remise, $photo_video, $commentaire, $target_file]);

                echo "Remise de clé enregistrée avec succès!";
            } else {
                echo "Erreur lors du téléchargement du fichier de signature.";
            }
        } else {
            echo "Type de fichier non autorisé pour la signature. Veuillez télécharger une image (jpg, jpeg, png, gif).";
        }
    } else {
        echo "Aucun fichier de signature téléchargé ou erreur lors du téléchargement.";
    }
}
?>
