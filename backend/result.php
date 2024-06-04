<?php
session_start();
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données envoyées via POST
    $data = json_decode(file_get_contents("php://input"), true);

    $date = $data['date'] ?? '';
    $numberOfWords = $data['numberOfWords'] ?? '';
    $wordScores = $data['wordScores'] ?? [];
    $playerName = $_SESSION['username'] ?? '';
    $totalScore = $data['totalscore'] ?? '';

    $max = max($wordScores);
    $min = min($wordScores);

    // Calcul du total du score à partir des scores individuels des mots
    

    // Connexion à la base de données (remplacez avec vos propres informations de connexion)
    try {
        $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');;
        
        // Paramétrage pour l'affichage des erreurs PDO
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête préparée pour l'insertion sécurisée des données
        $stmt = $conn->prepare("INSERT INTO parties (date, numberOfWords, totalScore, playerName, wordScores, max, min, type) VALUES (:date, :numberOfWords, :totalScore, :playerName, :wordScores, :max, :min, 'Pirate')");
        
        // Liaison des valeurs avec les paramètres de la requête préparée
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':numberOfWords', $numberOfWords);
        $stmt->bindParam(':totalScore', $totalScore);
        $stmt->bindParam(':playerName', $playerName);
        $stmt->bindValue(':wordScores', implode(',', $wordScores), PDO::PARAM_STR);
        // Enregistre les scores sous forme de liste sérialisée
        $stmt->bindParam(':max', $max);
        $stmt->bindParam(':min', $min);

        // Exécution de la requête
        $stmt->execute();

        echo "Données de la partie sauvegardées avec succès dans la base de données.";

        // Enregistrer des messages dans le journal des erreurs
        error_log("Date: $date, Number of words: $numberOfWords, Word scores: " . implode(', ', $wordScores));
    } catch(PDOException $e) {
        echo "Erreur lors de la sauvegarde des données : " . $e->getMessage();
    }

    // Fermer la connexion à la base de données
    $conn = null;
} else {
    echo "";
}


$cheminFichier = 'output.txt';

// Lire toutes les lignes du fichier dans un tableau
$lines = file($cheminFichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines !== false) {
    // Convertir chaque ligne en UTF-8
    foreach ($lines as $key => $line) {
        $lines[$key] = mb_convert_encoding($line, 'UTF-8', mb_detect_encoding($line, 'UTF-8, ISO-8859-1, ISO-8859-15', true));
    }

    // Sélectionner deux mots au hasard parmi les lignes lues
    $ligneAleatoire1 = trim($lines[array_rand($lines)]);
    $ligneAleatoire2 = trim($lines[array_rand($lines)]);
    $motdedepart = $ligneAleatoire1;
    $motarrive = $ligneAleatoire2;

    // Assurez-vous que ces mots sont également stockés dans partie.txt
    $filePath = '../partie.txt';
    file_put_contents($filePath, $motdedepart . PHP_EOL . $motarrive);

    // Exécutez la commande pour exécuter le fichier .exe avec les mots de partie.txt
    $output = shell_exec("C\\new_game.exe static_tree.lex $motdedepart $motarrive");

    // Lire toutes les lignes du fichier partie.txt dans un tableau
    $lines2 = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines2 !== false && count($lines2) > 0) {
        // Trouver la dernière ligne du tableau et la convertir en UTF-8
        $derniereLigne = mb_convert_encoding(end($lines2), 'UTF-8', mb_detect_encoding(end($lines2), 'UTF-8, ISO-8859-1, ISO-8859-15', true));

        // Diviser la dernière ligne en mots
        $motsDerniereLigne = explode(' ', $derniereLigne);

        // Assigner le premier mot de la dernière ligne
        $premiereLigne = isset($motsDerniereLigne[0]) ? $motsDerniereLigne[0] : "Mot introuvable";

        // Assigner le deuxième mot de la dernière ligne
        $deuxiemeLigne = isset($motsDerniereLigne[1]) ? $motsDerniereLigne[1] : "Mot introuvable";
    } else {
        $premiereLigne = "Ligne introuvable";
        $deuxiemeLigne = "Ligne introuvable";
    }
} else {
    $premiereLigne = "Impossible de lire le fichier.";
    $deuxiemeLigne = "Impossible de lire le fichier.";
}



?>

<!DOCTYPE html>
<html>
<head>
    <title>Réseau de Mots</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/result.css">
    <script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
</head>
<body style="background-color: #A5A77D">

