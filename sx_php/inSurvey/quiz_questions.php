<?php
if (intval($intQuizThemeID) == 0) {
    $intQuizThemeID = 0;
}
/**
 * Create variables for the java script radio control
 */
$strRadioVariables = "";
$strRadioCheck = "";

//Get information about the Quiz
$sql = "SELECT QuizTheme, StartDate, QuizNote FROM quiz_themes WHERE QuizThemeID = ? ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intQuizThemeID]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $strDate = $rs["StartDate"];
    $strQuizTheme = $rs["QuizTheme"];
    $strQuizNotes = $rs["QuizNote"];
}
$stmt = null;
$rs = null;

$sql = "SELECT QuizQuestionID, QuizQuestion, QuizMedia, NumberOfChoices,
     Choice1, Choice2, Choice3, Choice4, Choice5, Choice6, Choice7 
     FROM quiz_questions 
     WHERE QuizThemeID = ?
     ORDER BY QuizQuestionID ASC ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intQuizThemeID]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rs) {
    if (empty($str_QuizTittle)) {
        $str_QuizTittle  = lngQuiz;
    } ?>
    <section>
        <h1 class="head"><span><?= $str_QuizTittle  . ": " . $intQuizThemeID . " | " . $strDate ?></span></h1>
        <h2 class="head"><span><?= lngTheme ?>: <?= $strQuizTheme ?></span></h2>
        <?php
        if ($strQuizNotes != "") { ?>
            <div class="vote_wrapper"><?= $strQuizNotes ?></div>
        <?php
        } ?>
        <form class="formWrap" name="Form<?= $intQuizThemeID ?>" method="post" action="surveys.php?p=qr&quizThemeID=<?= $intQuizThemeID ?>">
            <?php
            $iRows = count($rs);
            for ($r = 0; $r < $iRows; $r++) { ?>
                <div class="vote_wrapper flex_start">
                    <?php
                    if (!empty($rs[$r]["QuizMedia"])) {
                        get_Any_Media($rs[$r]["QuizMedia"], 'Right', "", 'lightBox');
                    } ?>
                    <div>
                        <h4 class="indent"><span><?= $r + 1 . ". " ?></span> <?= $rs[$r]["QuizQuestion"] ?></h4>
                        <?php
                        $iChoices = $rs[$r]["NumberOfChoices"];
                        for ($i = 1; $i < $iChoices + 1; $i++) { ?>
                            <p class=indent>
                                <span><input onclick="radioCheck<?= $rs[$r]['QuizQuestionID'] ?>='Vote'" class="inRadio" type="radio" value="<?= $i ?>" name="Vote<?= $rs[$r]['QuizQuestionID'] ?>"></span>
                                <?= $rs[$r]["Choice" . $i] ?>
                            </p>
                        <?php
                        } ?>
                    </div>
                </div>
            <?php
                if (!empty($strRadioVariables)) {
                    $strRadioVariables .= ", ";
                }
                $strRadioVariables .= 'radioCheck' . $rs[$r]["QuizQuestionID"] . '=""';

                if (!empty($strRadioCheck)) {
                    $strRadioCheck .= " || ";
                }
                $strRadioCheck .= 'radioCheck' . $rs[$r]["QuizQuestionID"] . '==""';
            } ?>
            <p><input class="button-grey button-gradient" type="submit" value="<?= lngVote ?>" name="SendQuiz" onclick="return radio();"></p>
        </form>
    </section>
<?php
}
$stmt = null;
$rs = null;
?>
<script>
    // Basic form-controll for multiple radio imputs
    var <?= $strRadioVariables ?>;

    function radio() {
        if (<?= $strRadioCheck ?>) {
            alert("\n<?= lngMustAnswerAllQuestions ?>");
            return false;
        } else {
            return true;
        }
    }
</script>