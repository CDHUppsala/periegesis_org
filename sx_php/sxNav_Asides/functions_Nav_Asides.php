<?php
/*
 * ========================================================
 * Various lists that can be used as Sole Menus but also
 * included in both Tab and Accordion Menus:
 *  - Texts by Authors
 *  - Texts by Themes
 *  - Recent Texts
 *  - Most Read Texts
 *  - Recent Comments
 *  - Moste Commented Texts
 * ========================================================
 */

/**
 * Creates an accordion list of all Text Authors, classified in alphabetic order
 * @param string $displayAuthors : if the list will be displayed or not
 * @param int $id : The ID of the current author, just to mark it in the list
 * @return void : HTML List with links to Author ID
 */
function sx_GetNavAuthors($displayAuthors, $id = 0)
{
    if (empty($id) || !is_numeric($id)) {
        $id = 0;
    }
    $incResults = "";
    $conn = dbconn();
    $sql = "SELECT DISTINCT AuthorID, FirstName, LastName 
        FROM text_authors 
        WHERE Hidden = False ORDER BY lastName ASC ";
    $stmt = $conn->query($sql);
    $incResults = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null; ?>
    <ul style="display:<?= $displayAuthors ?>">
        <?php
        $radioSubLevel = false;
        if (is_array($incResults)) {
            $irs = count($incResults);
            $LastLetter = "";
            for ($ir = 0; $ir < $irs; $ir++) {
                $FirstName = $incResults[$ir][1];
                $LastName = $incResults[$ir][2];
                $LoopLetter = sx_RemovetGreekAcents(mb_substr(mb_strtoupper($LastName), 0, 1));

                if (sx_ListAuthorsByAlphabet) {
                    if ($LoopLetter != $LastLetter) {
                        if ($ir > 0) {
                            echo "</ul></li>";
                        }
                        echo '<li><div>' . $LoopLetter . '</div><ul style="display: none">';
                        $radioSubLevel = true;
                    }
                    $LastLetter = $LoopLetter;
                }

                $iLoopID = $incResults[$ir][0];
                $sClass = "";
                if ($id == $iLoopID) {
                    $sClass = 'class="open" ';
                } ?>
                <li><a <?= $sClass ?>href="texts.php?authorID=<?= $iLoopID ?>"><?= $LastName . " " . $FirstName ?></a></li>
            <?php }
        } else { ?>
            <li><?= lngNoRecords ?></li>
        <?php
        }
        if ($radioSubLevel) {
            echo "</ul></li>";
        } ?>
    </ul>
<?php
    $incResults = "";
}

/**
 * Creates an accordion list of all Text Themse, eventually classified by Theme Group
 * @param string $displayThemes : if the list will be displayed or not
 * @param int $id : Current Theme ID, just to mark it in the list
 * @param bool $radioAll : if the list will inlude all themes or just from the last 2 years
 * @return void
 */
