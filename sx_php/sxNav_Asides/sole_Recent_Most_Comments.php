<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

if ($radio_ShowRecentComments) { ?>
    <section class="jqNavSideToBeCloned">
        <h2 class="head slide_down jqToggleNextRight"><span><?= $str_RecentCommentsTitle ?></span></h2>
        <div class="nav_aside" style="display: none">
            <?php
            sx_getNavCommentsRecent("block");
            ?>
        </div>
    </section>
<?php
}
if ($radio_ShowMostCommented) { ?>
    <section class="jqNavSideToBeCloned">
        <h2 class="head slide_down jqToggleNextRight"><span><?= $str_MostCommentedTitle ?></span></h2>
        <div class="nav_aside" style="display: none">
            <?php
            sx_getNavCommentsMost("block");
            ?>
        </div>
    </section>
<?php
} ?>
