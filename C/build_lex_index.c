#include "loadStaticTree.c"
#include "convertvec.c"

int main(int argc, char *argv[]){
    if (argc==2 && strcmp("--help", argv[1])==0){
        printf("Usage:\n");
        printf(" build_lex_index word2vec.bin\n");
        printf(" where word2vec.bin is binary file who contains all words\n");
        exit(255);
    }
    if (argc<1 || argc >2){
        printf("mauvaise utilisation : .\\build_lex_index <dictionnary>");
        return 0;
    }
    CSTree tree = newCSTree('\0', -1, NULL, NULL);
    int offset_count = 0;

    bin2txt(argv[1], "output.txt");

    FILE *dico = fopen("output.txt", "r");
    if (dico == NULL) {
        perror("Erreur lors de l'ouverture du fichier");
        return EXIT_FAILURE;
    }



    char word[MAX_WORD_LENGTH];
    word[strcspn(word, "\n")] = '\0';
    while (fgets(word, MAX_WORD_LENGTH, dico) != NULL) {
        // Supprimer le saut de ligne (\n) à la fin du mot lu
        insertWordWithOffset(&tree, word, offset_count);
        offset_count ++;
    }
    fclose(dico);
    StaticTree st = exportStaticTree(tree);
    saveStaticTree(st,"static_tree.lex");
}