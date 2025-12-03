<?php

/** USED
 * X Rows by GROUP, CATEGORIES and SUBCATEGORIES
 * Optionally Filtered by Groups that are accessible only for Login Members
 * - if parameter $radio = true,
 *      - A separate menu will be shown with all groups (and their subclasses) that are accessible only for Login members
 * - if parameter $radio = false,
 *      - All groups (and their subclasses) that are accessible only for Non Login members will appear in the ordinary menu
 * Only for MySQL
 */

function sx_GetRecentTextsBySubCategories($radio)
{
    $conn = dbconn();
    $strLoginAliasAnd = str_LoginToReadAnd_Grupp;
    if ($radio) {
        $strLoginAliasAnd = " AND g.LoginToRead = TRUE ";
    }
    $sql = "SELECT 
        t.GroupID, 
        g.GroupName" . str_LangNr . " AS GroupName, 
        t.CategoryID, 
        c.CategoryName" . str_LangNr . " AS CategoryName, 
        t.SubCategoryID, 
        sc.SubCategoryName" . str_LangNr . " AS SubCategory_Name, 
        t.TextID, 
        t.Title, 
        t.PublishedDate,
        t.HideDate,
        a.FirstName, 
        a.LastName 
    FROM text_groups AS g 
        INNER JOIN 
        ((((SELECT 
            TextID, 
            GroupID, 
            CategoryID,
            SubCategoryID,
            AuthorID, 
            Title, 
            PublishedDate,
            HideDate,
            PublishOrder, 
            @g_count := IF(@current_group = GroupID, @g_count + 1, 1) AS g_count, 
                @current_group := GroupID, 
            @c_count := IF(CategoryID > 0, IF(@current_cat = CategoryID, @c_count + 1, 1),0) AS c_count, 
                @current_cat := CategoryID,
            @sc_count := IF(SubCategoryID > 0, IF(@current_scat = SubCategoryID, @sc_count + 1, 1),0) AS sc_count, 
                @current_scat := SubCategoryID 
            FROM " . sx_TextTableVersion . " 
            WHERE Publish = True " . str_LanguageAnd . " 
            ORDER BY GroupID, 
                CategoryID, 
                SubCategoryID, 
                PublishOrder DESC, 
                PublishedDate DESC, 
                TextID DESC) AS t 
        LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID) 
        LEFT JOIN text_categories AS c ON t.CategoryID = c.CategoryID) 
        LEFT JOIN text_subcategories AS sc ON t.SubCategoryID = sc.SubCategoryID)
            ON g.GroupID = t.GroupID 
    WHERE g.Hidden = False " . $strLoginAliasAnd . "
        AND ((t.g_count <= " . int_MaxInListArticles . " AND t.c_count = 0 AND t.sc_count = 0) 
            OR (t.c_count <= " . int_MaxInListArticles . " AND t.c_count > 0 AND t.sc_count = 0)
            OR (t.sc_count <= " . int_MaxInListArticles . " AND t.sc_count > 0)) 
    ORDER BY g.Sorting DESC, 
        g.GroupID, 
        c.Sorting DESC, 
        c.CategoryID, 
        sc.Sorting DESC, 
        sc.SubCategoryID, 
        t.PublishOrder DESC, 
        t.PublishedDate DESC, 
        t.TextID DESC ";
    //echo $sql;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($row) {
        return $row;
    } else {
        return null;
    }
}

/** USED
 * X Rows by GROUP and CATEGORIES
 * Optionally Filtered by Group Login (parameter $radio = true)
 */

