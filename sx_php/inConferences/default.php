<?php


function sx_showPastComingConferences($arrConference, $past = false)
{

    for ($c = 0; $c < count($arrConference); $c++) {
        $int_cid = $arrConference[$c]['ConferenceID'];

        $aResults = sx_getConferences($int_cid);

        if (is_array($aResults)) {
            $strTitle = $aResults['Title'];
            $strSubTitle = $aResults['SubTitle'];
            $strParticipationMode = $aResults['ParticipationMode'];

            $dateStartDate = $aResults['StartDate'];
            $dateEndDate = $aResults['EndDate'];

            $strPlaceName = $aResults['PlaceName'];
            $strPlaceAddress = $aResults['PlaceAddress'];
            $strPlacePostalCode = $aResults['PlacePostalCode'];
            $strPlaceCity = $aResults['PlaceCity'];
            $strOrganizers = $aResults['Organizers'];
            $strSponsors = $aResults['Sponsors'];
            $strContactPhone = $aResults['ContactPhone'];

            $strImageLinks = $aResults["ImageLinks"];
            $strMediaURL = $aResults['MediaURL'];
            $memoAbstract = $aResults['Abstract'];
            $radioHideSessionsInFirstPage = $aResults["HideSessionsInFirstPage"];

            /*
        $strPDFAttachements = $aResults['PDFAttachements'];
        $strConferenceWebinarURL = $aResults['ConferenceWebinarURL'];
        $strConferenceWebinarID = $aResults['ConferenceWebinarID'];
        $memoNotes = $aResults['Notes'];
        */
        } ?>

        <section class="conference_wrapper" aria-label="Conference Annousements">
            <?php
            if ($past) {
                echo '<h4 class="archives"><span>Last Event</span></h4>';
            } ?>
            <header>
                <h1 class="head"><span><?= $strTitle ?></span></h1>
                <?php
                if (!empty($strSubTitle)) { ?>
                    <h2><?= $strSubTitle ?></h2>
                <?php
                } ?>
                <h3>
                    <?php
                    if (!empty($dateStartDate)) {
                        echo $dateStartDate;
                    }
                    if (!empty($dateEndDate)) {
                        echo " | " . $dateEndDate;
                    } ?>
                </h3>
                <?php
                if (!empty($strSponsors)) {
                    echo "<p>" . lngWithTheKindSupport . ": " . $strSponsors . "</p>";
                } ?>

            </header>

            <?php
            /**
             * Images have priority in the first, default page
             */
            if (!empty($strImageLinks)) { ?>
                <figure>
                    <?php
                    if (strpos($strImageLinks, ";") > 0) {
                        get_Manual_Image_Cycler($strImageLinks, "", "");
                    } else {
                        get_Any_Media($strImageLinks, "Center", "");
                    } ?>
                </figure>
            <?php
            } elseif (!empty($strMediaURL)) { ?>
                <figure>
                    <?php
                    $strMsg = "";
                    if (strpos($strMediaURL, ";") > 0) {
                        $strMediaURL = explode(';', $strMediaURL)[0];
                        $strMsg = "More media files are available in conference description.";
                    }
                    get_Any_Media($strMediaURL, "Center", $strMsg);
                    ?>
                </figure>
            <?php
            } ?>

            <div class="text_bg align_center">
                <?php
                if (!empty($dateStartDate)) {
                    echo "<b>" . return_Week_Day_Name($dateStartDate) . "</b> " . $dateStartDate;
                }
                if (!empty($dateEndDate)) {
                    echo " | <b>" . return_Week_Day_Name($dateEndDate) . "</b> " . $dateEndDate;
                }
                if (!empty($strPlaceName)) {
                    echo "<br><b>" . lngPlace . ":</b> " . $strPlaceName;
                }
                if (!empty($strPlaceAddress)) {
                    echo ", " . $strPlaceAddress;
                }
                if (!empty($strPlacePostalCode)) {
                    echo ", " . $strPlacePostalCode;
                }
                if (!empty($strPlaceCity)) {
                    echo ", " . $strPlaceCity;
                }
                if (!empty($strContactPhone)) {
                    echo "<br><b>" . lngPhone . ":</b> " . $strContactPhone;
                }
                if (!empty($strOrganizers)) {
                    echo "<br><b>" . lngOrganizers . ":</b> " . $strOrganizers;
                } ?>
            </div>
            <?php
            if (!empty($memoAbstract)) { ?>
                <div class="text"><div class="text_max_width"><?php echo $memoAbstract ?></div></div>
                <?php
            }

            if ($radioHideSessionsInFirstPage == false) {
                $arrSessions = sx_getSessions($int_cid, 0);
                if (is_array($arrSessions)) { ?>
                    <h2 class="head"><span><?= lgnConferenceSessions ?></span></h2>
                    <?php
                    $z = 0;
                    $Loop = false;
                    $dLastDate = "";
                    foreach ($arrSessions as $row) {
                        $iSessionID = $row["SessionID"];
                        $sSessionTitle = $row["SessionTitle"];
                        $dSessionDate = $row["SessionDate"];
                        $dSessionStartTime = return_Time_Minutes($row["StartTime"]);
                        $dSessionEndTime = return_Time_Minutes($row["EndTime"]);
                        $bBreak = $row['Break'];
                        $sModerator = $row['Moderator'];
                        if (!empty($sModerator)) {
                            $sSessionTitle .= ', <span>CHAIR: ' . $sModerator . '</span>';
                        }


                        if ($Loop && $dLastDate != $dSessionDate) {
                            $z = 0;
                        }
                        if ($z == 0) {
                            if ($Loop) {
                                echo "</div>";
                            }
                            echo '<div class="flex_between">';
                            $Loop = true;
                        } ?>
                        <div class="flex_item">
                            <h5><?= return_Week_Day_Name($dSessionDate) . " " . $dSessionDate . "<br>" . $dSessionStartTime . " | " . $dSessionEndTime ?></h5>
                            <div class="text_xsmall">
                                <?php if ($bBreak) { ?>
                                    <span><?= $sSessionTitle ?></span>
                                <?php
                                } else { ?>
                                    <a href="conferences.php?sesid=<?= $iSessionID ?>"><?= $sSessionTitle ?></a>
                                <?php
                                } ?>
                            </div>
                        </div>
            <?php
                        $z++;
                        $dLastDate = $dSessionDate;
                    }
                    echo '</div>';
                }
                $arrSessions = null;
            }
            ?>
            <p class="align_center">
                <a href="conferences.php?confid=<?= $int_cid ?>" class="button button-shadow button-arrow"><?= lngReadMore ?></a>
                <a href="conferences.php?program=yes&confid=<?= $int_cid ?>" class="button button-shadow"><span><?= lngConferenceProgram ?></span></a>
                <?php
                if ($strParticipationMode != 'None') { ?>
                    <a href="conferences_login.php" class="button button-shadow button-shadow-border"><span><?= lngLogin ?></span></a>
                    <?php
                    if (!$past) { ?>
                        <a href="conferences_login.php?pg=join" class="button button-shadow button-shadow-white"><span><?= lngRegister ?></span></a>
                    <?php
                    }
                } else { ?>
                    <a href="javascript:void(0)" class="button button-shadow button-shadow-white"><span>Registration Not Available</span></a>
                <?php
                } ?>
            </p>
        </section>
<?php
    }
    $aResults = null;
}

/**
 * Coming Conferences
 */
$arrConference = sx_getPastComingConferences(false);

if (is_array($arrConference)) {
    sx_showPastComingConferences($arrConference);
}

/**
 * Previous Conferences
 */
$arrConference = sx_getPastComingConferences(true, true);
if (is_array($arrConference)) {
    sx_showPastComingConferences($arrConference, true);
} ?>