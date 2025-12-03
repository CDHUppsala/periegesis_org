<?php

?>
<section>
    <h1 class="head"><span><?= $strFAQFirstPageTitle ?></span></h1>
    <?php
    if (!empty($memoFAQNotes) && $int_SubjectID == 0) { ?>
        <div class="bg_grey"><?= $memoFAQNotes ?></div>
        <?php
    }

    $arr_Subjects = sx_getSubjects();

    if (!empty($arr_Subjects)) {
        for ($r = 0; $r < count($arr_Subjects); $r++) {
            $iSubjectID = $arr_Subjects[$r]["SubjectID"];
            $strSubjectName = $arr_Subjects[$r]["SubjectName"];
            $memoSubjectNotes = $arr_Subjects[$r]["SubjectNotes"];
        ?>
            <h2 class="head"><a href="faq.php?subjectid=<?= $iSubjectID ?>"><?= $strSubjectName ?></a></h2>
            <?php
            $strDisplay = 'style="display:block" ';
            if ($r == 0) {
                $strDisplay = 'style="display:block" ';
            } ?>
            <div class="text" <?= $strDisplay ?>><div class="text_max_width"><?= $memoSubjectNotes ?></div></div>
    <?php
        }
    } ?>
</section>