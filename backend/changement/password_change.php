<!DOCTYPE html>
<html>
<head>
    <title>Changer le mot de passe</title>
    <meta charset="UTF-8">
</head>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #709CA7;
        margin: 0;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    form {
        background-color: #B8CBD0;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 350px;
        margin-top: 20px;
    }

    label {
        display: block;
        margin-bottom: 10px;
        color: #333;
    }

    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        background-color: #137C8B;
        color: white;
        border: none;
        padding: 15px;
        margin-top: 10px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        box-sizing: border-box;
    }

    input[type="submit"]:hover {
        background-color: #709CA7;
    }

    br {
        display: none;
    }

</style>
<body>

<?php
session_start();

// Traitement du formulaire de mise à jour du mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];
    $playerName = $_SESSION['username'];

    try {
        $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $playerName = $_SESSION['username'];

        // Vérification du mot de passe
        if (strlen($newPassword) < 12 ||
            !preg_match('/[a-z]/', $newPassword) ||
            !preg_match('/[A-Z]/', $newPassword) ||
            !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $newPassword)) {
            $_SESSION['inscription_message'] = "Le mot de passe doit avoir au moins 12 caractères, une lettre minuscule, une lettre majuscule et un caractère spécial.";
            header('Location: ../changement/password_change.php');
            exit;
        }

        if ($newPassword !== $confirmNewPassword) {
            $_SESSION['inscription_message'] = "Les champs de mot de passe ne correspondent pas.";
            exit; 
        }

        $stmt = $conn->prepare("SELECT mdp FROM client WHERE pseudo = ?");
        if ($stmt->execute([$playerName])) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($currentPassword, $userData['mdp'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $updateStmt = $conn->prepare("UPDATE client SET mdp = ? WHERE pseudo = ?");
                $updateStmt->execute([$hashedPassword, $playerName]);



                $getCurrentUserIdStmt = $conn->prepare("SELECT id FROM client WHERE pseudo = :playerName");
                $getCurrentUserIdStmt->bindParam(':playerName', $playerName);
                $getCurrentUserIdStmt->execute();
                $userId = $getCurrentUserIdStmt->fetchColumn();

                $logStmt = $conn->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
                $action = "Changement de mot de passe";
                $logDate = date('Y-m-d H:i:s');
                $ipAddress = $_SERVER['REMOTE_ADDR']; // Récupération de l'adresse IP
                $logStmt->execute([$userId, $action, $logDate, $ipAddress]);

                echo "Le mot de passe a été mis à jour avec succès.";
                header('Location: ../connexion/loginn.php');
            } else {
                echo "Le mot de passe actuel est incorrect.";
                $logStmt = $conn->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
                $userId = $conn->lastInsertId(); // Récupère l'ID de l'utilisateur nouvellement inscrit
                $action = "Changement de mot de passe échoué";
                $logDate = date('Y-m-d H:i:s');
                $ipAddress = $_SERVER['REMOTE_ADDR']; // Récupération de l'adresse IP
                $logStmt->execute([$userId, $action, $logDate, $ipAddress]);
            }
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<form method="post" action="">

    <center><div class="message" style="color: red; margin-bottom: 3%">
            <?php
            if (isset($_SESSION['inscription_message']) && !empty($_SESSION['inscription_message'])) {
                echo '<div class="error-message">' . $_SESSION['inscription_message'] . '</div>';
                unset($_SESSION['inscription_message']);
            }
            ?></div></center>

    <label for="current_password">Mot de passe actuel :</label>
    <input type="password" id="current_password" name="current_password" required><br><br>

    <label for="new_password">Nouveau mot de passe :</label>
    <input type="password" id="new_password" name="new_password" required><br><br>

    <label for="confirm_new_password">Confirmer le nouveau mot de passe :</label>
    <input type="password" id="confirm_new_password" name="confirm_new_password" required><br><br>

    <input type="submit" value="Changer le mot de passe">
</form>

</body>
</html>

