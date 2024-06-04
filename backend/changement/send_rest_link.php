<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="votre-style/style.css">
</head>
<body>


<?php
error_reporting(E_ALL);
session_start();
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');
if (!file_exists('../PHPMailer/src/Exception.php')) exit("Le fichier 'PHPMailer/src/Exception.php' n'existe pas !");

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_POST) {
    $bdd= new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');

    $email = $_POST["email"];

    $ok = false;
    $results = $bdd->query("SELECT pseudo, email FROM client");
    while ($ligne = $results->fetch(PDO::FETCH_OBJ)) {
        if ($ligne->email == $email) {
            $ok = true;
            $pseudo = $ligne->pseudo;
        }
    }

    if ($ok) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'partage.u-pem.fr'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'matis.vivier@edu.univ-eiffel.fr'; 
            $mail->Password = 'Matis2004.'; 
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('matis.vivier@edu.univ-eiffel.fr', 'Matis');
            $mail->addAddress($email, $pseudo);
            $mail->isHTML(true);
            $mail->Subject = 'Reinitialisation de votre mot de passe';

            $mail->Body = "Bonjour $pseudo,<br><br>Cliquez sur ce lien pour reinitialiser votre mot de passe : <a href='https://etudiant.u-pem.fr/~matis.vivier/SAE_ALTER_2/PHP/changement/password_reset.php'>Reinitialiser le mot de passe</a>";

            $mail->send();


            $_SESSION['inscription_message'] = "Un e-mail a été envoyé";
            header ('Location: ../connexion/MDP.php');
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>
</body>
</html>
