<?php
if(!isset($arr_Subjects) || !is_array($arr_Subjects)) {
    $arr_Subjects = sx_getSubjects();
}

if (is_array($arr_Subjects)) {
    $strNavPath = "faq.php?";
    if (empty($strSubjectReportsTitle)) {
        $strSubjectReportsTitle = "Frequently Askes Questions";
    } ?>
    <section class="jqNavMainToBeCloned">
        <h2 class="head slide_up jqToggleNextRight"><span><?= $strFAQNavigationTitle ?></span></h2>
        <div class="sxAccordionNav">
            <ul>
                <?php
                $iRows = count($arr_Subjects);
                for ($r = 0; $r < $iRows; $r++) {
                    $intSubjectID = $arr_Subjects[$r]['SubjectID'];
                    $strSubjectName = $arr_Subjects[$r]['SubjectName'];
                    $strClass = "";
                    if ($intSubjectID == $int_SubjectID) {
                        $strClass = 'class="open" ';
                    } ?>
                    <li><a <?= $strClass ?>href="<?= $strNavPath ?>subjectid=<?= $intSubjectID ?>"><?= $strSubjectName ?></a></li>
                <?php
                } ?>
            </ul>
        </div>
    </section>
<?php
}
$arr_Subjects = null;


?>