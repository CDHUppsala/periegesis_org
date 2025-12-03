<?php

/**
 * Information about current and total Pages
 */
function getPageInformation($intCurrentPage, $intPageCount)
{
	echo "<div>" . strtoupper(lngPage) . " <span>" . $intCurrentPage . "</span> ";
	echo strtoupper(lngOf) . " <span>" . $intPageCount . "</span></div>";
}

/**
 * Form to select page and page size for search records
 */
function get_PageAndSizeNavigation_ByForm($path, $iPageSize, $iPageCount)
{
?>
	<form title="You can change either Page or Page Size (Page = 0), NOT both!" method="post" name="SearcgPageForm" action="<?= $path ?>#Page_Anchor">
		<label><?= lngSize ?>:</label>
		<select size="1" name="PageSize">
			<option value="<?= $iPageSize ?>"><?= $iPageSize ?></option>
			<option value="100">100</option>
			<option value="200">200</option>
			<option value="400">400</option>
			<option value="500">500</option>
			<option value="800">800</option>
			<option value="1000">1000</option>
		</select>
		<label><?= lngPage ?>:</label>
		<input type="number" name="page" value="0" min="0" max="<?= $iPageCount ?>" size="1">
		<input type="submit" name="PageSubmit" value="&#10095;&#10095;">
	</form>
<?php
}

/**
 * Page navigation by arrows (next-previous page)
 * @param mixed $path
 * @param mixed $intCurrentPage
 * @param mixed $intPageCount
 * @return void : a HTNL list with links to pages.
 */
function get_PageNavigation_ByArrows($path, $intCurrentPage, $intPageCount): void
{ ?>
	<ul>
		<li><a title="<?= lngFirstPage ?>" href="<?= $path . "page=1" ?>#Page_Anchor">&#10094;&#10094;&#10094;&#10094;</a></li>
		<?php if ($intCurrentPage > 1) { ?>
			<li><a title="<?= lngPreviousPage ?>" href="<?= $path . "page=" . ($intCurrentPage - 1) ?>#Page_Anchor">&#10094;&#10094;</a></li>
		<?php } else { ?>
			<li><span>&#10094;&#10094;</span></li>
		<?php } ?>
		<li><?= $intCurrentPage ?> / <?= $intPageCount ?></li>
		<?php if ($intCurrentPage < $intPageCount) { ?>
			<li><a title="<?= lngNextPage ?>" href="<?= $path . "page=" . ($intCurrentPage + 1) ?>#Page_Anchor">&#10095;&#10095;</a></li>
		<?php } else { ?>
			<li><span>&#10095;&#10095;</span></li>
		<?php } ?>
		<li><a title="<?= lngLastPage ?>" href="<?= $path . "page=" . $intPageCount ?>#Page_Anchor">&#10095;&#10095;&#10095;&#10095;</a></li>
	</ul>
<?php
}

/**
 * Define the search string according to search types: strEvery, strAny and strExact
 */

function get_SearchWhereString($fieldName, $searchString, $strType)
{
	//global $str_BindSearchWhere;
	global $arr_BindSearchWhere;

	$getWhereString = "";
	if (strpos($searchString, " ") > 0) {
		if ($strType == "Every") {
			$arrSearchSplit = explode(" ", $searchString);
			$iTemp = count($arrSearchSplit);
			for ($i = 0; $i < $iTemp; $i++) {
				$sTemp = trim($arrSearchSplit[$i]);
				if ($i == 0) {
					$getWhereString = " INSTR({$fieldName}, ?) > 0 ";
				} else {
					$getWhereString .= " AND INSTR({$fieldName}, ?) > 0 ";
				}
				$arr_BindSearchWhere[] = $sTemp;
			}
		} elseif ($strType == "Any") {
			$arrSearchSplit = explode(" ", $searchString);
			$iTemp = count($arrSearchSplit);
			for ($i = 0; $i < $iTemp; $i++) {;
				$sTemp = trim($arrSearchSplit[$i]);
				if ($i == 0) {
					$getWhereString = " INSTR({$fieldName}, ?) > 0 ";
				} else {
					$getWhereString .= " OR INSTR({$fieldName}, ?) > 0 ";
				}
				$arr_BindSearchWhere[] = $sTemp;
			}
		} else { // default value: if strType = "Exact"
			$getWhereString = " INSTR({$fieldName}, ?) > 0 ";
			$arr_BindSearchWhere[] = trim($searchString);
		}
	} elseif (!empty($strType)) {
		$getWhereString = " INSTR({$fieldName}, ?) > 0 ";

		$arr_BindSearchWhere[] = trim($searchString);
	}
	return  $getWhereString;
}

function sx_getPageCountPrepare($sql, $bind)
{
	$conn = dbconn();
	$arSql = explode("ORDER BY", $sql);
	$arrSql = explode("FROM ", $arSql[0]);
	$strSql = "SELECT count(*) 
		FROM " . $arrSql[1];
	/*
	echo $sql . "<hr>";
	echo $strSql . "<hr>";
	print_r($bind);
	exit;
	*/
	$stmt = $conn->prepare($strSql);

	$stmt->execute($bind);
	$iCount = $stmt->fetchColumn();
	if ($iCount) {
		return $iCount;
	} else {
		return 0;
	}
}
?>