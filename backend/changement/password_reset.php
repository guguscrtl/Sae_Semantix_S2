<!DOCTYPE html>
<html>
<head>
    <title>Réinitialisation du mot de passe</title>
</head>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #709CA7;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    /* Style pour l'en-tête du formulaire */
    h1 {
        color: #333;
        margin-bottom: 20px;
    }

    /* Style pour le formulaire */
    form {
        background-color: #B8CBD0;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 350px;
    }

    /* Style pour les labels */
    label {
        display: block;
        margin-top: 20px;
        color: #333;
    }

    /* Style pour les champs de saisie */
    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
    }

    img {
        width: 25px;
        height: 25px;
        margin-right: 10px;
        cursor: pointer;
    }

    /* Style pour le bouton */
    button[type="submit"] {
        background-color: #137C8B;
        color: white;
        border: none;
        padding: 15px;
        margin-top: 20px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        box-sizing: border-box;
    }

    button[type="submit"]:hover {
        background-color: #709CA7;
    }

</style>
<body>
    <h1>Réinitialisation du mot de passe</h1>

    <form action="../changement/password_reset_process.php" method="post">
        <center><?php
        session_start();

        if (isset($_SESSION['inscription_message'])) {
            echo '<div class="message">' . $_SESSION['inscription_message'] . '</div>';
            unset($_SESSION['inscription_message']);
        }
        ?></center>
        <tr>
            <td><label for="pseudo">Pseudo</label></td>
            <td><input type="text" id="pseudo" name="pseudo" required></td>
        </tr>

        <tr>
            <label for="new_password">Nouveau mot de passe :</label></td>
        </tr>

        <input type="password" id="new_password" name="new_password" required>
        <label for="confirm_password">Confirmez le nouveau mot de passe :</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit">Réinitialiser le mot de passe</button>
    </form>
</body>
</html>
