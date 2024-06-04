SAE - Semantix

GROUPE : 
    - VIVIER Matis
    - MENORET Denis
    - SCOTTO Ugo
    - GUERREIRO Noah

Etape pour lancer le projet (qui est pour l'instant seulement en local): 
- Clonez le projet dans un environnement wamp/xamp/mamp  (tant qu'il sait lire le php ca devrait le faire)
- Assurez vous que votre wamp/xamp/mamp soit bien actif
- Installer un jdk 21
- Le renommer "jdk-21"
- Le placer à la racine de ce projet
- Téléchargez le fichier binaire obligatoire via ce lien: https://embeddings.net/embeddings/frWac_non_lem_no_postag_no_phrase_200_skip_cut100.bin
- Une fois installé placez ce fichier dans le dossier "./C/" du projet (à côté des fichiers C)
- Renommez ce fichier "word2vec.bin"
- Exécutez la commande "npm install" à la racine du projet pour avoir la partie "node_modules/"
- Ouvrez le projet dans un vscode
- Démarrez le serveur en ouvrant un terminal dans vscode et en éxécutant la commande "node server.js"
- Démarrer le projet via votre invité de commande bash en éxécutant la commande "npm start"
- Patientez, une page devrait s'ouvrir sur votre navigateur (vous pouvez la fermer elle ne sert que pour lancer la partie react)
- Aller sur un navigateur et rentrez l'adresse : http://localhost/Sae_Semantix_S2/backend/
- Logs de test pour vous identifier : 
    - user : noah
    - password : noah
- Vous êtes maintenant connecté sur notre site !


Ce qu'il reste à faire au 04/06/24 : 
    - afficher le bon pseudo des joueurs en partie
    - gestion des plusieurs modes à partir du menu (le mode solo renvoie actuellement aussi sur la page multijoueur)
    - Style de la section multijoueur