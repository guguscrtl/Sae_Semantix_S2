<?php
session_start();
require_once 'config.php';

$friend = $_POST['friend'];
$user = $_POST['user'];

$stmt = $conn->prepare("SELECT * FROM messages WHERE (sender = :user AND receiver = :friend) OR (sender = :friend AND receiver = :user) ORDER BY timestamp");
$stmt->bindParam(":user", $user);
$stmt->bindParam(":friend", $friend);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $message) {
    // Appliquer la classe CSS appropriée en fonction de l'expéditeur du message
    $class = ($message['sender'] == $user) ? 'user-bubble' : 'friend-bubble';
    // Afficher la bulle de chat avec le message, l'heure et le bouton de suppression
    echo "<div class='chat-bubble-container'>";
    echo "<div class='chat-bubble $class'>";
    echo htmlspecialchars($message['message']);
    echo "<span class='message-time'>" . date('H:i', strtotime($message['timestamp'])) . "</span>";
    // Bouton de suppression avec l'ID du message comme attribut data
    if ($message['sender'] == $user) {
        echo "<img class='delete-message' data-message-id='" . $message['id'] . "' src='suppr.png' alt='Supprimer'>";
    }    
    echo "</div>";
    echo "</div>";
}
?>
