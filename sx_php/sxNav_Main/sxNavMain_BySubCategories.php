<?php

/**
 * =================================================
 * 3 levels accordion menu
 * =================================================
 * Header and Main menu use the same query from functions_HeadMainNavQuery.php
 */

/**
 * Include once as the file is also used (included) 
 * in Header (horizontally) navigation files (in sxNav_Header)
 */
include_once dirname(__DIR__) . "/sxNav_Header/functions_Nav_Queries.php";

function sx_GetNavBySubCategories($aResults, $path = "texts.php?", $display = "block")
{
    global $int_GroupID, $int_CatID, $int_SubCatID;
    if (is_array($aResults)) { ?>
        <ul style="display: <?= $display ?>">
            <?php
            $iRows = count($aResults);
            $levelUL1 = false;
            $levelUL2 = false;
            $loop1 = -1;
            $loop2 = -1;

            for ($iRow = 0; $iRow < $iRows; $iRow++) {
                $intMenuGroupID = (int) $aResults[$iRow][0];
                $strMenuGroupName = $aResults[$iRow][1];
                $intMenuCatID = (int) $aResults[$iRow][2];
                $strMenuCatName = $aResults[$iRow][3];
                $intMenuSubCatID = (int) $aResults[$iRow][4];
                $strMenuSubCatName = $aResults[$iRow][5];

                if (intval($intMenuGroupID) != intval($loop1)) {
                    if ($levelUL2) {
                        echo "</ul></li></ul></li>";
                    } elseif ($levelUL1) {
                        echo "</ul></li>";
                    }
                    $levelUL1 = false;
                    $levelUL2 = false;
                    if (intval($intMenuCatID) == 0) {
                        echo '<li><a href="' . $path . 'gid=' . $intMenuGroupID . '">' . $strMenuGroupName . '</a></li>';
                    } else {
                        $levelUL1 = true;
                        if (intval($int_GroupID) == intval($intMenuGroupID)) {
                            $strBoxView = "block";
                            $strClassView = ' class="open"';
                        } else {
                            $strBoxView = "none";
                            $strClassView = "";
                        }
                        echo '<li><div' . $strClassView . '>' . $strMenuGroupName . '</div>
                        <ul style="display: ' . $strBoxView . '">';
                    }
                }
                if ($levelUL1 && intval($intMenuCatID) != intval($loop2)) {
                    if ($levelUL2) {
                        echo "</ul></li>";
                    }
                    $levelUL2 = false;
                    $strBoxView = "none";
                    $strClassView = "";
                    if (intval($int_CatID) == intval($intMenuCatID)) {
                        $strBoxView = "block";
                        $strClassView = ' class="open"';
                    }
                    if (intval($intMenuSubCatID) == 0) { //The category has no subcategories
                        echo '<li><a' . $strClassView . ' href="' . $path . 'cid=' . $intMenuCatID . '">' . $strMenuCatName . '</a></li>';
                    } else {
                        $levelUL2 = true;
                        echo '<li><div' . $strClassView . '>' . $strMenuCatName . '</div>
                <ul style="display: ' . $strBoxView . '">';
                    }
                }
                if ($levelUL2) {
                    $strClassView = "";
                    if (intval($int_SubCatID) == intval($intMenuSubCatID)) {
                        $strClassView = ' class="open"';
                    }
                    echo '<li><a' . $strClassView . ' href="' . $path . 'scid=' . $intMenuSubCatID . '">' . $strMenuSubCatName . '</a></li>';
                }
                $loop1 = $intMenuGroupID;
                $loop2 = $intMenuCatID;
            }
            if ($levelUL2) {
                echo "</ul></li></ul></li>";
            } elseif ($levelUL1) {
                echo "</ul></li>";
            } ?>
        </ul>
    <?php
    }
}

$aResults = sx_GetRowsNavBySubCategories();
if (is_array($aResults)) {
    $strNavPath = "texts.php?";
    if (empty($str_TextClassesInMainMenuTitle)) {
        $str_TextClassesInMainMenuTitle = lngCategories;
    } ?>
    <section class="jqNavMainToBeCloned">
        <h2 class="head slide_up_NU jqToggleNextRight_NU"><span><?= $str_TextClassesInMainMenuTitle ?></span></h2>
        <nav class="sxAccordionNav jqAccordionNav">
            <?php
            sx_GetNavBySubCategories($aResults, $strNavPath, "block");
            ?>
        </nav>
    </section>
<?php
}
$aResults = "";
?>