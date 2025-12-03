<?php

/**
 * The application Items for Business Sphere
 * Get rows for items by Group
 */
function sx_getNavRowsForItemsByGroup()
{
    $conn = dbconn();
    $sLangNr = str_LangNr;
    $sql = "SELECT
        g.ItemGroupID,
        g.GroupName{$sLangNr} AS GroupName,
        i.ItemID,
        i.ItemTitle{$sLangNr} AS ItemTitle
    FROM (items i
        LEFT JOIN item_groups g ON i.ItemGroupID = g.ItemGroupID)
    WHERE i.Hidden = 0
        AND (g.Hidden = 0 OR g.Hidden IS NULL)
    ORDER BY g.Sorting DESC , g.ItemGroupID , i.Sorting DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;
    if ($row) {
        return $row;
    }else{
        return Null;
    }
}

/**
 * Nav Menu for the Items by Group (tables items and item_groups)
 * Uses the above function sx_getNavRowsForItemsByGroup()
 * @param array $arr : results from the databas
 * @param mixed $strNavPath : the name of the site page that opones the item 
 * @return void : HTML menu with one or two levels list of items
 * - If items are not grouped, returns one level list by single items
 * - If all items are grouped, returns a two level list by groups and their items
 * - If some items are grouped, returns first a two level list for the grouped items
 *   and then one level list for the rest of items, in this order
 */
function sx_getNavListForItemsByGroup($arr, $strNavPath)
{
    if (is_array($arr)) {
        $iLoop = 0;
        foreach ($arr as $row) {
            $iGroupID = (int) $row['ItemGroupID'];
            $strGroupName = $row['GroupName'];
            $iItemID = (int) $row['ItemID'];
            $strItemTitle = $row['ItemTitle'];

            if ($iGroupID != $iLoop) {
                if ($iLoop > 0) {
                    echo '</ul></li>';
                    $iLoop = 0;
                }
                if ($iGroupID > 0) {
                    echo "<li><span>{$strGroupName}</span><ul>";
                }
            }
            echo '<li><a href="' . $strNavPath . $iItemID . '">' . $strItemTitle . '</a></li>';
            if ($iGroupID > 0) {
                $iLoop = $iGroupID;
            }
        }
        if ($iLoop > 0) {
            echo '</ul></li>';
        }
    }
}

/**
 * Get items without classification in groups
 * As drop down list on the main manu
 */
function sx_getRowsNavForItems()
{
    $conn = dbconn();
    $sLangNr = str_LangNr;
    $sql = "SELECT
        ItemID,
        ItemTitle{$sLangNr} AS ItemTitle
    FROM items
        WHERE Hidden = 0 
    ORDER BY Sorting DESC, ItemID ASC ";
    $stmt = $conn->query($sql);
    $row = $stmt->fetchAll();
    $stmt = null;
    if ($row) {
        return $row;
    }else{
        return Null;
    }
}

/**
 * Menu for the Item System for Bisiness Sphere (table item)
 * Uses the above functions: sx_getRowsNavForItems()
 * @param array $arr : results from the databas
 * @param mixed $strNavPath : the name of the site page that opones the item 
 * @param string $strItemsTitle : A Common Title when Items are not classified in groups
 * @return void : HTML list with links to item
 */
function sx_getListNavForItems($arr, $strNavPath, $strItemsTitle)
{
    if (is_array($arr)) {
        $iRows = count($arr);
        echo '<li><span>' . $strItemsTitle . '</span><ul>';
        for ($r = 0; $r < $iRows; $r++) {
            $iItemID = (int) $arr[$r]['ItemID'];
            $strItemTitle = $arr[$r]['ItemTitle'];
            echo '<li><a href="' . $strNavPath . 'itemid=' . $iItemID . '">' . $strItemTitle . '</a></li>';
        }
        echo '</ul></li>';
    }
}