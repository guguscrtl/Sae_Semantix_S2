<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../connexion/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['new_username'];
    $og_pseudo = $_SESSION['username'];

    try {
            $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');;

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE client SET pseudo = :newUsername WHERE pseudo = :og_pseudo";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':newUsername', $newUsername);
        $stmt->bindParam(':og_pseudo', $og_pseudo);
        $stmt->execute();

        $sql = "UPDATE parties SET playerName = :newUsername WHERE playerName = :og_pseudo";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':newUsername', $newUsername);
        $stmt->bindParam(':og_pseudo', $og_pseudo);
        $stmt->execute();

        echo "Pseudo mis à jour avec succès pour $newUsername.";

        $getCurrentUserIdStmt = $conn->prepare("SELECT id FROM client WHERE pseudo = :newUsername");
        $getCurrentUserIdStmt->bindParam(':newUsername', $newUsername);
        $getCurrentUserIdStmt->execute();
        $userId = $getCurrentUserIdStmt->fetchColumn();

        $logStmt = $conn->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
        $action = "Changement de pseudo";
        $logDate = date('Y-m-d H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $logStmt->execute([$userId, $action, $logDate, $ipAddress]);

        $_SESSION['inscription_message'] = "Reconnectez-Vous";

        header("Location: ../connexion/loginn.php?reload=true");

    } catch(PDOException $e) {
        echo "Erreur : " . $e->getMessage();

    } finally {
        $conn = null;
    }
}
?>
