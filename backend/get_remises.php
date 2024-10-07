<?php
include 'config.php';

try {
    $stmt = $pdo->query("
    SELECT 
        r.id, 
        d.nom as donneur_nom, 
        re.nom as receveur_nom, 
        l.nom as lot_nom, 
        r.date_remise, 
        r.commentaire, 
        r.photo_video, 
        r.signature
    FROM Remisecle r
    JOIN personne d ON r.donneur_id = d.id
    JOIN personne re ON r.receveur_id = re.id
    JOIN immeuble l ON r.lot_id = l.id
");
$remises = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($remises);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de la récupération des remises : ' . $e->getMessage()]);
}
?>
