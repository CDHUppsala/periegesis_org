<?php
// Count Sections - if more than 2 Sections, Created Aside navigation
function sx_checkMultiSections($arr, $rows, $key)
{
    $radioTemp = false;
    if (is_array($arr)) {
        $iTemp = 0;
        $iLoop = 0;
        for ($r = 0; $r < $rows; $r++) {
            $iThis = $arr[$r][$key];
            if ($iThis != $iTemp) {
                $iLoop++;
            }
            $iTemp = $iThis;
            if ($iLoop > 2) {
                $radioTemp = true;
                break;
            }
        }
    }
    return $radioTemp;
}

/** 
 * None, Items, About, Articles, ArticleGroups, ArticleCategories, Texts, TextGroups, TextCategories
 * Create the Button Link if the Path is ID Number to a Table
 */
function sx_getLinkToTableID($sTable, $LinkID, $sTitle, $linkClass)
{
    $class = "";
    if (!empty($linkClass)) {
        $class = ' class="' . $linkClass . '"';
    }
    if ($sTable == "Texts" || $sTable == "TextGroups" || $sTable == "TextCategories") {
        if ($sTable == "Texts") {
            $strQuery = "tid=" . $LinkID;
        } elseif ($sTable == "TextGroups") {
            $strQuery = "gid=" . $LinkID;
        } elseif ($sTable == "TextCategories") {
            $strQuery = "cid=" . $LinkID;
        }
        if (!empty($strQuery)) {
            return '<a href="texts.php?' . $strQuery . '"' . $class . '>' . $sTitle . '</a>';
        }
    } elseif ($sTable == "Articles" || $sTable == "ArticleGroups" || $sTable == "ArticleCategories") {
        if ($sTable == "Articles") {
            $strQuery = "aid=" . $LinkID;
        } elseif ($sTable == "ArticleGroups") {
            $strQuery = "agid=" . $LinkID;
        } elseif ($sTable == "ArticleCategories") {
            $strQuery = "acid=" . $LinkID;
        }
        if (!empty($strQuery)) {
            return '<a href="articles.php?' . $strQuery . '"' . $class . '>' . $sTitle . '</a>';
        }
    } elseif ($sTable == "About") {
        return '<a href="about.php?aboutid=' . $LinkID . '"' . $class . '>' . $sTitle . '</a>';
    } elseif ($sTable == "Items") {
        return '<a href="items.php?itemid=' . $LinkID . '"' . $class . '>' . $sTitle . '</a>';
    } elseif ($sTable == "Products" || $sTable == "Groups" || $sTable == "Categories" || $sTable == "SubCategories" || $sTable == "SubSubCategories") {
        if ($sTable == "Products") {
            $strQuery = "pid=" . $LinkID;
        } elseif ($sTable == "Groups") {
            $strQuery = "int0=" . $LinkID;
        } elseif ($sTable = "Categories") {
            $strQuery = "int1=" . $LinkID;
        } elseif ($sTable == "SubCategories") {
            $strQuery = "int2=" . $LinkID;
        } elseif ($sTable == "SubSubCategories") {
            $strQuery = "int3=" . $LinkID;
        }
        if (!empty($strQuery)) {
            return '<a href="products.php?' . $strQuery . '"' . $class . '>' . $sTitle . '</a>';
        }
    } else {
        return "";
    }
}

/**
 * Create a single Button Link 
 */
function sx_getLinkPath($Table, $Link, $sTitle, $linkClass)
{
    $Title = lngMore;
    if (!empty($sTitle)) {
        $Title = $sTitle;
    }

    if (strpos($Title, ".png") > 0 || strpos($Title, ".jpg") > 0 || strpos($Title, ".gif") > 0 || strpos($Title, ".svg") > 0 || strpos($Title, ".jpeg") > 0) {
        $Title = '<img alt="" src="../images/' . $Title . '">';
        $linkClass = "button";
    }

    if (return_Filter_Integer($Link) > 0) {
        if (!empty($Table)) {
            return sx_getLinkToTableID($Table, $Link, $Title, $linkClass);
        }
    } else {
        $sLeft = return_Left_Link_Tag($Link, $linkClass);
        return  $sLeft . $Title . '</a>';
    }
}

/**
 * Get the Button Links for Section Items
 */
function sx_getButtons($sTable, $mLink_1, $Title_1, $mLink_2, $Title_2)
{
    if ($sTable == "None") {
        $sTable = null;
    }

    $FirstLink = "";
    if (!empty($mLink_1)) {
        $linkClass = sx_ButtonSectionClass_1;
        $FirstLink = sx_getLinkPath($sTable, $mLink_1, $Title_1, $linkClass);
    }

    $SecondLink = "";
    if (!empty($mLink_2)) {
        $linkClass = sx_ButtonSectionClass_2;
        $SecondLink = sx_getLinkPath($sTable, $mLink_2, $Title_2, $linkClass);
    }

    if (!empty($FirstLink) || !empty($SecondLink)) { ?>
        <div class="absolute-button">
            <?php
            echo $FirstLink . $SecondLink;
            ?>
        </div>
<?php
    }
}
?>