package fr.uge.semantix.Tree;

public class Branch {
    private String firstWord;
    private String otherWord;
    private final int similarity;

    public Branch(String firstWord, String otherWord, int similarity) {
        this.firstWord = firstWord;
        this.otherWord = otherWord;
        this.similarity = similarity;
    }

    public String getFirstWord() {
        return firstWord;
    }

    public String getOtherWord() {
        return otherWord;
    }

    public int getSimilarity() {
        return similarity;
    }

    public void setFirstWord(String word){
        firstWord = word;
    }

    public void setOtherWord(String word){
        otherWord = word;
    }

    public String getNeighbors(String word){
        return firstWord.equals(word) ? otherWord : firstWord;
    }
}