function sx_GetNavThemes($displayThemes, $id = 0, $radioAll = true)
{
    $strWhere = " AND LastInDate >= '" . (date('Y') - 2) . "-01-01'";
    if ($radioAll) {
        $strWhere = "";
    }
    if (empty($id) || !is_numeric($id)) {
        $id = 0;
    }
    $incResults = "";
    $conn = dbconn();
    $sql = "SELECT t.ThemeID, 
        t.ThemeName" . str_LangNr . " AS ThemeName, 
        t.LastInDate,
        t.InsertDate,
		t.ThemeGroupID,
		g.ThemeGroupName" . str_LangNr . " AS ThemeGroupName
    FROM themes AS t
        LEFT JOIN theme_groups AS g
			ON t.ThemeGroupID = g.ThemeGroupID
    WHERE t.Hidden = False ";
    if (sx_includeThemeListsByGroup) {
        $sql .= " ORDER BY t.ThemeGroupID, g.Sorting DESC, t.ThemeName" . str_LangNr;
    } else {
        if (sx_includeThemeListsByYear) {
            $sql .= " ORDER BY YEAR(t.LastInDate) DESC, t.Sorting DESC, t.ThemeName" . str_LangNr;
        } else {
            $sql .= $strWhere
                . " ORDER BY t.Sorting DESC, t.ThemeName" . str_LangNr;
        }
    }
    //echo $sql;
    $stmt = $conn->query($sql);
    $incResults = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null; ?>

    <ul style="display:<?= $displayThemes ?>">
        <?php
        if (is_array($incResults)) {
            $irs = count($incResults);
            $iLastYear = 0;
            $iLastGroupID = 0;
            $iLoopYear = 0;
            $radioSubLevel = false;

            for ($ir = 0; $ir < $irs; $ir++) {
                $iThemeID = $incResults[$ir][0];
                $sThemeName = $incResults[$ir][1];
                $dLastInDate = $incResults[$ir][2];
                $dInsertDate = $incResults[$ir][3];
                $iLoupGroupID = (int) $incResults[$ir][4];
                $sThemeGroupName = $incResults[$ir][5];
                if (int_ThemeGroupID > 0 && int_ThemeGroupID == $iLoupGroupID) {
                    $strClass = ' class="open"';
                    $strDisplay = 'block';
                } else {
                    $strClass = '';
                    $strDisplay = 'none';
                }

                if (sx_includeThemeListsByGroup) {
                    if ($iLoupGroupID != $iLastGroupID) {
                        if ($ir > 0) {
                            echo "</ul></li>";
                        }
                        echo '<li><div' . $strClass . '>' . $sThemeGroupName . '</div><ul style="display: ' . $strDisplay . '">';
                        $radioSubLevel = true;
                    }
                    $iLastGroupID = $iLoupGroupID;
                } elseif (sx_includeThemeListsByYear) {
                    if (return_Is_Date($dLastInDate)) {
                        $iLoopYear = return_Year($dLastInDate);
                        if ($iLoopYear != $iLastYear) {
                            if ($ir > 0) {
                                echo "</ul></li>";
                            }
                            echo '<li><div' . $strClass . '>' . $iLoopYear . '</div><ul style="display: ' . $strDisplay . '">';
                            $radioSubLevel = true;
                        }
                        $iLastYear = $iLoopYear;
                    }
                }
                $sClass = "";
                if ($id == $iThemeID) {
                    $sClass = 'class="open" ';
                }
                $strIncertDate = "";
                if (return_Is_Date($dInsertDate) && sx_ShowThemeYearInList) {
                    $strIncertDate = " <span>(" . lngFrom . " " . return_Year($dInsertDate) . ")</span>";
                } ?>
                <li><a <?= $sClass ?>href="texts.php?themeID=<?= $iThemeID ?>"><?= $sThemeName . $strIncertDate ?></a></li>
            <?php
            }
            if ($radioSubLevel) {
                echo "</ul></li>";
            }
        } else { ?>
            <li><?= lngNoRecords ?></li>
        <?php
        } ?>
    </ul>
<?php
    $incResults = "";
}

/**
 * Creates a list of X number of recent text, with the X defined dynamically
 * @param mixed $displayRecentTexts
 * @return void
 */
function sx_GetNavTextsRecent($displayRecentTexts)
{
    $incResults = "";
    $conn = dbconn();
    $sql = "SELECT
			t.TextID, t.Title, t.PublishedDate, a.FirstName, a.LastName, t.Coauthors, t.HideDate 
		FROM " . sx_TextTableVersion . " AS t 
			LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID 
		WHERE Publish = True " . str_LanguageAnd . "
		ORDER BY t.PublishOrder DESC, t.PublishedDate DESC, t.TextID DESC " . str_LimitFirstPage;
    $stmt = $conn->query($sql);
    $incResults = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null; ?>
    <ul style="display:<?= $displayRecentTexts ?>">
        <?php
        if (is_array($incResults)) {
            $irs = count($incResults);
            for ($ir = 0; $ir < $irs; $ir++) {
                $strName = $incResults[$ir][3] . " " . $incResults[$ir][4];
                $sCoauthors = $incResults[$ir][5];
                if ($strName != "") {
                    $strName = $strName . ", ";
                }
                if ($sCoauthors != "") {
                    $strName = $strName . $sCoauthors . ", ";
                } ?>
                <li><a href="texts.php?tid=<?= $incResults[$ir][0] ?>&nav=rt"><?= $incResults[$ir][1] . " <span>" . $strName . $incResults[$ir][2] ?></span> </a></li>
            <?php
            }
        } else { ?>
            <li><?= lngNoRecords ?></li>
        <?php
        } ?>
    </ul>
<?php
    $incResults = "";
}

