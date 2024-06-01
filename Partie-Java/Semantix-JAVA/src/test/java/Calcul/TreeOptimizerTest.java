package Calcul;

import fr.uge.semantix.Calcul.TreeOptimizer;
import fr.uge.semantix.Tree.Branch;
import fr.uge.semantix.Tree.Tree;
import org.assertj.core.api.Assertions;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.params.ParameterizedTest;
import org.junit.jupiter.params.provider.Arguments;
import org.junit.jupiter.params.provider.MethodSource;

import java.util.HashMap;
import java.util.stream.Stream;

public class TreeOptimizerTest {
    private TreeOptimizer builder = new TreeOptimizer();
    private static final String SIMILARITIES_SIMPLE_FILE_PATH = "src/test/java/files_text/similariteSimple.txt";
    private static final String SIMILARITIES_MEDIUM_FILE_PATH = "src/test/java/files_text/similariteMedium.txt";
    private static final String SIMILARITIES_ADVANCED_FILE_PATH = "src/test/java/files_text/similariteAdvanced.txt";
    private static final String TREE_SIMPLE_FILE_PATH = "src/test/java/files_text/arbreSimple.txt";
    private static final String TREE_MEDIUM_FILE_PATH = "src/test/java/files_text/arbreMedium.txt";
    private static final String TREE_ADVANCED_FILE_PATH = "src/test/java/files_text/arbreAdvanced.txt";

    @Test
    void should_calculate_score_after_optimisation(){
        //ON EFFECTUE LA METHODE DE CALCUL DE SCORE
        int min = builder.getScoreMin(mediumTrees(), "A", "B");
        //ON VERIFIE QUE LE SCORE EST CORRECT
        Assertions.assertThat(min).isEqualTo(15);
    }

    @Test
    void should_create_father_map(){
        Tree tree = simpleTrees();
        var fatherMap = builder.createFather(tree, "A", new HashMap<>());

        Assertions.assertThat(fatherMap).isEqualTo(expectedFatherMap());
    }

    //LE PARAMETERIZEDTEST PERMET DE TESTER AVEC PLUSIEURS DONNEES SANS REPETITION
    @ParameterizedTest(name = "should_add_new_word_to_a_{1}")
    @MethodSource("datas")
    void should_add_new_word_to_tree(Tree expected, String entryFilesPath, String filesPath) {
        Tree similarities = builder.readSimilaritiesFromFile(entryFilesPath);
        Tree tree = builder.optimizeTrees(similarities, filesPath);

        Assertions.assertThat(tree.getListTree().size()).isEqualTo(expected.getListTree().size());

        for (int i = 0; i < tree.getListTree().size(); i++) {
            Assertions.assertThat(expected.contains(tree.getListTree().get(i))).isTrue();
        }
    }

    static Tree advancedTrees() {
        Tree similarities = new Tree();
        similarities.add(new Branch("B", "C", 15));
        similarities.add(new Branch("A", "D", 50));
        similarities.add(new Branch("C", "D", 40));
        return similarities;
    }

    static Tree mediumTrees() {
        Tree similarities = new Tree();
        similarities.add(new Branch("A", "C", 20));
        similarities.add(new Branch("B", "C", 15));
        similarities.add(new Branch("A", "D", 50));
        return similarities;
    }

    static Tree simpleTrees() {
        Tree similarities = new Tree();
        similarities.add(new Branch("A", "C", 20));
        similarities.add(new Branch("B", "C", 15));
        return similarities;
    }

    static HashMap<String, String> expectedFatherMap(){
        var fatherMap = new HashMap<String, String>();
        fatherMap.put("A", "A");
        fatherMap.put("B", "C");
        fatherMap.put("C", "A");
        return fatherMap;
    }

    static Stream<Arguments> datas(){
        return Stream.of(Arguments.of(simpleTrees(), SIMILARITIES_SIMPLE_FILE_PATH, TREE_SIMPLE_FILE_PATH),
                Arguments.of(mediumTrees(), SIMILARITIES_MEDIUM_FILE_PATH, TREE_MEDIUM_FILE_PATH),
                Arguments.of(advancedTrees(), SIMILARITIES_ADVANCED_FILE_PATH, TREE_ADVANCED_FILE_PATH));
    }
}
