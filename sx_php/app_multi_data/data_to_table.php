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
                echo '</table>';
            }
            echo '<table class="table_multi_data">';
            $tempTilte = '';
            if ($radioShowSectionTitle && !empty($strSectionTitle)) {
                $tempTilte = "<h3>$strSectionTitle</h3>";
            }
            $temMemo = '';
            if ($radioShowSectionNotes && !empty($memoSectionNotes)) {
                $temMemo = $memoSectionNotes;
            }
            if (!empty($tempTilte) || !empty($temMemo)) {
                echo "<caption>$tempTilte $temMemo</caption>";
            }
        } elseif ($r === 0) {
            echo '<table class="table_multi_data">';
        }

        $length = count($arrPhotos);
        for ($p = 0; $p < $length; $p++) {
            $photoName  = trim($arrPhotos[$p]);
            if (!empty($photoName)) {
                echo '<tbody><tr>';

                $strObjectValue = return_Media_Type_URL($photoName);
                if (!empty($strObjectValue)) {
                    echo '<td>';
                    get_Media_Type_Player($photoName, $strObjectValue);
                    echo '</td>';
                } else {
                    if (!empty($strDataTitle)) {
                        $strAlt = sx_Replace_Quotes($strDataTitle);
                    } else {
                        $strAlt = get_Link_Title_From_File_Name($photoName);
                    }
                    echo '<td>';
                    echo '<figure class="align_left" data-lightbox="table_data_' . $r . '">';
                    echo '<img src="' .  $subFolder . $photoName . '" alt="' . $strAlt . ' - ' . SX_imageAltName . '" />';
                    echo '</figure>';
                    echo '</td>';
                }

                echo '<td>';
                if ($radioShowDataTitle && !empty($strDataTitle)) {
                    echo "<strong>$strDataTitle </strong>";
                }
                if ($radioShowDataNotes && !empty($memoDataNotes)) {
                    echo $memoDataNotes;
                }
                echo '</td>';

                echo '</tr></tbody>';
            }
        }
        $loopSectionID = $intSectionID;
    }
}
echo '</table>';
