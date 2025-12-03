<?php

$subFolder = '../images/';
$iRows = count($arr);
$loopSectionID = 0;

for ($r = 0; $r < $iRows; $r++) {
    $strDataTitle = $arr[$r]["Title"];
    if (!empty($strDataTitle) && str_contains($strDataTitle, '__')) {
        $split = explode("__", $strDataTitle);
        $strDataTitle = trim($split[1]);
    }
    $strMediaURL = $arr[$r]["MediaURL"];
    $strMediaFolder = $arr[$r]["MediaFolder"];
    $memoDataNotes = $arr[$r]["Notes"];
    if (!empty($memoDataNotes)) {
        $memoDataNotes = strip_tags($memoDataNotes, ['<p>', '<b>', '<strong>', '<i>', '<em>']);
    }
    $memoDataNotes = sx_Replace_Quotes($memoDataNotes);

    $intSectionID = $arr[$r]["SectionID"];
    $strSectionTitle = $arr[$r]["SectionTitle"];
    $memoSectionNotes = $arr[$r]["SectionNotes"];

    if (!empty($intSectionID)) {
        $intSectionID = (int) $intSectionID;
    }

    $sMediaPath = "";
    $radioSort = false;
    if (!empty($strMediaFolder)) {
        $radioSort = true;
        $sMediaPath = return_Folder_Images($strMediaFolder);
    } elseif (!empty($strMediaURL)) {
        $sMediaPath = $strMediaURL;
    }

    if (!empty($strMediaURL)) {
        if (strpos($sMediaPath, ";") == 0) {
            $sMediaPath .= ';';
        }

        $arrPhotos = explode(';', $sMediaPath);
        if ($radioSort) {
            sort($arrPhotos);
        }

        if ($intSectionID > 0 &&  $intSectionID != $loopSectionID) {
            if ($r > 0) {
                echo '</div>';
            }
            if ($radioShowSectionTitle && !empty($strSectionTitle)) {
                echo "<h3>$strSectionTitle</h3>";
            }
            if ($radioShowSectionNotes && !empty($memoSectionNotes)) {
                echo $memoSectionNotes;
            }
            echo '<div class="img_cycler_manual jqImgCyclerManual">';
        } elseif ($r === 0) {
            echo '<div class="img_cycler_manual jqImgCyclerManual">';
        }

        $radioTitleInCaption = true;
        if ($radioShowDataTitle == 'Title') {
            $radioTitleInCaption = false;
        }

        echo '<figure data-lightbox="manual_cycler_' . random_int(10, 10000) . '">';
        $length = count($arrPhotos);
        for ($p = 0; $p < $length; $p++) {
            $photoName  = trim($arrPhotos[$p]);
            if (!empty($photoName)) {
                if (!empty($strDataTitle)) {
                    $strAlt = sx_Replace_Quotes($strDataTitle);
                } else {
                    $strAlt = get_Link_Title_From_File_Name($photoName);
                }
                $radioNotes = false;
                echo '<img src="' .  $subFolder . $photoName . '" alt="' . $strAlt . ' - ' . SX_imageAltName . '"';
                if ($radioShowDataTitle && !empty($strDataTitle)) {
                    $radioNotes = true;
                    echo ' data-title = "<b>' . addslashes($strDataTitle) . '</b>"';
                }
                if ($radioShowDataNotes && !empty($memoDataNotes)) {
                    $radioNotes = true;
                    echo '  data-notes ="' . addslashes(strip_tags($memoDataNotes, '<b>, <strong>, <i>, <em>')) . '"';
                }
                echo ' />';
            }
        }
        echo '</figure>';
        $loopSectionID = $intSectionID;
?>
        <ul>
            <li class="more-prev"></li>
            <li>
                <ul>
                    <?php
                    for ($z = 0; $z < $length; $z++) {
                        if (!empty(trim($arrPhotos[$z]))) {
                            $strClass = "";
                            if ($z == 0) {
                                $strClass = 'class="selected"';
                            } ?>
                            <li <?= $strClass ?>><span><?= ($z + 1) ?></span></li>
                    <?php
                        }
                    } ?>
                </ul>
            </li>
            <li class="more-next"></li>
        </ul>
<?php
        if ($radioNotes) {
            echo '<div></div>';
        }
    }
}
echo '</div>';
