#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <limits.h>

#define MAX_WORD_LENGTH 100 // Taille maximale d'un mot

typedef char Element;

typedef struct node {
    Element elem;
    int offset;
    struct node* firstChild;
    struct node* nextSibling;
} Node;
typedef Node* CSTree;

typedef struct {
    Element elem;
    int offset;
    unsigned int firstChild;
    unsigned int nSiblings;
    unsigned int nextSibling; 
} ArrayCell;

typedef struct {
    ArrayCell* nodeArray;
    unsigned int nNodes;
} StaticTree;

#define NONE -1 

CSTree newCSTree(Element elem, int offset, CSTree firstChild, CSTree nextSibling) {
    CSTree t = malloc(sizeof(Node));
    if (t == NULL) {
        exit(EXIT_FAILURE);
    }
    t->elem = elem;
    t->offset = offset;
    t->firstChild = firstChild;
    t->nextSibling = nextSibling;
    return t;
}

int size(CSTree t) {
    if (t == NULL) {
        return 0;
    }
    return 1 + size(t->firstChild) + size(t->nextSibling);
}

int nbChildren(CSTree t) {
    if (t == NULL) {
        return 0;
    }
    return 1 + nbChildren(t->nextSibling);
}

int nChildren(CSTree t) {
    if (t == NULL) {
        return 0;
    }
    if (t->firstChild == NULL) {
        return 0;
    }
    return nbChildren(t->firstChild);
}

void fill_array_cells(StaticTree* st, CSTree t, unsigned int index_for_t, int nSiblings, int* reserved_cells) {
    unsigned int firstChild_index;
    if (t->firstChild != NULL) {
        firstChild_index = *reserved_cells;
    } else {
        firstChild_index = NONE;
    }

    st->nodeArray[index_for_t].elem = t->elem;
    st->nodeArray[index_for_t].offset = t->offset;
    st->nodeArray[index_for_t].nSiblings = nSiblings;

    if (t->offset != -1) {
        // Pour les nœuds avec un offset, lier correctement en tant que premier enfant
        st->nodeArray[index_for_t].firstChild = *reserved_cells;
    } else {
        st->nodeArray[index_for_t].firstChild = firstChild_index;
    }

    st->nodeArray[index_for_t].nextSibling = NONE;  // Ajustement pour commencer à NONE

    *reserved_cells += nChildren(t);

    if (t->nextSibling != NULL) {
        st->nodeArray[index_for_t].nextSibling = index_for_t + 1;
        fill_array_cells(st, t->nextSibling, index_for_t + 1, nSiblings - 1, reserved_cells);
    }
    if (t->firstChild != NULL) {
        fill_array_cells(st, t->firstChild, firstChild_index, nChildren(t) - 1, reserved_cells);
    }
}

void printCSTree(CSTree root, int depth) {
    if (root == NULL) {
        return;
    }

    for (int i = 0; i < depth; i++) {
        printf("  ");
    }

    printf("%c (Offset: %d)\n", root->elem, root->offset);

    printCSTree(root->firstChild, depth + 1);
    printCSTree(root->nextSibling, depth);
}

void insertWordWithOffset(CSTree* root, const char* word, int offset) {
    CSTree currentNode = *root;

    int i = 0;
    while (word[i] != '\0') {
        // Trouver la bonne position pour insérer le caractère dans l'ordre alphabétique
        CSTree *ptr = &(currentNode->firstChild);
        while (*ptr != NULL && (*ptr)->elem < word[i]) {
            ptr = &((*ptr)->nextSibling);
        }

        if (*ptr == NULL || (*ptr)->elem != word[i]) {
            // Insérer le caractère s'il n'existe pas déjà
            CSTree newChild = newCSTree(word[i], -1, NULL, NULL);
            newChild->nextSibling = *ptr;
            *ptr = newChild;
        }

        currentNode = *ptr;
        i++;
    }

    // Atteindre la fin du mot, mettre à jour l'offset
    currentNode->offset = offset;
}

// Function to print the StaticTree structure
void printStaticTree(StaticTree tree) {
    printf("Number of nodes in the tree: %u\n", tree.nNodes);
    printf("Printing nodes:\n");

    for (unsigned int i = 0; i < tree.nNodes; ++i) {
        printf("Node %u:\n", i); 
        printf("  Element: %c\n", tree.nodeArray[i].elem);
        printf("  Offset: %d\n", tree.nodeArray[i].offset);
        printf("  First Child: %u\n", tree.nodeArray[i].firstChild);
        printf("  Number of Siblings: %u\n", tree.nodeArray[i].nSiblings);
        printf("  Next Sibling: %u\n", tree.nodeArray[i].nextSibling);
        printf("\n");
    }
}

void writeStaticTree(StaticTree tree){
    FILE *arbre_lex = fopen("arbre.lex", "w"); 
    if (arbre_lex == NULL) {
        perror("Erreur lors de l'ouverture du fichier arbre.lex");
        return;
    }

    fprintf(arbre_lex, "Number of nodes in the tree: %u\n", tree.nNodes);
    fprintf(arbre_lex, "Printing nodes:\n");

    for (unsigned int i = 0; i < tree.nNodes; ++i) {
        fprintf(arbre_lex, "Node %u:\n", i); 
        fprintf(arbre_lex, "  Element: %c\n", tree.nodeArray[i].elem);
        fprintf(arbre_lex, "  Offset: %d\n", tree.nodeArray[i].offset);
        fprintf(arbre_lex, "  First Child: %u\n", tree.nodeArray[i].firstChild);
        fprintf(arbre_lex, "  Number of Siblings: %u\n", tree.nodeArray[i].nSiblings);
        fprintf(arbre_lex, "  Next Sibling: %u\n", tree.nodeArray[i].nextSibling);
        fprintf(arbre_lex,"\n");
    }
}

