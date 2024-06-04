<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Inclure les fichiers nécessaires pour PHPMailer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bdd= new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');
    $playerName = $_POST["pseudo"];
    $password = $_POST["new_password"];
    echo $playerName;
    $newPassword = $_POST["confirm_password"];

    // Vérification du mot de passe
    if (strlen($password) < 12 ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        $_SESSION['inscription_message'] = "Le mot de passe doit avoir au moins 12 caractères, une lettre minuscule, une lettre majuscule et un caractère spécial.";
        header('Location: ../changement/password_reset.php');
        exit;
    }

    if ($password == $newPassword) {
        // Hachez le nouveau mot de passe avant de le stocker
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $requete_login = $bdd->prepare("UPDATE client SET mdp = ? WHERE pseudo = ?");
        $requete_login->execute([$hashedPassword, $playerName]);

        $logStmt = $bdd->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
        $userId = $bdd->lastInsertId(); // Récupère l'ID de l'utilisateur nouvellement inscrit
        $action = "Changement de mot de passe réussi";
        $logDate = date('Y-m-d H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR']; // Récupération de l'adresse IP
    } else {
        echo "Les mots de passe ne correspondent pas";

        $logStmt = $bdd->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
        $userId = $bdd->lastInsertId(); // Récupère l'ID de l'utilisateur nouvellement inscrit
        $action = "Changement de mot de passe échoué";
        $logDate = date('Y-m-d H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR']; // Récupération de l'adresse IP
    }

    if ($requete_login) {
        $_SESSION['inscription_message'] = "Mot de passe changé, Reconnectez-vous";
        header ('Location : ../connexion/loginn.php');
    } else {
        echo "Erreur";
    }
}
?>