/**
 * Creates a list of X number of moste read text, with the X defined dynamically
 * @param mixed $displayMostRead
 * @return void
 */
function sx_GetNavTextsMost($displayMostRead)
{
    $incResults = "";
    global $i_CommentableDays;
    $conn = dbconn();
    $sql = "SELECT s.TextID, s.TotalVisits, 
				t.Title, t.PublishedDate, a.FirstName, a.LastName, t.Coauthors 
			FROM (visits_texts AS s 
				INNER JOIN " . sx_TextTableVersion . " AS t 
				ON s.TextID = t.TextID) 
				LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID 
			WHERE (s.PublishedDate >= '" . date("Y-m-d", strtotime(-$i_CommentableDays . "days")) . "') " . str_LimitFirstPage;
    $stmt = $conn->query($sql);
    $incResults = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null;
?>
    <ul style="display:<?= $displayMostRead ?>">
        <?php
        if (is_array($incResults)) {
            $irs = count($incResults);
            for ($ir = 0; $ir < $irs; $ir++) {
                $iTextID = $incResults[$ir][0];
                $iTotalVisits = $incResults[$ir][1];
                $sTitle = $incResults[$ir][2];
                $dPublishedDate = $incResults[$ir][3];
                $strName = $incResults[$ir][4] . " " . $incResults[$ir][5];
                $sCoauthors = $incResults[$ir][6];
                if ($strName != "") {
                    $strName = $strName . ", ";
                }
                if ($sCoauthors != "") {
                    $strName = $strName . $sCoauthors . ", ";
                } ?>
                <li title="<?= lngTotal . ": " . $iTotalVisits ?>"><a href="texts.php?tid=<?= $iTextID ?>&nav=mr"><?= $sTitle . " <span>" . $strName . $dPublishedDate ?></span></a></li>
            <?php
            }
        } else {
            ?><li class="list"><?= lngNoRecords ?></li>
        <?php
        } ?>
    </ul>
<?php
    $incResults = "";
}

/**
 * Creates a list of X number of recently commented text, with the X defined dynamically
 * @param mixed $displayRecentBlogs
 * @return void
 */
function sx_getNavCommentsRecent($displayRecentBlogs)
{
    $incResults = "";
    $conn = dbconn();
    $sql = "SELECT InsertID, TextID, FirstName, LastName, Title 
	FROM text_comments 
	WHERE Visible = 1 
    ORDER BY InsertID DESC " . str_LimitInList;
    $stmt = $conn->query($sql);
    $incResults = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null;
?>
    <ul style="display:<?= $displayRecentBlogs ?>">
        <?php
        if (is_array($incResults)) {
            $irs = count($incResults);
            for ($ir = 0; $ir < $irs; $ir++) {
                $i_InsertID = $incResults[$ir][0];
                $i_TextID = $incResults[$ir][1];
                $s_Name = $incResults[$ir][2] . " " . $incResults[$ir][3];
                $s_Title = $incResults[$ir][4]; ?>
                <li><a href="texts.php?tid=<?= $i_TextID . "&nav=rb&anchor=" . $i_InsertID . "#" . $i_InsertID ?>"><?= $s_Title ?> <span><?= $s_Name ?></span></a></li>
            <?php
            }
        } else { ?>
            <li><?= lngNoRecords ?></li>
        <?php
        } ?>
    </ul>
<?php
    $incResults = "";
}

