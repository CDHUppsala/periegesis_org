<?php

/**
 * Check if participent is loged in
 */

if ($radio_LoggedParticipant == false || (int) $int_ParticipantID == 0) {
    header('Location: index.php');
    exit();
}

/**
 * Check information comes from basic_queries.php
 * Loggin Participants can be allowd to send Abstract independently of Contributor Rights
 * Check if they are registered in a conferences and if that conference allows sending abstracts
 */
if ($radio_RightsToSendAbstracts == false || $int_RightsConferenceID == 0) {
    header("Location: index.php");
    exit();
}

/**
 * Check if abstract exists for this participant and conference
 */
function sx_getAbstract($partID, $confID)
{
    $conn = dbconn();
    $sql = "SELECT AbstractID, Title, SubTitle, Coauthors, Abstract
	FROM conf_abstracts 
	WHERE ParticipantID = ? AND ConferenceID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$partID, $confID]);
    $rs = $stmt->fetch(PDO::FETCH_NUM);
    if ($rs) {
        return $rs;
    } else {
        return false;
    }
}

$radio_UpdateAbstract = false;
$int_AbstractID = 0;

$arrAbstract = sx_getAbstract($int_ParticipantID, $int_RightsConferenceID);
if (is_array($arrAbstract)) {
    $radio_UpdateAbstract = true;
    $int_AbstractID = $arrAbstract[0];
}

$arrError = array();
$strSuccess = null;
$radioContinue = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $radioContinue = true;

    $intAbstractID = filter_var(trim($_POST["AbstractID"]), FILTER_SANITIZE_NUMBER_INT);
    $strTitle  = sx_Sanitize_Input_Text($_POST["Title"]);
    $strSubTitle  = sx_Sanitize_Input_Text($_POST["SubTitle"]);
    $strCoauthors  = sx_Sanitize_Input_Text($_POST["Coauthors"]);
    $memAbstract  = sx_Sanitize_Text_Area($_POST["Abstract"]);

    if (intval($intAbstractID) > 0) {
        $intAbstractID = (int)$intAbstractID;
    } else {
        $intAbstractID = 0;
    }

    /**
     * If IDs are not equal, exit 
     */
    if ($int_AbstractID != $intAbstractID) {
        header('Location: index.php');
        exit();
    }

    if (strlen($strTitle) > 255) {
        $radioContinue = false;
        $arrError[] = "The Title text is too long (" . strlen($strTitle) . ")!";
    }
    if (strlen($strSubTitle) > 255) {
        $radioContinue = false;
        $arrError[] = "The Subtitle text is too long (" . strlen($strSubTitle) . ")!";
    }
    if (strlen($strCoauthors) > 255) {
        $radioContinue = false;
        $arrError[] = "The Coauthors text is too long (" . strlen($strCoauthors) . ")!";
    }
    if (strlen($memAbstract) > 2800) {
        $radioContinue = false;
        $arrError[] = "The Abstract text is too long (" . strlen($memAbstract) . ")!";
    }

    if ($radioContinue) {
        if ($intAbstractID > 0 && $radio_UpdateAbstract) {
            $sql = "UPDATE conf_abstracts SET Title = ?, SubTitle = ?, Coauthors = ?, Abstract = ?
			WHERE AbstractID = ? AND ParticipantID = ? AND ConferenceID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$strTitle, $strSubTitle, $strCoauthors, $memAbstract, $intAbstractID, $int_ParticipantID, $int_RightsConferenceID]);
        } else {
            $sql = "INSERT INTO conf_abstracts (
				ConferenceID, ParticipantID, Title, SubTitle, Coauthors, Abstract)
				VALUES( ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$int_RightsConferenceID, $int_ParticipantID, $strTitle, $strSubTitle, $strCoauthors, $memAbstract]);
            $stmt = null;
        }
        $strSuccess = "The Abstract has been successfully saved!";
        // Repeat the request to get the last updates
        $arrAbstract = sx_getAbstract($int_ParticipantID, $int_RightsConferenceID);
    }
}

$intAbstractID = 0;
$strTitle = "";
$strSubTitle = "";
$strCoauthors = "";
$mem_Abstract = "";
$memAbstract = "";

if (is_array(($arrAbstract))) {
    $intAbstractID = $arrAbstract[0];
    $strTitle = $arrAbstract[1];
    $strSubTitle = $arrAbstract[2];
    $strCoauthors = $arrAbstract[3];
    $mem_Abstract = $arrAbstract[4];
    if (!empty($mem_Abstract)) {
        $memAbstract = str_replace("<p>", "", $mem_Abstract);
        $memAbstract = str_replace("</p>", "\r\n\n", $memAbstract);
    }
}
$arrAbstract = null;
?>

<h1 class="head"><span><?php echo lngSendEditPaperAbstract ?></span></h1>

<h3>Active Conference: <i><?= $str_RightsConferenceTitle ?></i></h3>
<p>Please, contact the administration if the conference is not the expected one!</p>
<div class="text text_bg">
    <div class="text_max_width">
        <p>
            You can add only one abstract per conference and only for the next coming conference for which you are registered.
            If an abstract already exists, it will be updated. So, you can save and then continue editing your abstact.
        </p>
        <p>
            Please notice that the information you enter here must first be processed by the administration
            of the site to be visible on the website.
        </p>
    </div>
</div>

<?php
if (!empty($arrError)) { ?>
    <div class="bg_error"><?= implode("<br>", $arrError) ?></div>
<?php
}
if (!empty($strSuccess)) { ?>
    <div class="bg_success"><?= $strSuccess ?></div>
<?php
} ?>
<form method="POST" name="AddAbstract" action="<?= sx_PATH ?>?pg=abstract">
    <input type="hidden" name="AbstractID" value="<?= $intAbstractID ?>">
    <fieldset>
        <label><b>Title:</b><br>
            <input type="text" name="Title" value="<?= $strTitle ?>" size="58"></label>
        <label><b>Sub Title:</b><br>
            <input type="text" name="SubTitle" value="<?= $strSubTitle ?>" size="58"></label>
        <label><b>Coauthors:</b><br>
            <input type="text" name="Coauthors" value="<?= $strCoauthors ?>" size="58"></label>
    </fieldset>
    <fieldset>
        <label><b>Abstract:</b> <input name="entered" disabled type="text" size="4"> <span>of Max 2500 characters</span><br>
            <textarea spellcheck id="Abstract" name="Abstract" rows="8" onFocus="countEntries('AddAbstract','Abstract',2500);"><?= $memAbstract ?></textarea></label>
        <p class="text_xsmall"><?= LNG_Form_WritePureText ?></p>
    </fieldset>
    <fieldset>
        <input type="submit" name="SubmitAbstract" value="Submit The Abstract">
    </fieldset>
</form>
<?php
if (!empty($mem_Abstract)) { ?>
    <h3>The Abstract Saved in HTML</h3>
    <div class="text text_small"><div class="text_max_width"><?= $mem_Abstract ?></div></div>
<?php
} ?>