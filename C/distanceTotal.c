#include <stdlib.h>
#include <stdio.h>
#include <assert.h>
#include <math.h>
#include <string.h>

// const long long max_size = 2000;         // max length of strings
// const long long N = 1000;                  // number of closest words that will be shown
// const long long max_w = 50;              // max length of vocabulary entries


typedef struct {
    int lenS;
    int lenT;
    int *tab;
} LevArray;

int min(int a, int b) {
    return a < b ? a : b;
}

LevArray init(int lenS, int lenT) {
    LevArray a;
    a.lenS = lenS;
    a.lenT = lenT;
    a.tab = malloc((lenS + 1) * (lenT + 1) * sizeof(int));
    assert(a.tab != NULL);

    // Initialize the array with zeros
    memset(a.tab, 0, (lenS + 1) * (lenT + 1) * sizeof(int));

    return a;
}

void set(LevArray *a, int indexS, int indexT, int val) {
    assert(indexS >= 0 && indexS <= a->lenS && indexT >= 0 && indexT <= a->lenT);
    a->tab[indexT * (a->lenS + 1) + indexS] = val;
}

int get(LevArray *a, int indexS, int indexT) {
    assert(indexS >= 0 && indexS <= a->lenS && indexT >= 0 && indexT <= a->lenT);
    return a->tab[indexT * (a->lenS + 1) + indexS];
}

int levenshtein(char *S, char *T) {
    size_t sizeS = strlen(S);
    size_t sizeT = strlen(T);
    LevArray a = init((int)sizeS, (int)sizeT);

    for (size_t i = 0; i <= sizeS; i++) {
        set(&a, (int)i, 0, (int)i);
    }
    for (size_t j = 0; j <= sizeT; j++) {
        set(&a, 0, (int)j, (int)j);
    }

    for (size_t i = 1; i <= sizeS; i++) {
        for (size_t j = 1; j <= sizeT; j++) {
            int val;
            if (S[i - 1] == T[j - 1]) {
                val = get(&a, (int)i - 1, (int)j - 1);
            } else {
                val = 1 + min(get(&a, (int)i - 1, (int)j - 1), min(get(&a, (int)i - 1, (int)j), get(&a, (int)i, (int)j - 1)));
            }
            set(&a, (int)i, (int)j, val);
        }
    }

    int distance = get(&a, (int)sizeS, (int)sizeT);
    free(a.tab); // Free allocated memory
    return distance;
}

