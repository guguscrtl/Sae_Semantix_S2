
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Classement des Parties</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/nav.css?v=2">
    <link rel="stylesheet" href="menu_nav.css?v=2">
    <link rel="stylesheet" href="../style/regles.css?v=2">
</head>
<body>

<div class="background"></div>


<div class="parent-container">
    <div class="container">
        <!-- Classement en Bootstrap -->
        <div class="row justify-content-center">
            <div class="col-md-8" id="colonne-regles">
                    <section>
                        <h1>Règles du Jeu</h1>
                        <h2>Objectif</h2>
                        <p>Vous êtes un pirate qui navigue en mer. Vous vous trouvez sur une des deux îles au début de la partie. Votre but est de trouver le chemin le plus court afin d'atteindre la deuxième île du début. A vous de savoir utiliser les bons mot pour le trouver </p>
                    </section>
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
    <a href="Logs.php" class="nav-link"><img src="image/crane.png" alt="Logs"> Logs</a>
    <a href="../connexion/deconnexion.php" class="nav-link" style="padding-left:60px"><img src="image/bateau.png" alt="Déconnexion" style="margin-right:20px"> Déconnexion</a>
</div>




<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>




<!--
<main>
    <section>
        <h1>Règles du Jeu</h1>
        <section>
            <h2>Objectif</h2>
            <p>Le joueur commence sur l'île de départ. Il doit naviguer d'île en île en entrant des mots pour créer de nouvelles destinations. Le but est de trouver le chemin le plus court entre le mot de départ et le mot d'arrivé, donc plus le score est bas, et plus vous monterez dans le classement. En sachant que plus vous surivez dans les mers, et plus vous aurez des chances de trouver les bons mots.</p>
        </section>
        <section>
            <h2>Navigation</h2>
            <p>Le joueur entre un mot pour naviguer vers une nouvelle île. Les îles se génèrent aléatoirement et chaque île a une certaine probabilité d'apparaître.</p>
        </section>
        <section>
            <h2>Événements Aléatoires</h2>
            <p>Des événements aléatoires peuvent se produire à tout moment, pouvant être bénéfiques ou préjudiciables :</p>
                <li>Tempête : perte d'essence, dommages au bateau</li>
                <li>Rencontre de pirates : gains ou pertes d'argent</li>
                <li>Rencontre de marchand ambulant : opportunité d'achat à prix intéressant</li>
                <li>Collision avec un rocher : dommages au bateau et perte d'essence</li>
                <li>Attaque de pirates : choix de se défendre ou payer une rançon</li>
        </section>
        <section>
            <h2>Consommables</h2>
            <p>Les consommables disponibles sont :</p>
                <li>Argent : utilisé pour les achats et les transactions</li>
                <li>Réservoir : utilisé pour chaque déplacement du bateau</li>
                <li>Magasin : pour acheter des outils</li>
                <li>Mécano : pour acheter de l'essence et réparer son bateau</li>
        </section>
        <section>
            <h2>Îles</h2>
            <p>Les types d'îles disponibles sont :</p>
                <li>Île de départ</li>
                <li>Île simple : récupère une petite quantité d'argent</li>
                <li>Île trésor : contient un trésor, nécessite des outils pour le récupérer</li>
                <li>Île Mécano : pour acheter de l'essence et réparer son bateau</li>
                <li>Île magasin : pour acheter des outils</li>
        </section>
    </section>
</main>
!-->