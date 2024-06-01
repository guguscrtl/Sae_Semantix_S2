#include "loadStaticTree.c"

int main(int argc, char *argv[]) {
    if (argc==2 && strcmp("--help", argv[1])==0){
        printf("Usage:\n");
        printf(" dictionary_lookup static_tree.lex mot\n");
        printf(" where mot is one string in the dictionnary\n");
        exit(255);
    }
    if (argc!=3){
        printf("Utilisation incorrect : .\\dictionary_lookup <FILE> <WORD>");
        return 0;
    }
    StaticTree st = loadStaticTree(argv[1]);
    int offset = searchWordInStaticTree(argv[2], st);
    printf("Offset du mot %s = %d", argv[2], offset);
}