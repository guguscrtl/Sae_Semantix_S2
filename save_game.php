<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

try {
    $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$gameId = isset($_GET['id']) ? $_GET['id'] : null;
$players = isset($_GET['players']) ? $_GET['players'] : null;
$score = isset($_GET['score']) ? (int)$_GET['score'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;

if ($gameId && $players && $score !== null && $date) {
    try {
        $stmt = $conn->prepare("INSERT INTO parties (game_id, playerName, totalScore, date) VALUES (:gameId, :players, :score, :date)");
        $stmt->bindParam(':gameId', $gameId);
        $stmt->bindParam(':players', $players);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: could not execute the statement";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Error: Missing parameters";
}

$conn = null;
?>
