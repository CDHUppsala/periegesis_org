<?php
include __DIR__ . "/search_functions.php";
include __DIR__ . "/search_process.php";
include __DIR__ . "/search_form.php";

/**
 * Get parameter values for the prepared statement
 */
$arrBind = $arr_BindSearchWhere;

$sql = "SELECT PersonID, person, personLabel, PausaniasPerson, genderLabel, personDescription,
		entityLabel, citizen, death, fatherLabel, motherLabel, birthplaceLabel,
		article, father, mother, birthplace, gender
	FROM wikidata $strSearchWhere $strOrderByWhere ";

$intRecordCount = sx_getPageCountPrepare($sql, $arrBind);
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

$aResults = null;
try {
	$stmt = $conn->prepare($sql);
	$stmt->execute($arrBind);
	$aResults = $stmt->fetchAll(PDO::FETCH_NUM);
} catch (PDOException $e) {
	error_log("Database error: " . $e->getMessage());
}

if (!is_array($aResults) || empty($aResults)) {
	echo '<h2 class="head"><span>' . lngSearchResults . "</span></h2>";
	echo "</p><b>" . lngNotTextFoundNerrowSearch . "</b></p>";
} else { ?>
	<section id="Page_Anchor">
		<div class="page_navigation">
			<?php get_PageNavigation_ByArrows("search_wikidata.php?search=text&", $iCurrentPage, $intPageCount) ?>
			<?php get_PageAndSizeNavigation_ByForm("search_wikidata.php?search=text", $intPageSize, $intPageCount) ?>
		</div>
		<div class="text_xxsmall align_right"><?= LNG__TotalRecords . ": " . $intRecordCount ?></div>
	</section>
	<?php
	$intRundom = date('Y-m-d');
	if ($strSort == "DESC") {
		$strSortMsg = lngChangeToDescendingOrder;
		$strSortImg = "&#x25BC;";
	} else {
		$strSortMsg = lngChangeToAscendingOrder;
		$strSortImg = "&#x25B2;";
	} ?>
	<section>
		<table class="csv_table" id="csv_table_search_<?= $intRundom ?>">
			<tr>
				<th><a href="search_wikidata.php?OrderByColumn=<?= ARR_WikidataFields[0] ?>">
						<span title="<?= $strSortMsg ?>"><?= $strSortImg ?></a></span></th>
				<?php
				$intColums = count(ARR_WikidataFields);
				for ($i = 1; $i < $intColums; $i++) {
					if (!in_array($i, $arrExcludeFieldIndex)) {
						$sHeaderValue = ARR_WikidataFields[$i];
						$sHeaderDesplay = sx_separateWordsWithCamelCase($sHeaderValue);
						if (!empty($arrRelatedFieldIndex)) {
							if ($i == 1) {
								$sHeaderDesplay = 'Wikidata ID';
							} else {
								$sHeaderDesplay = trim(str_replace('Label', '', $sHeaderDesplay));
								if (strpos($sHeaderDesplay, 'Description') !== false) {
									$sHeaderDesplay = trim(str_replace('person', '', $sHeaderDesplay));
								}
							}
						}
						if ($strOrderByColumn == $sHeaderValue) {
							$sHeaderDesplay = '<span title="' . lngOscilateBetweenDescAscOrder . '" class="sort_color">' . $sHeaderDesplay . "</span>";
						} else {
							$sHeaderDesplay = '<span title="' . lngOrderByThisField . '">' . $sHeaderDesplay . "</span>";
						}
						echo '<th><a href="search_wikidata.php?OrderByColumn=' . $sHeaderValue . '">' . $sHeaderDesplay . '<a></th>';
					}
				} ?>
			</tr>
			<?php
			$x = (($iCurrentPage - 1) * ($intPageSize)) + 1;
			$iRows = count($aResults);
			for ($r = 0; $r < $iRows; $r++) { ?>
				<tr>
					<td title="<?= $x ?>"><?= $aResults[$r][0] ?></td>
				<?php
				for ($j = 1; $j < $intColums; $j++) {
					if (!in_array($j, $arrExcludeFieldIndex)) {

						$strLoop = $aResults[$r][$j];
						if ($j == 1 && !empty($arrRelatedFieldIndex)) {
							$str__Loop = substr($strLoop, strrpos($strLoop, "/") + 1);
							$strLoop = '<a target="_blank" href="' . $strLoop . '">' .  $str__Loop . '</a>';
						} elseif (!empty($strLoop) && strpos($strLoop, '<a ') !== false) {
							if (strpos($strLoop, 'target=') === false) {
								$sLeft = substr($strLoop, 0, 2) . ' target="_blank"';
								$sRight = substr($strLoop, 2);
								$strLoop = $sLeft . $sRight;
							}
						} elseif (!empty($strLoop) && (strpos($strLoop, 'http://') !== false || strpos($strLoop, 'https://') !== false)) {
							$linkTitle = sx_separateWordsWithCamelCase(ARR_WikidataFields[$j]);
							$strLoop = '<a target="_blank" href="' . $strLoop . '">' .  $linkTitle . '</a>';
						} elseif (in_array($j, $arrRelatedFieldIndex)) {
							$indexPosition = array_search($j, $arrRelatedFieldIndex);
							$indexValue = $arrExcludeFieldIndex[$indexPosition];
							$str_Loop = $aResults[$r][$indexValue] ?? '';

							if (!empty($strLoop) && (strpos($str_Loop, 'http://') !== false || strpos($str_Loop, 'https://') !== false)) {
								$strLoop = '<a target="_blank" href="' . $str_Loop . '">' .  $strLoop . '</a>';
							}
						} else {
							if (!empty($strLoop) && strpos($strLoop, 'T00:00:00Z') !== false) {
								$strLoop = substr($strLoop, 0, strpos($strLoop, '-', 1));
							}
						}
						echo "<td>$strLoop</td>";
					}
				}
				$x = $x + 1;
			} ?>
				</tr>
		</table>
	</section>
	<section class="margin_top">
		<p class="align_center text_small">
			<a class="button-grey button-gradient jq_PrintElementToPDF" data-id="csv_table_search_<?= $intRundom ?>">Print as PDF</a>
			<a class="button-grey button-gradient jq_ExportTableToHTML" data-id="csv_table_search_<?= $intRundom ?>">Export as HTML</a>
			<a class="button-grey button-gradient jq_ExportTableToExcel" data-id="csv_table_search_<?= $intRundom ?>">Export to Excel</a>
			<a class="button-grey button-gradient jq_ExportElementToWord" data-id="csv_table_search_<?= $intRundom ?>">Export to WORD</a>
		</p>
	</section>
	<section class="align_center">
		<h3><?php getPageInformation($iCurrentPage, $intPageCount) ?></h3>
		<div class="page_navigation">
			<?php get_PageNavigation_ByArrows("search_wikidata.php?search=text&", $iCurrentPage, $intPageCount) ?>
		</div>
	</section>
<?php
}
$aResults = null;
?>