<?php

$sql = "SELECT QuizThemeID, QuizTheme 
    FROM quiz_themes 
    WHERE ShowInArchive = True
        AND ShowOnTop = false
    ORDER BY StartDate DESC";
$stmt = $conn->query($sql);
if ($stmt->rowCount() > 0) {
    if (!isset($str_QuizTitle)) {
        $str_QuizTitle = lngQuiz;
    } ?>
    <section>
        <h2 class="head"><span><?= lngArchive . ": " . $str_QuizTitle  ?></span></h2>
        <div class="vote_wrapper max_height">
            <p>
                <?php
                while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <a href="surveys.php?p=qr&quizThemeID=<?= $rs["QuizThemeID"] ?>">
                        » <?= $rs["QuizTheme"] ?></a><br>
                <?php
                } ?>
            </p>
        </div>
    </section>
<?php
}
$stmt = null;
?>