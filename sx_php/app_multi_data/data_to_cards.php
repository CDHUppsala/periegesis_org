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
        $memoDataNotes = strip_tags($memoDataNotes, ['<p>', '<b>', '<strong>', '<i>', '<em>', '<a>']);
    }
    //$memoDataNotes = sx_Replace_Quotes($memoDataNotes);

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
            echo '<div class="grid_cards">';
        } elseif ($r === 0) {
            echo '<div class="grid_cards">';
        }

        $radioTitleInCaption = true;
        if ($radioShowDataTitle == 'Title') {
            $radioTitleInCaption = false;
        }

        $length = count($arrPhotos);
        for ($p = 0; $p < $length; $p++) {
            $photoName  = trim($arrPhotos[$p]);
            if (!empty($photoName)) {
                echo '<figure>';

                $strObjectValue = return_Media_Type_URL($photoName);
                if (!empty($strObjectValue)) {
                    get_Media_Type_Player($photoName, $strObjectValue);
                } else {
                    if (!empty($strDataTitle)) {
                        $strAlt = sx_Replace_Quotes($strDataTitle);
                    } else {
                        $strAlt = get_Link_Title_From_File_Name($photoName);
                    }
                    echo '<figure data-lightbox="cards_data_' . $r . '">';
                    echo '<img src="' .  $subFolder . $photoName . '" alt="' . $strAlt . ' - ' . SX_imageAltName . '" />';
                    echo '</figure>';
                }

                echo '<figcaption>';
                if ($radioShowDataTitle && !empty($strDataTitle)) {
                    echo "<strong>" .  $strDataTitle . "</strong>, ";
                }
                if ($radioShowDataNotes && !empty($memoDataNotes)) {
                    // echo strip_tags($memoDataNotes);
                    echo $memoDataNotes;
                }
                echo '</figcaption>';

                echo '</figure>';
            }
        }
        $loopSectionID = $intSectionID;
    }
}
echo '</div>';
