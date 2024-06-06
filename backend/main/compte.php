<?php
session_start();
// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION['username'])) {
    header("Location: ../connexion/login.php");
    exit();
}
try {
    $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}

// Récupérer les informations de l'utilisateur depuis la base de données
$playerName = $_SESSION['username'];

try {
    $stmt = $conn->prepare("SELECT * FROM client WHERE pseudo = :nom_utilisateur");
    $stmt->bindParam(':nom_utilisateur', $playerName);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// Si le formulaire de mise à jour est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['new_username'];
    $newEmail = $_POST['new_email'];
    $newPassword = $_POST['new_password'];

    header("Location: compte.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mon Compte</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/compte.css">
    <link rel="stylesheet" href="../style/nav.css?v=2">
    <link rel="stylesheet" href="menu_nav.css?v=2">
</head>

<body>
<div class="background"></div>

<div class="left-container">
    <h1>Mon Compte</h1>
    <div class="message" style="color: white; margin-bottom: 3%">
        <?php
            if (isset($_SESSION['inscription_message']) && !empty($_SESSION['inscription_message'])) {
                echo '<div class="error-message">' . $_SESSION['inscription_message'] . '</div>';
                unset($_SESSION['inscription_message']);
            }
        ?>
    </div>
    
    <div class="info">
        <img src="image/pirate.png" alt="Icone utilisateur">
        <p>Pseudo : <?php echo $user['pseudo']; ?></p>
        <a href="#" class="edit-link" id="edit_username">Modifier</a>
    </div>
        
    <div class="info">
        <img src="image/parchemin.png" alt="Icone mail">
        <p>Adresse mail : <?php echo $user['email']; ?></p>
        <a href="#" class="edit-link" id="edit_email">Modifier</a>
    </div>

    <div class="info">
        <img src="image/password.png" alt="Icone mot de passe">
        <p>Mot de passe  :   <b style="margin-right: 8px"></b>*********</p>
        <a href="#" class="edit-link" id="edit_password">Modifier</a>
    </div>
    
    <div class="info-container">
        <!-- Changer Infos !-->
        <form method="post" action="../changement/update_pseudo.php" class="hidden-form" id="form_username">
            <label for="new_username">Nouveau Nom d'Utilisateur</label>
            <input type="text" id="new_username" name="new_username" placeholder="Nouveau nom d'utilisateur"><br>
            <input type="submit" value="Mettre à jour">
        </form>

        <form method="post" action="../changement/update_email.php" class="hidden-form" id="form_email">
            <label for="password">Mot de Passe Actuel:</label>
            <input type="password" id="password" name="password" placeholder="Mot de passe">
            <label for="new_email">Nouvelle Adresse E-mail:</label>
            <input type="email" id="new_email" name="new_email" placeholder="Nouvelle adresse e-mail"><br>
            <input type="submit" value="Mettre à jour">
        </form>

        <form method="post" action="../changement/update_password.php" class="hidden-form" id="form_password">
            <label for="password">Mot de Passe Actuel:</label>
            <input type="password" id="password" name="password" placeholder="Mot de passe"><br>
            <input type="submit" value="Mettre à jour">
        </form>
    </div>

</div>

<script src="menu.js"></script>
<div class="toggle-button">
    <button id="toggleNavbarButton"><img src="image/menub.png"></button>
</div>
<div class="vertical-navbar second">
    <br>
    <a href="menu.php" class="nav-link"><img src="image/bateau.png" > Menu</a>
    <a href="leaderboard.php" class="nav-link"><img src="../image/coupe.png"style="margin-right:25px; margin-left:15px;" >Classement</a>
    <a href="regles.php" class="nav-link"><img src="image/parchemin.png" alt="Règles"> Règles</a>
    <a href="historique.php" class="nav-link"><img src="image/scope.png" alt="Historique" style="margin-left:20px"> Historique</a>
    <a href="compte.php" class="nav-link"><img src="image/pirate.png" alt="Compte"> Compte</a>
    <a href="mentions.php" class="nav-link"><img src="image/parchemin.png" alt="Mentions"> Mentions légales</a>
    <a href="../connexion/deconnexion.php" class="nav-link" style="padding-left:60px"><img src="image/bateau.png" alt="Déconnexion" style="margin-right:20px"> Déconnexion</a>
</div>

<script>
    document.getElementById('edit_username').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('form_username').style.display = (document.getElementById('form_username').style.display == 'none') ? 'block' : 'none';
        document.getElementById('form_email').style.display = 'none';
        document.getElementById('form_password').style.display = 'none';
    });

    document.getElementById('edit_email').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('form_email').style.display = (document.getElementById('form_email').style.display == 'none') ? 'block' : 'none';
        document.getElementById('form_username').style.display = 'none';
        document.getElementById('form_password').style.display = 'none';
    });

    document.getElementById('edit_password').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('form_password').style.display = (document.getElementById('form_password').style.display == 'none') ? 'block' : 'none';
        document.getElementById('form_username').style.display = 'none';
        document.getElementById('form_email').style.display = 'none';
    });
</script>

</body>
</html>






