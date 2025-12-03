<?php

/**
 * This query gets X number of published texts from the requested class and is used in the next function
 */
function sx_return_TextsByClass($whereClassID)
{
    $conn = dbconn();
    $sql = "SELECT t.TextID, t.Title, t.PublishedDate, t.HideDate, 
        t.Coauthors, a.FirstName, a.LastName, a.Photo,
        t.FirstPageMediaURL, t.TopMediaURL,
        IF(FirstPageText IS NULL,MainText,FirstPageText) AS ShortText
        FROM " . sx_TextTableVersion . " AS t  
        LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID
        WHERE Publish = True " . $whereClassID . str_LanguageAnd . "
        ORDER BY t.PublishOrder DESC, t.PublishedDate DESC, t.TextID DESC " . str_LimitFirstPage;
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

/**
 * Secondary Text Menu by LISTS or CARDS on the Footer of Articles to show:
 *      X number of links to recent articles from the last selected classification level of Texts
 * Can also include images and an introductory texts in the form of Cards by
 *      setting the variable sx_SelectedClassByCards to True
 */
$strWhere = "";
$strSubName = "";
if (intval($int_SubCatID) > 0) {
    $strWhere = " AND SubCategoryID = " . $int_SubCatID;
    $strSubName = "/ " . $str_CategoryName . "/ " . $str_SubCategoryName;
} elseif (intval($int_CatID)) {
    $strWhere = " AND CategoryID = " . $int_CatID;
    $strSubName = "/ " . $str_CategoryName;
} elseif (intval($int_GroupID)) {
    $strWhere = " AND GroupID = " . $int_GroupID;
}

$aResults = "";
if (!empty($str_GroupName)) {
    $aResults = sx_return_TextsByClass($strWhere);
}

if (is_array($aResults)) {
    if (sx_SelectedClassByCards) { ?>
        <section class="grid_cards_wrapper">
            <h2><?= $str_GroupName . $strSubName ?></h2>
            <?php
            sx_getTextInCards($aResults, false, 'cycler_nav_middle', 'move_left_right', false, '', ''); ?>
        </section>
    <?php
        /**
         * The following is just a list, just in case!
         */
    } else { ?>
        <section class="jqNavSideToBeCloned">
            <h2 class="head slide_up jqToggleNextRight"><span><?= $str_GroupName . $strSubName ?></span></h2>
            <nav class="nav_aside">
                <ul class="max_height">
                    <?php
                    $iRows = count($aResults);
                    for ($r = 0; $r < $iRows; $r++) {
                        $i_TextID = $aResults[$r][0];
                        $s_Title = $aResults[$r][1];
                        $d_PublishedDate = $aResults[$r][2];
                        $s_Coauthors = $aResults[$r][3];
                        $strNames = $aResults[$r][4];
                        if (!empty($strNames)) {
                            $strNames = $strNames . " " . $aResults[$r][5];
                            if (!empty($s_Coauthors)) {
                                $strNames = $strNames . ", " . $s_Coauthors;
                            }
                        }
                        if (!empty($strNames)) {
                            $strNames .= ", ";
                        }
                        $strNames .= $d_PublishedDate; ?>
                        <li><a href="texts.php?tid=<?= $i_TextID ?>"><?= $s_Title ?> <span><?= $strNames ?></span></a></li>
                    <?php
                    } ?>
                </ul>
            </nav>
        </section>
<?php
    }
    $aResults = null;
}
?>