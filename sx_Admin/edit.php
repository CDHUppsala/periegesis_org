<?php
include "functionsLanguage.php";
include "login/lockPage.php";
include "functionsTableName.php";
include "functionsDBConn.php";
include "configFunctions.php";

if (in_array($request_Table, $arr_NotEditableTables)) {
	header("Location: main.php?msg=You+cannot+edit+records+in+table+" . $request_Table);
	exit();
}

$strIDName = $_GET["strIDName"] ?? ''; //The name of the ID File
$strIDValue = $_GET["strIDValue"] ?? 0; //The value of the ID File

$strReturn = $_GET["return"] ?? '';
if ($strReturn == "processing") {
	$strRedirect = "sxSalesAdmin/orders_Processing.php";
	$strReturnQuery = "&return=processing";
} else {
	$strRedirect = "list.php";
	$strReturnQuery = "";
}

if (empty($strIDName) || sx_checkTableAndFieldNames($strIDName) == false || (int) $strIDValue === 0) {
	header("Location: " . $strRedirect);
	exit();
}

include "functionsAddEdit.php";

if (!empty($_POST["Edit"])) {
	/**
	 * Get updated form values from function
	 */
	$arrUpdates = sx_getInsertUpdateRecords("update");
	$strUppdatePrepare = $arrUpdates[0];
	$arrUppdateValues = $arrUpdates[1];
	$arrUppdateValues[] = $strIDValue;

	$sql = "UPDATE " . $request_Table . " SET " . $strUppdatePrepare . " WHERE " . $strIDName . " = ?";

	$stmt = $conn->prepare($sql);
	$stmt->execute($arrUppdateValues);

	/**
	 * Delet all records in book_to_autors with the BookID = $int_BookID
	 * Before adding new authors
	 */
	if ($request_Table == "books" && !empty($_POST["BookToAuthors"])) {
		$int_BookID = intval($strIDValue);
		if (intval($int_BookID) > 0) {
			$sql = "DELETE FROM book_to_authors WHERE BookID = ?";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$int_BookID]);

			$strToAthors = $_POST["BookToAuthors"];
			if (strpos($strToAthors, ",") == 0) {
				$strToAthors .= ",";
			}
			$arrA = explode(",", $strToAthors);
			$iCount = count($arrA);
			for ($r = 0; $r < $iCount; $r++) {
				$iTemp = trim($arrA[$r]);
				if (intval($iTemp) > 0) {
					$iTemp = intval($iTemp);
					$sql = "INSERT INTO book_to_authors (BookID, AuthorID, AuthorOrdinal) Values(?,?,?)";
					$stmt = $conn->prepare($sql);
					$stmt->execute([$int_BookID, $iTemp, $r]);
				}
			}
		}
	}

	header("Location: " . $strRedirect . "?no=no");
	exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere CMS - Eddit Records</title>
	<link rel="stylesheet" href="../sxCss/root_Colors.css?v=2024-11">
	<link rel="stylesheet" href="../sxCss/root_Gradients.css?v=2024-11">
	<link rel="stylesheet" href="../sxCss/root_Variables.css?v=2024-11">
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js?v=2023"></script>
    <script src="js/jq/jquery.min.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqAjaxLoadArchives.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqLoadFormInputs.js?v=2023"></script>
	<?php
	if ($radio_useTinymce) { ?>
		<script src="tinymce/tinymce.min.js?v=2024-11"></script>
		<script src="tinymce/config/custom.js?v=2024-11"></script>
	<?php }
	if (!empty($strFormValidation)) { ?>
		<script type="text/javascript">
			// Public Sphere - Basic form-controll: checks that required fields are not empty
			function requiredFields(form) {
				<?= "if (" . $strFormValidation . ")" ?> {
					alert("<?= lngFillFieldsWithAsterisk ?>");
					return false;
				}
				return true;
			}
		</script>
	<?php
	} ?>
</head>

