<section>
    <aside class="print print_absolute">
        <?php
        getTextResizer();
        getTextPrinter("sx_PrintPage.php?subjectid=" . $int_SubjectID, $int_SubjectID)
        ?>
    </aside>
    <h1 class="head align_center"><span><?= $str_SubjectName ?></span></h1>
    <div class="text"><?= $str_SubjectNotes ?></div>
    <dl class="accordion jqAccordion">
        <?php
        if (is_array($arr_Answers)) {
            $rows = count($arr_Answers);
            for ($row = 0; $row < $rows; $row++) {
                $radioTemp = true;
                $int_SubjectID = $arr_Answers[$row]["SubjectID"];
                $strQuestion = $arr_Answers[$row]["Question"];
                $strSubQuestion = $arr_Answers[$row]["SubQuestion"];
                $dateInsertDate = $arr_Answers[$row]["InsertDate"];
                $strMediaURL = $arr_Answers[$row]["MediaURL"];
                $strMediaPlace = $arr_Answers[$row]["MediaPlace"];
                $strMediaNotes = $arr_Answers[$row]["MediaNotes"];
                $strFilesForDownload = $arr_Answers[$row]["FilesForDownload"];
                $memoAnswerText = $arr_Answers[$row]["AnswerText"];
                if ($row == 0) { ?>

                <?php }
                $slideClass = "";
                $displayMode = "none";
                if ($row == 0) {
                    $slideClass = "selected";
                    $displayMode = "block";
                }
                ?>
                <dt class="<?= $slideClass ?>"><?= $strQuestion ?></dt>
                <dd style="display: <?= $displayMode ?>">
                    <?php
                    if (!empty($strSubQuestion)) { ?>
                        <h3><?= $strSubQuestion ?></h3>
                    <?php }

                    if (!empty($strMediaURL)) {
                        if (strpos($strMediaURL, ";") > 0) {
                            if ($strMediaPlace === 'Center') {
                                get_Manual_Image_Cycler($strMediaURL, "", $strMediaNotes);
                            } elseif ($strMediaPlace === 'Right' || $strMediaPlace === 'Left') {
                                get_Right_Images($strMediaURL, $strMediaNotes, $strMediaPlace);
                            }
                        } else {
                            get_Any_Media($strMediaURL, $strMediaPlace, $strMediaNotes);
                        }
                    } ?>
                    <div class="text text_resizeable">
                        <div class="text_max_width">
                            <?php
                            echo $memoAnswerText;
                            if (!empty($strFilesForDownload)) {
                                sx_getDownloadableFiles($strFilesForDownload);
                            } ?>
                        </div>
                    </div>
                </dd>
        <?php
            }
        }
        $arr_Answers = null;
        ?>
    </dl>
</section>