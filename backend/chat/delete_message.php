<?php
session_start();
require_once 'config.php';

$messageId = $_POST['message_id'];

$stmt = $conn->prepare("DELETE FROM messages WHERE id = :id");
$stmt->bindParam(":id", $messageId);
$stmt->execute();

// Réponse JSON indiquant le succès de la suppression
$response = array('success' => true);
echo json_encode($response);
?>
