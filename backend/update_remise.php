<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $donneur_id = $_POST['donneur_id'];
    $receveur_id = $_POST['receveur_id'];
    $lot_id = $_POST['lot_id'];
    $date_remise = $_POST['date_remise'];
    $commentaire = $_POST['commentaire'];

    try {
        $stmt = $pdo->prepare("UPDATE Remisecle SET donneur_id = ?, receveur_id = ?, lot_id = ?, date_remise = ?, commentaire = ? WHERE id = ?");
        $stmt->execute([$donneur_id, $receveur_id, $lot_id, $date_remise, $commentaire, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Erreur lors de la mise à jour de la remise de clé.']);
    }
} else {
    echo json_encode(['error' => 'Requête invalide.']);
}
?>