function sx_GetRecentTextsByCategories($radio)
{
    /**
     * $radio = True    - Creates a Separate Members Menu including Only Hidden Groups (and their Categories).
     * $radio = False   - Creates an ordinary menu
     *                  - if members are logged in, it will including Hidden Groups (and their Categories)
     */
    return sx_GetRecentTextsByCategories_MySQL($radio);
    /*
    if (sx_radioMySQLDatabase) {
        return sx_GetRecentTextsByCategories_MySQL($radio);
    } else {
        return sx_GetRecentTextsByCategories_Top($radio);
    }
    */
}
function sx_GetRecentTextsByCategories_MySQL($radio)
{
    $conn = dbconn();
    $strLoginAliasAnd = str_LoginToReadAnd_Grupp;
    if ($radio) {
        $strLoginAliasAnd = " AND g.LoginToRead = TRUE ";
    }
    $sql = "SELECT 
        t.GroupID, 
        g.GroupName" . str_LangNr . " AS GroupName, 
        t.CategoryID, 
        c.CategoryName" . str_LangNr . " AS CategoryName, 
        t.TextID, 
        t.Title, 
        t.PublishedDate, 
        a.FirstName, 
        a.LastName 
    FROM text_groups AS g 
        INNER JOIN 
        (((SELECT LanguageID, 
            TextID, 
            GroupID, 
            CategoryID, 
            AuthorID, 
            Title, 
            PublishedDate, 
            PublishOrder, 
            @g_count := IF(@current_group = GroupID, @g_count + 1, 1) AS g_count, 
                @current_group := GroupID, 
            @c_count := IF(CategoryID > 0, IF(@current_cat = CategoryID, @c_count + 1, 1),0) AS c_count, 
                @current_cat := CategoryID 
            FROM " . sx_TextTableVersion . " 
            WHERE Publish = True " . str_LanguageAnd . " 
            ORDER BY GroupID, 
                CategoryID, 
                PublishOrder DESC, 
                PublishedDate DESC, 
                TextID DESC) AS t 
        LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID) 
        LEFT JOIN text_categories AS c ON t.CategoryID = c.CategoryID) 
            ON g.GroupID = t.GroupID 
    WHERE g.Hidden = False " . $strLoginAliasAnd . "
        AND ((g_count <= " . int_MaxInListArticles . " AND c_count = 0) 
        OR (c_count <= " . int_MaxInListArticles . " AND c_count >= 1)) 
    ORDER BY g.Sorting DESC, 
        g.GroupID, 
        c.Sorting DESC, 
        c.CategoryID, 
        t.PublishOrder DESC, 
        t.PublishedDate DESC, 
        t.TextID DESC ";
    //echo $sql;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($row) {
        return $row;
    } else {
        return null;
    }
}

/** Not Used
 * Only for Access and might be MSSQL
 * MySQL 5.7 doesn't yet support LIMIT & IN/ALL/ANY/SOME subquery
 */
function sx_GetRecentTextsByCategories_Top($radio)
{
    $conn = dbconn();
    $strLoginAliasAnd = str_LoginToReadAnd_Grupp;
    if ($radio) {
        $strLoginAliasAnd = " AND g.LoginToRead = TRUE ";
    }
    $sql = "SELECT
    t.GroupID AS GroupID, 
    g.GroupName" . str_LangNr . " AS GroupName, 
    t.CategoryID AS CategoryID, 
    c.CategoryName" . str_LangNr . " AS CategoryName, 
    t.TextID AS TextID, 
    t.Title AS Title, 
    t.PublishedDate AS PublishedDate, 
    a.FirstName AS FirstName, 
    a.LastName AS LastName 
    FROM text_groups g 
       INNER JOIN ((" . sx_TextTableVersion . " t 
       LEFT JOIN text_authors a ON t.AuthorID = a.AuthorID) 
       LEFT JOIN text_categories c ON t.CategoryID = c.CategoryID) 
         ON g.GroupID = t.GroupID 
    WHERE t.TextID IN (
       SELECT TextID 
         FROM " . sx_TextTableVersion . "
         WHERE GroupID = t.GroupID 
          AND (CategoryID = t.CategoryID OR CategoryID IS NULL OR CategoryID = 0) 
          AND (PublishedDate <= '" . date("Y-m-d") . "') 
          AND (Publish = True) " . str_LanguageAnd . "
         ORDER BY PublishOrder DESC, PublishedDate DESC, TextID DESC " . str_LimitInList . ") 
       AND (g.Hidden = False) " . $strLoginAliasAnd . "
       AND (c.Hidden = False OR c.Hidden IS NULL) 
    ORDER BY g.Sorting DESC, 
       g.GroupID, 
       c.Sorting DESC,
       c.CategoryID, 
       t.PublishOrder DESC,
       t.PublishedDate DESC, 
       t.TextID DESC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($row) {
        return $row;
    } else {
        return null;
    }
}


/** USED
 * X Number of Rows by GROUP
 */

function sx_GetRecentTextsByGroups()
{
    return  sx_GetRecentTextsByGroups_MySQL();
    /*
    if (sx_radioMySQLDatabase) {
        return  sx_GetRecentTextsByGroups_MySQL();
    } else {
        return  sx_GetRecentTextsByGroups_Top();
    }
    */
}

