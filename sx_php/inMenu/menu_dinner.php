<?php
$strCatWhere = "";
$intMenuGroupID = 0;
if (isset($_GET["mcid"]) && !empty($_GET["mcid"])) {
    $intMenuGroupID = (int) $_GET["mcid"];
}

if (intval($intMenuGroupID) > 0) {
    $strCatWhere = " AND m.GroupID = " . $intMenuGroupID;
}

$aRecords = null;
$sql = "SELECT m.MenuID, 
    m.GroupID, 
    m.CategoryID, 
    m.MenuTitle" . str_LangNr . " AS MenuTitle, 
    m.MenuImage, 
    m.Volume, 
    m.Price, 
    m.MenuContent, 
    mg.GroupName" . str_LangNr . " AS GroupName, 
    mg.GroupNotes" . str_LangNr . " AS GroupNotes,
    mc.CategoryName" . str_LangNr . " AS CategoryName, 
    mc.CategoryNotes" . str_LangNr . " AS CategoryNotes
FROM (menu_dishes AS m 
    INNER JOIN menu_dish_groups AS mg 
    ON m.GroupID = mg.GroupID) 
    LEFT JOIN menu_dish_categories AS mc ON m.CategoryID = mc.CategoryID
WHERE m.Available = True 
	AND mg.Hidden = False 
	AND (mc.Hidden = False OR mc.Hidden IS NULL) " . $strCatWhere . "
ORDER BY mg.Sorting DESC , 
    mg.GroupName, 
    mc.Sorting DESC , 
    mc.CategoryName";
$rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
if ($rs) {
    $aRecords = $rs;
}
$rs = null;
?>
<section class="menu_wrapper">
    <h1 class="head align_center"><span><?= $str_MenuListTitle ?></span></h1>
    <?php
    if (!is_array($aRecords)) { ?>
        <h3><?= lngRecordsNotFound ?></h3>
    <?php
    } else { ?>


        <div class="print">
            <?php
            getTextPrinter("sx_printPage.php?print=dinnermenu&mcid=" . $intMenuGroupID, "menu" . $intMenuGroupID);
            getLocalEmailSender("", $str_MenuNavTitle, $str_MenuListTitle, "");
            ?>
        </div>
        <?php
        if (!empty($memo_MenuTopNote)) { ?>
            <div class="text_normal bg_grey align_center"><?= $memo_MenuTopNote ?></div>
            <?php
        }

        $iRows = count($aRecords);
        $iLoopGroup = -1;
        $iLoopCat = -1;

        for ($r = 0; $r < $iRows; $r++) {
            $iMenuID = $aRecords[$r][0];
            $iGroupID = $aRecords[$r][1];
            $iCategoryID = $aRecords[$r][2];
            $sMenuTitle = $aRecords[$r][3];
            $sMenuImage = $aRecords[$r][4];
            $sVolume = $aRecords[$r][5];
            $sPrice = $aRecords[$r][6];
            $sMenuContent = $aRecords[$r][7];
            $sGroupName = $aRecords[$r][8];
            $sGroupNotes = $aRecords[$r][9];
            $sCategoryName = $aRecords[$r][10];
            $sCategoryNotes = $aRecords[$r][11];

            if (empty($sVolume)) {
                $sVolume = "&nbsp;";
            } else {
                $sVolume = rtrim($sVolume, ";");
                if (strpos($sVolume, ";") > 0) {
                    $sVolume = str_replace(";", "<br>", $sVolume);
                }
            }
            if (!empty($sPrice)) {
                $sPrice = str_replace(" ", "", $sPrice);
                $sPrice = rtrim($sPrice, ";");
                if (strpos($sPrice, ";") > 0) {
                    $sPrice = str_replace(";", "€<br>", $sPrice);
                }
            }
            if ($iLoopGroup != $iGroupID) {
                if ($iLoopGroup > 0) {
                    echo "</table>";
                } ?>
                <h2 class="head align_center"><span><?= $sGroupName ?></span></h2>
                <?php
                $strTemp = "!";
                if ($sGroupNotes != "") {
                    $strTemp = $strTemp . "<div>" . $sGroupNotes . "</div>";
                }
                if (!empty($strTemp)) { ?>
                    <div class="bg_grey align_center"><?= $strTemp ?></div>
                <?php
                }
                if (intval($iCategoryID) == 0) {
                    echo '<table>';
                }
            }
            if (intval($iCategoryID) == 0) { ?>
                <tr>
                    <?php
                    if ($radio_UseDishImages) { ?>
                        <td class="width_20"><img alt="<?= $sMenuTitle ?>" src="../images/<?= $sMenuImage ?>"></td>
                    <?php
                    } ?>
                    <td class="width_100">
                        <h4><?= $sMenuTitle ?></h4>
                        <div class="text_normal text_small"><?= $sMenuContent ?></div>
                    </td>
                    <td class="white_space_nowrap"><?= $sVolume ?></td>
                    <td class="align_right"><?= $sPrice . SX_usedCurrency ?></td>
                </tr>
                <?php
            } else {
                if ($iLoopCat != $iCategoryID) {
                    if ($iLoopCat > 0) {
                        echo "</table>";
                    } ?>
                    <h3><?= $sCategoryName ?></h3>
                <?php
                    if ($sCategoryNotes != "") {
                        echo $sCategoryNotes;
                    }
                    echo '<table>';
                } ?>
                <tr>
                    <?php
                    if ($radio_UseDishImages) { ?>
                        <td class="width_20"><img alt="<?= $sMenuTitle ?>" src="../images/<?= $sMenuImage ?>"></td>
                    <?php
                    } ?>
                    <td class="width_100">
                        <h4><?= $sMenuTitle ?></h4>
                        <div class="text_normal text_small"><?= $sMenuContent ?></div>
                    </td>
                    <td class="white_space_nowrap"><?= $sVolume ?></td>
                    <td><?= $sPrice . SX_usedCurrency ?></td>
                </tr>
            <?php
            }
            $iLoopGroup = $iGroupID;
            $iLoopCat = $iCategoryID;
        }
        echo "</table>";

        if (!empty($memo_MenuBottomNote)) { ?>
            <div class="text_normal bg_grey text_margin"><?= $memo_MenuBottomNote ?></div>
        <?php
        } ?>
    <?php
    }
    $aRecords = null;
    ?>
</section>