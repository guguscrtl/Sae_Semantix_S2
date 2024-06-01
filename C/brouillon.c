#include <stdlib.h>
#include <stdio.h>
#include <assert.h>
#include <math.h>
#include <string.h>

const long long max_size = 2000;         // max length of strings
const long long N = 1000;                  // number of closest words that will be shown
const long long max_w = 50;              // max length of vocabulary entries


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

float distanceSemantics(const char *file_name, const char *word1, const char *anciens_mots[], int nb_mots) {
  FILE *f;
  char st1[max_size];
  char *bestw[N];
  char  st[100][max_size];
  float dist, len, bestd[N], vec[max_size];
  long long words, size, a, b, c, d, cn, bi[100];
  char ch;
  float *M;
  char *vocab;
  float scores[nb_mots];
  f = fopen(file_name, "rb");
  if (f == NULL) {
    printf("Input file not found\n");
    return -1;
  }
  fscanf(f, "%lld", &words);
  fscanf(f, "%lld", &size);
  vocab = (char *)malloc((long long)words * max_w * sizeof(char));
  for (a = 0; a < N; a++) bestw[a] = (char *)malloc(max_size * sizeof(char));
  M = (float *)malloc((long long)words * (long long)size * sizeof(float));
  if (M == NULL) {
    printf("Cannot allocate memory: %lld MB    %lld  %lld\n", (long long)words * size * sizeof(float) / 1048576, words, size);
    return -1;
  }
  for (b = 0; b < words; b++) {
    a = 0;
    while (1) {
      vocab[b * max_w + a] = fgetc(f);
      if (feof(f) || (vocab[b * max_w + a] == ' ')) break;
      if ((a < max_w) && (vocab[b * max_w + a] != '\n')) a++;
    }
    vocab[b * max_w + a] = 0;
    for (a = 0; a < size; a++) fread(&M[a + b * size], sizeof(float), 1, f);
    len = 0;
    for (a = 0; a < size; a++) len += M[a + b * size] * M[a + b * size];
    len = sqrt(len);
    for (a = 0; a < size; a++) M[a + b * size] /= len;
  }
  fclose(f);

    for (a = 0; a < N; a++) bestd[a] = 0;
    for (a = 0; a < N; a++) bestw[a][0] = 0;
   // printf("Enter word or sentence (EXIT to break): ");
    a = 0;
    if (!strcmp(st1, "EXIT"));
    strcpy(st1, word1);
    cn = 0;
    b = 0;
    c = 0;
    while (1) {
      st[cn][b] = st1[c];
      b++;
      c++;
      st[cn][b] = 0;
      if (st1[c] == 0) break;
      if (st1[c] == ' ') {
        cn++;
        b = 0;
        c++;
      }
    }
    cn++;
    for (a = 0; a < cn; a++) {
      for (b = 0; b < words; b++) if (!strcmp(&vocab[b * max_w], st[a])) break;
      if (b == words) b = -1;
      bi[a] = b;
      //printf("\nWord: %s  Position in vocabulary: %lld\n", st[a], bi[a]);
      if (b == -1) {
        printf("Out of dictionary word!\n");
        break;
      }
    }
    if (b == -1);
    //printf("\n                                              Word       Cosine distance\n------------------------------------------------------------------------\n");
    for (a = 0; a < size; a++) vec[a] = 0;
    for (b = 0; b < cn; b++) {
      if (bi[b] == -1) continue;
      for (a = 0; a < size; a++) vec[a] += M[a + bi[b] * size];
    }
    len = 0;
    for (a = 0; a < size; a++) len += vec[a] * vec[a];
    len = sqrt(len);
    for (a = 0; a < size; a++) vec[a] /= len;
    for (a = 0; a < N; a++) bestd[a] = -1;
    for (a = 0; a < N; a++) bestw[a][0] = 0;
    for (c = 0; c < words; c++) {
      a = 0;
      for (b = 0; b < cn; b++) if (bi[b] == c) a = 1;
      if (a == 1) continue;
      dist = 0;
      for (a = 0; a < size; a++) dist += vec[a] * M[a + c * size];
      for (a = 0; a < N; a++) {
        if (dist > bestd[a]) {
          for (d = N - 1; d > a; d--) {
            bestd[d] = bestd[d - 1];
            strcpy(bestw[d], bestw[d - 1]);
          }
          bestd[a] = dist;
          strcpy(bestw[a], &vocab[c * max_w]);
          int found = 0;
            for (int i = 0; i < nb_mots; i++) {
                if (strcmp(bestw[a], anciens_mots[i]) == 0) {
                    found = 1;

                    // Retire anciens_mots[i] en déplaçant les éléments suivants vers la gauche
                    for (int j = i; j < nb_mots - 1; j++) {
                        strcpy(anciens_mots[j], anciens_mots[j + 1]);
                    }
                    nb_mots--; // Diminue la taille du tableau

                    break;
                }
            }

            if (found) {
                free(M);
                free(vocab);
                for (int a = 0; a < N; a++) {
                    free(bestw[a]);
                }
                for (int i = 0; i < nb_mots; i++) {
                  if (scores[i] == 0.0) {  // Vérifie si l'élément est non initialisé (0.0 est une valeur par défaut pour les float)
                      scores[i] = bestd[a];
                      break;
                  }
                }
                found = 0;
            }


          }
          break;
        }
      }
  
  free(M);
  free(vocab);
  for (int a = 0; a < N; a++) {
    free(bestw[a]);
  }
  
  return 0;
}

