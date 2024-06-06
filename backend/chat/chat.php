<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../connexion/login.php");
    exit();
}

$user = $_SESSION['username'];
$friend = $_GET['friend'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat privé avec <?php echo $friend; ?></title>
    <link rel="stylesheet" href="chat.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <a href="../main/menu.php"><img src="arrow.png" style="width:30px; margin-left:30px; margin-top: 10px"></a>
    <h1>Chat privé avec <?php echo $friend; ?></h1>

    <div id="chat-box">
        <!-- Zone de chat -->
    </div>

    <div class="container-message">
    <form id="message-form">
        <div style="display: flex; align-items: center;">
            <input type="text" id="message" placeholder="Votre message..." style="margin-right: 10px;">
            <label for="submit-button" style="cursor: pointer; margin-bottom: 0;">
                <img src="send.png" alt="Envoyer" style="vertical-align: middle;">
            </label>
            <input type="submit" id="submit-button" style="display: none;"><br><br>
            
        </div>
        
    </form>
    </div>


    <script>
    $(document).ready(function() {
        loadMessages(); // Charger les messages initiaux
        setInterval(loadMessages, 3000); // Recharger les messages toutes les 3 secondes

        $('#message-form').submit(function(event) {
            event.preventDefault();
            sendMessage($('#message').val());
        });
    });

    function loadMessages() {
        $.ajax({
            url: 'load_messages.php',
            type: 'POST',
            data: { friend: '<?php echo $friend; ?>', user: '<?php echo $user; ?>' },
            success: function(response) {
                $('#chat-box').html(response);
                scrollToBottom();
            },
            error: function(xhr, status, error) {
                console.error(status, error);
            }
        });
    }

    function sendMessage(message) {
        $.ajax({
            url: 'send_message.php',
            type: 'POST',
            data: { friend: '<?php echo $friend; ?>', user: '<?php echo $user; ?>', message: message },
            success: function(response) {
                $('#message').val('');
                // Note: Ne rechargez pas les messages ici, ils seront mis à jour automatiquement par le polling
            },
            error: function(xhr, status, error) {
                console.error(status, error);
            }
        });
    }

    function scrollToBottom() {
        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    }


    $(document).on('click', '.delete-message', function() {
    var messageId = $(this).data('message-id');
    if (confirm('Voulez-vous vraiment supprimer ce message ?')) {
        $.ajax({
            url: 'delete_message.php',
            type: 'POST',
            data: { message_id: messageId },
            success: function(response) {
                // Rafraîchir la liste des messages après la suppression
                loadMessages();
            },
            error: function(xhr, status, error) {
                console.error(status, error);
            }
        });
    }
});

function sendNotificationToFriend(friend) {
    // Envoyez une notification à l'ami ici, par exemple, en mettant à jour visuellement la liste d'amis

    // Sélectionnez l'élément d'ami correspondant dans la liste
    const friendListItem = $(`.friend-box[data-id='${friend}']`);

    // Ajoutez une classe ou une indication visuelle pour la notification
    friendListItem.addClass('notification'); // Vous pouvez personnaliser cette classe pour styliser la notification
}

</script>

</body>
</html>
