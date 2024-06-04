<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: ../connexion/login.php");
    exit();
}

// Connexion à la base de données
try {
        $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');;
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit();
}

// Récupérer l'utilisateur connecté
$username = $_SESSION['username'];

// Sélectionner l'historique des parties de l'utilisateur
$stmt = $conn->prepare("SELECT * FROM parties WHERE playerName = :username ORDER BY id DESC");

$stmt->bindParam(':username', $username);
$stmt->execute();
$historiqueParties = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (count($historiqueParties) > 0) {
    // Calculer les statistiques
    $meilleurScore = 0;
    $meilleurMax = 0;
    $meilleurMin = PHP_INT_MAX;

    foreach ($historiqueParties as $partie) {
        $score = $partie['totalScore'];
        $max = $partie['max'];
        $min = $partie['min'];

        // Mettre à jour les statistiques
        if ($score > $meilleurScore) {
            $meilleurScore = $score;
        }
        if ($max > $meilleurMax) {
            $meilleurMax = $max;
        }
        if ($min < $meilleurMin) {
            $meilleurMin = $min;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/historique.css">
    <title>Historique des Parties</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/nav.css?v=2">
    <link rel="stylesheet" href="menu_nav.css?v=2">
    <link rel="stylesheet" href="../style/leaderboard.css">
    <link rel="stylesheet" href="../style/historique.css">
</head>
<div class="background"></div>

<div class="parent-container">
    <div class="container">
    <?php
        echo '<div class="container mt-3">';
            echo '<h2>Meilleures Statistiques</h2>';
            echo '<table class="table table-bordered">';
                echo '<thead class="thead-dark">';
                echo '<tr>';
                    echo '<th>Meilleur Score</th>';
                    echo '<th>Meilleur Max</th>';
                    echo '<th>Meilleur Min</th>';
                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                echo '<tr>';
                    echo '<td>' . $meilleurScore . '</td>';
                    echo '<td>' . $meilleurMax . '</td>';
                    echo '<td>' . $meilleurMin . '</td>';
                    echo '</tr>';
                echo '</tbody>';
                echo '</table >';
            echo '</div>'; ?>


    <h1 class="mb-4">Historique des Parties</h1>
        <?php if (count($historiqueParties) > 0): ?>
            <table class="table table-bordered">
                <thead class="custom-header">
                <tr>
                    <th scope="col">ID de la Partie</th>
                    <th scope="col">Nombre de Mots</th>
                    <th scope="col">Score Total</th>
                    <th scope="col">Date</th>
                    <th scope="col">Max</th>
                    <th scope="col">Min</th>
                    <th scope="col">Mode</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $compteur = count($historiqueParties);
                foreach ($historiqueParties as $partie):
                    ?>

                    <tr>
                        <th scope="row"><?php echo $compteur; ?></th>
                        <td><?php echo $partie['numberOfWords']; ?></td>
                        <td><?php echo $partie['totalScore']; ?></td>
                        <td><?php echo $partie['date']; ?></td>
                        <td><?php echo $partie['max']; ?></td>
                        <td><?php echo $partie['min']; ?></td>
                        <td><?php echo $partie['type']; ?></td>
                    </tr>
                    <?php $compteur--; ?>
                <?php endforeach; ?>

                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune partie trouvée dans l'historique.</p>
        <?php endif; ?>
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
    <a href="../connexion/deconnexion.php" class="nav-link" style="padding-left:60px"><img src="image/bateau.png" alt="Déconnexion" style="margin-right:20px"> Déconnexion</a>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
