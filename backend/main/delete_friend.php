<?php
session_start();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id']) && isset($_SESSION['username'])) {
    $friendId = $input['id'];
    $user = $_SESSION['username'];

    $dsn = 'mysql:host=localhost;dbname=matis.vivier_db';
    $username = 'root';
    $password = '';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);

        $stmt = $pdo->prepare('DELETE FROM amis WHERE (id = :friendId AND utilisateur = :user) OR (id = :user AND utilisateur = :friendId)');
        $stmt->execute(['friendId' => $friendId, 'user' => $user]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