/**
 * Creates a list of X number of moste commented text, with the X defined dynamically
 * @param mixed $displayMostBloged
 * @return void
 */
function sx_getNavCommentsMost($displayMostBloged)
{
    global $i_CommentableDays;
    $incResults = "";
    $conn = dbconn();
    $sql = "SELECT DISTINCTROW
		cm.TextID, t.Title, t.PublishedDate, a.FirstName, a.LastName, 
		Count(cm.InsertID) AS Inserts 
	FROM (text_comments AS cm 
		INNER JOIN " . sx_TextTableVersion . " AS t ON cm.TextID = t.TextID) 
		LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID 
	WHERE (t.PublishedDate >= '" . date("Y-m-d", strtotime(-$i_CommentableDays . "days")) . "') 
	GROUP BY cm.TextID, t.Title, t.PublishedDate, a.FirstName, a.LastName 
    ORDER BY Count(cm.InsertID) DESC " . str_LimitInList;
    $stmt = $conn->query($sql);
    $incResults = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null; ?>
    <ul style="display:<?= $displayMostBloged ?>">
        <?php
        if (is_array($incResults)) {
            $irs = count($incResults);
            for ($ir = 0; $ir < $irs; $ir++) {
                $strName = $incResults[$ir][3];
                if ($strName != "") {
                    $strName = $strName . " " . $incResults[$ir][4] . ", ";
                } ?>
                <li><a href="texts.php?tid=<?= $incResults[$ir][0] ?>&nav=mb#comment">
                        <?= $incResults[$ir][1] ?> <span><?= $strName . $incResults[$ir][2] . " " . "[" . $incResults[$ir][5] . "]" ?></span></a></li>
            <?php
            }
        } else {
            ?>
            <li class="list"><?= lngNoRecords ?></li>
        <?php
        } ?>
    </ul>
<?php
    $incResults = "";
}

/**
 * For advance Text System:
 * Creates a list of X number of recent Aside Text or Texts from selected groups, with the X defined dynamically
 * @param mixed $display
 * @param mixed $iGroup
 * @return void
 */
function sx_RecentGroupsByAside($display, $iGroup = 0)
{
    $incResults = "";
    $strWhere = " AND PublishAside = True ";
    if (is_numeric($iGroup)) {
        if ((int)$iGroup > 0) {
            $strWhere = " AND GroupID = " . $iGroup . " ";
        }
    }
    $conn = dbconn();
    $sql = "SELECT
			t.TextID, t.Title, t.PublishedDate, a.FirstName, a.LastName, t.Coauthors, t.HideDate 
		FROM " . sx_TextTableVersion . " AS t 
			LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID 
		WHERE Publish = True " . $strWhere . str_LanguageAnd . "
		ORDER BY t.PublishOrder DESC, t.PublishedDate DESC, t.TextID DESC " . str_LimitFirstPage;
    $stmt = $conn->query($sql);
    $incResults = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null; ?>
    <ul style="display:<?= $display ?>">
        <?php
        if (is_array($incResults)) {
            $irs = count($incResults);
            for ($ir = 0; $ir < $irs; $ir++) {
                $strName = $incResults[$ir][3] . " " . $incResults[$ir][4];
                $sCoauthors = $incResults[$ir][5];
                if ($strName != "") {
                    $strName = $strName . ", ";
                }
                if ($sCoauthors != "") {
                    $strName = $strName . $sCoauthors . ", ";
                }
                $strClass = "";
                $iTextID = $incResults[$ir][0];
                if (int_TextID == $iTextID) {
                    $strClass = 'class="open" ';
                } ?>
                <li><a <?= $strClass ?>href="texts.php?tid=<?= $iTextID ?>&field=<?= $iGroup ?>"><?= $incResults[$ir][1] . " <span>" . $strName . $incResults[$ir][2] ?></span> </a></li>
            <?php
            }
        } else { ?>
            <li><?= lngNoRecords ?></li>
        <?php
        } ?>
    </ul>
<?php
    $incResults = "";
}
?>