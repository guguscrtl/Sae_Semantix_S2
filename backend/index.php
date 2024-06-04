<!DOCTYPE html>
<html>
<head>
    <title>Inscription/Connexion au Compte</title>
    <link href="style/index.css" rel="stylesheet">
    <link href="style/style.css" rel="stylesheet">
</head>
<style>
    /* Style global du corps de la page */
    body {
        font-family: Arial, sans-serif;
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
        background-color: rgba(255, 255, 255, 0.7) ;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 350px;
    }

    /* Style pour l'en-tête du formulaire */
    h1 {
        color: #333;
        text-align: center;
        margin-bottom: 30px;
    }

    /* Style pour les input dans le formulaire */
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 103%;
        padding: 20px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 15px;
    }

    /* Style pour le placeholder */
    ::placeholder {
        color: #aaa;
    }

    /* Style pour les images (icons) */
    .container .content img {
        width: 25px;
        height: 25px;
        margin-left: 10px;
    }

    /* Style pour les boutons */
    input[type="submit"] {
        background-color: #2178bb;
        color: white;
        border: none;
        padding: 15px;
        margin-top: 20px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        font-size: 17px;
    }

    input[type="submit"]:hover {
        background-color: #709CA7;
    }

    /* Style pour le lien de connexion */
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

    /* Alignement des éléments à gauche */
    label {
        display: block;
        margin-bottom: 5px;
    }

    .message {

    }

</style>
<body>
    <div class="background"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h1>Inscription</h1>
                
                <form action="connexion/inscription.php" method="post">
                    <div class="content">

                        <table>

                            <center><div class="message">
                            <?php
                            session_start();
                            // Vérifie s'il y a un message d'erreur dans la session
                            if (isset($_SESSION['inscription_message']) && !empty($_SESSION['inscription_message'])) {
                                echo '<div class="error-message">' . $_SESSION['inscription_message'] . '</div>';
                                // Efface le message d'erreur de la session pour ne pas l'afficher à nouveau
                                unset($_SESSION['inscription_message']);
                            }
                            ?></div></center><br>
                            <tr><td><label for="pseudo"><img src="image/user.png"></label></td>
                            <td><input type="text" id="pseudo" name="pseudo" placeholder="Pseudo" required></td></tr>

                            <tr>
                                <td><label  for="annee_naissance"><img src="image_nav/historique.png"></label></td>
                                <td>
                                    <select  style="width: 103%;
                                            padding: 20px;
                                            margin: 10px 0;
                                            border: 1px solid #ddd;
                                            background-color : white;
                                            border-radius: 5px;
                                            box-sizing: border-box;
                                            font-size: 15px;" 
                                    id="annee_naissance" name="annee_naissance" required>
                                        <option value="">Sélectionnez l'année de naissance</option>
                                        <?php
                                        $currentYear = date("Y");
                                        for ($year = $currentYear - 100; $year <= 2017; $year++) {
                                            echo "<option value=\"$year\">$year</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>

                            <tr><td><label for="email"><img src="image/email.png"></label></td>
                            <td><input type="email" id="email" name="email" placeholder="E-Mail" required></td></tr>

                            <tr><td><label for="motDePasse"><img src="image/cadenas.png"></label></td>
                                <td><input type="password" id="motDePasse" name="motDePasse" placeholder="Mot de Passe" required></td>
                                <td><img style="cursor: pointer; margin-left:20px"  src="image/masquer.png" id="togglePassword" alt="Afficher le mot de passe"></td>
                            </tr>
                            
                            <tr>
                                <td><label for="confirmerMotDePasse"><img src="image/cadenas.png" ></label></td>
                                <td><input type="password" id="confirmerMotDePasse" name="confirmerMotDePasse" placeholder="Confirmer Mot de Passe" required ></td>
                                <td><img style="cursor: pointer;margin-left:20px"  src="image/masquer.png" id="togglePassword2" alt="Afficher le mot de passe"></td>
                            </tr>
                                
                        </table>
                    </div>

                    <div class="inscrire">
                    <!-- Bouton pour la page de connexion -->
                        <input type="submit" name="inscription" value="S'inscrire">
                    </div>

                    <div class="connecter">
                        <a href="connexion/loginn.php">Déjà un Compte ? Se connecter</a>
                    </div>
                </form>
            </div>
            <div class="col-md-3">
            </div>
        </div>
    </div>

    <!-- Ajout de JavaScript pour basculer l'affichage du mot de passe -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const togglePasswordImage = document.getElementById("togglePassword");
            const togglePasswordImage2 = document.getElementById("togglePassword2");

            const passwordField = document.getElementById("motDePasse");
            const passwordField2 = document.getElementById("confirmerMotDePasse");

            let passwordVisible = false;

            togglePasswordImage.addEventListener("click", function () {
                passwordVisible = !passwordVisible;
                if (passwordVisible) {
                    passwordField.type = "text";
                    togglePasswordImage.src = "image/afficher.png";
                } else {
                    passwordField.type = "password";
                    togglePasswordImage.src = "image/masquer.png";
                }
            });

            togglePasswordImage2.addEventListener("click", function () {
                passwordVisible = !passwordVisible;
                if (passwordVisible) {
                    passwordField2.type = "text";
                    togglePasswordImage2.src = "image/afficher.png";
                } else {
                    passwordField2.type = "password";
                    togglePasswordImage2.src = "image/masquer.png";
                }
            });
        });

    </script>
</body>
</html>
