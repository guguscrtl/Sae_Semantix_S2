<?php
if (isset($_GET['index'])) {
    $index = $_GET['index'];

    $servername = ' db5015073818.hosting-data.io';
    $username = 'dbu854990';
    $password = '';
    $dbname = 'dbs12517386';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT maxScore FROM parties WHERE id = :id");
        $stmt->bindParam(':id', $index);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $maxScore = $result['maxScore']; // Score maximal spécifique à la partie avec l'index spécifié

        echo $maxScore;
    } catch(PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
