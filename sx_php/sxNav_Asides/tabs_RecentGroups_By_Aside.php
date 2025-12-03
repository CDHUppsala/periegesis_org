<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

/**
 * Specific adaptotion: 3 Tabs opening 
 * - Group News
 * - Group Articles
 * - Aside Texts
 * Show in Tab form recent texts from selected Groups.
 *   Additionally it can show recent Aside texts
 * Can be defined dynamically be entering a 
 *   corresponding field in Table TextGroups
 */
$displayArticles = "block";
$displayNews = "none";
$displayAsideTexts = "none";
$classArticles = 'class="selected"';
$classNews = "";
$classAsideTexts = "";

$intField = -1;
if (isset($_GET["field"])) {
    $intField = trim($_GET["field"]);
    $displayArticles = "none";
    $classArticles = "";
}

if (intval($intField) > 0) {
    if ($intField == 1) {
        $displayArticles = "block";
        $classArticles = 'class="selected"';
    } else {
        $displayNews = "block";
        $classNews = 'class="selected"';
    }
} elseif (intval($intField) == 0) {
    $displayAsideTexts = "block";
    $classAsideTexts = 'class="selected"';
}

$strGroupName_1 = return_Field_Value_From_Table("text_groups", "GroupName", "GroupID", 1);
$strGroupName_2 = return_Field_Value_From_Table("text_groups", "GroupName", "GroupID", 2);
?>
<section class="jqNavMainToBeCloned">
    <h2 class="head"><span><?= $str_PublishedTextsByClassTitle ?></span></h2>
    <nav class="nav_tabs_bg">
        <div class="nav_tabs jqNavTabs">
            <ul>
                <li <?= $classArticles ?>><span><?= $strGroupName_1 ?></span></li>
                <li <?= $classNews ?>><span><?= $strGroupName_2 ?></span></li>
                <?php
                if ($radio_UseAsideTexts && sx_includeAsideTexts) { ?>
                    <li <?= $classAsideTexts ?>><span><?= $str_AsideTextsTitle ?></span></li>
                <?php
                } ?>
            </ul>
        </div>
        <div class="nav_tab_layers sxAccordionNav jqAccordionNav">
            <?php
            sx_RecentGroupsByAside($displayArticles, 1);
            sx_RecentGroupsByAside($displayNews, 2);
            if ($radio_UseAsideTexts && sx_includeAsideTexts) {
                sx_RecentGroupsByAside($displayAsideTexts);
            } ?>
        </div>
    </nav>
</section>