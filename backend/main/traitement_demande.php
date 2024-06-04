<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../connexion/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"], $_POST["expediteur"])) {
        $action = $_POST["action"];
        $expediteur = $_POST["expediteur"];

        $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');       
        if ($action === "accepter") {
            $stmt = $conn->prepare("UPDATE demandes_amis SET statut = 'acceptee' WHERE expediteur = :expediteur AND destinataire = :destinataire");
            $stmt->bindParam(":expediteur", $expediteur);
            $stmt->bindParam(":destinataire", $_SESSION['username']);
            $stmt->execute();

            $stmt_insert_friend = $conn->prepare("INSERT INTO amis (utilisateur, pseudo_amis) VALUES (:utilisateur, :pseudo_amis)");
            $stmt_insert_friend->bindParam(":utilisateur", $_SESSION['username']);
            $stmt_insert_friend->bindParam(":pseudo_amis", $expediteur);
            $stmt_insert_friend->execute();

            $stmt_insert_friend2 = $conn->prepare("INSERT INTO amis (utilisateur, pseudo_amis) VALUES (:utilisateur, :pseudo_amis)");
            $stmt_insert_friend2->bindParam(":utilisateur", $expediteur);
            $stmt_insert_friend2->bindParam(":pseudo_amis", $_SESSION['username']);
            $stmt_insert_friend2->execute();

        } elseif ($action === "refuser") {
            $stmt = $conn->prepare("UPDATE demandes_amis SET statut = 'refusee' WHERE expediteur = :expediteur AND destinataire = :destinataire");
            $stmt->bindParam(":expediteur", $expediteur);
            $stmt->bindParam(":destinataire", $_SESSION['username']);
            $stmt->execute();
        }

        $conn = null;

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

exit();
?>
