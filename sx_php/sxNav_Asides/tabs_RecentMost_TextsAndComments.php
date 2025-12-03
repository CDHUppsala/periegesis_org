<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

/**
 * The use of this menu is NOT determined dynamically
 * but from the disgn page, by the variable:
 * sx_ShowRecentMost_BothTextsAndComments_Tabs
 */

$displayRecentTexts = "none";
$selectRecentTexts = "";
$displayRecentBlogs = "none";
$selectRecentBlogs = "";
$displayMostBloged = "none";
$selectMostBloged = "";
$displayMostRead = "none";
$selectMostRead = "";
if (isset($_GET["nav"])) {
    $strNav = $_GET["nav"];
    if ($strNav == "rb") {
        $displayRecentBlogs = "block";
        $selectRecentBlogs = "selected";
    } elseif ($strNav == "mb") {
        $displayMostBloged = "block";
        $selectMostBloged = "selected";
    } elseif ($strNav == "mr") {
        $displayMostRead = "block";
        $selectMostRead = "selected";
    } else {
        $displayRecentTexts = "block";
        $selectRecentTexts = "selected";
    }
} ?>
<section class="jqNavSideToBeCloned">
    <h2 class="head"><span><?= lngRecentAndMost ?></span></h2>
    <nav class="nav_tabs_bg">
        <div class="nav_tabs jqNavTabs">
            <ul>
                <?php
                if ($radio_ShowRecentTexts) { ?>
                    <li class="<?= $selectRecentTexts ?>"><span><?= $str_RecentTextsTitle ?></span></li>
                <?php }
                if ($radioShowMostReadTexts) { ?>
                    <li class="<?= $selectMostRead ?>"><span><?= $str_MostReadTextsTitle ?></span></li>
                <?php }
                if ($radio_ShowMostCommented) { ?>
                    <li class="<?= $selectMostBloged ?>"><span><?= $str_RecentCommentsTitle ?></span></li>
                <?php }
                if ($radio_ShowRecentComments) { ?>
                    <li class="<?= $selectRecentBlogs ?>"><span><?= $str_MostCommentedTitle ?></span></li>
                <?php } ?>
            </ul>
        </div>
        <div class="nav_aside nav_tab_layers">
            <?php
            if ($radio_ShowRecentTexts) {
                sx_GetNavTextsRecent($displayRecentTexts);
            }
            if ($radioShowMostReadTexts) {
                sx_GetNavTextsMost($displayMostRead);
            }
            if ($radio_ShowMostCommented) {
                sx_getNavCommentsMost($displayMostBloged);
            }
            if ($radio_ShowRecentComments) {
                sx_getNavCommentsRecent($displayRecentBlogs);
            } ?>
        </div>
    </nav>
</section>