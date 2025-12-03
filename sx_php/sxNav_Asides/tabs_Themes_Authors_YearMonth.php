<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

/**
 * Texts by Theme, Author and Year/Month
 */
if ($radio_ShowTextsByThemes || $radio_ShowTextsByAuthors || $radio_ShowTextsByYearMonth) {

    $displayThemes = "none";
    $displayAuthors = "none";
    $displayYearMonth = "none";
    $classThemes = "";
    $classAuthors = "";
    $classYearMonth = "";
    /*
    if (intval($int_ThemeID) > 0) {
        $displayThemes = "block";
        $classThemes = 'class="selected"';
    } elseif (intval($int_AuthorID) > 0) {
        $displayAuthors = "block";
        $classAuthors = 'class="selected"';
    } elseif (intval($int_Month) > 0) {
        $displayYearMonth = "block";
        $classYearMonth = 'class="selected"';
    }
    */
?>
    <section class="jqNavSideToBeCloned">
        <h2 class="head"><span><?= $str_ByTextsMenuTitle ?></span></h2>
        <nav class="nav_tabs_bg">
            <div class="nav_tabs jqNavTabs">
                <ul>
                    <?php
                    if ($radio_ShowTextsByThemes) { ?>
                        <li <?= $classThemes ?>><span><?= $str_TextsByThemesTitle ?></span></li>
                    <?php
                    }
                    if ($radio_ShowTextsByAuthors) { ?>
                        <li <?= $classAuthors ?>><span><?= $strTextsByAuthorsTitle ?></span></li>
                    <?php
                    }
                    if ($radio_ShowTextsByYearMonth) { ?>
                        <li <?= $classYearMonth ?>><span><?= $str_TextsByYearMonthTitle ?></span></li>
                    <?php
                    } ?>
                </ul>
            </div>
            <div class="sxAccordionNav jqAccordionNav nav_tab_layers">
                <?php
                if ($radio_ShowTextsByThemes) {
                    sx_GetNavThemes($displayThemes, $int_ThemeID);
                }
                if ($radio_ShowTextsByAuthors) {
                    sx_GetNavAuthors($displayAuthors, $int_AuthorID);
                }
                if ($radio_ShowTextsByYearMonth) {
                    include dirname(__DIR__) . "/inText_Calendar/textsByYearMonth.php";
                } ?>
            </div>
        </nav>
    </section>
<?php
} ?>