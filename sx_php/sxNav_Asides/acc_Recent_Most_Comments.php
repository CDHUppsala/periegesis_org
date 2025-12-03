<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

/**
 * Recent commented texts och most commented text
 */
if ($radio_ShowRecentComments || $radio_ShowMostCommented) {
    $displayRecentBlogs = "none";
    $displayMostBloged = "none";
    $classRecentBlogs = "";
    $classMostBloged = "";

    if (isset($_GET["nav"])) {
        $strReqNav = $_GET["nav"];
        if ($strReqNav = "rb") {
            $displayRecentBlogs = "block";
            $classRecentBlogs = ' class="selected"';
        } elseif ($strReqNav = "mb") {
            $displayMostBloged = "block";
            $classMostBloged = ' class="selected"';
        }
    } ?>

    <section class="jqNavSideToBeCloned">
        <h2 class="head"><span><?= $str_RecentAndMostCommentedMenuTitle ?></span></h2>
        <nav class="nav_aside jqAccordionByDiv">
            <?php
            if ($radio_ShowRecentComments) { ?>
                <div <?php echo $classRecentBlogs ?>><span><?= $str_RecentCommentsTitle ?></span></div>
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
<?php
} ?>