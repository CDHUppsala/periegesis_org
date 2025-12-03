<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

/**
 * Recent commented texts och most commented text
 */
if ($radio_ShowRecentComments || $radio_ShowMostCommented) {
    $displayRecentBlogs = "none";
    $selectRecentBlogs = "";
    $displayMostBloged = "none";
    $selectMostBloged = "";
    if (isset($_GET["nav"])) {
        $strNav = $_GET["nav"];
        if ($strNav == "mb") {
            $displayMostBloged = "block";
            $selectMostBloged = "selected";
        } else {
            $displayRecentBlogs = "block";
            $selectRecentBlogs = "selected";
        }
    } ?>
    <section class="jqNavSideToBeCloned">
        <h2 class="head"><span><?= $str_RecentAndMostCommentedMenuTitle ?></span></h2>
        <nav class="nav_tabs_bg">
            <div class="nav_tabs jqNavTabs">
                <ul>
                    <?php
                    if ($radio_ShowMostCommented) { ?>
                        <li class="<?= $selectMostBloged ?>"><span><?= $str_MostCommentedTitle ?></span></li>
                    <?php }
                    if ($radio_ShowRecentComments) { ?>
                        <li class="<?= $selectRecentBlogs ?>"><span><?= $str_RecentCommentsTitle ?></span></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="nav_aside nav_tab_layers">
                <?php
                if ($radio_ShowMostCommented) {
                    sx_getNavCommentsMost($displayMostBloged);
                }
                if ($radio_ShowRecentComments) {
                    sx_getNavCommentsRecent($displayRecentBlogs);
                } ?>
            </div>
        </nav>
    </section>
<?php
} ?>