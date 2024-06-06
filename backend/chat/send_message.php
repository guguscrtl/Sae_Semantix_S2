<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username'])) {
    exit("Erreur: Utilisateur non connecté");
}

$user = $_SESSION['username'];
$friend = $_POST['friend'];
$message = $_POST['message'];

$stmt = $conn->prepare("INSERT INTO messages (sender, receiver, message) VALUES (:user, :friend, :message)");
$stmt->bindParam(":user", $user);
$stmt->bindParam(":friend", $friend);
$stmt->bindParam(":message", $message);

if ($stmt->execute()) {
    exit("Message envoyé avec succès");
} else {
    exit("Erreur lors de l'envoi du message");
}
?>
