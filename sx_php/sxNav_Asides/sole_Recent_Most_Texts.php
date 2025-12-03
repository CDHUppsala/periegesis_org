<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

if ($radio_ShowRecentTexts) { ?>
    <section class="jqNavSideToBeCloned">
        <h2 class="head slide_down jqToggleNextRight"><span><?= $str_RecentTextsTitle ?></span></h2>
        <div class="nav_aside" style="display: none">
            <?php
            sx_getNavTextsRecent("block");
            ?>
        </div>
    </section>
<?php
}
if ($radioShowMostReadTexts) { ?>
    <section class="jqNavSideToBeCloned">
        <h2 class="head slide_down jqToggleNextRight"><span><?= $str_MostReadTextsTitle ?></span></h2>
        <div class="nav_aside" style="display: none">
            <?php
            sx_getNavTextsMost("block");
            ?>
        </div>
    </section>
<?php
}