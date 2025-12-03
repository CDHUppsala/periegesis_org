<?php

/**
 * ===========================================================
 * Functions to selected About and Article Texts for the header or footer menus
 * ===========================================================
 */

/**
 * Get an array of About texts for Header or Footer menus,
 *      either by an About Group ID or not (Group ID = 0) 
 *       Is called from function sx_getAboutMenu_HeaderFooterTexts()
 * @param string $place : the place of the manu, header or footer
 * @param int $groupid : the ID of the About Group, zero (0) returns all
 *      texts that are marked to be published in requested place 
 * @return array : an array with database results
 */
function sx_returnAboutRows_HeaderFooterTexts($place, $groupid = 0)
{
    $place = sx_GetSanitizedLatinLetters($place);
    $strAndWhere = "";
    if ((int)$groupid > 0) {
        $strAndWhere = " AND AboutGroupID = " . (int) $groupid;
    }
    if ($place == "footer") {
        $strAndWhere .= " AND FooterMenu = True ";
    } else {
        $strAndWhere .= " AND HeaderMenu = True ";
    }
    $conn = dbconn();
    $s_return = null;
    $sql = "SELECT AboutID, AboutGroupID, Title 
        FROM about 
        WHERE Hidden = False " . $strAndWhere . str_LanguageAnd . "
        ORDER BY Sorting DESC, InsertDate DESC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rows) {
        $s_return = $rows;
    }
    $stmt = null;
    $rows = null;
    return $s_return;
}

/**
 * This function can be called in 2 different ways:
 *      - As a separate function, from the Footer of the site to create
 *        a menu of About Texts from one About Group
 *      - From the function sx_getAboutGroupsForHeader() to create
 *        links to one or more About Groups on the Header Menu
 * @param string $place : the place of the menu, on "footer" or "header".
 * @param int $groupid : the ID of the requested About Group, zero (0) returns all
 *      texts that are marked to be published in requested place.
 * @return mixed :  HTML menu lists as string.
 */
function sx_getAboutMenu_HeaderFooterTexts($place, $title, $groupid = 0)
{
    $arrAp = sx_returnAboutRows_HeaderFooterTexts($place, $groupid);
    if (is_array($arrAp)) {
        $iRows = count($arrAp);
        if ($place == "footer") {
            if (!empty($title)) { ?>
                <h4><?= $title ?></h4>
            <?php
            } ?>
            <ul class="text_links">
                <?php
                for ($r = 0; $r < $iRows; $r++) { ?>
                    <li>
                        <a href="about.php?agid=<?= $arrAp[$r][1] ?>&aboutid=<?= $arrAp[$r][0] ?>"><?= $arrAp[$r][2] ?></a>
                    </li>
                <?php
                } ?>
            </ul>
        <?php
        } else { ?>
            <li><span><?= $title ?></span>
                <ul>
                    <?php
                    for ($r = 0; $r < $iRows; $r++) { ?>
                        <li><a href="about.php?agid=<?= $arrAp[$r][1] ?>&aboutid=<?= $arrAp[$r][0] ?>"><?= $arrAp[$r][2] ?></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php
        }
    } elseif ($place == "header") {
        if (intval($groupid) > 0) { ?>
            <li><a href="about.php?agid=<?= $groupid ?>"><?= $title ?></a></li>
        <?php
        } else { ?>
            <li><a href="about.php"><?= $title ?></a></li>
        <?php
        }
    }
    $arrAp = null;
}

/**
 * Usually, on the headet menu, there is only one link that opens the About page
 *      which includes a side menu with all groups, categories and texts describing the site.
 * You can use this function for multiple but complementary purposes:
 *      - To show on the Header menu Links to one or more Groups of About texts.
 *      - To separate the place of About Groups between the left and right side
 *        of the Header menu (ususally before or efter the Text Groups,
 *        or enywhere you place this function).
 *      - To descide if the About Groups should be simple links or pop-down menus 
 *        showing links to seleced texts for corresponding Group.
 * @param bool $radioShowTexts
 *      - NO means that the Group will appear as link,
 *      - YES means that the group will appear as drop-down menu with selected texts
 * @param bool $radioLeft
 *      - YES means that About Groups for the Left side of the menu (Before Text Groups) will be requested.
 *      - NO will request all ATHER groups.
 * @return mixed : HTML menu lists as string.
 */
function sx_getAboutMenu_HeaderGroups($radioShowTexts = false, $radioLeft = false, $both_sides = false)
{
    $conn = dbconn();
    $sLangNR = str_LangNr;
    $sWhere = " AND PlaceBeforeTextGroups = 0 ";
    if ($radioLeft) {
        $sWhere = " AND PlaceBeforeTextGroups = 1 ";
    }
    if ($both_sides) {
        $sWhere = '';
    }
    $sql = "SELECT AboutGroupID, GroupName{$sLangNR} AS GroupName
        FROM about_groups
        WHERE Hidden = False $sWhere
        ORDER BY Sorting DESC, AboutGroupID ASC ";
    $stmt = $conn->query($sql);
    while ($frs = $stmt->fetch(PDO::FETCH_NUM)) {
        if ($radioShowTexts) {
            /**
             * Get from this group all texts that are marked to be published on header menu
             */
            sx_getAboutMenu_HeaderFooterTexts("header", $frs[1], $frs[0]);
        } else { ?>
            <li><a href="about.php?agid=<?= $frs[0] ?>"><?= $frs[1] ?></a></li>
        <?php
        }
    }
    $stmt = null;
}

function sx_getAboutHeaderMenuByGroupInList($sAboutTitle)
{
    $conn = dbconn();
    $sql = "SELECT AboutGroupID, GroupName" . str_LangNr . " AS GroupName
        FROM about_groups
        WHERE Hidden = False 
        ORDER BY Sorting DESC, AboutGroupID ASC ";
    $stmt = $conn->query($sql);
    if ($stmt) {
        echo "<li><span>$sAboutTitle</span> <ul>";
        while ($frs = $stmt->fetch(PDO::FETCH_NUM)) {  ?>
            <li><a href="about.php?agid=<?= $frs[0] ?>"><?= $frs[1] ?></a></li>
        <?php
        }
        echo '</ul></li>';
    }
    $stmt = null;
}

/**
 * Get records for footer articles
 * @return array|null
 */
function sx_returnFooterArticles()
{
    $conn = dbconn();
    $s_return = null;
    $sql = "SELECT ArticleID, Title 
        FROM articles 
        WHERE Hidden = False AND FooterMenu = True " . str_LanguageAnd . "
        ORDER BY Sorting DESC, InsertDate DESC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($rows) {
        $s_return = $rows;
    }
    $stmt = null;
    $rows = null;
    return $s_return;
}


/**
 * Get footer texts from the table articles (when the table about is not used, as usually in Business Sphere)
 * @param mixed $reqTitle : that title of the footer menu (the same as with the about table)
 * @return void
 */
function sx_getFooterArticles($title)
{
    $arrAp = sx_returnFooterArticles();
    if (is_array($arrAp)) {
        if (!empty(trim($title))) { ?>
            <h4><?= $title ?></h4>
        <?php
        } ?>
        <ul class="text_links">
            <?php
            $iRows = count($arrAp);
            for ($r = 0; $r < $iRows; $r++) { ?>
                <li>
                    <a href="articles.php?aid=<?= $arrAp[$r][0] ?>"><?= $arrAp[$r][1] ?></a>
                </li>
            <?php
            } ?>
        </ul>
<?php
    }
}
?>