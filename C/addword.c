#include <stdio.h>
#include <string.h>
#include "loadStaticTree.c"
#include "distanceSemantique.c"
#include "distanceTotal.c"


void ecritDansFichierPartie(char mot[], StaticTree tree, char save_file[]) {
    FILE *filePtr;
    char buffer[MAX_SIZE * 100]; // Assez grand pour stocker tout le fichier
    char *bufferPtr = buffer;
    char line[MAX_SIZE];
    int offset = searchWordInStaticTree(mot, tree);
    if (offset == -1) return; //Si le mot n'est pas dans le dictionnaire
    char motNouveau[MAX_SIZE];
    char mots[MAX_SIZE][MAX_SIZE]; //Liste des mots de depart et par joueur pour calculer la distance
    strcpy(motNouveau, mot);

    char filePath[256];
    snprintf(filePath, sizeof(filePath), "./save/%s.txt", save_file);
    filePtr = fopen(filePath, "r");
    if (filePtr == NULL) {
        printf("Erreur lors de l'ouverture du fichier pour lecture !");
        return;
    }

    int motsCount = 0;
    int addmot = 0;
    // Lire le contenu du fichier en mémoire
    while (fgets(line, MAX_SIZE, filePtr) != NULL) {
        if (strstr(line, "Mots de départ :") != NULL) {
            sprintf(bufferPtr, "%s", line); //Ecriture des mots de départ
            bufferPtr += strlen(line);
            addmot = 1;
        }
        else if (strstr(line, "Mots du joueur1 :") != NULL) {
            sprintf(bufferPtr, "%s%s\n", line, motNouveau); //Ecriture des mots entrés par le joueur 1
            bufferPtr += strlen(bufferPtr);
            addmot = 1;
        }
        else if (strstr(line, "Offset de chaque mot :") != NULL) {
            sprintf(bufferPtr, "%s%s %d\n", line, motNouveau, offset); //Ecriture de la partie offset de la forme : mot offset
            bufferPtr += strlen(bufferPtr);
            addmot = 0;
        }
        else if (addmot==1){
            strcpy(mots[motsCount++], line); //Ajout d'un mot à retenir dans la liste
            sprintf(bufferPtr, "%s", line);
            bufferPtr += strlen(line);
        }
        else {
            // Copier la ligne dans le buffer
            sprintf(bufferPtr, "%s", line);
            bufferPtr += strlen(line);
        }
    }
    fclose(filePtr);

    // Réouvrir le fichier en mode écriture pour écraser le contenu
    filePtr = fopen("../partie.txt", "w");
    if (filePtr == NULL) {
        printf("Erreur lors de la réouverture du fichier pour écriture !");
        return;
    }

    // Écrire le buffer modifié dans le fichier
    fprintf(filePtr, "%s", buffer);
    fclose(filePtr);

    for (int i =0; i<motsCount; i++){
    }

    filePtr = fopen(filePath, "a");
    if (filePtr == NULL) {
        perror("Erreur lors de l'ouverture du fichier ../partie.txt");
        return;
    }

    WordDistance* liste_cousin = DistanceSem("C\\word2vec.bin", motNouveau);
    for (int i = 0; i < motsCount; i++) {
        //printf("\nMOT : %s", mots[i]);
        mots[i][strcspn(mots[i], "\n")] = '\0';
        float poids_sem = 0;
        float poids_lev = 10;
        int scoreSem = 0;
        
        for (int j = 0; j < N; j++) {
            if (strcmp(mots[i], liste_cousin[j].mots) == 0) {
                //printf("cousin valide\n");
                poids_sem = 0.8;
                poids_lev = 0.2;
                scoreSem = (int)floor(liste_cousin[j].distance);
                break;
            }
        }
        
        int score_total = 100;

        if (strcmp(mots[i], motNouveau) != 0) {
            int size_mot_long = strlen(mots[i]);
            
            if (strlen(motNouveau) > size_mot_long) {
                size_mot_long = strlen(motNouveau);
            }

            

            float distance_normalisee = (float)levenshtein(motNouveau, mots[i])/(size_mot_long+1);
            //printf("\ndistance lev: %f poids lev : %f", distance_normalisee, poids_lev);
            //printf("\ndistance sem : %d poids sem : %f", scoreSem, poids_sem);
            if (scoreSem==0){
                score_total=(1-distance_normalisee)*40;
            }
            else {
                score_total = (int)floor(((scoreSem *poids_sem ) + ((1 - distance_normalisee) * poids_lev))*100);
            }
            //printf("\n Score total : %d", score_total);
        }

        fprintf(filePtr, "\n%s %s %d", motNouveau, mots[i], score_total);
    }



    // Fermer le fichier
    fclose(filePtr);
}



int main(int argc, char *argv[]) {
    if (argc==2 && strcmp("--help", argv[1])==0){
        printf("Usage:\n");
        printf(" addword static_tree.lex mot1\n");
        printf(" where mot1 is one string in the dictionnary\n");
        exit(255);
    }
    // Vérifier si un argument a été fourni
    if (argc != 4) {
        printf("Usage: %s <file> <mot>\n", argv[0]);
        return 1;
    }
    StaticTree st = loadStaticTree(argv[1]);

    // Appel de la fonction avec le mot passé en argument
    ecritDansFichierPartie(argv[3], st, argv[2]);

    return 0;
}
