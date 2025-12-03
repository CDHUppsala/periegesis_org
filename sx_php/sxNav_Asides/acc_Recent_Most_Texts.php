<?php
include_once __DIR__ . "/functions_Nav_Asides.php";
/**
 * Recent and most red texts
 */
if ($radio_ShowRecentTexts || $radioShowMostReadTexts) {

    $displayRecentTexts = "none";
    $displayMostRead = "none";
    $classRecentTexts = "";
    $classMostRead = "";

    if (isset($_GET["nav"])) {
        $strReqNav = $_GET["nav"];
        if ($strReqNav == "rt") {
            $displayRecentTexts = "block";
            $classRecentTexts = ' class="selected"';
        } elseif ($strReqNav == "mr") {
            $displayMostRead = "block";
            $classMostRead = ' class="selected"';
        }
    } ?>

    <section class="jqNavSideToBeCloned">
        <h2 class="head slide_up jqToggleNextRight"><span><?= $str_RecentAndMostReadMenuTitle ?></span></h2>
        <nav class="nav_aside jqAccordionByDiv">
            <?php
            if ($radio_ShowRecentTexts) { ?>
                <div<?= $classRecentTexts ?>><span><?= $str_RecentTextsTitle ?></span></div>
                <?php
                sx_getNavTextsRecent($displayRecentTexts);
            }
            if ($radioShowMostReadTexts) { ?>
                    <div<?= $classMostRead ?>><span><?= $str_MostReadTextsTitle ?></span></div>
                    <?php
                    sx_getNavTextsMost($displayMostRead);
                } ?>
        </nav>
    </section>
<?php
} ?>