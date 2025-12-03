<?php
include __DIR__ . "/participation_form_process.php";
?>
<section id="ParticipationForm">
    <?php
    if ($radio_DisplayWarning) { ?>
        <h2>Thank you for your interest!</h2>
        <div class="bg_error">
            <p>An error did occur. We apologize for that.<br>
                Please try again in a few hours. If the error recurs,
                please contact the administration from the Contact Menu.
            <p>
        </div>
    <?php
    } elseif ($radioContinue) { ?>
        <h2>Thank you for your registration!</h2>
        <div class="bg_success"><?= $str__ParticipationNote ?></div>
        <?php
    } else {
        /**
         * Greate the token and its session here, on the server, 
         *   and a time stamp to account requests intervals
         *   don't uppdate time stamp on error
         */

        if (empty($arrError)) {
            $_SESSION['EventFormCreationTime'] = date('Y-m-d H:i:s');
        }
        $str_EventFormToken = sx_generate_form_token('EventFormToken', 128);

        /**
         * Get the place of the event
         */

        $s_PlaceName = $sPlaceName;
        if (!empty($sPlaceAddress)) {
            $s_PlaceName .= ", " . $sPlaceAddress;
        }

        $s_City = 'Athens';
        if (!empty($sPlaceCity)) {
            $s_City = $sPlaceCity;
            $s_PlaceName .= ", " . $sPlaceCity;
        }

        if (!empty($arrError)) { ?>
            <div class="bg_error"><?php echo implode("<br>", $arrError) ?></div>
        <?php
        } ?>

        <h2><?= lngRegisterToParticipate ?></h2>
        <div class="overflow_hidden">
            <form class="jq_load_modal_window" name="EventParticipationForm" method="post">
                <input type="hidden" name="EventFormToken" value="<?php echo $str_EventFormToken ?>">
                <input type="hidden" name="FormName" value="EventParticipation" />
                <input type="hidden" name="EventID" value="<?= $iEventID ?>" />
                <fieldset>
                    <label><?= lngParticipationMode ?>:</label>
                    <?php
                    if ($strParticipationMode == "Both") {
                        $strLiveChecked = " checked";
                        $strOnlineChecked = "";
                        if ($strMode == "Online") {
                            $strLiveChecked = "";
                            $strOnlineChecked = " checked";
                        } ?>
                        <div class="flex_start flex_align_start flex_nowrap">
                            <div><input type="radio" name="Mode" value="Live" <?php echo $strLiveChecked ?> /></div>
                            <div><b>In-person in <?php echo $s_City ?>:</b><br>
                                <?= $s_PlaceName  ?>
                            </div>
                        </div>
                        <div class="flex_start flex_align_start flex_nowrap">
                            <div><input type="radio" name="Mode" value="Online" <?php echo $strOnlineChecked ?> /></div>
                            <div><b>Online via Zoom:</b><br>
                                An <b>access link</b> will be sent to your email address.
                            </div>
                        </div>
                    <?php
                    } elseif ($strParticipationMode == "Online") { ?>
                        <div class="flex_start flex_align_start flex_nowrap">
                            <div><input type="radio" name="Mode" value="Online" checked readonly /></div>
                            <div><b>Online via Zoom:</b><br>
                                An <b>access link</b> will be sent to your email address.
                            </div>
                        </div>
                    <?php
                    } else { ?>
                        <div class="flex_start flex_align_start flex_nowrap">
                            <div><input type="radio" name="Mode" value="Live" checked readonly /></div>
                            <div><b>In-person in <?php echo $s_City ?>:</b><br>
                                <?= $s_PlaceName ?><br>
                                <b>Please notice</b>: Online participation is not available for this event.
                            </div>
                        </div>
                    <?php
                    } ?>

                </fieldset>

                <fieldset class="form_inputs_grid">
                    <label><?= LNG__FirstName ?>:</label>
                    <input type="text" name="FirstName" value="<?= $strFirstName ?>" required />

                    <label><?= LNG__LastName ?>:</label>
                    <input type="text" name="LastName" value="<?= $strLastName ?>" required />

                    <label>Affiliation or Organization:</label>
                        <input type="text" name="Affiliation" value="" />
                        
                    <label class="input_text">City:
                        <input type="text" name="City" value="" /></label>

                    <label class="input_text">Country:
                        <input type="text" name="Country" value="" /></label>

                    <label><?= LNG__Email ?>:</label>
                    <input type="email" name="Email" value="<?= $strEmail ?>" required />

                    <label><?= LNG__EmailRepeat ?>:</label>
                    <input type="email" name="EmailRepeat" value="<?= $strEmailRepeat ?>" required />

                </fieldset>
                <fieldset class="form_fieldsets_grid">
                    <div class="white_space_nowrap">
                        <?php include "../sxPlugins/captcha/include.php" ?>
                        <br><input class="captcha_input" type="text" name="captcha_input" size="8" value="" required />
                    </div>
                    <div class="text_xxsmall"><?= LNG_Form_EnterCaptcha ?></div>
                </fieldset>
                <fieldset class="align_center">
                    <input class="jq_submit" type="submit" name="Submit" value="<?= LNG_Form_Submit ?>" />
                </fieldset>
            </form>
        </div>
    <?php
    } ?>
</section>
<?php
?>