<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = $pdo->prepare("SELECT r.*, donneur.nom AS donneur_nom, receveur.nom AS receveur_nom, lot.nom AS lot_nom
                           FROM Remisecle r
                           JOIN personne donneur ON r.donneur_id = donneur.id
                           JOIN personne receveur ON r.receveur_id = receveur.id
                           JOIN immeuble lot ON r.lot_id = lot.id
                           WHERE r.id = ?");
    $query->execute([$id]);
    $remise = $query->fetch(PDO::FETCH_ASSOC);

    if ($remise) {
        echo json_encode($remise);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Remise non trouvée']);
    }
}
?>
