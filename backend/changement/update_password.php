<?php
session_start();

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION['username'])) {
    header("Location: ../connexion/login.php");
    exit();
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $password = $_POST['password'];

    // Connexion à la base de données
    try {
            $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');;

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
    }

    // Récupérer les informations de l'utilisateur depuis la base de données
    $playerName = $_SESSION['username'];

    $stmt = $conn->prepare("SELECT * FROM client WHERE pseudo = '$playerName' ");
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    $email = $userData['email'];
    $pseudo = 'Matis';


    try {
        $stmt = $conn->prepare("SELECT mdp FROM client WHERE pseudo = :nom_utilisateur");
        $stmt->bindParam(':nom_utilisateur', $playerName);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        // Vérifier si le mot de passe actuel correspond
        if (password_verify($password, $userData['mdp'])) {
            // Inscription réussie, envoi d'un e-mail de confirmation

            $validation_token = md5(uniqid(rand(), true));

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP(); // Utilisation de SMTP
                $mail->Host = 'partage.u-pem.fr'; // Serveur SMTP
                $mail->SMTPAuth = true; // Authentification SMTP activée
                $mail->Username = 'matis.vivier@edu.univ-eiffel.fr'; // Votre adresse e-mail SMTP
                $mail->Password = 'Matis2004.'; // Votre mot de passe SMTP
                $mail->SMTPSecure = 'ssl'; // Cryptage SSL
                $mail->Port = 465; // Port SMTP

                $mail->setFrom('matis.vivier@edu.univ-eiffel.fr', 'Matis');
                $mail->addAddress($email, $pseudo);
                $mail->isHTML(true);
                $mail->Subject = 'Changement de Mot de Passe';
                $mail->Body = "Bonjour $pseudo,<br><br>Cliquez sur ce lien pour changer votre mot de passe : <a href='https://etudiant.u-pem.fr/~matis.vivier/SAE_ALTER_2/PHP/changement/password_change.php?token=$validation_token'>Cliquez Ici pour valider votre compte</a>";

                $mail->send();
                $_SESSION['inscription_message'] = 'Un lien de vérification a été envoyé à votre e-mail : ' . $email;
                header('Location : ../main/compte.php');
            } catch (Exception $e) {
                echo 'Erreur lors de l\'envoi de l\'e-mail : ' . $mail->ErrorInfo;
        } }else {
            echo "Le mot de passe actuel est incorrect.";
        }
    } catch(PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
