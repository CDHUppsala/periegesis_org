<?php

$radioSearch = True;
if ($strSearchWhere == "" && $strGroupWhere == "" && $strDatumWhere == "") {
	$radioSearch = False;
	echo '<h2 class="head"><span>' . lngSearchResults . "</span></h2>";
	echo "<p><b>" . lngSearchKeyWord . "</b></p>";
}

/**
 * Get parameter values for the prepared statement
 * Must be in the same order as the statement variables (or ?)
 * 1. Date, 2. Group ID, 3. Category ID, 4. Sesrch strings
 */

$arrBind = [];
if (!empty($strBindDatumWhere)) {
	$arrBind[] = $strBindDatumWhere;
}

if (!empty($strBindGroupWhere)) {
	$arrBind[] = $strBindGroupWhere;
}

if (!empty($arrBindCategoryWhere)) {
	$arrBind[] = $arrBindCategoryWhere;
}

if (!empty($arr_BindSearchWhere)) {
	$arrBind = array_merge($arrBind, $arr_BindSearchWhere);
}

if ($radioSearch) {
	$sql = "SELECT
		t.ArticleID, 
		t.Title, 
		t.AuthorName, 
		t.InsertDate,
		t.ArticleGroupID,
		t.ArticleCategoryID,
		g.GroupName{$str_LangNr} AS GroupName,
		c.CategoryName{$str_LangNr} AS CategoryName
		FROM (articles AS t 
		INNER JOIN article_groups AS g 
		ON t.ArticleGroupID = g.ArticleGroupID)
		LEFT JOIN article_categories AS c
		ON t.ArticleCategoryID = c.ArticleCategoryID
	WHERE g.Hidden = False 
		AND t.InsertDate <= '" . date('Y-m-d') . "' "
		. $strDatumWhere
		. str_LanguageAnd
		. $strGroupWhere
		. $strCategoryWhere
		. $strSearchWhere
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

	/*
	echo "<hr><hr>";
	echo $sql;
	echo "<pre>";
	print_r($arrBind);
	echo "</pre>";
	*/

	$aResults = null;
	try {
		$stmt = $conn->prepare($sql);
		$stmt->execute($arrBind);
		$aResults = $stmt->fetchAll(PDO::FETCH_NUM);
		$stmt = null;
	} catch (PDOException $e) {
		error_log("Database error: " . $e->getMessage());
	}

	if (!is_array($aResults) || empty($aResults)) {
		echo '<h2 class="head"><span>' . lngSearchResults . "</span></h2>";
		echo "</p><b>" . lngNotTextFoundNerrowSearch . "</b></p>";
	} else { ?>
		<section id="anchore">
			<div class="page_navigation">
				<?php sx_getPageNavigation_ByArrows("search.php?search=text&", $iCurrentPage, $intPageCount) ?>
				<?php sx_getPageAndSizeNavigation_ByForm("search.php?search=text", $intPageSize, $intPageCount) ?>
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
		if (strpos($strOrderByWhere, "AuthorName") > 0) {
			$headLastName = '<span title="' . lngOscilateBetweenDescAscOrder . '" class="sort_color">' . lngAuthor . "</span>";
		} else {
			$headLastName = '<span title="' . lngOrderByThisField . '">' . lngAuthor . "</span>";
		}
		if (strpos($strOrderByWhere, "GroupID") > 0) {
			$headGroupID = '<span title="' . lngOscilateBetweenDescAscOrder . '" class="sort_color">' . lngGroup . "</span>";
		} else {
			$headGroupID = '<span title="' . lngOrderByThisField . '">' . lngGroup . "</span>";
		}

		if (strpos($strOrderByWhere, "CategoryID") > 0) {
			$headCategoryID = '<span title="' . lngOscilateBetweenDescAscOrder . '" class="sort_color">' . lngCategory . "</span>";
		} else {
			$headCategoryID = '<span title="' . lngOrderByThisField . '">' . lngCategory . "</span>";
		}
		if (strpos($strOrderByWhere, "InsertDate") > 0) {
			$headPublishedDate = '<span title="' . lngOscilateBetweenDescAscOrder . '" class="sort_color">' . lngDate . '</span>';
		} else {
			$headPublishedDate = '<span title="' . lngOrderByThisField . '">' . lngDate . '</span>';
		}
		?>
		<section>
			<table id="table_search">
				<tr>
					<th><a title="<?= $strSortMsg ?>" href="search.php?orderBy=sort"><?= $strSortImg ?></a></th>
					<th><a href="search.php?orderBy=Title#anchore"><?= $headTitle ?></a></th>
					<th><a href="search.php?orderBy=AuthorName#anchore"><?= $headLastName ?></a></th>
					<th><a href="search.php?orderBy=GroupID#anchore"><?= $headGroupID ?></a></th>
					<th><a href="search.php?orderBy=CategoryID#anchore"><?= $headCategoryID ?></a></th>
					<th><a href="search.php?orderBy=InsertDate#anchore"><?= $headPublishedDate ?></a></th>
				</tr>
				<?php
				$x = (($iCurrentPage - 1) * ($intPageSize)) + 1;
				$iRows = count($aResults);
				for ($r = 0; $r < $iRows; $r++) {
					$iTextID = $aResults[$r][0];
					$strTitle = $aResults[$r][1];
					$stAuthors = $aResults[$r][2];
					$datePublishedDate = $aResults[$r][3];
					$iGroupID = $aResults[$r][4];
					$iCategoryID = $aResults[$r][5];
					$strGroupName = $aResults[$r][6];
					$strCategoryName = $aResults[$r][7];
				?>
					<tr>
						<td class="no_wrap"> <?= $x ?>
							<a title="<?= lngOpenInNewWindow ?>" href="sx_PrintPage.php?aid=<?= $iTextID ?>" onclick="openCenteredWindow(this.href,'<?= $iTextID ?>','860','');return false;">
								<span class="icon_fonts">&#128462</span></a>
						</td>
						<td><a class="list" title="<?= lngOpenInSameWindow ?>" href="articles.php?aid=<?= $iTextID ?>"><?= $strTitle ?></a> </td>
						<td>
							<?php
							if ($stAuthors != "") { ?>
								<?= $stAuthors ?>
							<?php
							} else {
								echo "&nbsp;";
							} ?>
						</td>
						<td><a class="list" title="<?= lngOpenInSameWindow ?>" href="articles.php?agid=<?= $iGroupID ?>"><?= $strGroupName ?></a></td>
						<td><a class="list" title="<?= lngOpenInSameWindow ?>" href="articles.php?acid=<?= $iCategoryID ?>"><?= $strCategoryName ?></a></td>
						<td><?= $datePublishedDate ?></td>
					<?php
					$x = $x + 1;
				}
					?>
					</tr>
			</table>
		</section>
		<section>
			<p class="align_center text_small">
				<a class="button-grey button-gradient jq_CopyElementToClipboard" data-id="table_search">Copy to Clipboard</a>
				<a class="button-grey button-gradient jq_PrintElementToPDF" data-id="table_search">Print as PDF</a>
				<a class="button-grey button-gradient jq_ExportTableToExcel" data-id="table_search">Export to Excel</a>
				<a class="button-grey button-gradient jq_ExportElementToWord" data-id="table_search">Export to WORD</a>
			</p>
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

//echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';

?>