<?php
session_start();
$conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');

// Récupérer le nom d'utilisateur de la session
$user = isset($_SESSION["username"]) ? $_SESSION["username"] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"])) {
        $username = $_POST["username"];

        if (!empty($username)) {
            // Vérifier si l'ami existe dans la base de données
            $stmt_check_friend = $conn->prepare("SELECT COUNT(*) FROM client WHERE pseudo = :pseudo");
            $stmt_check_friend->bindParam(":pseudo", $username);
            $stmt_check_friend->execute();
            $friend_exists = $stmt_check_friend->fetchColumn();

            if ($friend_exists) {
                // Enregistrer la demande d'ami dans la table demandes_amis
                $requete = "INSERT INTO demandes_amis (expediteur, destinataire) VALUES (:expediteur, :destinataire)";
                $stmt = $conn->prepare($requete);

                $stmt->bindParam(":expediteur", $user);
                $stmt->bindParam(":destinataire", $username);

                if ($stmt->execute()) {
                    // Afficher un message de succès avec le nom d'utilisateur
                    echo "Demande d'ami envoyée à : " . htmlspecialchars($username);
                } else {
                    // Afficher un message d'erreur en cas d'échec de l'exécution de la requête
                    echo "Erreur lors de l'envoi de la demande d'ami : " . $stmt->errorInfo()[2];
                }

                // Fermer la déclaration
                $stmt->closeCursor();
            } else {
                // Afficher un message d'erreur si l'ami n'existe pas dans la base de données
                echo "L'ami que vous voulez ajouter n'existe pas.";
            }
        } else {
            // Afficher un message d'erreur si le champ de nom d'utilisateur est vide
            echo "Veuillez entrer un nom d'utilisateur.";
        }
    } else {
        // Afficher un message d'erreur si le champ de nom d'utilisateur n'a pas été soumis
        echo "Le champ de nom d'utilisateur n'a pas été soumis.";
    }
}

// Fermer la connexion à la base de données
$conn = null;
?>
