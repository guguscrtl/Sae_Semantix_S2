<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <meta charset="utf-8">
    <link href="../style/style.css?v=2" rel="stylesheet">
    
</head>
<style>
    /* Style global du corps de la page */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #709CA7;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    /* Style du conteneur principal du formulaire */
    .container {
        background-color: rgba(255, 255, 255, 0.7)
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 300px;
    }

    /* Style pour l'en-tête du formulaire */
    h1 {
        color: #333;
        text-align: center;
        margin-bottom: 30px;
    }

    /* Style pour les éléments de formulaire */
    input[type="text"],
    input[type="password"] {
        width: 100%; /* On enlève la place pour l'icône */
        padding: 20px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 17px;    
    }

    /* Style pour les boutons */
    input[type="submit"] {
        background-color: #2178bb;
        color: white;
        border: none;
        padding: 10px;
        margin-top: 20px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
    }

    input[type="submit"]:hover {
        background-color: #709CA7;
    }

    /* Style pour le lien de réinitialisation du mot de passe */
    .connecter a {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #08f;
        text-decoration: none;
    }

    .connecter a:hover {
        text-decoration: underline;
    }

    /* Style pour les images (icons) */
    .container img {
        width: 25px;
        height: 25px;
        margin-right: 10px;
        cursor: pointer;
    }

    /* Style pour la table dans le formulaire */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    td {
        padding: 0;
    }

    /* Cacher les bordures de la table */
    td, table {
        border: none;
    }

    /* Styles supplémentaires si nécessaire */
    .message {
        color: red; /* Ou toute autre couleur de message de succès */
        text-align: center;
        margin-bottom: 20px;
    }

    .inscrire a:hover {
        background-color: #709CA7;
    }

    .inscrire a {
        background-color: #2178bb;
        display: block;
        color: white;
        border: none;
        padding: 10px;
        margin-top: 20px;
        border-radius: 5px;
        cursor: pointer;
        width: 70%;
        margin-left: 10%;
        margin-right: 10%;
        text-align: center;
        text-decoration: none;
    }

    /* Alignement des éléments à gauche */
    label {
        display: block;
        margin-bottom: 5px;
    }

</style>
<body>
    <div class="background"></div>

    <div class="container">
        <h1>Connexion</h1>
        <form action="login.php" method="post">
        <?php
            session_start();

            // Vérifier si un message de confirmation est présent
            if (isset($_SESSION['inscription_message'])) {
                echo '<div class="message">' . $_SESSION['inscription_message'] . '</div>';
                // Effacer le message de la session pour ne pas l'afficher à nouveau
                unset($_SESSION['inscription_message']);
            }
        ?>
            <div class="content">
                <table>
                    <tr>
                        <td><label for="username"><img src="../image/user.png"></label></td>
                        <td><input type="text" id="username" name="username" placeholder="Pseudo" required></td>
                    </tr>
                    <tr>
                        <td><label for="mdp"><img src="../image/cadenas.png"></label></td>
                        
                        <td><input type="password" id="mdp" name="mdp" placeholder="Mot de passe" required></td>
                        <td><img style="cursor: pointer; margin-left:15px"   src="../image/masquer.png" id="togglePassword" alt="Afficher le mot de passe"></td>
                    </tr>
                </table>
                <input type="submit" name="connexion" value="Se connecter">
            </div>
        </form>

        <div class="inscrire">
            <a href="../connexion/../index.php">S'inscrire</a>
        </div>
        <!-- Bouton pour la page d'inscription -->
        <div class="connecter">
            <a href="MDP.php">Mot de Passe oublier ? Réinitialiser</a>
        </div>
    </div>

    <!-- Ajout de JavaScript pour basculer l'affichage du mot de passe -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const togglePasswordImage = document.getElementById("togglePassword");
            const passwordField = document.getElementById("mdp");
            let passwordVisible = false;

            togglePasswordImage.addEventListener("click", function () {
                passwordVisible = !passwordVisible;
                if (passwordVisible) {
                    passwordField.type = "text";
                    togglePasswordImage.src = "../image/afficher.png";
                } else {
                    passwordField.type = "password";
                    togglePasswordImage.src = "../image/masquer.png";
                }
            });
        });
    </script>
</body>
</html>
