<?php

/**
 * =================================================
 * 2 levels accordion menu 
 * =================================================
 * Header and Main menu use the same query from functions_HeadMainNavQuery.php
 */
/**
 * Include once as the file is also used (included) 
 * in Header (horizontally) navigation files (in sxNav_Header)
 */
include_once dirname(__DIR__) . "/sxNav_Header/functions_Nav_Queries.php";

function sx_GetNavByCategories($aResults, $path = "texts.php?", $display = "block")
{
    global $int_GroupID, $int_CatID;
    if (is_array($aResults)) {
        echo '<ul style="display:' . $display . '">';
        $iRows = count($aResults);
        $levelUL1 = false;
        $loop1 = -1;
        $loop2 = -1;
        for ($iRow = 0; $iRow < $iRows; $iRow++) {
            $intMenuGroupID = (int) $aResults[$iRow][0];
            $strMenuGroupName = $aResults[$iRow][1];
            $intMenuCatID = (int) $aResults[$iRow][2];
            $strMenuCatName = $aResults[$iRow][3];
            if (intval($intMenuGroupID) != intval($loop1)) {
                if ($levelUL1) {
                    echo "</ul></li>";
                }
                $levelUL1 = false;
                if (intval($intMenuCatID) == 0) {
                    echo "<li><a href=\"" . $path . "gid=" . $intMenuGroupID . "\">" . $strMenuGroupName . "</a></li>";
                } else {
                    $levelUL1 = true;
                    if (intval($int_GroupID) == intval($intMenuGroupID)) {
                        $strBoxView = "block";
                        $strClassView = ' class="open"';
                    } else {
                        $strBoxView = "none";
                        $strClassView = "";
                    }
                    echo '<li>';
                    echo '<div' . $strClassView . '><' . $strMenuGroupName . '</div>';
                    echo '<ul style="display: ' . $strBoxView . '">';
                }
            }
            if ($levelUL1 && intval($intMenuCatID) != intval($loop2)) {
                echo "<li><a href=\"" . $path . "cid=" . $intMenuCatID . "\">" . $strMenuCatName . "</a></li>";
            }
            $loop1 = $intMenuGroupID;
            $loop2 = $intMenuCatID;
        }
        if ($levelUL1) {
            echo "</ul></li>";
        }
        echo "</ul>";
    }
}

$aResults = sx_getRowsNavByCategories();
if (is_array($aResults)) {
    $strNavPath = "texts.php?";
    if (empty($str_TextClassesInMainMenuTitle)) {
        $str_TextClassesInMainMenuTitle = lngCategories;
    } ?>
    <section class="jqNavMainToBeCloned">
        <h2 class="head slide_up_NU jqToggleNextRight_NU"><span><?= $str_TextClassesInMainMenuTitle ?></span></h2>
        <nav class="sxAccordionNav jqAccordionNav">
            <?php
            sx_GetNavByCategories($aResults, $strNavPath, "block");
            ?>
        </nav>
    </section>
<?php
}
$aResults = "";
?>