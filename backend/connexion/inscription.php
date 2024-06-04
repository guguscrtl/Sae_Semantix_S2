<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!file_exists('../PHPMailer/src/Exception.php')) {
    exit("Le fichier 'PHPMailer/src/Exception.php' n'existe pas !");
}

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = $_POST["pseudo"];
    $email = $_POST["email"];
    $motDePasse = $_POST["motDePasse"];
    $confirmerMotDePasse = $_POST["confirmerMotDePasse"];
    $anneeNaissance = $_POST["annee_naissance"];

    try {
        $bdd= new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "La connexion à la base de données a échoué : " . $e->getMessage();
        exit;
    }

    $stmt = $bdd->prepare("SELECT email FROM client WHERE email = ?");
    $stmt->execute([$email]);
    $existingEmail = $stmt->fetchColumn();

    if ($existingEmail) {
        $_SESSION['inscription_message'] = "Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.";
        header('Location: ../index.php');
        exit;
    }

    if (strlen($motDePasse) < 12 ||
        !preg_match('/[a-z]/', $motDePasse) ||
        !preg_match('/[A-Z]/', $motDePasse) ||
        !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $motDePasse)) {
        $_SESSION['inscription_message'] = "Le mot de passe doit avoir au moins 12 caractères, une lettre minuscule, une lettre majuscule et un caractère spécial.";
        header('Location: ../index.php');
        exit;
    }

    if ($motDePasse !== $confirmerMotDePasse) {
        $_SESSION['inscription_message'] = "Les mots de passe ne correspondent pas";
        header('Location: ../index.php');
        exit;
    }

    $currentYear = date("Y");
    if ($anneeNaissance < 1900 || $anneeNaissance > 2017) {
        $_SESSION['inscription_message'] = "L'année de naissance n'est pas valide.";
        header('Location: ../index.php');
        exit;
    }

    $hashedPassword = password_hash($motDePasse, PASSWORD_DEFAULT);

    $validation_token = md5(uniqid(rand(), true));

    $requete_login = $bdd->prepare("INSERT INTO demandes_en_attente (pseudo, mdp, email, annee_naissance, validation_token) VALUES (?, ?, ?, ?, ?)");
    if ($requete_login->execute([$pseudo, $hashedPassword, $email, $anneeNaissance, $validation_token])) {

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP(); // Utilisation de SMTP
            $mail->Host = 'partage.u-pem.fr'; // Serveur SMTP
            $mail->SMTPAuth = true; // Authentification SMTP activée
            $mail->Username = 'matis.vivier@edu.univ-eiffel.fr'; 
            $mail->Password = 'Matis2004.';
            $mail->SMTPSecure = 'ssl'; // Cryptage SSL
            $mail->Port = 465; // Port SMTP

            $mail->setFrom('matis.vivier@edu.univ-eiffel.fr', 'Matis');
            $mail->addAddress($email, $pseudo);
            $mail->isHTML(true);
            $mail->Subject = 'Validation de votre compte';
            $mail->Body = "Bonjour $pseudo,<br><br>Cliquez sur ce lien pour valider votre compte : <a href='https://etudiant.u-pem.fr/~matis.vivier/sae_php/partie_php_sae/connexion/activation.php?token=$validation_token'>Cliquez Ici pour valider votre compte</a>";

            $mail->send();
            echo 'Un lien de vérification a été envoyé à votre e-mail : ' . $email;
        } catch (Exception $e) {
            echo 'Erreur lors de l\'envoi de l\'e-mail : ' . $mail->ErrorInfo;
            $logStmt = $bdd->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
            $userId = $bdd->lastInsertId(); // Récupère l'ID de l'utilisateur nouvellement inscrit
            $action = "Inscription réussie";
            $logDate = date('Y-m-d H:i:s');
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        $_SESSION['inscription_message'] = "Un lien de vérification a été envoyé à votre e-mail";

        // Redirection vers la page de connexion
        header('Location: loginn.php');
        exit;
    } else {
        $_SESSION['inscription_message'] = "L'inscription a échoué";
        header('Location: ../index.php');
        exit;
    }
}
?>
