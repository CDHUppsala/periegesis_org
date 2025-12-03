<?php

/**
 * Foooter Navigations by Lists or Cards
 */
function sx_getNavTextsByTheme_Rows($id)
{
	$conn = dbconn();
	$sql = "SELECT t.TextID, t.Title, t.PublishedDate, t.HideDate, 
        t.Coauthors, a.FirstName, a.LastName, a.Photo,
        t.FirstPageMediaURL, t.TopMediaURL,
		IF(t.FirstPageText IS NULL,t.MainText,t.FirstPageText) AS ShortText,
        th.ThemeName" . str_LangNr . " AS ThemeName
        FROM (" . sx_TextTableVersion . " AS t 
        LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID) 
        INNER JOIN themes AS th ON t.ThemeID = th.ThemeID 
        WHERE t.ThemeID = ? AND t.Publish = True " . str_LanguageAnd_Text . "
        ORDER BY t.PublishedDate DESC , t.TextID DESC 
        LIMIT 8 ";
	//echo $sql;
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);
	$row = $stmt->fetchAll(PDO::FETCH_NUM);
	if ($row) {
		return $row;
	} else {
		return Null;
	}
}

/**
 * Secondary Text Menu by LISTS or CARDS on the Footer of Articles to show:
 * 		Texts from the same theme
 * Can also include images and an introductory texts in the form of Cards by
 *      setting the variable sx_SelectedThemeByCards to True
 */
if (empty($str_TextsByThemesTitle)) {
	$str_TextsByThemesTitle = lngThemes;
}
if (intval($iThemeID) > 0) {
	$aResults = sx_getNavTextsByTheme_Rows($iThemeID);
	if (is_array($aResults)) {
		if (sx_SelectedThemeByCards) { ?>
			<section class="grid_cards_wrapper">
				<h2><?= $str_TextsByThemesTitle . ': ' . $strThemeName ?></h2>
				<?php
				sx_getTextInCards($aResults, false, 'cycler_nav_bottom', 'move_left_right', true, 'themeID', $iThemeID);
				?>
			</section>
		<?php
			/**
			 * Thefollowing is just a list, just in case!
			 */
		} else { ?>
			<section class="jqNavSideToBeCloned">
				<h2 class="head slide_up jqToggleNextRight"><span><?= $str_TextsByThemesTitle . ': ' . $strThemeName ?></span></h2>
				<nav class="nav_aside">
					<ul class="max_height">
						<?php
						$iRows = count($aResults);
						for ($iRow = 0; $iRow < $iRows; $iRow++) {
							$iTextID = $aResults[$iRow][0];
							$sTitle = $aResults[$iRow][1];
							$strAuthorsName = "";
							if (!empty($aResults[$iRow][5])) {
								$strAuthorsName = ', ' . $aResults[$iRow][5] . " " . $aResults[$iRow][6];
							}
							$strCoauthors = $aResults[$iRow][4];
							if ($strCoauthors != "") {
								$strAuthorsName .= ", " . $strCoauthors;
							}
							if ($aResults[$iRow][3] == false) {
								$strAuthorsName .=  ", " . $aResults[$iRow][2];
							}
							$strClassView = "";
							if (intval(int_TextID) == intval($iTextID)) {
								$strClassView = 'class="open" ';
							} ?>
							<li>
								<a <?= $strClassView ?>href="texts.php?themeID=<?= $id ?>&tid=<?= $iTextID ?>">
									<?= $sTitle ?><span><?= $strAuthorsName ?></span></a>
							</li>
						<?php
						} ?>
					</ul>
				</nav>
			</section>
<?php
		}
	}
} ?>