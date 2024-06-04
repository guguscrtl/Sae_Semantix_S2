<?php
// Démarrez ou reprenez la session en cours
session_start();

// Détruisez toutes les données de session
session_destroy();

// Redirigez l'utilisateur vers la page de connexion
header('Location: loginn.php');
exit;
?>