int distanceTotal(char *mot1, char *mot2){
    if (strcmp(mot1, mot2)==0){
        return 100;
    }
    int distance = (25-levenshtein(mot1, mot2))+(distanceSemantics("C\\word2vec.bin", mot1, mot2)*100); //formule de la distance : (25-levenstein)+(semantics*100)
    return distance;
}





#include <stdlib.h>
#include <stdio.h>
#include <assert.h>
#include <math.h>
#include <string.h>

const long long max_size = 2000;         // max length of strings
const long long N = 1000;                  // number of closest words that will be shown
const long long max_w = 50;              // max length of vocabulary entries


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

int* levenshtein(char *S, char *T[], int nb_mots) {
    size_t sizeS = strlen(S);
    int* distances = (int*)malloc(nb_mots * sizeof(int));

    for (int k = 0; k < nb_mots; k++) {
        size_t sizeT = strlen(T[k]);
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
                if (S[i - 1] == T[k][j - 1]) {
                    val = get(&a, (int)i - 1, (int)j - 1);
                } else {
                    val = 1 + min(get(&a, (int)i - 1, (int)j - 1), min(get(&a, (int)i - 1, (int)j), get(&a, (int)i, (int)j - 1)));
                }
                set(&a, (int)i, (int)j, val);
            }
        }

        distances[k] = get(&a, (int)sizeS, (int)sizeT);
        free(a.tab); // Libérer la mémoire allouée pour cette instance de Levenshtein
    }

    return distances;
}


