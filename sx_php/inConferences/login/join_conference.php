<?php

if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
    header('Location: index.php');
    exit();
}

function sx_checkParticipantExists($cid, $pid)
{
    $conn = dbconn();
    $sql = "SELECT AsksToUploadFiles  
	FROM conf_to_participants 
	WHERE ConferenceID = ?
	AND ParticipantID = ? ";
    $stmtf = $conn->prepare($sql);
    $stmtf->execute([$cid, $pid]);
    $rsf = $stmtf->fetch(PDO::FETCH_NUM);
    if ($rsf) {
        return true;
    } else {
        return false;
    }
}

$strSubmitMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /**
     * 1. Get all comming conferences 
     * 2. Check if the array of selected conferences for registration is EMPTY or not
     * A.	If the array is NOT empty:
     * 			Check if a comming conferenc exist in the array of selected confeences,
     * 				inform the database and send emails
     * 			Check also, if a comming conference does not exist in the array,
     * 				if the member is trying to CANCELLED a previous registration
     * 				inform the database (don't send email for cancellation)
     * B.	If the array of selected conferences is empty:
     * 			Check if the member is trying to CANCELLED a previous registration,
     * 			inform the database (don't send email for cancellation)
     */
    $arrComing = null;
    $sql = "SELECT ConferenceID, Title, SubTitle,
		        StartDate,
        		EndDate,
	        	PlaceName,
		        PlaceAddress,
    		    PlacePostalCode,
	    	    PlaceCity,
				ConferenceWebinarURL
			FROM conferences 
			WHERE Hidden = 0 
			AND EndDate >= ? 
			ORDER BY EndDate ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([date("Y-m-d")]);
    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (($rs)) {
        $arrComing = $rs;
    }
    $stmt = null;
    $rs = null;

    /**
     * An array with coming conferences that are checked by the participant for registration
     */
    $arr_Confs = '';
    if (isset($_POST["ConferenceID_Array"]) && is_array($_POST["ConferenceID_Array"])) {
        $arr_Confs = $_POST["ConferenceID_Array"];
    }
    /*
	*
	 * Check if ask for right to upload files are selected for a conference
	 * The ask contains the ID of the conference as value
	 */
    $iRightsForConferenceID = 0;
    if (isset($_POST["AskForRights"])) {
        $iRightsForConferenceID = intval($_POST["AskForRights"]);
    }

    /**
     * If there are at least one selected conference for registration
     */
    if (is_array($arr_Confs) && is_array($arrComing)) {

        /**
         * Loop through comming conferences,
         * check if they exist in the selected array
         * if a conference exists, inform the database and send emails
         */

        $strParticipantEmail = return_Field_Value_From_Table('conf_participants', 'Email', 'ParticipantID', $int_ParticipantID);

        $iRows = count($arrComing);
        for ($r = 0; $r < $iRows; $r++) {
            $iConferenceID = $arrComing[$r]['ConferenceID'];
            $sTitle = $arrComing[$r]['Title'];
            $sSubTitle = $arrComing[$r]['SubTitle'];
            $sStartDate = $arrComing[$r]['StartDate'];
            $sEndDate = $arrComing[$r]['EndDate'];
            $sPlaceName = $arrComing[$r]['PlaceName'];
            $sPlaceAddress = $arrComing[$r]['PlaceAddress'];
            $sPlacePostalCode = $arrComing[$r]['PlacePostalCode'];
            $sPlaceCity = $arrComing[$r]['PlaceCity'];
            $sConferenceWebinarURL = $arrComing[$r]['ConferenceWebinarURL'];

            $radioAskForRights = 0;

            /**
             * Check if the member is already registered in current conference
             * A.	Used to Update or inster in the databse
             * B.	Used to check Cancelling of previous registration:
             * 		If the member is registered in a conference that not exist in the array of selected conferences 
             * 		the member is trying to CANCEL the registration
             */
            $radioParticipantExists = sx_checkParticipantExists($iConferenceID, $int_ParticipantID);


            // Check if a coming conference has ben selected
            if (array_key_exists($iConferenceID, $arr_Confs)) {

                /**
                 * Chcek ask for rights
                 */
                if ($iConferenceID == $iRightsForConferenceID) {
                    $radioAskForRights = 1;
                }

                /**
                 * Check participation mode
                 */
                $arrModes = ['Live', 'Online'];
                $strPartMode = "Live";
                if ($_POST["ModeOptions_" . $iConferenceID] == 'Both') {
                    $strPartMode = $_POST["Mode_" . $iConferenceID];
                } elseif ($_POST["ModeOptions_" . $iConferenceID] == 'Online') {
                    $strPartMode = "Online";
                }

                /**
                 * Check the participation mode aggainst a whitelist, just in case...
                 * ... show no mercy
                 */
                if (!in_array($strPartMode, $arrModes)) {
                    session_destroy();
                    header('Location: index.php');
                    exit();
                }

                /**
                 * Update or Insert into the Conference To Participant Table
                 */
                if ($radioParticipantExists) {
                    $sql = "UPDATE conf_to_participants SET
						ParticipationMode = ?,
						AsksToUploadFiles = ?,
						Cancelled = ?
					WHERE ConferenceID = ? AND ParticipantID = ? ";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$strPartMode, $radioAskForRights, 0, $iConferenceID, $int_ParticipantID]);
                } else {
                    $sql = "INSERT INTO conf_to_participants
						(ConferenceID, ParticipantID, ParticipationMode, AsksToUploadFiles,InsertDate)
					VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$iConferenceID, $int_ParticipantID, $strPartMode, $radioAskForRights, date('Y-m-d')]);
                }
                /**
                 * Send mail to all registered participents with a zoom link if particiapation mode is Online
                 */
                /**
                 * The include file bellow contains the Mail Template, which is hold in the variable:
                 * 		$sx_mail_body
                 * Constant and global variables used in the template and by the mail sender:
                 * 		str_SiteTitle
                 * 		str_SiteEmail
                 * 		str_SiteInfo
                 * 		LNG_Mail_SendingFromSite
                 * 		Link to logotype: sx_ROOT_HOST . '"/images/"' . $str_LogoImageEmail
                 * 		Link to home page: sx_ROOT_HOST . '/' . sx_CurrentLanguage . '/index.php
                 * Variables to be defined here:
                 * 		$sx_send_to_email: The mail of the reciever
                 * 		$sx_mail_subject: The subject of mail
                 * 		$sx_mail_content: whatever
                 */

                if (!empty($strParticipantEmail) && filter_var($strParticipantEmail, FILTER_VALIDATE_EMAIL)) {
                    $sx_send_to_email = $strParticipantEmail;
                    $sx_mail_subject = 'Registration for a Conference or Workshop';

                    /**
                     * =======================================
                     *  START CONTENT
                     * =======================================
                     */

                    // Information about the registration and link to conference
                    $strLinkPage = sx_ROOT_HOST . '/' . sx_CurrentLanguage . '/conferences.php?confid=' . $iConferenceID;
                    $sx_mail_content = '<h4>Thank You for the Registration</h4>';
                    $sx_mail_content .= '<p>You are registered for participation in the following conference or workshop:</p>';
                    $sx_mail_content .= '<p><strong><a style="text-decoration: none; color: #0B4CB8;" target="_blank" href="' . $strLinkPage . '">' . $sTitle . '</a>.</strong></p>';

                    // Start of common paragraph
                    $sx_mail_content .= '<p>';

                    // Date
                    $str_DatePeriod = $sStartDate;
                    if (!empty($sEndDate) && strval($sEndDate) != strval($sStartDate)) {
                        $str_DatePeriod .= ' To ' . $sEndDate;
                    }
                    if (!empty($str_DatePeriod)) {
                        $sx_mail_content .= '<b>' . lngDate . ':</b> ' . $str_DatePeriod . '<br>';
                    }

                    // Place
                    $str_Place = '<b>' . lngPlace . '</b>: ';
                    if (!empty($sPlaceName)) {
                        $str_Place .= $sPlaceName;
                    }
                    if (!empty($sPlaceAddress)) {
                        $str_Place .= ', ' . $sPlaceAddress;
                    }
                    if (!empty($sPlacePostalCode)) {
                        $str_Place .= ', ' . $sPlacePostalCode;
                    }
                    if (!empty($sPlaceCity)) {
                        $str_Place .= ', ' . $sPlaceCity;
                    }
                    if (!empty($str_Place)) {
                        $sx_mail_content .=  $str_Place . '<br>';
                    }

                    // Participation Mode
                    $str__Mode = $strPartMode;
                    if ($strPartMode == "Live") {
                        $str__Mode = "Live Presence";
                    }
                    $sx_mail_content .= '<b>' . lngParticipationMode . ':</b> ' . $str__Mode;

                    // End of common paragraph
                    $sx_mail_content .= '</p>';

                    if ($radioAskForRights) {
                        $sx_mail_content .= '<p>If <b>rights to upload files</b> will be conferred by the organizers of the conference,
							you will see an extra menu on the right column after your login in the site.';
                    }

                    /**
                     * =======================================
                     *  END OF CONTENT
                     * =======================================
                     */

                    // Link for online participation
                    if (!empty($sConferenceWebinarURL) && $strPartMode == "Online") {
                        $strLink = '<a style="text-decoration: none; color: #0B4CB8;" target="_blank" href="' . $sConferenceWebinarURL . '">Link to access the event</a>';
                        $sx_mail_content .= '<p>To <b>access</b> the conference or workshop please use the following link, 
								which will open about 10 minutes before the start time: ' . $strLink . '</p>';
                    }

                    // Send the mail
                    include dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";
                }
            } elseif ($radioParticipantExists) {
                /**
                 * The Participant is registered in a conference that has not been checked
                 * So, cancel registration to that conference
                 */
                $sql = "UPDATE conf_to_participants SET
					AsksToUploadFiles = 0,
					Cancelled = 1
				WHERE ConferenceID = ? AND ParticipantID = ? ";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$iConferenceID, $int_ParticipantID]);
            }
        }
    } elseif (is_array($arrComing)) {
        /**
         * No conference has been selected,
         * Check any way if the member is trying to CANCEL previous registration
         */
        $iRows = count($arrComing);
        for ($r = 0; $r < $iRows; $r++) {
            $iConferenceID = $arrComing[$r]['ConferenceID'];
            $radioParticipantExists = sx_checkParticipantExists($iConferenceID, $int_ParticipantID);
            /**
             * If no conference is checked, cancel existing registration in all future conferences 
             */
            if ($radioParticipantExists) {
                $sql = "UPDATE conf_to_participants SET
					AsksToUploadFiles = 0,
					Cancelled = 1
				WHERE ConferenceID = ? AND ParticipantID = ? ";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$iConferenceID, $int_ParticipantID]);
            }
        }
    }
    $arrComing = null;
    $strSubmitMsg = "Requested updates have been successfully pursued";
}

