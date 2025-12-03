<?php
/**
 * Show only actual themes:
 * - by Show On Top
 * - and optionally by date 
 */
$sql = "SELECT QuizThemeID, QuizTheme 
    FROM quiz_themes 
    WHERE ShowOnTop = True 
        AND (StartDate <= '" . date('Y-m-d') . "' OR StartDate Is Null) 
        AND (EndDate >= '" . date('Y-m-d') . "' OR EndDate Is Null) 
    ORDER BY StartDate DESC";
$stmt = $conn->query($sql);
if ($stmt->rowCount() > 0) {
    if (!isset($str_QuizTitle)) {
        $str_QuizTitle = lngQuiz;
    } ?>
    <section>
        <h2 class="head"><span><?= $str_QuizTitle  ?></span></h2>
        <div class="vote_wrapper">
            <p>
                <?php
                while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <a href="surveys.php?p=q&quizThemeID=<?= $rs["QuizThemeID"] ?>"> » <?= $rs["QuizTheme"] ?></a>
                    [<a href="surveys.php?p=qr&quizThemeID=<?= $rs["QuizThemeID"] ?>"><span><?= lngViewResults ?></span></a>]<br>
                <?php
                }  ?>
            </p>
        </div>
    </section>
<?php
}
$stmt = null;
?>