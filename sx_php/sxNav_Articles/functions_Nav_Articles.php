<?php

/**
 * For Public Sphere: Articles
 * Get an array with Article Groups and Categories
 */
function sx_getRowsNavByArticleClasses()
{
    $conn = dbconn();
    $sLangNr = str_LangNr;
    $sql = "SELECT 
        g.ArticleGroupID,
        g.GroupName{$sLangNr} AS GroupName,
        c.ArticleCategoryID,
        c.CategoryName{$sLangNr} AS CategoryName
    FROM (article_groups g
        LEFT JOIN article_categories c ON ((g.ArticleGroupID = c.ArticleGroupID)))
    WHERE ((g.Hidden = 0)
        AND ((c.Hidden = 0) OR (c.Hidden IS NULL)))
    ORDER BY g.Sorting DESC , g.ArticleGroupID , c.Sorting DESC , c.ArticleCategoryID";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;
    if ($row) {
        return $row;
    } else {
        return Null;
    }
}

function sx_getListNavByArticleClasses($arr, $strNavPath)
{
    if (is_array($arr) && !empty($arr)) {
        $levelUL1 = false;
        $loop1 = 0;
        foreach ($arr as $row) {
            $intMenuGroupID = (int) $row['ArticleGroupID'];
            $strMenuGroupName = $row['GroupName'];
            $intMenuCatID = (int) $row['ArticleCategoryID'];
            $strMenuCatName = $row['CategoryName'];

            if ($intMenuGroupID !== $loop1) {
                if ($levelUL1) {
                    echo "</ul></li>";
                }
                // Group without categories
                if ($intMenuCatID === 0) {
                    echo '<li><a href="' . $strNavPath . 'agid=' . $intMenuGroupID . '">' . $strMenuGroupName . '</a></li>';
                    $levelUL1 = false;
                } else {
                    //To make Groups clickable, even if they have categories
                    if (SX_useLinksInArticleGroups) {
                        echo '<li><span><a href="' . $strNavPath . 'agid=' . $intMenuGroupID . '">' . $strMenuGroupName . '</a></span>';
                    } else {
                        echo "<li><span>{$strMenuGroupName}</span>";
                    }
                    $levelUL1 = true;
                    echo '<ul>';
                }
            }
            if ($intMenuCatID > 0) {
                echo '<li><span><a href="' . $strNavPath . 'acid=' . $intMenuCatID . '">' . $strMenuCatName . '</a></span>';
            }
            $loop1 = $intMenuGroupID;
        }
        if ($levelUL1) {
            echo "</ul></li>";
        }
    }
}

function sx_getRowsNavByArticles()
{
    $conn = dbconn();
    $sLangNr = str_LangNr;
    $sLanguageAnd = str_LanguageAnd;
    $sql = "SELECT
        g.ArticleGroupID,
        g.GroupName{$sLangNr} AS GroupName,
        c.ArticleCategoryID,
        c.CategoryName{$sLangNr} AS CategoryName,
        a.ArticleID,
        a.Title
    FROM (articles a
        LEFT JOIN article_groups g ON a.ArticleGroupID = g.ArticleGroupID)
        LEFT JOIN article_categories c ON a.ArticleCategoryID = c.ArticleCategoryID
    WHERE a.Hidden = 0
        AND (g.Hidden = 0 OR g.Hidden IS NULL)
        AND (c.Hidden = 0 OR c.Hidden IS NULL) {$sLanguageAnd}
    ORDER BY g.Sorting DESC , g.ArticleGroupID , c.Sorting DESC ,c.ArticleCategoryID , a.Sorting DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;
    if ($row) {
        return $row;
    } else {
        return Null;
    }
}
function sx_getListNavByArticles($arr, $strNavPath)
{
    if (is_array($arr) && !empty($arr)) {
        $levelUL1 = false;
        $levelUL2 = false;
        $loop1 = 0;
        $loop2 = 0;
        foreach ($arr as $row) {
            $intMenuGroupID = (int) $row['ArticleGroupID'];
            $strMenuGroupName = $row['GroupName'];
            $intMenuCatID = (int) $row['ArticleCategoryID'];
            $strMenuCatName = $row['CategoryName'];
            $intMenuArticleID = $row['ArticleID'];
            $strMenuTitle = $row['Title'];

            if ($intMenuGroupID !== $loop1) {
                if ($levelUL2) {
                    echo "</ul></li>";
                }
                $levelUL2 = false;
                if ($levelUL1) {
                    echo "</ul></li>";
                }
                //To make Groups clickable, even if they have categories
                if (SX_useLinksInArticleGroups) {
                    echo '<li><span><a href="' . $strNavPath . 'agid=' . $intMenuGroupID . '">' . $strMenuGroupName . '</a></span>';
                } else {
                    echo '<li><span>' . $strMenuGroupName . '</span>';
                }
                $levelUL1 = true;
                echo '<ul>';
            }
            if ($levelUL1 && $intMenuCatID !== $loop2) {
                if ($levelUL2) {
                    echo "</ul></li>";
                }
                if (SX_useLinksInArticleGroups) {
                    echo '<li><span><a href="' . $strNavPath . 'acid=' . $intMenuCatID . '">' . $strMenuCatName . '</a></span>';
                } else {
                    echo "<li><span>{$strMenuCatName}</span>";
                }
                $levelUL2 = true;
                echo '<ul>';
            }
            echo '<li><a href="' . $strNavPath . 'aid=' . $intMenuArticleID . '">' . $strMenuTitle . '</a></li>';
            $loop1 = $intMenuGroupID;
            $loop2 = $intMenuCatID;
        }
        if ($levelUL2) {
            echo "</ul></li>";
        }
        if ($levelUL1) {
            echo "</ul></li>";
        }
    }
}