float* distanceSemantics(const char *file_name, const char *word1, const char *anciens_mots[], int nb_mots) {
  FILE *f;
  char st1[max_size];
  char *bestw[N];
  char  st[100][max_size];
  float dist, len, bestd[N], vec[max_size];
  long long words, size, a, b, c, d, cn, bi[100];
  char ch;
  float *M;
  char *vocab;
  float *scores = (float*)malloc(nb_mots * sizeof(float));
  f = fopen(file_name, "rb");
  if (f == NULL) {
    printf("Input file not found\n");
    float* erreurArray = (float*)malloc(sizeof(float));
        *erreurArray = -1.0;
        return erreurArray;
  }
  fscanf(f, "%lld", &words);
  fscanf(f, "%lld", &size);
  vocab = (char *)malloc((long long)words * max_w * sizeof(char));
  for (a = 0; a < N; a++) bestw[a] = (char *)malloc(max_size * sizeof(char));
  M = (float *)malloc((long long)words * (long long)size * sizeof(float));
  if (M == NULL) {
    printf("Cannot allocate memory: %lld MB    %lld  %lld\n", (long long)words * size * sizeof(float) / 1048576, words, size);
    float* erreurArray = (float*)malloc(sizeof(float));
        *erreurArray = -1.0;
        return erreurArray;
  }
  for (b = 0; b < words; b++) {
    a = 0;
    while (1) {
      vocab[b * max_w + a] = fgetc(f);
      if (feof(f) || (vocab[b * max_w + a] == ' ')) break;
      if ((a < max_w) && (vocab[b * max_w + a] != '\n')) a++;
    }
    vocab[b * max_w + a] = 0;
    for (a = 0; a < size; a++) fread(&M[a + b * size], sizeof(float), 1, f);
    len = 0;
    for (a = 0; a < size; a++) len += M[a + b * size] * M[a + b * size];
    len = sqrt(len);
    for (a = 0; a < size; a++) M[a + b * size] /= len;
  }
  fclose(f);

    for (a = 0; a < N; a++) bestd[a] = 0;
    for (a = 0; a < N; a++) bestw[a][0] = 0;
   // printf("Enter word or sentence (EXIT to break): ");
    a = 0;
    if (!strcmp(st1, "EXIT"));
    strcpy(st1, word1);
    cn = 0;
    b = 0;
    c = 0;
    while (1) {
      st[cn][b] = st1[c];
      b++;
      c++;
      st[cn][b] = 0;
      if (st1[c] == 0) break;
      if (st1[c] == ' ') {
        cn++;
        b = 0;
        c++;
      }
    }
    
    cn++;
    for (a = 0; a < cn; a++) {
      for (b = 0; b < words; b++) if (!strcmp(&vocab[b * max_w], st[a])) break;
      if (b == words) b = -1;
      bi[a] = b;
      //printf("\nWord: %s  Position in vocabulary: %lld\n", st[a], bi[a]);
      if (b == -1) {
        printf("Out of dictionary word!\n");
        break;
      }
    }
    if (b == -1);
    //printf("\n                                              Word       Cosine distance\n------------------------------------------------------------------------\n");
    for (a = 0; a < size; a++) vec[a] = 0;
    for (b = 0; b < cn; b++) {
      if (bi[b] == -1) continue;
      for (a = 0; a < size; a++) vec[a] += M[a + bi[b] * size];
    }
    len = 0;
    for (a = 0; a < size; a++) len += vec[a] * vec[a];
    len = sqrt(len);
    for (a = 0; a < size; a++) vec[a] /= len;
    for (a = 0; a < N; a++) bestd[a] = -1;
    for (a = 0; a < N; a++) bestw[a][0] = 0;
    for (c = 0; c < words; c++) {
      a = 0;
      for (b = 0; b < cn; b++) if (bi[b] == c) a = 1;
      if (a == 1) continue;
      dist = 0;
      for (a = 0; a < size; a++) dist += vec[a] * M[a + c * size];
      for (a = 0; a < N; a++) {
        if (dist > bestd[a]) {
          for (d = N - 1; d > a; d--) {
            bestd[d] = bestd[d - 1];
            strcpy(bestw[d], bestw[d - 1]);
          }
          bestd[a] = dist;
          strcpy(bestw[a], &vocab[c * max_w]);
            for (int i = 0; i < nb_mots; i++) {
                if (strcmp(bestw[a], anciens_mots[i]) == 0) {
                    scores[i] = bestd[a];
                }
            }


          }
        }
      }
  
          printf("je suis la");
  free(M);
  free(vocab);
  for (int a = 0; a < N; a++) {
    free(bestw[a]);
  }
  
  return scores;
}

int* distanceTotal(char *mot1, char *liste_mots[], int nb_mots){
    // if (strcmp(mot1, mot2)==0){
    //     return 100;
    // }


    const char *liste_mots_const[nb_mots];
    for (int i = 0; i < nb_mots; i++) {
        liste_mots_const[i] = liste_mots[i];
    }


    float *resultatsDistance = distanceSemantics("C\\word2vec.bin", mot1, liste_mots_const, nb_mots);
    int *resultatsLevenshtein = levenshtein(mot1, liste_mots, nb_mots);
    int *distanceTotal;

    for (int i= 0; i<nb_mots; i++){
      printf("distance sem : %f", resultatsDistance[i]);
      distanceTotal[i] = (25-resultatsLevenshtein[i])+(resultatsDistance[i]*100); //formule de la distance : (25-levenstein)+(semantics*100)
    }

    for (int i = 0; i < nb_mots; i++) {
      printf("resultatsDistance[%d] : %f\n", i, resultatsDistance[i]);
    }

    return distanceTotal;
}
