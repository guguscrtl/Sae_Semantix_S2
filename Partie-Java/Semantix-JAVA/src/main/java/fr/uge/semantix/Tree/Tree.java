package fr.uge.semantix.Tree;

import java.util.ArrayList;
import java.util.List;

public class Tree {
    private ArrayList<Branch> listBranch;

    public Tree() {
        this.listBranch = new ArrayList<Branch>();
    }

    public ArrayList<Branch> getListTree() {
        return listBranch;
    }

    public void add(Branch branch){
        listBranch.add(branch);
    }

    //METHODE CONTAINS POUR LES TESTS UNITAIRES
    public boolean contains(Branch branch){
        for (Branch branchList : listBranch){
            if (branch.getFirstWord().equals(branchList.getFirstWord()) || branch.getFirstWord().equals(branchList.getOtherWord())){
                if (branch.getOtherWord().equals(branchList.getOtherWord()) || branch.getOtherWord().equals(branchList.getFirstWord())){
                    if (branch.getSimilarity() == (branchList.getSimilarity())){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    //METHODE QUI RENVOIE LE NOMBRE DE MOTS DIFFERENTS DANS UN ARBRE
    public int nbWordsInTree() {
        int compteur = 0;
        List<String> words = new ArrayList<>();
        for (Branch branch : listBranch){
            if (!words.contains(branch.getFirstWord())){
                words.add(branch.getFirstWord());
                compteur++;
            }
            if (!words.contains(branch.getOtherWord())){
                words.add(branch.getOtherWord());
                compteur++;
            }
        }
        return compteur;
    }

    public int getSimilarityForWords(String firstWord, String lastWord){
        for (Branch branch : listBranch){
            if ((branch.getFirstWord().equals(firstWord) || branch.getOtherWord().equals(firstWord))
            && (branch.getFirstWord().equals(lastWord) || branch.getOtherWord().equals(lastWord))){
                return branch.getSimilarity();
            }
        }
        return 0;
    }

    public String toString(){
        var sb = new StringBuilder();
        for (Branch branch : listBranch){
            sb.append(branch.getFirstWord() + " " + branch.getOtherWord() + " " + branch.getSimilarity() + "\n");
        }
        return sb.toString();
    }
}
