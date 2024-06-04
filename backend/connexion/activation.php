<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
echo "Activation script started.";

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["token"])) {
    $token = $_GET["token"];
    echo"Je suis bien dans validation";

    try {
        $bdd= new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "La connexion à la base de données a échoué : " . $e->getMessage();
        exit;
    }

    $stmt = $bdd->prepare("SELECT * FROM demandes_en_attente WHERE validation_token = ?");
    $stmt->execute([$token]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        $insertStmt = $bdd->prepare("INSERT INTO client (pseudo, mdp, email, annee_naissance) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([$userData["pseudo"], $userData["mdp"], $userData["email"], $userData["annee_naissance"]]);

        $deleteStmt = $bdd->prepare("DELETE FROM demandes_en_attente WHERE validation_token = ?");
        $deleteStmt->execute([$token]);

        $_SESSION['inscription_message'] = "Votre compte a été validé avec succès.";
    } else {
        $_SESSION['inscription_message'] = "Lien de validation invalide.";
    }

    header('Location: loginn.php');
    exit;
} else {
    echo "Accès non autorisé.";
    exit;
}
?>
