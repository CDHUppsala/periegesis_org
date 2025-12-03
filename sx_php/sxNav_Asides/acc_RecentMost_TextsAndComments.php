<?php
include_once __DIR__ . "/functions_Nav_Asides.php";
/**
 * The use of this menu is NOT determined dynamically
 * but from the disgn page, by the variable:
 * sx_ShowRecentMost_BothTextsAndComments_Accordion
 */

$displayRecentTexts = "none";
$displayMostRead = "none";
$displayRecentBlogs = "none";
$displayMostBloged = "none";
$classRecentTexts = "";
$classMostRead = "";
$classRecentBlogs = "";
$classMostBloged = "";

if (isset($_GET["nav"])) {
    $strReqNav = $_GET["nav"];
    if ($strReqNav == "rt") {
        $displayRecentTexts = "block";
        $classRecentTexts = ' class="selected"';
    } elseif ($strReqNav == "mr") {
        $displayMostRead = "block";
        $classMostRead = ' class="selected"';
    } elseif ($strReqNav = "rb") {
        $displayRecentBlogs = "block";
        $classRecentBlogs = ' class="selected"';
    } elseif ($strReqNav = "mb") {
        $displayMostBloged = "block";
        $classMostBloged = ' class="selected"';
    }
} ?>

<section class="jqNavSideToBeCloned">
    <h2 class="head slide_up jqToggleNextRight"><span><?= lngRecentAndMost ?></span></h2>
    <nav class="nav_aside jqAccordionByDiv">
        <?php
        if ($radio_ShowRecentTexts) { ?>
            <div <?= $classRecentTexts ?>><span><?= $str_RecentTextsTitle ?></span></div>
        <?php
            sx_getNavTextsRecent($displayRecentTexts);
        }
        if ($radioShowMostReadTexts) { ?>
            <div <?= $classMostRead ?>><span><?= $str_MostReadTextsTitle ?></span></div>
        <?php
            sx_getNavTextsMost($displayMostRead);
        }
        if ($radio_ShowRecentComments) { ?>
            <div <?= $classRecentBlogs ?>><span><?= $str_RecentCommentsTitle ?></span></div>
        <?php
            sx_getNavCommentsRecent($displayRecentBlogs);
        }
        if ($radio_ShowMostCommented) { ?>
            <div <?= $classMostBloged ?>><span><?= $str_MostCommentedTitle ?></span></div>
        <?php
            sx_getNavCommentsMost($displayMostBloged);
        } ?>
    </nav>
</section>