// Can be used by all databases exept ms access
function sx_GetRecentTextsByGroups_MySQL()
{
    $strWhere = "";
    // strWhere = " AND t.PublishInFirstPage = True "
    $conn = dbconn();
    $sql = "SELECT 
       t.GroupID, 
        g.GroupName" . str_LangNr . " AS GroupName, 
        t.TextID, 
        t.Title, 
        t.PublishedDate, 
        a.FirstName, 
        a.LastName 
    FROM text_groups AS g 
        INNER JOIN 
            ((SELECT LanguageID, 
            GroupID, 
            AuthorID, 
            TextID, 
            Title, 
            PublishedDate, 
            PublishOrder, 
            @g_count := IF(@current_group = GroupID, @g_count + 1, 1) AS g_count, 
                @current_group := GroupID 
            FROM " . sx_TextTableVersion . " 
            WHERE Publish = True " . $strWhere . str_LanguageAnd . "
            ORDER BY GroupID, 
                PublishOrder DESC, 
                PublishedDate DESC, 
                TextID DESC) AS t 
        LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID) 
            ON g.GroupID = t.GroupID 
    WHERE g.Hidden = False " . str_LoginToReadAnd_Grupp . "
        AND g_count <= " . int_MaxInListArticles . "
    ORDER BY g.Sorting DESC, 
        g.GroupID, 
        t.PublishOrder DESC, 
        t.PublishedDate DESC, 
        t.TextID DESC ";
    //echo $sql;
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($row) {
        return $row;
    } else {
        return null;
    }
}

/** Not Used
 * Only for Access and might be MSSQL
 * - Not MySQL (5.7 do not accepts LIMIT in IN-subquery)
 */
function sx_GetRecentTextsByGroups_Top()
{
    $strWhere = "";
    //strWhere = " AND PublishInFirstPage = True "
    $conn = dbconn();
    $sql = "SELECT 
        t.GroupID AS GroupID, 
        g.GroupName" . str_LangNr . " AS GroupName, 
        t.TextID AS TextID, 
        t.Title AS Title, 
        t.PublishedDate AS PublishedDate, 
        a.FirstName AS FirstName, 
        a.LastName AS LastName 
    FROM text_groups g 
        INNER JOIN (" . sx_TextTableVersion . " t LEFT JOIN text_authors a ON t.AuthorID = a.AuthorID) 
            ON g.GroupID = t.GroupID 
    WHERE t.TextID IN (
        SELECT TextID 
            FROM " . sx_TextTableVersion . "
            WHERE GroupID = t.GroupID 
                AND (PublishedDate <= '" . date("Y-m-d") . "') 
                AND (Publish = True) " . $strWhere . str_LanguageAnd . "
            ORDER BY PublishOrder DESC, PublishedDate DESC, TextID DESC " . str_LimitInList . ") 
        AND (g.Hidden = False) " . str_LoginToReadAnd_Grupp . "
    ORDER BY g.Sorting DESC, t.GroupID ASC, t.PublishOrder DESC, t.PublishedDate DESC, t.TextID DESC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($row) {
        return $row;
    } else {
        return null;
    }
}


 /** NOT USED ANYWHER
 * Returns a Menu with All rows of the Text Table!
 * To be Used for information sites where all information texts are available in the manu
*/
function sx_GetAllTextsBySubCategories()
{
    $conn = dbconn();
    $sql = "SELECT 
        t.GroupID, 
        g.GroupName" . str_LangNr . " AS Group_Name, 
        t.CategoryID, 
        c.CategoryName" . str_LangNr . " AS Category_Name, 
        t.SubCategoryID, 
        sc.SubCategoryName" . str_LangNr . " AS SubCategory_Name, 
        t.TextID, 
        t.Title, 
        t.PublishedDate, 
        t.HideDate,
        a.FirstName, 
        a.LastName
    FROM (((" . sx_TextTableVersion . " AS t 
        INNER JOIN text_groups AS g ON t.GroupID = g.GroupID) 
        LEFT JOIN text_categories AS c ON t.CategoryID = c.CategoryID) 
        LEFT JOIN text_subcategories AS sc ON t.SubCategoryID = sc.SubCategoryID) 
        LEFT JOIN text_authors AS a ON t.authorID = a.AuthorID 
    WHERE t.Publish = True 
        AND (t.PublishedDate <= '" . date('Y-m-d') . "' OR (t.PublishedDate) IS NULL)
        " . str_LoginToReadAnd_Grupp . str_LanguageAnd . "
    ORDER BY g.Sorting DESC, 
        g.GroupID, 
        c.Sorting DESC, 
        c.CategoryID, 
        sc.Sorting DESC, 
        sc.SubCategoryID, 
        t.PublishOrder DESC, 
        t.PublishedDate DESC, 
        t.TextID DESC ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_NUM);
    if ($row) {
        return $row;
    } else {
        return null;
    }
}
