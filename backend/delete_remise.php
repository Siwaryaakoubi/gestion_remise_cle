<?php
include 'config.php';

header('Content-Type: application/json'); // Set the response type to JSON

// Check for the 'id' parameter in the URL query string
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the delete query
    $stmt = $pdo->prepare("DELETE FROM Remisecle WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Remise supprimée avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de la remise.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de remise manquant.']);
}
?>
