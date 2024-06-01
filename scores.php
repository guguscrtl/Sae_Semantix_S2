<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");


$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "game_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $word = $data['word'];
    $score = calculateScore($word);

    $stmt = $conn->prepare("INSERT INTO scores (word, score) VALUES (?, ?)");
    $stmt->bind_param("si", $word, $score);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["score" => $score]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM scores");
    $scores = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($scores);
}

$conn->close();

function calculateScore($word) {
    // Votre logique de calcul des scores ici
    return strlen($word); // Exemple : score basé sur la longueur du mot
}
?>
