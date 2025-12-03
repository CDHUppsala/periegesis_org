<?php
include_once dirname(__DIR__) . "/inText_Archives/archives_TextsPagingQuery.php";

include __DIR__ ."/search_functions.php";
include __DIR__ ."/search_process.php";
include __DIR__ ."/search_form.php";

/**
 * ===============================
 */

$radioSearch = True;
if ($strSearchWhere == "" && $strGroupWhere == "" && $strDatumWhere == "") {
	$radioSearch = False;
	echo '<h2 class="head"><span>' . lngSearchResults . "</span></h2>";
	echo "<p><b>" . lngSearchKeyWord . "</b></p>";
}

/**
 * Get parameter values for the prepared statement
 */

$arrBind = [];
if (!empty($strBindDatumWhere)) {
	$arrBind[] = $strBindDatumWhere;
}

if (!empty($strBindGroupWhere)) {
	$arrBind[] = $strBindGroupWhere;
}

if (!empty($arr_BindSearchWhere)) {
	$arrBind = array_merge($arrBind, $arr_BindSearchWhere);
}

$aResults = null;
$langNr = str_LangNr;
if ($radioSearch) {
	$sql = "SELECT
		t.TextID, 
		t.Title, 
		t.Coauthors, 
		t.PublishedDate, 
		g.GroupName {$langNr} AS GroupName, 
		a.AuthorID, 
		a.FirstName, 
		a.LastName 
	FROM (texts AS t 
		INNER JOIN text_groups AS g 
		ON t.GroupID = g.GroupID) 
		LEFT JOIN text_authors AS a 
		ON t.AuthorID = a.AuthorID 
	WHERE g.Hidden = False 
		AND g.LoginToRead = False 
		AND t.Publish = True 
		AND t.PublishedDate <= '" . date('Y-m-d') . "' "
		. $strDatumWhere
		. str_LanguageAnd
		. $strSearchWhere
		. $strGroupWhere
		. $strOrderByWhere;

	$intRecordCount = sx_getPageCountPrepare($sql, $arrBind, $intMaxTopSearch);
	$intPageCount = ceil($intRecordCount / $intPageSize);
	if ($intPageCount < $intRecordCount / $intPageSize) {
		$intPageCount = $intPageCount + 1;
	}

	if ($intPageCount > 1) {
		if ($iCurrentPage > $intPageCount) {
			$iCurrentPage = $intPageCount;
		}
		$iStartRecord = ($intPageSize * $iCurrentPage) - $intPageSize;
		$sql = $sql . " LIMIT " . $iStartRecord . "," . $intPageSize;
	}

	$stmt = $conn->prepare($sql);
	$stmt->execute($arrBind);
	$rows = $stmt->fetchAll(PDO::FETCH_NUM);
	if ($rows) :
		$aResults = $rows;
	endif;
	$stmt = null;
	$rows = null;
	if (!is_array($aResults)) {
		echo '<h2 class="head"><span>' . lngSearchResults . "</span></h2>";
		echo "</p><b>" . lngNotTextFoundNerrowSearch . "</b></p>";
	} else { ?>
		<section>
			<div class="page_navigation">
				<?php sx_getPageNavigation_ByArrows("search.php?search=text&", $iCurrentPage, $intPageCount) ?>
				<?php getPageAndSizeNavigationForm("search.php?search=text", $intPageSize) ?>
			</div>
			<div class="text_xxsmall align_right"><?= LNG__TotalRecords . ": " . $intRecordCount . " (" . lngMax . ": " . $intMaxTopSearch . ")" ?></div>
		</section>
		<?php
		if ($_SESSION["sort"] == "DESC ") {
			$strSortMsg = lngChangeToAscendingOrder;
			$strSortImg = "&#x25BC;";
		} else {
			$strSortMsg = lngChangeToDescendingOrder;
			$strSortImg = "&#x25B2;";
		}
		if (strpos($strOrderByWhere, "Title") > 0) {
			$headTitle = '<span title="' . lngOscilateBetweenDescAscOrder . '" class="sort_color">' . lngArticleTitle . "</span>";
		} else {
			$headTitle = '<span title="' . lngOrderByThisField . '">' . lngArticleTitle . "</span>";
		}
		if (strpos($strOrderByWhere, "LastName") > 0) {
			$headLastName = '<span title="' . lngOscilateBetweenDescAscOrder . '" class="sort_color">' . lngAuthor . "</span>";
		} else {
			$headLastName = '<span title="' . lngOrderByThisField . '">' . lngAuthor . "</span>";
		}
		if (strpos($strOrderByWhere, "GroupID") > 0) {
			$headGroupID = '<span title="' . lngOscilateBetweenDescAscOrder . '" class="sort_color">' . lngGroup . "</span>";
		} else {
			$headGroupID = '<span title="' . lngOrderByThisField . '">' . lngGroup . "</span>";
		}
		if (strpos($strOrderByWhere, "PublishedDate") > 0) {
			$headPublishedDate = '<span title="' . lngOscilateBetweenDescAscOrder . '" class="sort_color">' . lngDate . '</span>';
		} else {
			$headPublishedDate = '<span title="' . lngOrderByThisField . '">' . lngDate . '</span>';
		}
		?>
		<section>
			<table id="table_search">
				<tr>
					<th><a title="<?= $strSortMsg ?>" href="search.php?orderBy=sort"><?= $strSortImg ?></a></th>
					<th><a href="search.php?orderBy=Title"><?= $headTitle ?></a></th>
					<th><a href="search.php?orderBy=LastName"><?= $headLastName ?></a></th>
					<th><a href="search.php?orderBy=GroupID"><?= $headGroupID ?></a></th>
					<th><a href="search.php?orderBy=PublishedDate"><?= $headPublishedDate ?></a></th>
				</tr>
				<?php
				$x = (($iCurrentPage - 1) * ($intPageSize)) + 1;
				$iRows = count($aResults);
				for ($r = 0; $r < $iRows; $r++) {
					$iTextID = $aResults[$r][0];
					$strTitle = $aResults[$r][1];
					$strCoauthors = $aResults[$r][2];
					$datePublishedDate = $aResults[$r][3];
					$strGroupName = $aResults[$r][4];
					$iAuthorID = $aResults[$r][5];
					$strAuthorFirstName = $aResults[$r][6];
					$strAuthorLastName = $aResults[$r][7];
				?>
					<tr>
						<td>
							<a title="<?= lngOpenInNewWindow ?>" href="sx_PrintPage.php?tid=<?= $iTextID ?>" onclick="openCenteredWindow(this.href,'<?= $iTextID ?>','860','');return false;">
								<span class="icon_fonts">&#128462</span></a> <?= $x ?>
						</td>
						<td><a class="list" title="<?= lngOpenInSameWindow ?>" href="texts.php?tid=<?= $iTextID ?>"><?= $strTitle ?></a> </td>
						<td>
							<?php
							if (intval($iAuthorID) > 0) { ?>
								<a href="texts.php?authorID=<?= $iAuthorID ?>"><?= $strAuthorLastName . " " . $strAuthorFirstName ?></a>
								<?php
								if ($strCoauthors != "") { ?>
									<div class="text_xsmall"><?= str_replace(",", "<br>", $strCoauthors) ?></div>
							<?php
								}
							} else {
								echo "&nbsp;";
							} ?>
						</td>
						<td><?= $strGroupName ?></td>
						<td><?= $datePublishedDate ?></td>
					<?php
					$x = $x + 1;
				}
					?>
					</tr>
			</table>
		</section>
		<section class="align_center">
			<h3><?php getPageInformation($iCurrentPage, $intPageCount) ?></h3>
			<div class="page_navigation">
				<?php sx_getPageNavigation_ByArrows("search.php?search=text&", $iCurrentPage, $intPageCount) ?>
			</div>
		</section>
<?php
	}
}
$aResults = null;
?>