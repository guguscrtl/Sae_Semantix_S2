<?php
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["message" => "Non autorisé"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['friend_id'])) {
    http_response_code(400);
    echo json_encode(["message" => "Paramètres invalides"]);
    exit;
}

$bdd = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');

$userId = $_SESSION['user']['id'];
$friendId = $data['friend_id'];

// Ajouter un ami
$stmt = $bdd->prepare('INSERT INTO friends (user_id, friend_id) VALUES (?, ?)');
$stmt->execute([$userId, $friendId]);

echo json_encode(["message" => "Ami ajouté avec succès"]);
?>
