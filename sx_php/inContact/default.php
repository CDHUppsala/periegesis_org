<?php
include __DIR__ . "/form_validation.php";

function sx_GetSubOfficeSetup()
{
    $conn = dbconn();
    $sql = "SELECT SiteID, SiteTitle, SiteAddress, SitePostalCode, SiteCity, SiteCountry,
		SitePhone, SiteMobile, SiteFax, SiteEmail, OfficeHours, PhoneHours,
		UseMap, MapLatitude, MapLongitude, GoogleFrameMapSource
	FROM site_setup 
	WHERE SubOffice = 1 " . str_LanguageAnd . "
	ORDER BY SiteID ASC ";
    $smtp = $conn->prepare($sql);
    $smtp->execute();
    $rs = $smtp->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        return  $rs;
    } else {
        return "";
    }
}
$sql = "SELECT ContactTitle, ContactMessage 
	FROM site_setup 
	WHERE SubOffice = False " . str_LanguageAnd . "
	ORDER BY SiteID ASC ";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $sContactTitle = $rs["ContactTitle"];
    $memoContactMessage = $rs["ContactMessage"];
}
$smtp = null;
$rs = null;

if(empty($sContactTitle)) {
    $sContactTitle = lngSendMessage;
}

/**
 * The same query results are also used in contact_right.php and map_contact.php
 * Unused Language CONSTANDS: lngSendMessage, lngSendMessage
 */
$arrOffices = sx_GetSubOfficeSetup();

?>
<section>
    <?php
    if (!empty($sContactTitle)) { ?>
        <h1 class="head"><span><?= $sContactTitle ?></span></h1>
    <?php
    } ?>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($radioValidCaptcha === false) { ?>
            <p class="bg_error"><?= LNG__CaptchaError ?></p>
        <?php
        } elseif ($radioSent === false) { ?>
            <p class="bg_error"><?= LNG_Form_AsteriskFieldsRequired ?></p>
        <?php
        }
    }
    if ($radioSent) { ?>
        <p class="bg_success"><?= lngMessageIsSent ?></p>
    <?php
    } elseif ($radioSentError) { ?>
        <p class="bg_error"><?= lngInfoErrorTryAgain ?></p>
    <?php
    } ?>

    <form name="EmailForm" id="EmailForm" method="POST" action="contact.php">
        <input type="hidden" name="FormToken" value="<?= sx_generate_form_token('EmailForm', 64) ?>">
        <?php
        if (is_array($arrOffices)) {
            $iRows = count($arrOffices) ?>
            <fieldset>
                <label><?= lngRecipient ?>:</label>
                <select name="Recipient" size="1">
                    <option value="<?= $int_SiteID ?>"><?= str_SiteTitle ?></option>
                    <?php
                    for ($r = 0; $r < $iRows; $r++) {
                        $iSiteID = $arrOffices[$r][0];
                        $sSubTitle = $arrOffices[$r][1];
                        $strSelected = "";
                        if (intval($intRecipient) == intval($iSiteID)) {
                            $strSelected = " selected";
                        }
                        if (intval($iSiteID) > 0 && strlen($sSubTitle) > 0) { ?>
                            <option<?= $strSelected ?> value="<?= $iSiteID ?>"><?= $sSubTitle ?></option>
                        <?php
                        }
                    } ?>
                </select>
            </fieldset>
        <?php
        } ?>
        <fieldset>
            <input type="hidden" name="visitorMsg" value="no">
            <input type="text" size="28" name="SenderName" value="<?= $strSenderName ?>" placeholder="<?= lngName ?>" required /> *<br>
            <input class="input_text" type="text" size="32" name="senderLastName" value="" placeholder="<?= LNG__LastName ?>" />
            <input type="text" size="28" value="<?= $strFrom ?>" name="SenderEmail" placeholder="<?= LNG__Email ?>" required /> *<br>
            <input type="text" size="28" value="<?= $strPhone ?>" name="SenderPhone" placeholder="<?= lngPhone ?>"><br>
            <?php
            if (defined('SX_includeSubjectInContactForm') && SX_includeSubjectInContactForm) { ?>
                <input spellcheck type="text" size="39" value="<?= $strMailTitle ?>" name="Title" placeholder="<?= lngSubject ?>"><br>
            <?php
            }
            if (defined('SX_countMessageLength') && SX_countMessageLength) { ?>
                <label><?= lngMessage ?>: <input name="entered" disabled type="text" size="4">
                    <?= LNG_Form_EnterMaxCharacters . ": " . $i_MaxEmailLength ?> *</label>
                <textarea spellcheck name="Message" rows="8" onFocus="countEntries('EmailForm','Message',<?= $i_MaxEmailLength ?>);" required><?= $strSenderMessage ?></textarea>
                <div class="text_xsmall"><?= LNG_Form_WritePureText ?></div>
            <?php
            } else { ?>
                <label><?= lngMessage ?>: *</label>
                <textarea spellcheck name="Message" rows="8" required><?= $strSenderMessage ?></textarea>
            <?php
            } ?>
        </fieldset>
        <?php
        if (sx_radio_UseContactCaptcha) { ?>
            <fieldset>
                <?php include DOC_ROOT . "/sxPlugins/captcha/include.php"; ?>
                <div><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required /></div>
                <div class="text_xsmall"><?= LNG_Form_EnterCaptcha ?></div>
            </fieldset>
        <?php
        } ?>
        <fieldset>
            <div class="float_right text_xxsmall"> * <?= lngRequiredInformation ?> </div>
            <div><input class="submit" type="submit" value="<?= LNG_Form_Submit ?>" name="send"></div>
        </fieldset>
    </form>
</section>

<?php

if (!empty($memoContactMessage)) { ?>
    <section>
        <div class="text" lang="<?= sx_CurrentLanguage ?>"><div class="text_max_width"><?= $memoContactMessage ?></div></div>
    </section>
<?php
} ?>