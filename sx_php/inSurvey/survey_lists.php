<?php
$sql = "SELECT SurveyID, SurveyTheme 
    FROM surveys 
    WHERE ShowInSite = True 
    AND (StartDate <= '" . date('Y-m-d') . "' OR StartDate Is Null) 
    AND (EndDate >= '" . date('Y-m-d') . "' OR EndDate Is Null) 
    ORDER BY InsertDate desc";
$stmt = $conn->query($sql);
if ($stmt->rowCount() > 0) {
    if (strlen($str_SurveyTittle) == 0) {
        $str_SurveyTittle = lngSurvey;
    } ?>
    <section>
        <h2 class="head"><span><?= $str_SurveyTittle ?></span></h2>
        <?php
        while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
            <div class="vote_wrapper">
                <h4><?= $rs["SurveyTheme"] ?></h4>
                <p>
                    <a href="surveys.php?p=s&surveyID=<?= $rs["SurveyID"] ?>"> » <?= lngAnswerSurveyQuesttions ?></a><br>
                    <a href="surveys.php?p=sr&surveyID=<?= $rs["SurveyID"] ?>"> » <?= lngViewResults ?></a><br>
                </p>
            </div>
        <?php
        } ?>
        <div class="vote_wrapper">
            <a href="surveys.php?p=sa"> » <?= $str_SurveyTittle . ": " . lngViewArchives ?></a>
        </div>
    </section>
<?php
}
$stmt = null;
?>