<nav class="navbar" style="margin-left: 35%; margin-right: 35%; margin-bottom: -2%">
    <ul>
        <li><a href="main/menu.php"><img src="image_nav/accueil.png"  title="Acceuil"></a></li>
        <li><a href="main/historique.php"><img src="image_nav/bouton-dhorloge-historique.png" title="Historique"></a></li>
        <li><a href="main/compte.php" ><img src="image_nav/user.png" title="Mon compte"></a></li>
        <li><a href="main/Logs.php" ><img src="https://assets.stickpng.com/images/59cfc4d2d3b1936210a5ddc7.png" title="Logs"></a></li>
        <li><a href="connexion/deconnexion.php"><img src="https://cdn-icons-png.flaticon.com/512/25/25376.png" title="Logs"></a></li>
    </ul>
</nav>

<!-- affichage game over !-->
<div id="gameOverDiv" style="display: none; position: absolute; z-index: 999;top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <h2>Game Over!</h2>
    <p>Votre partie est terminée. Merci d'avoir joué!</p>
    <button onclick="window.location.href = 'main/historique.php';">Quitter</button>
</div>

<div class="bateau">
    <div class="bateau-contain" style="margin-bottom: -3%">
    <img src="image_jeu/santé.png">
        <progress id="boatHealth" value="100" max="100"></progress>
        <video autoplay loop mutued playsinline id="video_bateau" style="display :none">
            <source src="image_jeu/bateau_tempete.mp4" type="video/mp4">
        </video>
        <p>Le bateau est sur l'île : <span id="currentIslandName"> ... </span></p>
            <div id="score">
                0
            </div>

    </div>
</div>

<div id="event">
    <h2></h2>
    <p></p>
    <button onclick="hideDivEvent()">Compris</button>
</div>

<div class="network-score">
    <div class="test pirate-theme">
        <div class="test">
            <h3><br>Inventaire</h3>
            <div class="stats" id="argentStats" style="margin-top: 120px; margin-bottom:-10%; margin-left: 7%">
                <p>Argent : 20</p>
                <img src="image_jeu/euro.png" style="width:30px; height:30px; margin-left : 3%; ">
            </div>
            <div class="stats" id="carburantStats" style="margin-bottom:-10%; margin-left: 5%">
                <p>Carburant : 10</p>
                <img src="image_jeu/petrole.png" style="width:20px; height:20px; margin-left : 3%">
            </div>
            <div class="stats" id="pelle" style="margin-bottom:-10%">
                <p>Pelle : non</p>
            </div>
            <div class="stats" id="pioche">
                <p>Piôche : non</p>
            </div>
        </div>
        </div>

        <div id="network">
        </div>

    <div class="tableau">
        <center><h3><br>Scores Global</h3><div class="fond"><div id="scores"></div></div></center>
        <center><h3><br>Score Îles</h3><div class="fond"><div id="scores2"></div></div></center>
    </div>
</div>

    <div id="hiddenDiv">
        <div class="hidden_contain">
            <h3>Acheter des outils</h3>
            <div class="contain_img">
                <span id="span1"><p style="float:left; margin: left -40px;">5<img src="image_jeu/euro.png"></p></span>
                <p style="float:right; margin-left:200px; width:400px;">5<img src="image_jeu/euro.png"></p>
            </div>
            <div class="contain_button">
                <button onclick="buyShovel()" style="float:left">Acheter pelle</button>
                <button onclick="acheterPioche()" style="float:right">Acheter pioche</button>
            </div>
            <div class="fermer">
                <button onclick="hidenDiv();">Fermer</button>
            </div>
        </div>
    </div>
    <div id="hiddenDivMecano">
        <div class="hidden_contain">
            <div class="contain_img">
                <h3 style="float:left">Acheter de l'essence</h3>
                <h3 style="float: right">Réparer son Bateau</h3>
            </div>
            
            <div class="contain_button" style="margin-top: 25%; margin-bottom : 10%;width:90%; margin-left:3%">
                <button onclick="acheterEssence()" style="float:left;">Acheter 5 <img src="image_jeu/petrole.png"> pour 10<img src="image_jeu/euro.png"></button>
                <button onclick="réparer()" style="float:right; padding:10px;">Réparer pour 10<img src="image_jeu/euro.png"></button>
            </div>
            <div class="fermer">
                <button onclick="hidenDivMecano()">Fermer</button>
            </div>
        </div>
    </div>

<audio id="myAudio" autoplay loop>
  <source src="music/music_fond.mp3" type="audio/mp3">
</audio>

<audio id="myAudio2" autoplay loop>
  <source src="" type="audio/mp3">
</audio>

