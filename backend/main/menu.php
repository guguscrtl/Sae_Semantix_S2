<?php 
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../connexion/login.php");
    exit();
}

$conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');

$user = $_SESSION['username'];

$stmt = $conn->prepare("SELECT expediteur FROM demandes_amis WHERE destinataire = :destinataire AND statut = 'en_attente'");
$stmt->bindParam(":destinataire", $user);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT id, pseudo_amis FROM amis WHERE utilisateur = :utilisateur");
$stmt->bindParam(":utilisateur", $user);
$stmt->execute();
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="menu.css?v=2">
    <link rel="stylesheet" href="menu_nav.css?v=2">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="background"></div>

<div class="popup" id="messagePopup"></div>

<div class="container">
    <div class="buttons">
        <a href="http://localhost:3000" class="button play-button"><span>Jouer</span></a>
        <a href="http://localhost:3000" class="button private-button"><span>Créer une partie privée</span></a>
        <a href="http://localhost:3000" class="button join-button"><span>Rejoindre une partie</span></a>
        <a href="compte.php" class="button info-button"><span>Mon compte & Infos</span></a>
    </div>
    <div class="toggle-button">
        <button id="toggleNavbarButton"><img src="image/menub.png"></button>
    </div>
    <div class="vertical-navbar">
        <button id="showFormButton" class="button_add_friend">
            <span class="button_text">Ajouter des ami(e)s</span>
            <img src="image/parrot.png" class="button_icon">
        </button>
        <div id="formContainer" class="form-container">
            <form id="addFriendForm" class="hidden">
                <label for="username">Pseudo :</label>
                <input type="text" id="username" name="username">
                <br><br><button type="submit">Envoyer</button>
            </form>
        </div>
        <ul>
            <?php if (count($friends) > 0): ?>
                <li><a href="#">Liste d'amis</a></li>
                <ul>
                    <?php foreach ($friends as $index => $friend): ?>
                        <li>
                            <div class="friend-box" data-id="<?php echo $friend['id']; ?>" onclick="expandFriendBox(this, <?php echo $index; ?>)">
                                <?php echo htmlspecialchars($friend['pseudo_amis']); ?>
                                <div class="friend-actions">
                                    <button class="accept-button">Inviter cet(te) ami(e)</button>
                                    <button class="reject-button" onclick="deleteFriend('<?php echo $friend['pseudo_amis']; ?>')">Supprimer cet(te) ami(e)</button>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if (count($requests) > 0): ?>
                <li><a href="#">Demandes en attente :</a></li>
                <ul>
                    <?php foreach ($requests as $request): ?>
                        <li><?php echo htmlspecialchars($request['expediteur']); ?></li>
                        <form id="request-form" method="post" class="request-form">
                            <input type="hidden" name="expediteur" value="<?php echo htmlspecialchars($request['expediteur']); ?>">
                            <button type="button" name="action" value="accepter" onclick="sendRequest('accepter')">Accepter</button>
                            <button type="button" name="action" value="refuser" onclick="sendRequest('refuser')">Refuser</button>
                        </form>
                        <script>
</script>

                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <li class="user-info">Bonjour <b><?php echo $user ?></b></li>
        </ul>
    </div>

    <script src="menu.js"></script>
    <script>
        $(document).ready(function() {
            $('#addFriendForm').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: 'form_amis.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        showMessage(response);
                        document.getElementById('username').value = "";
                    },
                    error: function(xhr, status, error) {
                        console.error(status, error);
                    }
                });
            });


            function showMessage(message) {
                $('#messagePopup').text(message);
                $('#messagePopup').fadeIn().delay(5000).fadeOut();
            }
        });

        function deleteFriend(friendId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet(te) ami(e) ?")) {
                fetch('delete_friend.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: friendId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Ami(e) supprimé(e) avec succès');
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression de l\'ami(e)');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>
</html>
