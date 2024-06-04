<?php
session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION['username'])) {
    header("Location: ../connexion/login.php");
    exit();
}

// Vérifier si des données ont été soumises
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si l'action et l'expéditeur ont été envoyés
    if (isset($_POST["action"], $_POST["expediteur"])) {
        // Récupérer l'action et l'expéditeur de la demande
        $action = $_POST["action"];
        $expediteur = $_POST["expediteur"];

        // Connexion à la base de données
        $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');        // Mettre à jour le statut de la demande d'ami en fonction de l'action
        if ($action === "accepter") {
            // Mettre le statut de la demande à "acceptee"
            $stmt = $conn->prepare("UPDATE demandes_amis SET statut = 'acceptee' WHERE expediteur = :expediteur AND destinataire = :destinataire");
            $stmt->bindParam(":expediteur", $expediteur);
            $stmt->bindParam(":destinataire", $_SESSION['username']);
            $stmt->execute();

            // Ajouter l'ami dans la table amis
            $stmt_insert_friend = $conn->prepare("INSERT INTO amis (utilisateur, pseudo_amis) VALUES (:utilisateur, :pseudo_amis)");
            $stmt_insert_friend->bindParam(":utilisateur", $_SESSION['username']);
            $stmt_insert_friend->bindParam(":pseudo_amis", $expediteur);
            $stmt_insert_friend->execute();

            $stmt_insert_friend2 = $conn->prepare("INSERT INTO amis (utilisateur, pseudo_amis) VALUES (:utilisateur, :pseudo_amis)");
            $stmt_insert_friend2->bindParam(":utilisateur", $expediteur);
            $stmt_insert_friend2->bindParam(":pseudo_amis", $_SESSION['username']);
            $stmt_insert_friend2->execute();

        } elseif ($action === "refuser") {
            // Mettre le statut de la demande à "refusee"
            $stmt = $conn->prepare("UPDATE demandes_amis SET statut = 'refusee' WHERE expediteur = :expediteur AND destinataire = :destinataire");
            $stmt->bindParam(":expediteur", $expediteur);
            $stmt->bindParam(":destinataire", $_SESSION['username']);
            $stmt->execute();
        }

        // Fermer la connexion à la base de données
        $conn = null;

        // Rediriger vers la page actuelle pour actualiser les données
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

exit();
?>
