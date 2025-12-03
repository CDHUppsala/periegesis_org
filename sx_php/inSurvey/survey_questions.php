<?php
if (intval($intSurveyID) == 0) {
    $intSurveyID = 0;
}

$strRadioVariables = "";
$strRadioCheck = "";

//Get information about the survey
$sql = "SELECT SurveyID, SurveyTheme, InsertDate, SurveyNote FROM Surveys WHERE SurveyID = ? ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intSurveyID]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $strDate = $rs["InsertDate"];
    $strSurveyTheme = $rs["SurveyTheme"];
    $strSurveyNotes = $rs["SurveyNote"];
}
$stmt = null;
$rs = null;

$sql = "SELECT SurveyQuestionID, SurveyID, SurveyQuestion, NumberOfChoices, 
     Choice1, Choice2, Choice3, Choice4, Choice5, Choice6, Choice7, Choice8, Choice9, Choice10 
     FROM survey_questions 
     WHERE SurveyID = ?
     ORDER BY SurveyQuestionID ASC ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intSurveyID]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rs) {
    if (empty($str_SurveyTittle)) {
        $str_SurveyTittle = lngSurvey;
    } ?>
    <section>
        <h1 class="head"><span><?= $str_SurveyTittle . ": " . $intSurveyID . " | " . lngDate . ": " . $strDate ?></span></h1>
        <h2 class="head"><span><?= lngTheme ?>: <?= $strSurveyTheme ?></span></h2>
        <?php
        if ($strSurveyNotes != "") { ?>
            <div class="vote_wrapper"><?= $strSurveyNotes ?></div>
        <?php
        } ?>
        <form class="formWrap" name="Form<?= $intSurveyID ?>" method="post" action="surveys.php?p=sr&surveyID=<?= $intSurveyID ?>">
            <?php
            $iRows = count($rs);
            for ($r = 0; $r < $iRows; $r++) { ?>
                <div class="vote_wrapper">
                    <p class="indent"><span><?= $r + 1 . ". " ?></span> <?= $rs[$r]["SurveyQuestion"] ?></p>
                    <?php
                    $iChoices = $rs[$r]["NumberOfChoices"];
                    for ($i = 1; $i < $iChoices + 1; $i++) { ?>
                        <p class="indent">
                            <span><input onclick="radioCheck<?= $rs[$r]['SurveyQuestionID'] ?>='Vote'" class="inRadio" type="radio" value="<?= $i ?>" name="vote<?= $rs[$r]['SurveyQuestionID'] ?>"></span>
                            <?= $rs[$r]["Choice" . $i] ?>
                        </p>
                    <?php
                    } ?>
                </div>
            <?php
                //Create variables for the java script radio control
                if (!empty($strRadioVariables)) {
                    $strRadioVariables .= ", ";
                }
                $strRadioVariables .= 'radioCheck' . $rs[$r]["SurveyQuestionID"] . '=""';

                if (!empty($strRadioCheck)) {
                    $strRadioCheck .= "|| ";
                }
                $strRadioCheck .= 'radioCheck' . $rs[$r]["SurveyQuestionID"] . '=="" ';
            } ?>

            <p><input class="button-grey button-gradient" type="submit" value="<?= lngVote ?>" name="SendSurvey" onclick="return radio();"></p>
        </form>
        <p>
            <a href="surveys.php?p=sr&surveyID=<?= $intSurveyID ?>"><?= lngViewResults ?> »</a><br>
            <a href="surveys.php?p=sa&"><?= lngViewArchives ?> »</a>
        </p>
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