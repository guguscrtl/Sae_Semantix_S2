package fr.uge.semantix.Calcul;

import fr.uge.semantix.Tree.Branch;
import fr.uge.semantix.Tree.Tree;

import java.io.BufferedWriter;
import java.io.FileWriter;
import java.io.IOException;
import java.util.*;

public class TreeOptimizer {

    //LIGNE DANS LE FICHIER DU C QUI NOUS ANNONCE QUE L'ON VA LIRE LES DIFFERENTES SIMILARITES
    private static final String LINES_SIMILARITIES = "Distance entre mots :";

    //METHODE POUR OPTIMISER L'ARBRE
    public Tree optimizeTrees(Tree similarities, String treeFilePath) {
        Tree tree = new Tree();
        Set<String> visitedWords = new HashSet<>();
        //ON AJOUTE LE MOT DE DEPART AUX MOTS VISITES
        if (!similarities.getListTree().isEmpty()) {
            visitedWords.add(similarities.getListTree().get(0).getFirstWord());
        }
        //ON FAIT CA TANT QU'ON A PAS LU TOUT LES MOTS
        while (visitedWords.size() < similarities.nbWordsInTree()) {
            Branch maxSimilarityBranch = null;
            int maxSimilarity = Integer.MIN_VALUE;
            //ON PARCOURS TOUTES LES BRANCHES DE LA LISTE TRIEE
            for (Branch branch : similarities.getListTree()) {
                String firstWord = branch.getFirstWord();
                String otherWord = branch.getOtherWord();
                //SI L'UN DES DEUX MOTS N'ONT PAS ETE DEJA LU
                if ((visitedWords.contains(firstWord) && !visitedWords.contains(otherWord))
                        || (visitedWords.contains(otherWord) && !visitedWords.contains(firstWord))) {
                    //ALORS ON CHANGE LA SIMILARITES MAX ET LA PLUS GRANDE BRANCHE
                    if (branch.getSimilarity() > maxSimilarity) {
                        maxSimilarity = branch.getSimilarity();
                        maxSimilarityBranch = branch;
                    }
                }
            }
            //SI ON A ENREGISTREES UNE BRANCHE MAXIMALE
            if (maxSimilarityBranch != null) {
                //ON AJOUTE LES MOTS ET SIMILARITE DE CETTE BRANCHE AUX MOTS ET SIMILARITES VISITES
                visitedWords.add(maxSimilarityBranch.getFirstWord());
                visitedWords.add(maxSimilarityBranch.getOtherWord());
                tree.add(maxSimilarityBranch);
            } else {
                //AUCUNE BRANCHE NE PEUT ETRE AJOUTEE
                break;
            }
        }
        //ON APPELLE LA FONCTION QUI ECRIT DANS LE FICHIER
        writeIntoFiles(treeFilePath, tree);
        //ON RETOURNE L'ARBRE OPTIMISE
        return tree;
    }

    //METHODE QUI PERMET DE LIRE DANS LE FICHIER
    public Tree readSimilaritiesFromFile(String filePath) {
        Tree tree = new Tree();
        int test = 0;
        try (Scanner scanner = new Scanner(new java.io.File(filePath))) {
            //TANT QU'ON LIS PAS LA LIGNE QUI ANNONCE LES SIMILARITES ON NE FAIT RIEN
            while(!scanner.nextLine().equals(LINES_SIMILARITIES)){
                test++;
            }
            //TANT QU'IL Y A DES SIMILARITES A LIRE ON LES LIS
            while (scanner.hasNext()) {
                //PUIS ON CREE DES BRANCHES QU'ON AJOUTE A NOTRE ARBRE
                String word1 = scanner.next();
                String word2 = scanner.next();
                int similarity = scanner.nextInt();
                tree.add(new Branch(word1, word2, similarity));
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
        //ON RETOURNE L'ARBRE LU A PARTIR DU FICHIER
        return tree;
    }

    //METHODE POUR ECRIRE DANS UN FICHIER
    private static void writeIntoFiles(String treeFilePath, Tree similarities) {
        try (BufferedWriter writer = new BufferedWriter(new FileWriter(treeFilePath))) {
            Set<String> processed = new HashSet<>();
            //ON ECRIT LA LIGNE QUI ANNONCE LES SIMILARITES
            writer.write("Distance entre mots :\n");
            //POUR CHAQUE BRANCHE DE L'ARBRE OPTIMISE, ON ECRIT BRANCHE PAR BRANCHE DANS LE FICHIER
            for (Branch branch : similarities.getListTree()) {
                String word = branch.getFirstWord();
                String neighbor = branch.getOtherWord();
                int similarity = branch.getSimilarity();

                //ECRIRE LA SIMILARITES SI ELLE N'A PAS DEJA ETE TRAITEE ET N'EST PAS UN DOUBLON
                if (!processed.contains(word + " " + neighbor) && !processed.contains(neighbor + " " + word)
                        && !word.equals(neighbor)) {
                    writer.write(word + " " + neighbor + " " + similarity + System.lineSeparator());
                    processed.add(word + " " + neighbor);
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public int getScoreMin(Tree tree, String firstWord, String lastWord){
        var fatherMap = createFather(tree, firstWord, new HashMap<>());
        var listSimilarity = new ArrayList<Integer>();
        while (!firstWord.equals(lastWord)){
            var fatherOfWord = fatherMap.get(lastWord);
            listSimilarity.add(tree.getSimilarityForWords(fatherOfWord, lastWord));
            lastWord = fatherOfWord;
        }
        return getMin(listSimilarity);
    }

    public Map<String, String> createFather(Tree tree, String word, Map<String, String> fatherMap){
        if (tree.getListTree().size()<=0 || tree.equals(null)){
            return fatherMap;
        }
        var listConnection = new ArrayList<String>();
        var newTree = new Tree();
        if (fatherMap.isEmpty()){
            fatherMap.put(word, word);
        }
        for (Branch branch : tree.getListTree()){
            if (branch.getFirstWord().equals(word) || branch.getOtherWord().equals(word)){
                listConnection.add(branch.getNeighbors(word));
            }
            else {
                newTree.add(branch);
            }
        }
        for (String connection : listConnection){
            fatherMap.put(connection, word);
            return createFather(newTree, connection, fatherMap);
        }
        return fatherMap;
    }

    private int getMin(List<Integer> liste){
        var min = 1000000000;
        for (Integer n : liste){
            if (n < min){
                min = n;
            }
        }
        return min;
    }
}
