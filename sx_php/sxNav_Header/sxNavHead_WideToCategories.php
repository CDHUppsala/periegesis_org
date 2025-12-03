<?php
/**
 * Include once as the file is also used (included) 
 * in Main (vertically) navigation files (in sxNav_Main)
 */
include_once __DIR__ . "/functions_Nav_Queries.php";
include __DIR__ . "/functions_Nav_Menu_Lists.php";

$strNavPath = "texts.php?";
if (empty($str_TextClassesInMainMenuTitle)) {
    $str_TextClassesInMainMenuTitle = lngCategories;
}
?>
<div class="sxNavHeader" id="jqNavHeader">
    <ul>
        <li><a href="index.php"><?= lngHomePage ?></a></li>
        <?php
        $aResults = sx_getRowsNavByCategories();
        if (is_array($aResults)) { ?>
            sx_getHeaderNavList_ToCategories_Wide($aResults, $strNavPath, $str_TextClassesInMainMenuTitle)
        <?php
        }
        $aResults = "";

        if ($radio_UseEvents) { ?>
            <li><a href="events.php"><?= $str_EventsMenuTitle ?></a></li>
            <?php }
        if ($radio_UseTextsAbout) {
            if ($radio_ShowAboutTextsInHeader) {
                sx_getAboutMenu_HeaderFooterTexts("header", $str_TextsAboutTitle);
            } else { ?>
                <li><a href="about.php"><?= $str_TextsAboutTitle ?></a></li>
            <?php }
        }
        if ($radio_IncludeCourses && sx_IncludeCourses) { ?>
            <li><a href="courses.php"><?= $str_CoursesLinkTitle ?></a></li>
        <?php
        }
        if ($radio_UseMembersList) { ?>
            <li><a href="about.php?members=yes"><?= $str_MembersListTitle ?></a></li>
        <?php }

        if (sx_includeForum && $radio_IncludeForum && !empty($str_ForumLinkTitle)) { ?>
            <li><a href="sxInc_Forum.php"><?= $str_ForumLinkTitle ?></a></li>
        <?php
        }
        if (sx_IncludeTopContact == false) { ?>
            <li><a href="contact.php"><?= lngContact ?></a></li>
        <?php }
        if (sx_IncludeTopSearch == false) { ?>
            <li><a href="search.php"><?= lngSearch ?></a></li>
        <?php } ?>
    </ul>
</div>