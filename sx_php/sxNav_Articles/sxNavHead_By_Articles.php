<?php
include __DIR__ . "/functions_Nav_Articles.php";

$strNavPath = "articles.php?";
$aResults = null;

?>
<div class="nav_head_menu" id="jqNavHeadMenu">
    <ul>
        <?php
        if ($radio_UseTextsAbout && sx_ShowAboutHeaderMenuAtStart) {
            if (sx_AboutHeaderMenuByGroupsInList) {
                // Exclusive alternative: all about groups ind drop-down manu
                sx_getAboutHeaderMenuByGroupInList($str_TextsAboutTitle);
            } elseif (sx_AboutHeaderMenuByGroups) {
                // Non-exclusiv: Only About Groups marked to be placed at start are shown, 
                // Eventually, with links to About Texts as drop-down menu
                // The rest are to be placed at the end of the menu
                sx_getAboutMenu_HeaderGroups($radio_ShowAboutTextsInHeader, true);
            } else { ?>
                <li><a href="about.php"><?= $str_TextsAboutTitle ?></a></li>
                <?php
            }
        }
        if (defined('SX_Use_Extra_Tools') && SX_Use_Extra_Tools) {
            if (defined('SX_Inclued_Extra_Tools_In_Main_Menu') && SX_Inclued_Extra_Tools_In_Main_Menu) {
                if ($radio_IncludeExternalSource && !empty($str_ExternalSourceTitle) && !empty($str_ExternalSourceURL)) { ?>
                    <li><a href="<?php echo $str_ExternalSourceURL ?>" target="_blank"><?php echo $str_ExternalSourceTitle ?></a></li>
                <?php
                }
                if ($radio_IncludeTextInMap && !empty($str_TextInMapTitle)) { ?>
                    <li><a href="map_periegesis.php"><?php echo $str_TextInMapTitle ?></a></li>
                <?php
                }
                if ($radio_IncludeMapTools && !empty($str_MapToolsTitle)) { ?>
                    <li><a href="map_search.php"><?php echo $str_MapToolsTitle ?></a></li>
                <?php
                }
            }
        }

        if ($radio_IncludeArticles && sx_radioUseArticles) {
            if (sx_radioArticleMenuByArticle) {
                $aResults = sx_getRowsNavByArticles();
                if (is_array($aResults)) {
                    sx_getListNavByArticles($aResults, $strNavPath);
                }
            } else {
                $aResults = sx_getRowsNavByArticleClasses();
                if (is_array($aResults)) {
                    sx_getListNavByArticleClasses($aResults, $strNavPath);
                }
            }
            $aResults = null;
        }
        if ($radio_UseTextsAbout) {
            if (sx_ShowAboutHeaderMenuAtStart == false) {
                if (sx_AboutHeaderMenuByGroupsInList) {
                    // Exclusive alternative: all About Groups in drop-down manu
                    sx_getAboutHeaderMenuByGroupInList($str_TextsAboutTitle);
                } elseif (sx_AboutHeaderMenuByGroups) {
                    // Exclusiv: All About Groups are shown as links at the end of the menu
                    // Eventually, with links to About Texts as drop-down menu
                    sx_getAboutMenu_HeaderGroups($radio_ShowAboutTextsInHeader, false, true);
                } else { ?>
                    <li><a href="about.php"><?= $str_TextsAboutTitle ?></a></li>
            <?php
                }
            } elseif (sx_AboutHeaderMenuByGroups) {
                // Non-exclusiv: The rest of About Groups to be placed at the end
                // Eventually, with links to About Texts as drop-down menu
                sx_getAboutMenu_HeaderGroups($radio_ShowAboutTextsInHeader, false);
            }
        }
        if (sx_includeForum && $radio_IncludeForum && !empty($str_ForumLinkTitle)) { ?>
            <li><a href="forum.php"><?= $str_ForumLinkTitle ?></a></li>
        <?php
        }

        if ($radio_UseEvents) { ?>
            <li><a href="events.php"><?= $str_EventsMenuTitle ?></a></li>
        <?php
        }
        if ($radio_UseMembersList) { ?>
            <li><a href="about.php?members=yes"><?= $str_MembersListTitle ?></a></li>
        <?php
        }
        if (sx_IncludeTopContact == false) { ?>
            <li><a href="contact.php"><?= lngContact ?></a></li>
        <?php
        }
        if ($radio_UseLinks && sx_IncludeNavLinks && !sx_IncludeTopLinks) { ?>
            <li><a href="links.php"><?= $str_LinksTitle ?></a></li>
        <?php
        }
        if (sx_IncludeNavSearch && $radio_UseSearch) { ?>
            <li><a href="search.php?clear=yes"><?= lngSearch ?></a></li>
        <?php
        }
        if (defined('SX_IncludeWikidataSearch') && SX_IncludeWikidataSearch) { ?>
            <li><a href="search_wikidata.php?clear=yes"><?= lngSearch ?> Wikidata</a></li>
        <?php
        } ?>
    </ul>
</div>
<?php
?>