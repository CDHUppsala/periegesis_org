<?php
/**
 * Obs! Comon queries both Header and Main Navigation
 * Get Rows for Different Navigations Levels:
 *      Groups, Catagories and SubCatagories
 */

function sx_getRowsNavBySubCategories()
{
    $conn = dbconn();
    $sql = "SELECT 
    g.GroupID,
    g.GroupName" . str_LangNr . " AS GroupName,
    c.CategoryID,
    c.CategoryName" . str_LangNr . " AS CategoryName,
    sc.SubCategoryID,
    sc.SubCategoryName" . str_LangNr . " AS SubCategoryName
    FROM ((text_groups g
        LEFT JOIN text_categories c ON ((g.GroupID = c.GroupID)))
        LEFT JOIN text_subcategories sc ON ((c.CategoryID = sc.CategoryID)))
    WHERE ((g.Hidden = 0)
        AND ((c.Hidden = 0)
        OR (c.Hidden IS NULL))
        AND ((sc.Hidden = 0)
        OR (sc.Hidden IS NULL))) "  . str_LoginToReadAnd_Grupp ."
    ORDER BY g.Sorting DESC , g.GroupID , c.Sorting DESC , c.CategoryID , sc.Sorting DESC , sc.SubCategoryID ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($row) {
        return $row;
    }else{
        return Null;
    }
}

function sx_getRowsNavByCategories()
{
    $conn = dbconn();
    $sql = "SELECT 
        g.GroupID,
        g.GroupName" . str_LangNr . " AS GroupName,
        c.CategoryID AS CategoryID,
        c.CategoryName" . str_LangNr . " AS CategoryName
    FROM (text_groups g
        LEFT JOIN text_categories c ON ((g.GroupID = c.GroupID)))
    WHERE ((g.Hidden = 0)
        AND ((c.Hidden = 0)
        OR (c.Hidden IS NULL))) "  . str_LoginToReadAnd_Grupp ."
    ORDER BY g.Sorting DESC , g.GroupID , c.Sorting DESC , c.CategoryID";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($row) {
        return $row;
    }else{
        return Null;
    }
}

function sx_getRowsNavByGroups()
{
    $conn = dbconn();
    $sql = "SELECT GroupID, GroupName" . str_LangNr . " AS GroupName
        FROM text_groups
        WHERE Hidden = False " . str_LoginToReadAnd . "
        ORDER BY Sorting DESC, GroupID ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($row) {
        return $row;
    }else{
        return Null;
    }
}

function sx_getComingConferences() {
    $conn = dbconn();
    $sql = "SELECT ConferenceID, Title,
        CONCAT(StartDate, ' ', EndDate) AS Conference_Date
    FROM  conferences
    WHERE Hidden = 0 AND EndDate >= ? 
    ORDER BY StartDate DESC, ConferenceID ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([date('Y-m-d')]);
    $rs = $stmt->fetchAll();
    if ($rs) {
        return $rs;
    }else{
        return null;
    }
}
