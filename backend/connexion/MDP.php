<!DOCTYPE html>
<html>
<head>
    <title>Mot de passe oublier</title>
        <link rel="stylesheet" href="style/login.css">

</head>
<style>
	/* Style global du corps de la page */
	body {
		font-family: 'Arial', sans-serif;
		background-color: #709CA7;
		margin: 0;
		padding: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		height: 100vh;
	}

	/* Style du conteneur principal du formulaire */
	.container {
		background-color: #B8CBD0;
		padding: 40px;
		border-radius: 10px;
		box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
		width: 350px;
		text-align: center;
	}

	/* Style pour l'en-tête du formulaire */
	h1 {
		color: #333;
		margin-bottom: 30px;
	}

	/* Style pour les éléments de formulaire */
	input[type="email"] {
		width: 100%;
		padding: 15px;
		margin: 10px 0;
		border: 1px solid #ddd;
		border-radius: 5px;
		box-sizing: border-box;
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
	}

	button[type="submit"]:hover {
		background-color: #709CA7;
	}

	/* Style pour la table dans le formulaire */
	table {
		width: 100%;
		border-collapse: collapse;
	}

	td {
		padding: 0;
	}

	/* Cacher les bordures de la table */
	td, table {
		border: none;
	}

	/* Style pour le label */
	label {
		display: block;
		margin-bottom: 10px;
		color: #333;
	}

</style>
<body>
	<div class="container">
	    <h1>Mot de passe oublier</h1>
	    <form action="../changement/send_rest_link.php" method="post">
	    <div class="content">
			<center><div class="message" style="color: green; margin-bottom: 3%">
				<?php
                    session_start();
                    // Vérifie s'il y a un message d'erreur dans la session
                    if (isset($_SESSION['inscription_message']) && !empty($_SESSION['inscription_message'])) {
                        echo '<div class="error-message">' . $_SESSION['inscription_message'] . '</div>';
				// Efface le message d'erreur de la session pour ne pas l'afficher à nouveau
				unset($_SESSION['inscription_message']);
				}
                ?></div>
                <table>
                    <tr>
					    <label for="email">Entrez votre adresse e-mail :</label>
					</tr>
					<tr>
					    <td><input type="email" id="email" name="email" required></td>
					</tr>
					<tr>
					    <td>
					        <button type="submit">Envoyer la demande</button>
					    </td>
					</tr>
				</table>
	    </form>
	</div>
</body>
</html>
