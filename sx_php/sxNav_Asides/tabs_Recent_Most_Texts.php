<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

/**
 * Recent and most red texts
 */
if ($radio_ShowRecentTexts || $radioShowMostReadTexts) {

    $displayRecentTexts = "none";
    $selectRecentTexts = "";
    $displayMostRead = "none";
    $selectMostRead = "";
    if (isset($_GET["nav"])) {
        $strNav = $_GET["nav"];
        if ($strNav == "mr") {
            $displayMostRead = "block";
            $selectMostRead = "selected";
        } else {
            $displayRecentTexts = "block";
            $selectRecentTexts = "selected";
        }
    } ?>
    <section class="jqNavSideToBeCloned">
        <h2 class="head"><span><?= $str_RecentAndMostReadMenuTitle ?></span></h2>
        <nav class="nav_tabs_bg">
            <div class="nav_tabs jqNavTabs">
                <ul>
                    <?php
                    if ($radio_ShowRecentTexts) { ?>
                        <li class="<?= $selectRecentTexts ?>"><span><?= $str_RecentTextsTitle ?></span></li>
                    <?php }
                    if ($radioShowMostReadTexts) { ?>
                        <li class="<?= $selectMostRead ?>"><span><?= $str_MostReadTextsTitle ?></span></li>
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
                } ?>
            </div>
        </nav>
    </section>
<?php
} ?>