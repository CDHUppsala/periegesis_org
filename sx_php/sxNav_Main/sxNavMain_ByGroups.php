<?php

/**
 * =================================================
 * 1 level accordion menu 
 * =================================================
 * Header and Main menu use the same query from functions_HeadMainNavQuery.php
 */

/**
 * Include once as the file is also used (included) 
 * in Header (horizontally) navigation files (in sxNav_Header)
 */
include_once dirname(__DIR__) . "/sxNav_Header/functions_Nav_Queries.php";

function sx_GetNavByGroups($aResults, $path = "texts.php?", $display = "block")
{
    if (is_array($aResults)) {
        echo '<ul style="display:' . $display . '">';
        $iRows = count($aResults);
        for ($r = 0; $r < $iRows; $r++) {
            echo '<li><a href="' . $path . 'gid=' . $aResults[$r][0] . '">' . $aResults[$r][1] . '</a></li>';
        }
        echo "</ul>";
    }
}

$aResults = sx_GetRowsNavByGroups();
if (is_array($aResults)) {
    $strNavPath = "texts.php?";
    if (empty($str_TextClassesInMainMenuTitle)) {
        $str_TextClassesInMainMenuTitle = lngCategories;
    } ?>
    <section class="jqNavMainToBeCloned">
        <h2 class="head slide_up_NU jqToggleNextRight_NU"><span><?= $str_TextClassesInMainMenuTitle ?></span></h2>
        <nav class="sxAccordionNav jqAccordionNav">
            <?php
            sx_GetNavByGroups($aResults, $strNavPath, "block");
            ?>
        </nav>
    </section>
<?php
}
$aResults = "";
?>