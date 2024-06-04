<?php
session_start();

// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION['username'])) {
    header("Location: ../connexion/login.php");
    exit();
}

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $password = $_POST['password'];
    $newEmail = $_POST['new_email'];

    // Validation des données (vous pouvez ajouter des vérifications supplémentaires si nécessaire)

    // Connexion à la base de données
    try {
        $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');


        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
    }

    // Vérifier si l'adresse e-mail est déjà utilisée
    $stmt = $conn->prepare("SELECT email FROM client WHERE email = ?");
    $stmt->execute([$newEmail]);
    $existingEmail = $stmt->fetchColumn();

    if ($existingEmail) {
        $_SESSION['inscription_message'] = "Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.";
        header('Location: ../index.php');
        exit;
    }

    // Récupérer les informations de l'utilisateur depuis la base de données
    $playerName = $_SESSION['username'];

    try {
        $stmt = $conn->prepare("SELECT mdp FROM client WHERE pseudo = :nom_utilisateur");
        $stmt->bindParam(':nom_utilisateur', $playerName);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si le mot de passe actuel correspond
        if (password_verify($password, $userData['mdp'])) {
            // Mettre à jour l'adresse e-mail
            $updateStmt = $conn->prepare("UPDATE client SET email = :new_email WHERE pseudo = :nom_utilisateur");
            $updateStmt->bindParam(':new_email', $newEmail);
            $updateStmt->bindParam(':nom_utilisateur', $playerName);
            $updateStmt->execute();


            $getCurrentUserIdStmt = $conn->prepare("SELECT id FROM client WHERE pseudo = :playerName");
            $getCurrentUserIdStmt->bindParam(':playerName', $playerName);
            $getCurrentUserIdStmt->execute();
            $userId = $getCurrentUserIdStmt->fetchColumn();

            $logStmt = $conn->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
            $action = "Changement d'adresse mail";
            $logDate = date('Y-m-d H:i:s');
            $ipAddress = $_SERVER['REMOTE_ADDR']; // Récupération de l'adresse IP
            $logStmt->execute([$userId, $action, $logDate, $ipAddress]);


            // Redirection vers la page de compte après la mise à jour
            header("Location: ../main/compte.php");
            exit();
        } else {
            echo "Le mot de passe actuel est incorrect.";
            $logStmt = $conn->prepare("INSERT INTO logs (user_id, action, log_date, ip_address) VALUES (?, ?, ?, ?)");
            $action = "Changement d'adresse mail échoué";
            $logDate = date('Y-m-d H:i:s');
            $ipAddress = $_SERVER['REMOTE_ADDR']; // Récupération de l'adresse IP
            $logStmt->execute([$userId, $action, $logDate, $ipAddress]);

        }
    } catch(PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
