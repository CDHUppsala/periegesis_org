<?php
include "functionsLanguage.php";
include "login/lockPage.php";
include "functionsTableName.php";
include "functionsDBConn.php";

$iAuthorID = 0;
$srtMessage = "";
if (!empty(@$_POST["AddAuthors"])) {
	$strFirstName = "";
	if (!empty($_POST["FirstName"])) {
		$strFirstName = sx_replaceSingleQuotes(trim($_POST["FirstName"]));
	}
	$strLastName = "";
	if (!empty($_POST["LastName"])) {
		$strLastName = sx_replaceSingleQuotes(trim($_POST["LastName"]));
	}
	$strPhoto = "";
	if (!empty($_POST["Photo"])) {
		$strPhoto = sx_replaceSingleQuotes(trim($_POST["Photo"]));
	}
	$strEmail = "";
	if (!empty($_POST["Email"])) {
		$strEmail = sx_replaceSingleQuotes(trim($_POST["Email"]));
	}
	$strWebSite = "";
	if (!empty($_POST["WebSite"])) {
		$strWebSite = sx_replaceSingleQuotes(trim($_POST["WebSite"]));
	}
	$strCity = "";
	if (!empty($_POST["City"])) {
		$strCity = sx_replaceSingleQuotes(trim($_POST["City"]));
	}
	$strCountry = "";
	if (!empty($_POST["Country"])) {
		$strCountry = sx_replaceSingleQuotes(trim($_POST["Country"]));
	}

	/**
	 * Check if the author allready exists
	 */
	$sql = "SELECT AuthorID 
	FROM book_authors 
	WHERE Hidden = False AND LastName = ? AND FirstName = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$strLastName, $strFirstName]);
	$rs = $stmt->fetchColumn();
	if (!empty($rs)) {
		$iAuthorID = $rs;
	}
	$stmt = null;
	$rs = null;

	if (intval($iAuthorID) == 0) {
		if (!empty($strFirstName) && !empty($strLastName)) {
			$sql = "INSERT INTO book_authors (FirstName, LastName, Photo, Email, WebSite, City, Country) 
			VALUES (?,?,?,?,?,?,?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$strFirstName, $strLastName, $strPhoto, $strEmail, $strWebSite, $strCity, $strCountry]);
			$iAuthorID = $conn->lastInsertId();
			if (intval($iAuthorID) == 0) {
				$iAuthorID = 0;
			}
		}
	} else {
		$srtMessage = "The author already exist in the database.
			If it is a different author, please add some qualification in the First Name.";
	}
}

$aResults = null;
if (!empty($_POST["GetAuthors"]) || intval($iAuthorID) > 0) {
	$strWhere = "";
	$sLast_Names = "";
	if (!empty($_POST["Last_Names"])) {
		$sLast_Names = sx_replaceSingleQuotes(trim($_POST["Last_Names"]));
	}
	$checkSortNumerically = "";
	if (!empty($_POST["SortNumerically"])) {
		$checkSortNumerically = $_POST["SortNumerically"];
	}
	$strOrderBy = " LastName ASC ";
	if ($checkSortNumerically == "Yes") {
		$strOrderBy = " AuthorID DESC ";
	}
	if (intval($iAuthorID) > 0) {
		$strWhere = " AND AuthorID = " . $iAuthorID;
	} elseif (!empty($sLast_Names)) {
		if (strpos($sLast_Names, ",") == 0) {
			$strWhere = " AND INSTR(UPPER(LastName), '" . strtoupper($sLast_Names) . "') > 0 ";
		} else {
			$arName = explode(",", $sLast_Names);
			$iCount = count($arName);
			for ($r = 0; $r < $iCount; $r++) {
				if ($r == 0) {
					$strWhere = " AND (INSTR(UPPER(LastName), '" . strtoupper($arName[$r]) . "') > 0 ";
				} else {
					$strWhere = $strWhere . " OR INSTR(UPPER(LastName), '" . strtoupper($arName[$r]) . "') > 0 ";
				}
			}
			$strWhere = $strWhere . ") ";
		}
	}
	$sql = "SELECT AuthorID, LastName, FirstName 
		FROM book_authors 
		WHERE Hidden = False
		" . $strWhere . "
		ORDER BY " . $strOrderBy;
	$rs = $conn->query($sql)->fetchAll();
	if ($rs) {
		$aResults = $rs;
	}
	$rs = null;
}