void displayStaticTree(const char* filename) {
    FILE *file = fopen(filename, "rb");
    if (file == NULL) {
        perror("Erreur lors de l'ouverture du fichier pour lecture");
        exit(EXIT_FAILURE);
    }

    unsigned int nNodes;
    fread(&nNodes, sizeof(unsigned int), 1, file);

    ArrayCell* nodeArray = malloc(nNodes * sizeof(ArrayCell));
    if (nodeArray == NULL) {
        fclose(file);
        perror("Allocation de mémoire échouée");
        exit(EXIT_FAILURE);
    }

    fread(nodeArray, sizeof(ArrayCell), nNodes, file);
    fclose(file);

    printf("Number of nodes in the tree: %u\n", nNodes);
    printf("Printing nodes:\n");

    for (unsigned int i = 0; i < nNodes; ++i) {
        printf("Node %u:\n", i); 
        printf("  Element: %c\n", nodeArray[i].elem);
        printf("  Offset: %d\n", nodeArray[i].offset);
        printf("  First Child: %u\n", nodeArray[i].firstChild);
        printf("  Number of Siblings: %u\n", nodeArray[i].nSiblings);
        printf("  Next Sibling: %u\n", nodeArray[i].nextSibling);
        printf("\n");
    }

    free(nodeArray);
}



void saveStaticTree(StaticTree tree, const char* filename) {
    FILE *file = fopen(filename, "wb");
    if (file == NULL) {
        perror("Erreur lors de l'ouverture du fichier pour sauvegarde");
        exit(EXIT_FAILURE);
    }

    // Écrire la taille de l'arbre dans le fichier binaire
    fwrite(&(tree.nNodes), sizeof(unsigned int), 1, file);

    // Écrire le tableau d'éléments de l'arbre statique dans le fichier binaire
    fwrite(tree.nodeArray, sizeof(ArrayCell), tree.nNodes, file);

    fclose(file);
}

StaticTree loadStaticTree(const char* filename) {
    FILE *file = fopen(filename, "rb");
    if (file == NULL) {
        perror("Erreur lors de l'ouverture du fichier pour lecture");
        exit(EXIT_FAILURE);
    }

    StaticTree tree;
    fread(&(tree.nNodes), sizeof(unsigned int), 1, file);

    tree.nodeArray = malloc(tree.nNodes * sizeof(ArrayCell));
    if (tree.nodeArray == NULL) {
        fclose(file);
        perror("Allocation de mémoire échouée");
        exit(EXIT_FAILURE);
    }

    fread(tree.nodeArray, sizeof(ArrayCell), tree.nNodes, file);
    fclose(file);

    return tree;
}

int searchWordInStaticTree(const char* word, StaticTree tree) {
    if (word == NULL || tree.nodeArray == NULL) {
        return -1; // Gestion des cas où le mot ou l'arbre est NULL
    }

    int pos = tree.nodeArray[0].firstChild; // On commence par le premier nœud de l'arbre
    for (int i = 0; word[i] != '\0'; ++i) {
        int found = 0;
        while (tree.nodeArray[pos].elem != '\0') {
                if (word[i + 1] == '\0' && tree.nodeArray[pos].elem == word[i]) { // Si c'est le dernier caractère du mot
                    pos= tree.nodeArray[pos].firstChild;
                    while ((int)tree.nodeArray[pos].elem != 10){
                        pos = tree.nodeArray[pos].nextSibling;
                    }
                    return tree.nodeArray[pos].offset; // Renvoie l'offset de cu mot
                }
                while (tree.nodeArray[pos].elem == '\0'){
                    pos = tree.nodeArray[pos].nextSibling;
                }
            if (tree.nodeArray[pos].elem == word[i]) {
                pos = tree.nodeArray[pos].firstChild; // Aller au premier enfant pour le caractère suivant
                found = 1;
                break;
            } else {
                pos = tree.nodeArray[pos].nextSibling; // Aller au frère suivant
            }

            if (pos == NONE) {
                return -1; // Sortir de la boucle si on atteint la fin des frères
            }
        }

        if (found != 1) {
            return -1; // Retourner -1 si le caractère n'est pas trouvé
        }
    }

    // Si le mot est trouvé en entier, mais le code n'est jamais censé arriver ici
    return -1;
}

// Crée un arbre statique avec le même contenu que t.
StaticTree exportStaticTree(CSTree t) {
    StaticTree st = {NULL, 0};
    int reserved_cells = 0;

    st.nNodes = size(t);
    st.nodeArray = malloc(st.nNodes * sizeof(ArrayCell));
    reserved_cells = nbChildren(t);

    fill_array_cells(&st, t, 0, reserved_cells - 1, &reserved_cells);

    if (reserved_cells != st.nNodes && t != NULL) {
        printf("Erreur lors de la création de l'arbre statique, taille finale incorrecte\n");
        exit(EXIT_FAILURE);
    }

    return st;
}
