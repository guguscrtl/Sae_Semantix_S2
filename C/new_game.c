#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "loadStaticTree.c"
#include "distanceSemantique.c"
#include "distanceTotal.c"

void writeWordsToTextFile(int argc, char *argv[]) {
    FILE *filePtr;
    char filePath[256];
    snprintf(filePath, sizeof(filePath), "./save/%s.txt", argv[2]);
    filePtr = fopen(filePath, "w");

    if (filePtr == NULL) {
        perror("Erreur lors de l'ouverture du fichier");
        fprintf(stderr, "Erreur lors de l'ouverture du fichier %s\n", filePath);
        exit(EXIT_FAILURE);
    }

    StaticTree st = loadStaticTree(argv[1]);

    fprintf(filePtr, "Mots de départ :\n");
    int offset;
   for (int i = 3; i < argc ; i++) {
        offset = searchWordInStaticTree(argv[i], st);
        if(offset != -1){
            fprintf(filePtr, "%s\n", argv[i]);
        } 
        else{
            printf("mot : %s n'est pas dans le dictionnaire.\n", argv[i]);
        }
    }
    fprintf(filePtr, "Mots du joueur1 :");
    fprintf(filePtr, "\nOffset de chaque mot :");
    for (int i = 3; i < argc ; i++) {
        offset = searchWordInStaticTree(argv[i], st);
        if(offset != -1){
            fprintf(filePtr, "\n%s %d", argv[i], offset);
        } 
        else{
            printf("mot : %s n'est pas dans le dictionnaire.\n", argv[i]);
        }
    }
    fprintf(filePtr, "\nDistance entre mots :");




    for (int i=3; i<argc; i++){
        WordDistance* liste_cousin = DistanceSem("C\\word2vec.bin", argv[i]);
        for (int k=i+1; k<argc; k++){
        argv[k][strcspn(argv[k], "\n")] = '\0';
        float poids_sem = 0;
        float poids_lev = 10;
        int scoreSem = 0;
        
        for (int j = 0; j < N; j++) {
            if (strcmp(argv[k], liste_cousin[j].mots) == 0) {
                //printf("cousin valide\n");
                poids_sem = 0.8;
                poids_lev = 0.2;
                scoreSem = (int)floor(liste_cousin[j].distance);
                break;
            }
        }
        
        int score_total = 100;

        if (strcmp(argv[k], argv[i]) != 0) {
            int size_mot_long = strlen(argv[k]);
            
            if (strlen(argv[i]) > size_mot_long) {
                size_mot_long = strlen(argv[i]);
            }
            float distance_normalisee = (float)levenshtein(argv[i], argv[k])/(size_mot_long+1);
            if (scoreSem==0){
                score_total=(1-distance_normalisee)*40;
            }
            else {
                score_total = (int)floor(((scoreSem *poids_sem ) + ((1 - distance_normalisee) * poids_lev))*100);
            }
        }

        fprintf(filePtr, "\n%s %s %d", argv[i], argv[k], score_total);
        }
    }
       
    fclose(filePtr);
}

int main(int argc, char *argv[]) {
    if (argc==2 && strcmp("--help", argv[1])==0){
        printf("Usage:\n");
        printf(" new_game static_tree.lex mot1 mot2 ..... motn\n");
        printf(" where mot1, mot2, ...., motn are two strings in the dictionnary\n");
        exit(255);
    }
    if (argc > 2) {
        writeWordsToTextFile(argc, argv);
    } else {
        printf("Aucun argument passé au programme.\n");
    }

    return 0;
}