<div class="container">
    <div class="formu">
        <form id="addWordForm">
            <label for="newWord"><br>Ajouter un nouveau mot <br><br></label>
            <input type="text" id="newWord" name="newWord" maxlength="15" minlength="3" pattern="^[A-Za-zÀ-ÖØ-öø-ÿ]+$" title="Veuillez entrer un mot de 3 à 15 lettres sans caractères spéciaux ni chiffres." required><br><br>
            <button type="submit">Ajouter</button>
            <button type="button" id="endGame">Fin de partie</button>
        </form>

    </div>

    <script>

        var audio = document.getElementById("myAudio");
        var audio2 = document.getElementById("myAudio2");
        document.addEventListener("click", function() {
        // Baissez le volume à 0.5 (50%)
        audio.volume = 0.5;

        // Démarrez la lecture
        audio.play();

        // Retirez l'écouteur d'événements après la première interaction pour ne pas relancer la lecture
        document.removeEventListener("click", arguments.callee);
        });

        function stopMusic() {
            audio2.pause();
            audio.play();
        }

        // Fonction pour basculer l'affichage de la div
        function toggleDiv() {
            var div = document.getElementById("hiddenDiv");
            div.style.display = "block";
        }

        function hidenDiv(){
            var div = document.getElementById("hiddenDiv");
            div.style.display = "none";
            stopMusic();
        }

        function toggleDivMecano() {
            var div = document.getElementById("hiddenDivMecano");
            div.style.display = "block";
        }

        function hidenDivMecano(){
            var div = document.getElementById("hiddenDivMecano");
            div.style.display = "none";
            stopMusic();
        }

        function toggleDivEvent() {
            var div = document.getElementById("event");
            div.style.display = "block";
        }

        function hideDivEvent(){
            var div = document.getElementById("event");
            div.style.display = "none";
        }
    </script>

    <script type="text/javascript">
        var pelle = false;
        var pioche = false;
        hidenDiv();

        function buyShovel() {
            var cout = 5;
            if (!pelle && argent >= cout) { // Vérifie si la pelle n'a pas encore été achetée
                argent -= cout;
                pelle = true;

                var argentElement = document.querySelector('#argentStats p');
                argentElement.textContent = 'Argent : ' + argent;

                var pelleElement = document.querySelector('#pelle p');
                pelleElement.textContent = 'Pelle : ' + 'Oui';
                console.log(pelle);
            } else if (pelle) {
                alert("Vous avez déjà une pelle !");
            } else {
                alert("Vous n'avez pas assez d'argent");
            }
        }

        function acheterPioche() {
            var cout = 5;
            if (!pioche && argent >= cout) { // Vérifie si la pioche n'a pas encore été achetée
                argent -= cout;
                pioche = true;

                var argentElement = document.querySelector('#argentStats p');
                argentElement.textContent = 'Argent : ' + argent;

                var piocheElement = document.querySelector('#pioche p');
                piocheElement.textContent = 'Pioche : ' + 'Oui';
                console.log(pioche);
            } else if (pioche) {
                alert("Vous avez déjà une pioche !");
            } else {
                alert("Vous n'avez pas assez d'argent");
            }
        }

        function acheterEssence(){
            var cout = 10;
            if (argent >= cout){
                argent -= cout;
                carburant += 5;

                var argentElement = document.querySelector('#argentStats p');
                argentElement.textContent = 'Argent : ' + argent;

                var carburantElement = document.querySelector('#carburantStats p');
                carburantElement.textContent = 'Carburant : ' + carburant;
            } else {
                alert("Vous n'avez pas assez d'argent");
            }
        }

        function réparer(){
            var cout = 10;
            if (argent >= cout){
                argent -= cout;
                vie_bateau += 20;

                var boatHealthElement = document.getElementById('boatHealth');
                boatHealthElement.value = vie_bateau;

                var carburantElement = document.querySelector('#carburantStats p');
                carburantElement.textContent = 'Carburant : ' + carburant;
                updateBoatHealth();
                updateArgent();

            } else {
                alert("Vous n'avez pas assez d'argent");
            }
        }

        function updateArgent() {
            var argentElement = document.querySelector('#argentStats p');
            argentElement.textContent = 'Argent : ' + argent;
        }


        // GAME OVER
        function gameOver() {
            hideDivEvent();
            hidenDiv();
            hidenDivMecano();

            var gameOverDiv = document.getElementById("gameOverDiv");
            gameOverDiv.style.display = "block";

            // Sauvegarder les données avant de réinitialiser la partie
            saveGameDataToDatabase();

        }
        // FIN GAME OVER

        // AFFICHAGE NETWORK
        var images = [
            'image_jeu/sable.jpg', 
            'image_jeu/roche.jpg',
            'image_jeu/shop.jpg',
            'image_jeu/repair.jpg',
            'image_jeu/maudit.jpg',
            'image/tigre.jpg',
            'image_jeu/ile.jpg'
        ];

        var initialWords = ['Départ'];

        function generateRandomScore() {
            console.log("test");
            return Math.floor(Math.random() * 100) + 1; // Score aléatoire entre 1 et 100
        }

        // INITIALISATION MOT DE DEBUT
        var getRandomWords = (arr, num) => {
            let result = [];
            while (result.length < num) {
                const randomIndex = Math.floor(Math.random() * arr.length);
                const word = arr.splice(randomIndex, 1)[0];
                result.push({ label: word, score: generateRandomScore() });
            }
            return result;
        };
        </script>

        <script>

        // IMAGE ET OPTION ILES DEBUT
        var premierNoeud = {
            id: 0,
            label: "<?php echo $premiereLigne; ?>", // Utilisez la première ligne
            score: generateRandomScore(),
            image: images[6],
            borderWidth: 0,
        };

        var deuxiemeNoeud = {
            id: 1,
            label: "<?php echo $deuxiemeLigne; ?>", // Utilisez le premier mot de la dernière ligne
            score: generateRandomScore(),
            image: images[6],
            borderWidth: 0,
        };

        // AFFICHAGE GRAPHIQUE NETWORK
        var options = {
            nodes: {
                shape: 'circularImage',
                size: 40,
                font: {
                    size: 25,
                    color: 'black'
                },
                borderWidth: 1,
                image: images[6],
                color: {
                    background: 'transparent',
                    border: 'red'
                },
            },
            edges: {
                width: 2,
                color: {
                    color: '#344D59',
                    highlight: '#344D59',
                    opacity: 1.0
                }
            }
        };

        
        var edges = [
            { from: 0, to : 1 }
        ];

        let nodes = [premierNoeud, deuxiemeNoeud]; 

        var data = {
            nodes: new vis.DataSet(nodes),
            edges: new vis.DataSet(edges),
        };

        var network = new vis.Network(document.getElementById('network'), data, options);
        // FIN AFFICHAGE NETWORK

        // AFFICHAGE TABLEAU SCORE
        window.addEventListener('DOMContentLoaded', function() {
            displayScores(); // Affiche le tableau des scores vide au chargement initial de la page
            displayScores2();
        });

        // RECOMMENCER UNE PARTIE
        function resetGame() {
            nodes = getRandomWords(initialWords, 1).map((word, index) => ({
                id: index + 1,
                label: word.label,
                score: word.score,
                image: images[nouveau],
                boatPosition: 0
            }));

            edges = [
                { from: 0, to: 1 }
            ];

            data.nodes = new vis.DataSet(nodes);
            data.edges = new vis.DataSet(edges);
            network.setData(data);

            score_total = nodes[0].score;  // Mettez à jour score_total avec le score du premier mot
            compteur_mot = 0;
            firstWordAdded = false;
            pelle = false;
            pelleElement.textContent = 'Equipé : ' + 'Non';

            displayScores();
            displayScores2();
        }

        // BARRE DE VIE DU BATEAU
        function updateBoatHealth() {
            var boatHealthElement = document.getElementById('boatHealth');
            boatHealthElement.value = vie_bateau;

            // Ajoutez des vérifications pour le cas où la vie devient négative ou dépasse la valeur maximale
            if (vie_bateau <= 0) {
                vie_bateau = 0;
                // Ajoutez ici les actions à effectuer lorsque la vie du bateau atteint zéro (par exemple, gameOver() ou autres)
                gameOver();
            } else if (vie_bateau > 100) {
                vie_bateau = 100;
            }
        }

        // UPDATE CARBURANT

        function updateCarburant() {
            var argentElement = document.querySelector('#carburantStats p');
            argentElement.textContent = 'Carburant : ' + carburant;

            if (carburant <= 0) {
                gameOver();
            }
        }

        // EVENNEMENT ALEATOIRE

        var dernierEvenement = -1; //garde trace du dernier event

        function handleRandomEvent() {
            var randomEvent;
            do {
                randomEvent = Math.floor(Math.random() * 3);
            } while (randomEvent === dernierEvenement);  // Répéter jusqu'à ce que ce soit un nouvel événement
            dernierEvenement = randomEvent;

            switch (randomEvent) {
                case 0:
                    tempete();
                    break;
                case 1:
                    rencontre_pirate();
                    break;
                case 2:
                    rocher_heurter();
                    break;
                default:
                    // Aucun événement aléatoire
                    break;
            }
        }

        function tempete() {
            var degats = Math.floor(Math.random() * 2) + 1;
            var tempeteh2 = document.querySelector("#event h2");
            var tempetep = document.querySelector("#event p");
            tempeteh2.textContent = "Une Tempête Surgit !";

            if (degats == 1 && carburant > 6) {
                carburant -= 5;
                updateCarburant();
                tempetep.textContent = "Vous perdez 5 de carburant";
            } else if (degats == 2 && vie_bateau > 40) {
                vie_bateau -= 35;
                updateBoatHealth();
                tempetep.textContent = "Votre bateau à pris un sacré choc";
            } else {
                tempetep.textContent = "";  // Efface le texte en cas d'événement par défaut
                handleRandomEvent();
            }
            toggleDivEvent();
        }

        function rocher_heurter() {
            var degats = Math.floor(Math.random() * 2) + 1;
            var degatsh2 = document.querySelector("#event h2");
            var degatsp = document.querySelector("#event p");
            degatsh2.textContent = "Vous avez heurté un rocher !";
            if (degats == 1 && carburant > 4) {
                carburant -= 3;
                updateCarburant();
                degatsp.textContent = "Vous perdez 3 de carburant";
            } else if (degats == 2 && vie_bateau > 30) {
                vie_bateau -= 25;
                updateBoatHealth();
                degatsp.textContent = "Votre bateau à pris un sacré coup";
            } else {
                degatsp.textContent = "";  // Efface le texte en cas d'événement par défaut
                handleRandomEvent();
            }
            toggleDivEvent();
        }

        function rencontre_pirate() {
            var pourcentage = 65;
            var aleatoire = Math.random() * 100;

            if (aleatoire > pourcentage) {
                var volh2 = document.querySelector("#event h2");
                var volp = document.querySelector("#event p");
                volh2.textContent = "Oh non, les Pirates Attaquent !"
                var vol = Math.floor(Math.random() * 2) + 1;
                if (vol == 1 && argent > 20) {
                    argent -= 10;
                    volp.textContent = "Ils vous ont volé 10 pièces";
                } else if (vol == 2 && carburant > 4) {
                    carburant -= 3;
                    updateCarburant();
                    volp.textContent = "Ils vous ont volé 3 de carburant";
                } else {
                    volp.textContent = "";  // Efface le texte en cas d'événement par défaut
                    handleRandomEvent();
                }
            } else {
                var volh2 = document.querySelector("#event h2");
                var volp = document.querySelector("#event p");
                volh2.textContent = "Vous faites amis-amis avec des pirates, quelle chance !"
                var gain = Math.floor(Math.random() * 2) + 1;
                if (gain == 1) {
                    argent += 10;
                    volp.textContent = "Ils vous ont donné 10 pièces !";
                } else if (gain == 2) {
                    carburant += 3;
                    updateCarburant();
                    volp.textContent = "Ils vous passent 3 barils de carburant ! ";
                }
            }
            toggleDivEvent();
        }

        // décrémenter la vie du bateau
        function decreaseBoatHealth(amount) {
            vie_bateau -= amount;
            updateBoatHealth();
        }

        // INITIALISATION DONNEES
        var score_total = 0;
        var compteur_mot = 0;
        var firstWordAdded = false;
        var currentIsland = "Ile de départ";
        var carburant = 10;
        var argent = 20;
        var type_ile = "";
        var vie_bateau = 100;

        var fetchExe2; // Déclarer fetchExe2 en dehors de la portée de la fonction addEventListener

        var compteur_sable = 0;
        var compteur_roche = 0;

        var verifArgentRecupere = {};

        function supprimerLien(fromNodeId, toNodeId) {
            var lienIndex = edges.findIndex(edge => edge.from === fromNodeId && edge.to === toNodeId);
            if (lienIndex !== -1) {
                edges.splice(lienIndex, 1);
                data.edges = new vis.DataSet(edges);
                network.setData(data);
            }
        }

        var audio = document.getElementById("myAudio");
        var audio2 = document.getElementById("myAudio2");

        // AJOUT D'UN MOT
        document.getElementById('addWordForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var newWord = document.getElementById('newWord').value;
            var capitalizedWord = newWord.charAt(0).toUpperCase() + newWord.slice(1);

            var audioElement = document.getElementById('myAudio2');
            audio2.pause();
            audio.play();

            if (Math.random() < 0.10) {
                handleRandomEvent();
            }

            // Vérifier si le mot existe déjà dans la liste
            var existingNode = nodes.find(node => node.label === capitalizedWord);

            if (existingNode) {
                hidenDiv();
                hidenDivMecano();
                // Mettre à jour la bordure rouge du dernier nœud visité
                if (lastVisitedNodeId !== null) {
                    var lastVisitedNode = nodes.find(node => node.id === lastVisitedNodeId);
                    if (lastVisitedNode) {
                        lastVisitedNode.borderWidth = 0;
                    }
                }

                if (!verifArgentRecupere.hasOwnProperty(existingNode.id)) {
                    verifArgentRecupere[existingNode.id] = false;
                }  
                    switch (existingNode.image) {
                        case images[0]:  // Île de sable
                        if (pelle == true && verifArgentRecupere[existingNode.id] == false) {
                            argent += existingNode.score;
                            compteur_sable += 1;
                            updateArgent();
                            verifArgentRecupere[existingNode.id] = true;  // Marquer l'île comme visitée
                        }
                        break;
                        // À l'intérieur du cas pour l'île de roche
                        case images[1]:  // Île de roche
                            if (pioche == true && verifArgentRecupere[existingNode.id] == false) {
                                argent += existingNode.score;
                                compteur_roche += 1;
                                updateArgent();
                                verifArgentRecupere[existingNode.id] = true;  // Marquer l'île comme visitée
                            }
                            break;
                        case images[2]:  // Île Magasin
                            toggleDiv();
                            break;
                        case images[3]:  // Île Mécano
                            toggleDivMecano();
                            decreaseBoatHealth(10);
                            updateBoatHealth();
                            break;
                        default:
                            // Aucune action spécifique pour d'autres types d'îles
                            break;
                }

                // Mettre à jour la bordure du nœud actuel
                existingNode.borderWidth = 5;
                lastVisitedNodeId = existingNode.id;

                // Réinitialiser les propriétés d'interface utilisateur pour les îles précédemment visitées
                nodes.forEach(node => {
                    if (node.id !== existingNode.id) {
                        node.borderWidth = 0;
                        // Réinitialiser d'autres propriétés d'interface utilisateur si nécessaire
                    }
                });

                // Mettre à jour d'autres éléments d'interface utilisateur (texte, etc.)
                var visits = verifArgentRecupere[existingNode.id] || 0;
                var argentElement = document.querySelector('#carburantStats p');

                // Vérifier si le nombre total de visites est inférieur à 3 pour autoriser la visite
                if (visits < 3) {
                    carburant = carburant - (visits + 2);  // +2 car la première visite est déjà consommée
                    argentElement.textContent = 'Carburant : ' + carburant;
                    verifArgentRecupere[existingNode.id] = visits + 1;

                    if (carburant <= 0) {
                        gameOver();
                    }
                } else {
                    // Si le nombre de visites est supérieur à 2, fermer l'île (mettre à jour le message par exemple)
                    document.getElementById('currentIslandName').textContent = capitalizedWord + " est fermée!";
                }

                // Mettre à jour les données du réseau
                network.setData(data);

                // Afficher le message indiquant que le bateau est sur l'île correspondante avec sa valeur précédente
                document.getElementById('currentIslandName').textContent = capitalizedWord;
                document.getElementById('newWord').value = '';
            } else {
                let couplesList = [];  // Variable pour stocker la liste de couples

            // Première requête fetch - execute_exe
            var fetchExe1 = fetch('execute_exe.php?exe=C\\addword.exe&param=static_tree.lex ' + newWord)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la requête réseau pour execute_exe.php');
                    }
                    return response.text();
                })
                .then(data => {
                    console.log("Réponse de 'execute_exe.php':", data);

                    // Deuxième requête fetch - optim_tree
                    return fetch('optim_tree.php');
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la requête réseau pour optim_tree.php');
                    }
                    return response.text();
                })
                .then(data => {
                    console.log("Réponse de 'optim_tree.php':", data);

                    // Troisième requête fetch - chemin_opti
                    return fetch('chemin_opti.php');
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la requête réseau pour chemin_opti.php');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.error) {
                        data.forEach(pair => {
                            //console.log("Mot1: " + pair.mot1 + ", Mot2: " + pair.mot2);
                            couplesList.push({mot1: pair.mot1, mot2: pair.mot2, score: pair.score});  // Ajouter à la liste de couples
                        });
                        console.log("Liste de couples :", couplesList);
                    } else {
                        throw new Error(data.error);
                    }
                    return fetch('get_max_score.php?file=../partie.txt&word=' + newWord);
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la requête réseau pour get_max_score.php');
                    }
                    return response.text();
                })
                .then(data => {
                    console.log('score : ' + data);
                    fetchExe2 = data;
                    return parseInt(data, 10);
                })
                .catch(error => {
                    console.error('Erreur lors de la chaîne de requêtes fetch:', error);
                });

            // Utilisation de la valeur finale de fetchExe1 (si nécessaire)
            // fetchExe1.then(value => {
            //     console.log('Valeur finale de fetchExe1:', value);
            // });

            // Attendre que les deux requêtes fetch soient terminées
            Promise.all([fetchExe1, fetchExe2]).then(function() {
                hidenDiv();
                hidenDivMecano();



                var middleNodeId = edges[0].to;
                var newNodeId = nodes.length + 1;
                var newScore = fetchExe2;
                console.log(pelle);
                var nombreAleatoire = Math.floor(Math.random() * 101);
                if (nombreAleatoire > 40 && nombreAleatoire <= 55) {
                    var imageid = 0;
                    type_ile = "Île au trésor de sable";
                    if (pelle == true){
                        argent += parseInt(newScore, 10);
                        compteur_sable += 1;
                    }
                } else if (nombreAleatoire > 55 && nombreAleatoire <= 70) {
                    imageid = 1;
                    type_ile = "Île au trésor de roche";
                    if (pioche == true){
                        argent += parseInt(newScore, 10);
                        compteur_roche += 1;
                    }
                } else if (nombreAleatoire > 70 && nombreAleatoire <= 85) {
                    imageid = 2;
                    type_ile = "Île Magasin";
                    toggleDiv();
                    audio.pause();
                    audioElement.src = "music/shop_sound.mp3"; 
                } else if (nombreAleatoire > 85 && nombreAleatoire <= 100) {
                    imageid = 3;
                    type_ile = "Île Mécano";
                    toggleDivMecano();
                    audio.pause();
                    audioElement.src = "music/mecano.mp3";
                } else if (nombreAleatoire >= 0 && nombreAleatoire <= 40){
                    imageid = 4;
                    type_ile = "Île simple";
                    argent += parseInt(newScore, 10);
                    console.log(parseInt(newScore, 10));
                    updateArgent();                    
                }
                
                var counting_sand = 0;
                var counting_rock = 0;

                if (compteur_sable >= 2){
                    pelle = false;
                    var pelleElement = document.querySelector('#pelle p');
                    pelleElement.textContent = 'Pelle : ' + 'Non';
                    if (counting_sand == 0){
                        alert("votre pelle s'est cassé !");
                        counting_sand += 1;
                    }
                }

                if (compteur_roche >= 2){
                    pioche = false;
                    var pelleElement = document.querySelector('#pioche p');
                    pelleElement.textContent = 'Pioche : ' + 'Non';
                    if (counting_rock == 0){
                        alert("votre pioche s'est cassé !");
                        counting_rock += 1;
                    }
                }

                console.log("pelle : ", compteur_sable);
                console.log("pioche : ", compteur_roche);
                
                decreaseBoatHealth(5);
                updateBoatHealth();

                lastVisitedNodeId = newNodeId;  // Mettre à jour la variable seulement lors de l'ajout d'un nouveau mot

                nodes.push({ id: newNodeId, label: capitalizedWord, score: newScore, image: images[imageid], nombreAleatoire: nombreAleatoire });
                
                for (let i=0; i<nodes.length; i++) {
                    for (let j=0; j<nodes.length; j++){
                        supprimerLien(nodes[i].id, nodes[j].id);
                    }
                } 

                couplesList.forEach(pair => {
                    for (let i=0; i<nodes.length; i++){
                        for (let j=0; j<nodes.length; j++){
                            pair_mot1_maj = pair.mot1.charAt(0).toUpperCase() + pair.mot1.slice(1);
                            pair_mot2_maj = pair.mot2.charAt(0).toUpperCase() + pair.mot2.slice(1);
                            mot1_label = nodes[i].label.charAt(0).toUpperCase() + nodes[i].label.slice(1);
                            mot2_label = nodes[j].label.charAt(0).toUpperCase() + nodes[j].label.slice(1);
                            if (mot1_label === pair_mot1_maj && mot2_label === pair_mot2_maj){
                                edges.push({ from: nodes[i].id, to: nodes[j].id });
                            }
                        }
                    }
                });

                compteur_mot += 1;
                let motdepart = '<?php echo $motdedepart; ?>';
                let motarrive = '<?php echo $motarrive; ?>';
                fetch("calcul_score.php?depart="+motdepart+"&arrive="+motarrive)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la requête réseau');
                    }
                    return response.text(); // ou response.json() si la réponse est au format JSON
                })
                .then(data => {
                    // Utilisez ici les données récupérées depuis le serveur (score_total)
                    score_total = data; // Affectez les données à la variable score_total
                    var scoreElement = document.getElementById("score");

                    // Modifiez le contenu de l'élément pour y mettre "test"
                    scoreElement.innerHTML = score_total;
                    console.log("SCOOOOOOOR TOTAAAAAL : " + score_total);

                    // Vous pouvez faire d'autres traitements avec score_total ici
                })
                .catch(error => {
                    console.error('Erreur lors de la requête :', error);
                });

                data.nodes = new vis.DataSet(nodes);
                data.edges = new vis.DataSet(edges);
                network.setData(data);

                var currentIsland = capitalizedWord;
                document.getElementById('currentIslandName').textContent = currentIsland + " qui est une " + type_ile;

                var argentElement = document.querySelector('#carburantStats p');
                carburant = carburant - 1;
                argentElement.textContent = 'Carburant : ' + carburant;
                if (carburant <= 0) {
                    gameOver();
                }

                displayScores(couplesList);
                displayScores2();
                document.getElementById('newWord').value = '';
            });
            }
        });

        // SAUVEGARDE DONNES DANS BDD
        function saveGameDataToDatabase() {
            var date = new Date().toISOString().slice(0, 19).replace('T', ' ');
            var wordScores = nodes.slice(2).map(node => node.score); // Exclure les deux premiers mots

            var gameData = {
                date: date,
                numberOfWords: compteur_mot, // Soustraire les deux premiers mots
                wordScores: wordScores,
                totalscore: score_total
            };

            fetch('result.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(gameData)
            })
            .then(response => {
                if (response.ok) {
                    alert('Données de la partie sauvegardées avec succès!');
                    console.log("données : ", gameData);
                } else {
                    alert('Erreur lors de la sauvegarde des données.');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la requête:', error);
                alert('Erreur lors de la sauvegarde des données.');
            });
        }


        function displayScores(couplesList) {
            var scores = "<table style='border-collapse: collapse;'><tr><th style='border: 2px solid #344D59;'>Mot 1</th><th style='border: 2px solid #344D59;'>Mot 2</th><th style='border: 2px solid #344D59;'>Score</th></tr>";

            couplesList.forEach(function(pair) {
                var scoreColor = pair.score > 70 ? 'lightgreen' : (pair.score > 40 ? 'orange' : 'lightcoral');
                scores += "<tr><td style='border: 2px solid #344D59; text-align: center;'>" + pair.mot1 + "</td><td style='border: 2px solid #344D59; text-align: center;'>" + pair.mot2 + "</td><td style='border: 2px solid #344D59; text-align: center; background-color:" + scoreColor + ";'>" + "<span style='color:black;'>" + pair.score + "</span>" + "</td></tr>";
            });

            scores += "</table>";
            document.getElementById('scores').innerHTML = scores;
        }

        function displayScores2() {
            var scores = "<table style='border-collapse: collapse;'><tr><th style='border: 2px solid #344D59;'>Nom Île</th><th style='border: 2px solid #344D59;'>Valeur</th></tr>";
            var totalScoreIle = 0; // Variable pour calculer le score total
            
            for (var i = 1; i < nodes.length; i++) {
                var score = nodes[i].score;
                var tester = score;
                var nombreAleatoire = nodes[i].nombreAleatoire

                console.log("score node : ", tester);
                var scoreColor = score > 70 ? 'lightgreen' : (score > 40 ? 'orange' : 'lightcoral');

                if (nombreAleatoire > 40 && nombreAleatoire <= 70) {
                    scores += "<tr><td style='border: 2px solid #344D59; text-align: center;'>" + nodes[i].label + "</td><td style='border: 2px solid #344D59; text-align: center; background-color:" + scoreColor + ";'>" + "<span style='color:black; padding : -2px;'>" + score + "</span>" + "</td></tr>";
                }
            }
            
            var argentElement = document.querySelector('#argentStats p');
            argentElement.textContent = 'Argent : ' + argent;
            // Ajouter une ligne pour afficher le score total à la fin du tableau
            
            scores += "</table>";
            document.getElementById('scores2').innerHTML = scores;
        }


    </script>
    
</div>
</body>
</html>