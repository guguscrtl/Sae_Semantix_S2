######Présentation########
Dossier qui gère la partie C du projet;
Dans cette partie nous retrouvons la création d'un CSTree, l'export en staticTree et l'écriture du tree dans un fichier en binaire (static_tree.lex).
Les indispensables a retrouver sont : addword.c, build_lex_index.c, convertvec.c,  distanceTotal.c, new_game.c, wordvec.bin
Les fichiers optionnels sont : dictionary_lookup.c, lev_similarity.c, sem_similarity.c

######Important########
1. Le fichier word2vec.bin n'est pas fourni dans le git. Il est a télécharger ici : https://embeddings.net/embeddings/frWac_non_lem_no_postag_no_phrase_200_skip_cut100.bin
2. Le mettre dans le répertoire "SAE-Semantix/C/" et le renommer "word2vec.bin"
3. Avant d'éxécuter n'importe quelle commande, placez vous dans le dossier "SAE-Semantix/PHP"
4. Dans cette partie on gère uniquement l'entrée de mots seuls. Pas de groupe de mots, phrase, chiffre ... 


########COMPILATION##########

Placez vous dans le dossier PHP/C 
Toutes les commandes si dessous sont faites sous Windows, elles sont a adapté en java.

1. build_lex_index 
Commande : gcc -o build_lex_index build_lex_index.c

2. new_game
Commande : gcc -o new_game new_game.c

3. addword
Commande : gcc -o addword addword.c

4. dictionary_lookup
Commande: gcc -o dictionary_lookup dictionary_lookup.c

5. lev_similarity
Commande: gcc -o lev_similarity lev_similarity.c

6.sem_similarity
Commande: gcc -o sem_similarity sem_similarity.c


########## Les executables ###########
Pour tous les commandes qui vont suivre, il est important de se placer dans le dossier parent du dossier C (il faut se trouver dans le dossier PHP).
Si vous êtes actuellement dans le dossier C dans votre terminal, executez la commande suivante : "cd .." .

ASSUREZ vous d'avoir le fichier wordvec.bin dans PHP/C/

1. build_lex_index (nécessaire au premier lancement du projet et a chaque changement de dicionnaire word2vec.bin)
Objectif de l'executable : créer l'arbre static_tree des mots contenus dans notre fichier binaire.
Commande : "C\build_lex_index.exe C\word2vec.bin"

2. new_game
Objectif: créer + initialiser un fichier partie.txt stocké à la racine du projet
Commande : "C\new_game.exe static_tree.lex <mot_de_depart>"

3. addword
Objectif : ajouter un mot et son score dans le fichier partie.txt
Commande : "C\addword.exe static_tree.lex <mot_a_inserer>"

4. dictionary_lookup
Objectif : affiche l'offset d'un mot donné
Commande : "C\dictionary_lookup.exe static_tree.lex <mot>"

5. lev_similarity
Objectif : affiche la distance de levenshtein entre deux mots donnés
Commande : "C\lev_similarity.exe <mot1> <mot2>"

6. sem_similarity
Objectif : affiche la distance sémantique entre deux mots donnés
Commande : "C\sem_similarity.exe C\word2vec.bin <mot1> <mot2>"

####### UTILISATION DES INDESPENSABLES########

1. Lorsque l'on change de dictionnaire (fichier word2vec.bin), il est nécessaire de créer le fichier static_tree.lex en éxécutant build_lex_index.exe avec l'argument word2vec.bin
2. Ensuite il faut éxécuter new_game.exe avec les arguments static_tree.lex et tous les mots de départ
3. A chaque ajout de mot par le joueur, éxécution de addword.exe avec en argument : static_tree.lex et le mot inséré par le joueur


####### UTILISATION DES OPTIONNELS########

1. dictionary_lookup permet de trouver l'offset d'un mot du fichier static_tree. Pour s'y faire éxécuter la commande : dictionary_lookup static_tree.lex <mot>
2. lev_similarity calcul la distance de levenshtein entre 2 mots, éxécuter la commande : lev_similarity <mot1> <mot2>
3. sem_similarity calcul la distance sémantique entre  mots, éxécuter la commande : sem_similarity word2vec.bin <mot1> <mot2>

######A Savoir#########
- Les données d'une partie sont stockés dans le fichier ../partie.txt. On y retrouve les mots de départs, les mots par joueur, les offsets de chaque mots, la distance entre chaque paire de mots.
- L'éxécutable build_lex_index crée un fichier output.txt regroupant tous les mots présent dans le fichier wowrd2vec. Une fois l'éxécution de build_lex_index terminée, ce fichier peut être supprimé.
- Le fichier convertvec a été fourni par l'auteur des différents fichiers word2vec afin de traduire les fichiers binaires en langage courant ou inversement. (nous avons modifié ce fichier dans notre usage)


Les calculs pour la distance entre les mots est faite dans le fichier distanceTotal.c avec la formule : 
distance = (25-levenshtein(mot1, mot2))+(distanceSemantics(mot1, mot2)*100);
Si les deux mots ne sont pas cousins:
    score_total=(1-(distance_levenshtein/(taille_mot_le_plus_long+1)))*40;
si les deux mots sont cousins: 
    score_total = ((scoreSem *0.8 ) + ((1 - distance_levenshtein/(taille_mot_le_plus_long+1)) * 0.2))*100;
    


Bon jeu à vous !
