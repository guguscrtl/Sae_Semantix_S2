<?php
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["message" => "Non autorisé"]);
    exit;
}

$bdd = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');

// Récupérer la liste des amis
$userId = $_SESSION['user']['id'];
$stmt = $bdd->prepare('SELECT * FROM friends WHERE user_id = ?');
$stmt->execute([$userId]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($friends);
?>
