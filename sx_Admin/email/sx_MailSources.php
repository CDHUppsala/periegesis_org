<?php

/**
 * GET REQUEST strings from content.php, if any
 * - to mark in adnvance the sources of email recipients
 */

$str_To = "";
if (isset($_GET["to"])) {
    $str_To = $_GET["to"];
}
?>

<h2>Select the Source of Emails</h2>
<form id="jqLoadSelectForm" data-url="jqLoadEmailLayer" name="GetEmailLetter" method="POST" action="ajax_getMailList.php">
    <fieldset>
        <?php
        $strChecked = "";
        if ($str_To == "Newsletters") {
            $strChecked = " checked";
        } ?>
        <p><input type="radio" name="ListFrom" value="Newsletters" required<?= $strChecked ?>>
            From Newsletters - Optionally, select a language</p>

        <?php
        if (sx_RadioMultiLang) {
            $bResults = sx_getAllLanguages();
            if (is_array($bResults)) { ?>
                <div style="padding-left: 50px">
                    <p><b>Obs!</b> Inludes also records with Language ID = 0.</p>
                    <p>
                        <input type="radio" name="LanguageID" checked value="0" /> All Languages
                        <?php
                        $iRows = count($bResults);
                        for ($r = 0; $r < $iRows; $r++) { ?>
                            <input type="radio" name="LanguageID" value="<?= $bResults[$r][0] ?>" /> <?= $bResults[$r][1] ?>
                        <?php
                        } ?>
                    </p>
                </div>
            <?php
            }
        }
        $bResults = null;
        if (sx_radioIncludeNewslettersByGroup) {
            $bResults = sx_getNewslttersByGroup();

            if (is_array($bResults)) { ?>
                <div style="padding-left: 50px">
                    <p><label>Select Group</label>
                        <select name="NewsGroupID">
                            <option value="0" selected>All Groups </option>
                            <?php
                            $iRows = count($bResults);
                            for ($r = 0; $r < $iRows; $r++) {  ?>
                                <option value="<?= $bResults[$r][0] ?>"> <?= $bResults[$r][1] ?> </option>
                            <?php
                            }  ?>
                        </select>
                    </p>
                </div>
        <?php
            }
        } ?>
    </fieldset>

    <?php
    if (sx_radioIncludeMembersLists) { ?>
        <fieldset>
            <?php
            $strChecked = "";
            if ($str_To == "MembersList") {
                $strChecked = " checked";
            } ?>
            <p><input type="radio" name="ListFrom" value="MembersList" required<?= $strChecked ?>>
                From Members List</p>
        </fieldset>
    <?php
    }

    if (sx_radioIncludeUsersList) { ?>
        <fieldset>
            <?php
            $strChecked = "";
            if ($str_To == "UsersList") {
                $strChecked = " checked";
            } ?>
            <p><input type="radio" name="ListFrom" value="UsersList" required<?= $strChecked ?>>
                From Users List</p>
        </fieldset>
    <?php
    }

    if (defined('sx_radioIncludeForumMembersList') && sx_radioIncludeForumMembersList) { ?>
        <fieldset>
            <?php
            $strChecked = "";
            if ($str_To == "UsersList") {
                $strChecked = " checked";
            } ?>
            <p><input type="radio" name="ListFrom" value="ForumMembersList" required<?= $strChecked ?>>
                From Forum Members List</p>
        </fieldset>
    <?php
    }

    if (sx_includeConferenceParticipants) { ?>
        <fieldset>
            <?php
            $strChecked = "";
            if ($str_To == "Participants") {
                $strChecked = " checked";
            } ?>
            <p><input type="radio" name="ListFrom" value="Participants" required<?= $strChecked ?>>
                From Conference Participants - Optionally select participants by Conference </p>
            <?php
            $bResults = null;
            if (sx_radioParticipantsByConference) {
                $bResults = sx_getConferenceInformation();
            }
            if (is_array($bResults)) { ?>
                <div style="padding-left: 40px">
                    <p><input type="radio" name="ConferenceID" value="0" checked /> All Participants</p>
                    <dl>
                        <?php
                        $iRows = count($bResults);
                        for ($r = 0; $r < $iRows; $r++) { ?>
                            <dt><input type="radio" name="ConferenceID" value="<?= $bResults[$r][0] ?>" />
                                <b><?= $bResults[$r][1] ?></b>
                            </dt>
                            <dd><b>Period:</b> <?= $bResults[$r][2] . " | " . $bResults[$r][3] ?></dd>
                        <?php
                        }
                        $bResults = null ?>
                    </dl>
                </div>
            <?php
            } ?>
        </fieldset>
    <?php
    }

    if (sx_radioIncludeStudentsList) { ?>
        <fieldset>
            <?php
            $strChecked = "";
            if ($str_To == "StudentsList") {
                $strChecked = " checked";
            } ?>
            <p><input type="radio" name="ListFrom" value="StudentsList" required<?= $strChecked ?>>
                From Students List - Optionally select Students by Course</p>
            <?php
            $bResults = null;
            if (sx_radioIncludeStudentsListByCourse) {
                $bResults = sx_getCoursesInformation();
            }
            if (is_array($bResults)) { ?>
                <div style="padding-left: 40px">
                    <p><input type="radio" name="CourseID" value="0" checked /> All Students</p>
                    <dl>
                        <?php
                        $iRows = count($bResults);
                        for ($r = 0; $r < $iRows; $r++) { ?>
                            <dt><input type="radio" name="CourseID" value="<?= $bResults[$r][0] ?>" />
                                <b><?= $bResults[$r][1] ?></b>
                            </dt>
                            <dd><b>Teacher:</b> <?= $bResults[$r][2] . ", <b>Period:</b> " . $bResults[$r][3] ?></dd>
                        <?php
                        }
                        $bResults = null ?>
                    </dl>
                </div>
            <?php
            } ?>
        </fieldset>
    <?php
    }

    if (sx_radioIncludeCustomersList) { ?>
        <fieldset>
            <?php
            $strChecked = "";
            if ($str_To == "CustomersList") {
                $strChecked = " checked";
            } ?>
            <p><input type="radio" name="ListFrom" value="CustomersList" required<?= $strChecked ?>>
                From Customers' Mail List - Optionally select City, Country or Customer Type</p>

            <div style="padding-left: 40px">
                <ul>
                    <li><input type="radio" name="ToCustomers" value="All" checked /> Mail to All Customers</li>
                    <li><input type="radio" name="ToCustomers" value="Registered"> Mail to Registered Customers</li>
                    <li><input type="radio" name="ToCustomers" value="Wholesalers"> Mail to Wholesale Customers</li>
                    <li><input type="radio" name="ToCustomers" value="District"> Mail to City Customers</li>
                </ul>
                <?php
                $bResults = getCustomerDistricts();

                if (is_array($bResults)) { ?>
                    <p class="paddingLeft">
                        <select name="DistrictID">
                            <option value="0">Select City</option>
                            <?php $iRows = count($bResults);
                            for ($r = 0; $r < $iRows; $r++) {  ?>
                                <option value="<?= $bResults[$r][0] ?>"><?= $bResults[$r][1] ?></option>
                            <?php
                            } ?>
                        </select>
                    </p>
                <?php
                }
                $bResults = null
                ?>

                <p><input type="radio" name="ToCustomers" value="Country"> Mail to Country Customers</p>
                <?php $bResults = getCustomerCountries();
                if (is_array($bResults)) { ?>
                    <p class="paddingLeft">
                        <select name="CountryID">
                            <option value="0">Select Country</option>
                            <?php $iRows = count($bResults);
                            for ($r = 0; $r < $iRows; $r++) { ?>
                                <option value="<?= $bResults[$r][0] ?>"><?= $bResults[$r][1] ?></option>
                            <?php
                            } ?>
                        </select>
                    </p>
                <?php
                }
                $bResults = null;
                ?>
            </div>
        </fieldset>
    <?php
    } ?>

    <fieldset>
        <div class="alignRight" style="margin-right: 20px;"><input type="Submit" value="Get Mail List"></div>
    </fieldset>
</form>
<section>
<h3>Click the button over a textarea to Add the List to the Sending Form</h3>
    <p>You can add only one textarea every 10 minutes.</p>

    <div id="jqLoadEmailLayer">
        <pre>Please, select the Source of Emails to Load Textareas with Email Lists.</pre>
    </div>
</section>