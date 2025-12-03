<?php

/**
 * Advertising open as dialog box
 * Inserted in Table AdvertisesHeader with the place variable set in Dialog
 */
if (!isset($_COOKIE["dialog_ads"]) && sx_IncludeDialogAds) {
    $radioTemp = False;
    $sql = "SELECT AdvertiseID, Title, ImageURL, LinkURL, Notes
        FROM advertises_logo
        WHERE Publish = True AND PublishPlace = 'Dialog' 
        AND (StartDate <= '" . Date("Y-m-d") . "' OR StartDate IS NULL) 
        AND (EndDate >= '" . Date("Y-m-d") . "' OR EndDate IS NULL) " . str_LanguageAnd;
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if (($rs)) {
        $radioTemp = True;
        $int_AdvertiseID = $rs["AdvertiseID"];
        $strTitle = $rs["Title"];
        $strImageURL = $rs["ImageURL"];
        $strLinkURL = $rs["LinkURL"];
        $memoNotes = $rs["Notes"];
    }
    $stmt = null;
    $rs = null;
    if ($radioTemp) { ?>
        <div class="dialog_ads jqDialogAds">
            <?php
            $aTagOpen = "";
            $aTagClose = "";
            if ($strLinkURL != "") {
                $aTagClose = "</a>";
                $aTagOpen = return_Left_Link_Tag($strLinkURL);
            }
            if (!empty($strImageURL)) {
                get_Any_Media($strImageURL, "", $strTitle, $strLinkURL);
            }
            if (!empty($strTitle)) { ?>
                <h3><?= $aTagOpen . $strTitle . $aTagClose ?></h3>
            <?php }
            if ($memoNotes != "") { ?>
                <div class="text_normal"><?= $memoNotes ?></div>
            <?php } ?>
            <p><button class="jqRemoveDialogAds">OK</button></p>
        </div>
<?php }
}
?>