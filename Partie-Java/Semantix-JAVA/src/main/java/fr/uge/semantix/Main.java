package fr.uge.semantix;

import fr.uge.semantix.Calcul.TreeOptimizer;
import fr.uge.semantix.Tree.Tree;

public class Main {
    public static void main(String[] args) {
        if (args.length <= 2) {
            System.out.println("La fonction doit avoir comme arguments : \n" +
                    "'score' ou 'optim'\n" +
                    "si score : \n" +
                    "   - un fichier qui contient les similarités\n" +
                    "   - un mot de depart\n" +
                    "   - un mot d'arrivé\n" +
                    "si optim : \n" +
                    "   - un fichier d'entrée avec les similarités du C\n" +
                    "   - un fichier de sortie que l'on va renvoyer");
        }
        var optimizerTree = new TreeOptimizer();
        var similarities = optimizerTree.readSimilaritiesFromFile(args[1]);
        if (args[0].equals("optim")){
            Tree tree = optimizerTree.optimizeTrees(similarities, args[2]);
            System.out.println(tree.toString());
        }
        if (args[0].equals("score")){
            int score = optimizerTree.getScoreMin(similarities, args[2], args[3]);
            System.out.println(score);
        }
    }
}
