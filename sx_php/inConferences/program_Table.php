<?php

if (!is_array($arProgram)) {
    echo "<h1>Coming soon!</h1>";
} else {
    $iRows = count($arProgram);
    $sPlaceName = $arProgram[0]['PlaceName'];
    $sPlaceAddress = $arProgram[0]['PlaceAddress'];
    $sPlacePostalCode = $arProgram[0]['PlacePostalCode'];
    $sPlaceCity = $arProgram[0]['PlaceCity'];
    $sOrganizers = $arProgram[0]['Organizers'];

    $strCaptionTitle = "<h4>" . $arProgram[0]['Conference_StartDate'] . " | " . $arProgram[0]['Conference_EndDate'] . "</h4>
    <h1>" . $arProgram[0]['Conference_Title'] . "</h1>";

    $strCaptionSubTitle = "<p><b>" . lngPlace . "</b>: " . $sPlaceName . ", " .  $sPlaceAddress . ", " .  $sPlacePostalCode . " " .  $sPlaceCity . ". 
        <b>" . lngOrganizers . "</b>: " . $sOrganizers . "</p>";

    $strWebinarLink = "";
    if ($radio_LoggedParticipant) {
        if (!empty($str_WebinarURL)) {
            $strWebinarLink = '<p class="text_small"><b>Live Connection URL:</b> <a href="' . $str_WebinarURL . '">' . lngClickHere . '</a></p>';
        }
    }
?>
    <section>
        <table class="no_bg table_print">
            <caption>
                <div class="print_sole"><?php getTextPrinter("sx_PrintPage.php?confid=" . $int_ConferenceID, $int_ConferenceID) ?></div>
                <?= $strCaptionTitle . $strCaptionSubTitle . $strWebinarLink ?>

            </caption>
            <?php
            $loopID = -1;
            $loopSubID = -1;
            $bLoop1 = false;
            $bLoop2 = false;
            for ($r = 0; $r < $iRows; $r++) {
                $dSessionDate = $arProgram[$r]["Session_Date"];
                if (return_Is_Date($dSessionDate)) {
                    $sDateName = return_Week_Day_Name($dSessionDate) . " " . $dSessionDate;
                    $iDateID = intval(str_replace("-", "", $dSessionDate));
                }

                $iSessionID = $arProgram[$r]['SessionID'];
                if (return_Filter_Integer($iSessionID) == 0) {
                    $iSessionID = 0;
                }
                $sStartTime = return_Time_Minutes($arProgram[$r]['Session_StartTime']);
                $sEndTime = return_Time_Minutes($arProgram[$r]['Session_EndTime']);
                $sTitle = $arProgram[$r]['Session_Title'];
                $sModerator = $arProgram[$r]['Session_Moderator'];
                if (!empty($sModerator)) {
                    $sTitle .= ', <span>CHAIR: ' . $sModerator . '</span>';
                }
                $sBreak = $arProgram[$r]['Session_Break'];

                $iPaperID = $arProgram[$r]['PaperID'];
                $pStartTime = return_Time_Minutes($arProgram[$r]['Paper_StartTime']);
                $pEndTime = return_Time_Minutes($arProgram[$r]['Paper_EndTime']);
                $pTitle = $arProgram[$r]['Paper_Title'];
                $pAuthors = $arProgram[$r]['Paper_Authors'];
                $pSpeakers = $arProgram[$r]['Speakers'];
                $pBreak = $arProgram[$r]['Paper_Break'];
                if (!empty($pAuthors)) {
                    $pAuthors .= ", ";
                }
                if (!empty($pSpeakers)) {
                    $pAuthors .= $pSpeakers . ", ";
                }

                if (intval($loopID) != intval($iDateID)) {
                    if ($r > 0) {
                        echo "</tbody>";
                    } ?>
                    <thead>
                        <tr>
                            <th colspan="5" class="slide_up jqToggleNextTbody"><?= $sDateName ?></th>
                        </tr>
                    </thead>
                <?php
                    echo "<tbody>";
                }
                if (intval($iSessionID) > 0 && intval($loopSubID) != intval($iSessionID)) { ?>
                    <tr class="head">
                        <td class="bg"><span><?= $sStartTime ?></span></td>
                        <td class="bg"><span><?= $sEndTime ?></span></td>
                        <?php
                        if ($sBreak) { ?>
                            <td colspan="3"><span><?= $arProgram[$r]['Session_PlaceName'] ?></span> <?= $sTitle ?></td>
                        <?php
                        } else { ?>
                            <td colspan="3"><span><?= $arProgram[$r]['Session_PlaceName'] ?></span> <a href="conferences.php?sesid=<?= $iSessionID ?>"><?= $sTitle ?></a></td>
                        <?php
                        } ?>
                    </tr>
                <?php
                }
                if (!empty($iPaperID)) { ?>
                    <tr>
                        <td> </td>
                        <td> </td>
                        <td class="bg"><span><?= $pStartTime ?></span></td>
                        <td class="bg"><span><?= $pEndTime ?></span></td>
                        <?php
                        if ($pBreak) { ?>
                            <td style="width: 100%"><?= $pAuthors ?> <?= $pTitle ?></td>
                        <?php
                        } else { ?>
                            <td style="width: 100%"><?= $pAuthors ?> <a href="conferences.php?paperid=<?= $iPaperID ?>"><?= $pTitle ?></a></td>
                        <?php
                        } ?>
                    </tr>
            <?php
                }
                $loopID = $iDateID;
                $loopSubID = $iSessionID;
            }
            echo "</tbody>";
            ?>
        </table>
    </section>
<?php
}
?>