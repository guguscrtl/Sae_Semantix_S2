<?php
session_start();
error_reporting(E_ALL);

// Récupérer les données du classement depuis la base de données
try {
    $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');

;

    // Paramétrage pour l'affichage des erreurs PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête préparée pour récupérer les parties classées par score décroissant
    $rankingStmt = $conn->prepare("SELECT * FROM parties ORDER BY totalScore asc LIMIT 10");
    $rankingStmt->execute();
    $rankings = $rankingStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur lors de la récupération du classement : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Classement des Parties</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/leaderboard.css?v=2">
    <link rel="stylesheet" href="../style/nav.css?v=2">
    <link rel="stylesheet" href="menu_nav.css?v=2">
</head>
<body>

<div class="background"></div>

<div class="parent-container">
    <div class="container">
        <!-- Classement en Bootstrap -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h3 class="mb-4">Classement des Parties</h3>
                <table class="table table-bordered">
                <thead class="custom-header">
                <tr>
                    <th class="position-column">Position</th>
                    <th>Joueur</th>
                    <th>Nombre de Mots</th>
                    <th>Score Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Afficher les données du classement
                $position = 1;
                foreach ($rankings as $ranking) {
                    echo "<tr" . ($position === 1 ? " class='gold-row'" : ($position === 2 ? " class='silver-row'" : ($position === 3 ? " class='bronze-row'" : ""))) . ">";
                    echo "<td class='position-column'>";
                    // Afficher l'image de médaille en fonction de la position
                    if ($position === 1) {
                        echo "<img src='../image/coupe.png' alt='Gold Medal'>";
                    }
                    else {
                        echo $position; // Afficher le numéro de position pour les autres rangs
                    }
                    echo "</td>";
                    echo "<td>" . $ranking['playerName'] . "</td>";
                    echo "<td>" . $ranking['numberOfWords'] . "</td>";
                    echo "<td>" . $ranking['totalScore'] . "</td>";
                    echo "</tr>";
                    $position++;
                }
                ?>
            </tbody>


                </table>
            </div>
        </div>
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



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
