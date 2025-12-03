<?php
if (!isset($int_SessionID) || intval($int_SessionID) == 0) {
    header("Location: index.php");
    exit;
} else {
    $arrSession = sx_getSessions(0, $int_SessionID);
    if (is_array($arrSession)) {
        $sSessionTitle = $arrSession["SessionTitle"];
        $sSessionSubTitle = $arrSession["SessionSubTitle"];
        $sModerator = $arrSession["Moderator"];
        $dSessionDate = $arrSession["SessionDate"];
        $dSessionStartTime = return_Time_Minutes($arrSession["StartTime"]);
        $dSessionEndTime = return_Time_Minutes($arrSession["EndTime"]);
        $sSessionPlaceName = $arrSession["PlaceName"];
        $strImageLinks = $arrSession["ImageLinks"];
        $sSessionMediaURL = $arrSession["MediaURL"];
        $sSessionPDFAttachements = $arrSession["PDFAttachements"];
        $memoSessionNotes = $arrSession["Notes"];
        $arrSession = null; ?>

        <section id="jq_CopyPrint">
            <header>
                <h5><?= $str_ConferenceTitle ?></h5>
                <h1 class="head"><span><?= lngConferenceSession ?></span></h1>
                <h5>
                    <?php
                    echo return_Week_Day_Name($dSessionDate) . " " . $dSessionDate . "<br>" . $dSessionStartTime . " | " . $dSessionEndTime;
                    if (!empty($sSessionPlaceName)) {
                        echo "<br>" . lngPlace . ": " . $sSessionPlaceName;
                    }
                    ?>
                </h5>
                <?php
                if (!empty($sModerator)) {
                    echo "<h4>" . lngModerator . ": " . $sModerator . "</h4>";
                }

                if ($radio_LoggedParticipant) {
                    if (!empty($str_WebinarURL)) {
                        echo '<p><b>Live Connection URL:</b> <a href="' . $str_WebinarURL . '">' . lngClickHere . '</a></p>';
                    }
                }

                ?>
            </header>
            <article>
                <h2 class="head"><span><?= $sSessionTitle ?></span></h2>
                <?php
                if (!empty($sSessionSubTitle)) { ?>
                    <h3><?= $sSessionSubTitle ?></h3>
                <?php }

                $radioMediaLinks = false;
                if ($radio_ShowSocialMediaInText) {
                    $radioMediaLinks = true;
                }

                include PROJECT_PHP . "/basic_PrintIncludes.php";

                $radioShowAttachments = false;
                if ($radio_LoginToViewConferenceAttachments == false || $radio_LoggedParticipant) {
                    $radioShowAttachments = true;
                }

                if (!empty($sSessionMediaURL)) {
                    if (strpos($sSessionMediaURL, ";") > 0) {
                        get_Manual_Image_Cycler($sSessionMediaURL, "", "");
                    } elseif (empty(return_Media_Type_URL($sSessionMediaURL)) || $radioShowAttachments) {
                        get_Any_Media($sSessionMediaURL, "Center", "");
                    }
                }

                if (!empty($memoSessionNotes)) { ?>
                    <div class="text text_resizeable">
                        <div class="text_max_width"><?= $memoSessionNotes; ?></div>
                    </div>
                <?php
                }

                if (!empty($sSessionPDFAttachements && $radioShowAttachments)) { ?>
                    <h3><?= lngDownloadSessionFiles ?></h3>
                    <div class=" text">
                        <div class="text_max_width">
                            <?php echo sx_getLinksToPDF($sSessionPDFAttachements, false); ?>
                        </div>
                    </div>
                    <?php
                }

                $radioShowPapersInSession = false;
                if (!empty($int_ConferenceID) && (int) $int_ConferenceID > 0) {
                    $radioShowPapersInSession = return_Field_Value_From_Table('conferences', 'ShowPapersInSession', 'ConferenceID', $int_ConferenceID);
                }
                if ($radioShowPapersInSession) {
                    $arrPapers = sx_getPapers($int_SessionID, 0);
                    if (is_array($arrPapers)) { ?>
                        <h3 class="jq_PrintNext bg_grey slide_up jqToggleNextRight"><?= lgnSessionPapersAbstracts ?></h3>
                        <div style="overflow: hidden">

                            <?php
                            foreach ($arrPapers as $rs) {
                                $iPaperID = $rs['PaperID'];
                                $dPresentationDate = $rs['PresentationDate'];
                                $strStartTime = return_Time_Minutes($rs['StartTime']);
                                $strEndTime = return_Time_Minutes($rs['EndTime']);
                                $strPaperTitle = $rs['PaperTitle'];
                                $strPaperSubTitle = $rs['PaperSubTitle'];
                                $strPaperAuthors = $rs['PaperAuthors'];
                                $strSpeakers = $rs['Speakers'];
                                $memoAboutAuthors = $rs['AboutAuthors'];
                                $memoAbstract = $rs['Abstract'];
                            ?>
                                <h4 class="jq_PrintNext slide_left_down jqToggleNextLeft"><?= return_Week_Day_Name($dSessionDate) . " " . $dPresentationDate . " | " . $strStartTime . " | " . $strEndTime ?></h4>
                                <div class="text text_resizeable" style="display: none; overflow: hidden;;">
                                    <div class="text_max_width">
                                        <h4><a href="conferences.php?paperid=<?= $iPaperID ?>"><?= $strPaperTitle ?></a></h4>
                                        <h5>
                                            <?php
                                            if (!empty($strPaperSubTitle)) {
                                                echo $strPaperSubTitle;
                                                if (!empty($strPaperAuthors) || !empty($strSpeakers)) {
                                                    echo "<br><br>";
                                                }
                                            }
                                            if (!empty($strPaperAuthors)) {
                                                echo $strPaperAuthors;
                                            }
                                            if (!empty($strSpeakers)) {
                                                if (!empty($strPaperAuthors)) {
                                                    echo ", ";
                                                }
                                                echo $strSpeakers;
                                            }
                                            if (!empty($memoAbstract)) {
                                                echo "<br><br>" . lngAbstract;
                                            }
                                            ?>
                                        </h5>
                                        <?= $memoAbstract ?>
                                    </div>
                                </div>
                            <?php
                            } ?>
                        </div>
                <?php
                    }
                    $arrPapers = null;
                } ?>

            </article>
        </section>
        <section class="flex_between">
            <button class="button-grey button-gradient-border jq_CopyToClipboard" data-id="jq_CopyPrint">Copy to Clipboard as Text</button>
            <button class="button-grey button-gradient-border jq_PrintDivElement" data-id="jq_CopyPrint">Print as PDF</button>
        </section>
<?php
    }
} ?>