<body class="body">
	<header id="header">
		<h2><?= lngTable . ": " . strtoupper($request_Table) ?><br><?= lngEditRecord ?></h2>
		<?php include "add_menu.php"; ?>
	</header>
	<h3><a href="<?= $strRedirect ?>"><?= lngBackToRecodList ?></a></h3>

	<section>
		<form method="post" id="sxAddEdit" name="sxAddEdit" action="edit.php?strIDName=<?= $strIDName ?>&strIDValue=<?= $strIDValue . $strReturnQuery ?>" <?php if (!empty($strFormValidation)) { ?>onsubmit="return requiredFields(this)" <?php } ?>>
			<table class="edit_table">
				<?php if ($request_Table == "books") {
					/**
					 * Set True/False to also include Author Notes
					 */
					sx_getBookAuthorsInput(true, false, '');

					$strNames = getBookAuthorsNames($strIDValue);
					if (!empty($strNames)) {
						echo "<tr><th>Authors:</th><td>
						<p>" . $strNames . "</p>
						</td></tr>";
					}
				}
				$iLoop = count(ARR_FieldNames);
				for ($i = 0; $i < $iLoop; $i++) {
					$xName = ARR_FieldNames[$i][0];
					$xType = ARR_FieldNames[$i][1];
					$strHelp = ARR_FieldNames[$i][2];
					$xValue = $arr_EditResults[$i];

					/**
					 * Add 0809 To exclude unused fields
					 */
					if (sx_getUpdateableFieldType($xName) != 50) { ?>
						<tr>
							<th><?= sx_checkAsName($xName) ?>: <?= sx_getAsterix($xName) ?></th>
							<?php
							if ($i == 0) {
								if ($boolIsAuto) { ?>
									<td>
										<input class="button floatRight" type="submit" name="Edit" value="<?= lngUpdate ?>">
										<p><?= lngAutoNumber . ": " . $xValue ?> <?= sx_getHelpForJava($xName, $strHelp) ?></p>
									</td>
								<?php
								} else {
                                    Header("Location: main.php?strMsg=noPK");
                                    exit;
								}
							} else {
								if ($xType == "BLOB") { ?>
									<td><textarea spellcheck id="<?= $xName ?>" name="<?= $xName ?>" style="height: 400px; width: 600px"><?= ($xValue) ?></textarea>
										<div><?= sx_getHelpForJava($xName, $strHelp) ?>
									</td>
								<?php
								} elseif ($xType == "TINY") { ?>
									<td>
										<input type="radio" value="Yes" <?php if (boolval($xValue)) { ?> checked<?php } ?> name="<?= $xName ?>">:<?= lngYes ?>&nbsp;
										<input type="radio" value="No" <?php if (!boolval($xValue)) { ?> checked<?php } ?> name="<?= $xName ?>">:<?= lngNo ?> <?= sx_getHelpForJava($xName, $strHelp) ?>
									</td>
								<?php
								} elseif ($xType == "DATE") {
								?>
									<td><input type="date" name="<?= $xName ?>" autocomplete="off" value="<?= $xValue ?>"><?= sx_getHelpForJava($xName, $strHelp) ?></td>
								<?php
								} elseif ($xType == "DATETIME") {
								?>
									<td><input type="datetime-local" step="60" name="<?= $xName ?>" autocomplete="off" value="<?= $xValue ?>"><?= sx_getHelpForJava($xName, $strHelp) ?></td>
								<?php
								} elseif ($xType == "TIME") { ?>
									<td><input type="time" name="<?= $xName ?>" autocomplete="off" value="<?= $xValue ?>"><?= sx_getHelpForJava($xName, $strHelp) ?></td>
								<?php

								} elseif ($xType == "DOUBLE" || $xType == "FLOAT") {
									if (intval($xValue) == 0) {
										$xValue = 0;
									} ?>
									<td><input type="number" step="any" name="<?= $xName ?>" value="<?= $xValue ?>"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
								<?php
								} elseif ($xType == "SHORT") {
									if (intval($xValue) == 0) {
										$xValue = 0;
									} elseif (intval($xValue) > 9999) {
										$xValue = 9999;
									} ?>
									<td><input type="number" min="0" max="99999" step="1" maxlength="4" name="<?= $xName ?>" value="<?= $xValue ?>"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
								<?php
								} elseif ($xType == "LONG" && $xName == "LoginAdminID") { //Disable and set the ID of the administrator that is loged in
									if (intval($xValue) == 0) {
										$xValue = 0;
									}
									if (intval($intLoginAdminID) > 0) {
										$xValue = $intLoginAdminID;
									} ?>
									<td><input type="number" title="You cannot edit this field!" name="<?= $xName ?>" value="<?= $xValue ?>" readonly="readonly"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
								<?php

								} elseif (is_array($arrFieldRelations) && array_key_exists($xName, $arrFieldRelations)) {
									$strRFVAdd = "";
									$strRFV = $xValue;
								?>
									<td><?php sx_getRelationInputs($xName, $strRFVAdd, $strRFV) ?> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
								<?php
								} else { 
                                    if(!empty($xValue)) {
                                        $xValue = htmlspecialchars($xValue);
                                    } ?>
									<td><input type="text" name="<?= $xName ?>" value="<?= $xValue ?>"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
							<?php
								}
							} ?>
						</tr>
				<?php
					}
				}

				?>
			</table>
			<p>
				<?php
				$strAsterixMsg = "";
				if (!empty($strRequiredFieldsArray)) {
					$strAsterixMsg = "* " . lngAsteriskFieldsRequired;
				}

				if (!empty(@$errorMsg)) {
					echo $errorMsg;
				}
				echo $strAsterixMsg;
				?>
				<input class="button" type="submit" name="Edit" value="<?= lngUpdate ?>">
			</p>
		</form>
	</section>
	<div id="jqLoadArchivesWrapper">
		<div title="Toggle Show/Hide" id="jqLoadArchivesToggle" class="aside_hide"></div>
		<div title="Toggle Width between Default and 50%" id="jq_width" class="aside_show"></div>
		<div id="jqLoadArchivesLayer"></div>
	</div>
	<div id="absoluteHelp"></div>
	<?php include "errorMsgForClient.php"; ?>
</body>

</html>