/**
 * Get information about coming conferences
 * and the current participant's relation to them
 */

$aResults = null;
$sql = "SELECT ctp.ConferenceID,
		ctp.ParticipationMode,
		ctp.AsksToUploadFiles
	FROM conf_to_participants AS ctp
	INNER JOIN conferences AS c ON ctp.ConferenceID = c.ConferenceID 
	WHERE ctp.Cancelled = 0
	AND ctp.ParticipantID = ?
	AND c.EndDate >= ? ";
$stmt = $conn->prepare($sql);
$stmt->execute([$int_ParticipantID, date("Y-m-d")]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (($rs)) {
    $aResults = $rs;
}
$stmt = null;
$rs = null;

$bResults = null;
$sql = "SELECT ConferenceID, Title, StartDate, EndDate, PlaceName, 
			ParticipationMode, AllowAskToUploadFiles, ConferenceWebinarURL
		FROM conferences 
		WHERE Hidden = 0 
		AND EndDate >= ? 
		ORDER BY EndDate ASC ";
$stmt = $conn->prepare($sql);
$stmt->execute([date("Y-m-d")]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (($rs)) {
    $bResults = $rs;
}
$stmt = null;
$rs = null;
?>

<h1 class="head"><span><?php echo lngRegisterForConferences ?></span></h1>
<?php if ($strSubmitMsg != "") { ?>
    <div class="bg_success"><?= $strSubmitMsg ?></div>
<?php } ?>

<h2>Select Conference:</h2>
<?php
if (!is_array($bResults)) { ?>
    <p class="text">There is no announcement for a New Conference.</p>
<?php

} else { ?>

    <form name="RegisterForConference" action="<?= sx_PATH ?>?pg=conference" method="post">
        <?php
        $iRows = count($bResults);
        $radioAskForRights = false;
        $radioAllowRights = false;
        for ($r = 0; $r < $iRows; $r++) {
            $strCheckedConference = "";
            $strCheckLive = " checked";
            $strCheckOnline = "";
            $strCheckAsk = "";

            $intConferenceID = (int) $bResults[$r]['ConferenceID'];
            $strTitle = $bResults[$r]['Title'];
            $dStartDate = $bResults[$r]['StartDate'];
            $dEndDate = $bResults[$r]['EndDate'];
            $sPlaceName = $bResults[$r]['PlaceName'];
            $strParticipationMode = $bResults[$r]['ParticipationMode'];
            $radioAllowAskToUploadFiles = $bResults[$r]['AllowAskToUploadFiles'];

            $radioAsk = false;
            if ($radio_UseFileApload && $radioAllowAskToUploadFiles) {
                $radioAsk = true;
            }
            if ($radioAsk) {
                $radioAllowRights = true;
            }

            if (is_array($aResults)) {
                foreach ($aResults as $record) {
                    if ($record['ConferenceID'] == $intConferenceID) {
                        $strCheckedConference = "checked";
                        $mode = $record['ParticipationMode'];
                        if ($strParticipationMode == 'Both' && !empty($mode)) {
                            if ($mode == "Online") {
                                $strCheckOnline = " checked";
                                $strCheckLive = "";
                            }
                        }
                        if ($record['AsksToUploadFiles']) {
                            $strCheckAsk = "checked";
                            $radioAskForRights = true;
                        }
                        break;
                    }
                }
            } ?>
            <fieldset>
                <div class="flex_between flex_align_start">
                    <label class="flex_items">
                        <div class="flex_start">
                            <?php
                            if ($strParticipationMode != "None") { ?>
                                <input type="checkbox" name="ConferenceID_Array[<?= $intConferenceID ?>]" value="Yes" <?= $strCheckedConference ?> />
                            <?php
                            } ?>
                            <h3><?= $strTitle ?></h3>
                        </div>
                        <div><b><?= lngDate ?>:</b> <?= $dStartDate . " " . lngTo . " " . $dEndDate ?></div>
                        <div><b><?= lngPlace ?>:</b> <?= $sPlaceName ?></div>
                        <?php
                        if ($strParticipationMode == "None") { ?>
                            <p><strong>Online registration in this site is not available for this conference</strong><br />
                                Please see the conference announcement for participation conditions or contact its organizers.</p>
                        <?php
                        } else { ?>
                            <p><b>Participation Mode:</b><br>
                                <?php
                                $strMode = 'Live';
                                if ($strParticipationMode == 'Both') {
                                    $strMode = 'Both';
                                ?>
                                    Live Presence: <input type="radio" name="Mode_<?= $intConferenceID ?>" value="Live" <?= $strCheckLive ?>>
                                    Online: <input type="radio" name="Mode_<?= $intConferenceID ?>" value="Online" <?= $strCheckOnline ?>>
                                <?php
                                } elseif ($strParticipationMode == 'Online') {
                                    $strMode = 'Online';
                                ?>
                                    Online: <input type="radio" name="ReadOnline" value="Online" checked readonly>
                                <?php
                                } else { ?>
                                    Live Presence: <input type="radio" name="ReadLive" value="Live" checked readonly>
                                <?php
                                } ?>
                                <input type="hidden" name="ModeOptions_<?= $intConferenceID ?>" value="<?= $strMode ?>">
                            </p>
                        <?php
                        } ?>
                    </label>
                    <?php
                    if ($radioAsk) { ?>
                        <label class="flex_items align_right">
                            Give me Uppload Rights:
                            <input type="radio" name="AskForRights" value="<?= $intConferenceID ?>" <?= $strCheckAsk ?>>
                        </label>
                    <?php
                    } ?>
                </div>
                <?php
                if ($strCheckedConference) {
                    echo '<p class="text_small bg_info">You are already registered for this Conference! If you want to deregistrate or change your upload rights, 
                    make your changes and click on Update</p>';
                } ?>

            </fieldset>
        <?php
        }
        if ($radioAllowRights) {
            $strCheckAsk = "";
            if ($radioAskForRights == false) {
                $strCheckAsk = " checked";
            } ?>
            <fieldset class="align_right">
                <label>No Upload Rights: <input type="radio" name="AskForRights" value="0" <?= $strCheckAsk ?> /></label>
            </fieldset>
        <?php
        }
        if ($radio_UseFileApload) { ?>
            <fieldset>
                <div class="text_small"><?= $memo_FileAploadNotes  ?></div>
            </fieldset>
        <?php
        } ?>
        <fieldset>
            <input type="Submit" name="EditAction" value="<?= lngUpdate ?>">
        </fieldset>
    </form>
<?php
}
$bResults = null;
$aResults = null;
?>