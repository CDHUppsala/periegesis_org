<?php

/**
 * Functions FOR SEARCH PAGES
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

function getPageAndSizeNavigationForm($path, $intCurrentSize)
{
?>
	<form method="post" name="searchForm" action="<?= $path ?>">
		<label><?= lngSize ?>:</label>
		<select size="1" name="PageSize">
			<option value="<?= $intCurrentSize ?>"><?= $intCurrentSize ?></option>
			<option value="10">10</option>
			<option value="20">20</option>
			<option value="30">30</option>
			<option value="40">40</option>
			<option value="50">50</option>
			<option value="60">60</option>
		</select>
		<label><?= lngPage ?>:</label>
		<input type="text" name="page" size="1">
		<input type="submit" name="go" value="&#10095;&#10095;">
	</form>
<?php
}

/**
 * Define the search string according to search types: strEvery, strAny and strExact
 */

function get_SearchWhereString($fieldName, $searchString, $strType)
{
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
			$getWhereString = "INSTR({$fieldName}, ?) > 0 ";
			$arr_BindSearchWhere[] = trim($searchString);
		}
	} elseif (strlen($strType) > 0) {
		$getWhereString = " INSTR({$fieldName}, ?) > 0 ";
		$arr_BindSearchWhere[] = trim($searchString);
	}
	return  $getWhereString;
}

function sx_getPageCountPrepare($sql, $bind, $iLimit = 200)
{
	$sLimit = "";
	if (intval($iLimit) > 0) {
		$sLimit = " LIMIT " . $iLimit;
	}
	$conn = dbconn();
	$arSql = explode("ORDER BY", $sql);
	$arrSql = explode("FROM ", $arSql[0]);
	$strSql = "SELECT count(*) 
		FROM " . $arrSql[1] . $sLimit;

	echo $sql ."<hr>";
	echo $arrSql[1] . "<hr>";
	echo $strSql ."<hr>";
	print_r($bind);
	/*
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