$connClose;
?>
<section style="padding: 0 10px;">
	<h2><span class="text_xsmall info floatRight jqInfoToggle">?</span>Book Authors</h2>
	<div class="textMsg" style="display: none;">
		<p>Use the forms here to Automatically add a New book and its Author(s) in the <b>Book To Authors</b> table</p>
		<ul class="nerrow">
			<li>Add fist New Authors (if any) in the Authors Table by clicking on the Tab <b>Add New Author</b>.</li>
			<li>Return then to the Tab <b>Insert Authors</b> and:
				<ul>
					<li>Use the search forms to get a List of Relevant Authors</li>
					<li>Use the List of Auhtors to insert the ID(s) of Author(s) in the <b>Temporal Field</b>,
						on the top of left side of the page.</li>
				</ul>
			</li>
			<li>The book ID and the ID(s) of Author(s) will automatically by added in the <b>Book To Authors</b> table when you add a new book</li>
		</ul>
		<div>
</section>
<section>
	<div id="tabs">
		<a data-id="layer1" class="selected" href="javascript:void(0)">Insert Authors</a>
		<a data-id="layer2" href="javascript:void(0)">Add New Author</a>
	</div>
	<div id="layer1" style="display: block; padding: 10px;">
		<p><span class="info floatRight jqInfoToggle">?</span><b>Search Authors:</b></p>
		<ul class="textBG" style="display: none">
			<li>Write (part of) the <b>Last Name</b> of the Author to get a list of all authors with that Name.</li>
			<li>For <b>multiple</b> authors, separate their Last Names with a comma (Name1, Name2, etc.).</li>
			<li>If the field is <b>empty</b>, a list of <b>All</b> Authors will be shown.</li>
		</ul>
		<form action="ajax_bookToAuthors.php" method="post" name="sxGetAuthor" class="jqLoadSelectForm">
			<input type="hidden" name="GetAuthors" value="Yes">
			<input type="text" name="Last_Names" value="<?= @$sLast_Names ?>" placeholder="Write (part) of Last Names" size="24">
			<input type="submit" value="Get Authors" name="Submit"><br>
			<input type="checkbox" name="SortNumerically" value="Yes"> Sort by Last Added
		</form>
		<?php
		if (is_array($aResults)) { ?>
			<p><span class="info floatRight jqInfoToggle">?</span><b>How to insert Author(s):</b></p>
			<ul class="textBG" style="display: none">
				<li>You insert the ID of an Author in the <b>Temporal Field</b> by checking the Box on the left of the Author's Name.</li>
				<li><b>Obs!</b> Check <b>multiple</b> names in the <b>order</b> defined by the Publisher.</li>
				<li><b>On Error</b>, uncheck the boxe(s) and try again.</li>
			</ul>
			<?php
			if (!empty($srtMessage)) {
				echo '<div class="msgError">' . $srtMessage . '</div>';
			}
			echo '<div class="text jqAddAuthor"><table>';
			$iRows = count($aResults);
			for ($r = 0; $r < $iRows; $r++) { ?>
				<tr>
					<td><input type="checkbox" name="Name_<?= $r ?>" value="<?= $aResults[$r][0] ?>"> <?= $aResults[$r][1] . " " . $aResults[$r][2] ?></td>
					<td><span class="info"><?= $aResults[$r][0] ?></span></td>
				</tr>
		<?php
			}
			echo "</table></div>";
		}
		?>
	</div>

	<div id="layer2" style="display: none; padding: 10px;">
		<p>Please, check First if the Author already exists in the Book Authors Table. You can add more details later by edditing the <b>Book Authors</b> Table.</p>
		<form action="ajax_bookToAuthors.php" method="post" name="sxAddAuthor" class="jqLoadSelectForm">
			<input type="hidden" name="AddAuthors" value="Yes">
			<table>
				<tr>
					<th>First Name: </th>
					<td><input name="FirstName" size="28" required> </td>
				</tr>
				<tr>
					<th>Last Name: </th>
					<td><input name="LastName" size="28" required> </td>
				</tr>
				<tr>
					<th>Photo: </th>
					<td><input name="Photo" size="28"> </td>
				</tr>
				<tr>
					<th>Email: </th>
					<td><input name="Email" size="28"> </td>
				</tr>
				<tr>
					<th>WebSite: </th>
					<td><input name="WebSite" size="28"> </td>
				</tr>
				<tr>
					<th>City: </th>
					<td><input name="City" size="28"> </td>
				</tr>
				<tr>
					<th>Country: </th>
					<td><input name="Country" size="28"> </td>
				</tr>
			</table>
			<p class="alignCenter"><input type="submit" value="Add Author" name="Submit"></p>
		</form>
	</div>
</section>
<script>
	sxAjaxLoadArchives();
	sxReloadTabs();
	sxReloadInfoToggle();
</script>