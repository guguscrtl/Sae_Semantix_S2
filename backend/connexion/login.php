<!DOCTYPE html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style/login.css">
</head>
<body>

<?php
session_start(); // Initialisation de la session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['mdp'];
    $bdd= new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');
    //$bdd = new PDO('mysql:host=localhost;dbname=dbs12517386', 'matis.vivier', 'Matis2004.');

    // Utilisation d'une requête préparée pour éviter les injections SQL
    $requete = $bdd->prepare("SELECT * FROM client WHERE pseudo = ?");
    $requete->execute([$username]);
    $user = $requete->fetch();

    if ($username == "admin" && $password == "admin"){
        header('Location: ../main/Logs.php');
        exit;
    }

    if ($user && password_verify($password, $user['mdp'])) {
        // Enregistrement du log pour la connexion réussie
        $logStmt = $bdd->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
        $userId = $user['id']; // Suppose que l'ID de l'utilisateur est stocké dans la table client
        $action = "Connexion réussie pour l'utilisateur : $username";
        $logDate = date('Y-m-d H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR']; // Récupération de l'adresse IP

        $logStmt->execute([$userId, $action, $logDate, $ipAddress]);

        $_SESSION['username'] = $username;
        header('Location: ../main/menu.php');
        exit;
    } else {
        // Enregistrement du log pour la tentative de connexion échouée
        $logStmt = $bdd->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
        $userId = $user ? $user['id'] : null; // Si l'utilisateur existe, récupérer son ID
        $action = "Tentative de connexion échouée pour l'utilisateur : $username";
        $logDate = date('Y-m-d H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR']; // Récupération de l'adresse IP

        $logStmt->execute([$userId, $action, $logDate, $ipAddress]);

        $_SESSION['inscription_message'] = "Mauvais Login ou Mot de Passe";
        header('Location: ../connexion/loginn.php');

    }
}
?>

</body>
</html>
