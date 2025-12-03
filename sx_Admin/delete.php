<?php
include "functionsLanguage.php";
include "login/lockPage.php";
include "functionsTableName.php";
include "login/adminLevelPages.php";
include "functionsDBConn.php";
include "configFunctions.php";

if (in_array($request_Table, $arr_NotDeleteableTables)) {
	header("Location: main.php?msg=You+cannot+delete+records+in+table+" . $request_Table);
	exit();
}

//Gets request variables
$strIDName = @$_GET["strIDName"];
$strIDValue = @$_GET["strIDValue"];
$strReturn = @$_GET["return"];

$strRedirect = "list.php";
$strReturnQuery = null;
if ($strReturn == "listAdmin") {
	$strRedirect = "sxSaleFiles/listAdmin.php";
	$strReturnQuery = "&return=listAdmin";
}

if (empty($strIDName) || sx_checkTableAndFieldNames($strIDName) == false || !is_numeric($strIDValue)) {
	header("Location: " . $strRedirect);
	exit();
}

/**
 * Deletes the record, if confirmed, and returns to navigation list
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["ConfirmDelete"] == "Delete") {
	$sql = "DELETE FROM " . $request_Table . " WHERE " . $strIDName . " =  ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$strIDValue]);
	$stmt = null;
	$sql = "ALTER TABLE " . $request_Table . " AUTO_INCREMENT =0";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$stmt = null;
	/*
*/
	header("Location: " . $strRedirect);
	exit();
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere Content Management System</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body class="body">
	<header id="header">
		<h2><?= lngTable ?>: <?= strtoupper($request_Table) ?><br><?= lngDeleteRecord ?></h2>
	</header>

	<section class="maxWidth">
		<h1>
			Conform Deleting Record from <?= lngTable ?></b>: <?= $request_Table ?> -
			<?= $strIDName ?>: <?= $strIDValue ?><br>
		</h1>

		<form id="editForm" action="delete.php?strIDName=<?= $strIDName ?>&strIDValue=<?= $strIDValue . $strReturnQuery ?>" method="post">
			<h4 class="floatRight">Not Delete: <a href="<?= $strRedirect ?>"><?= lngBackToRecodList ?></a></h4>
			<input type="hidden" name="ConfirmDelete" value="Delete">
			<p><input class="button" type="submit" name="Action1" value="<?= lngConfirmDelete ?>"></p>
			<table class="no_gradient">
				<?php

				$strSQL = "SELECT * FROM " . $request_Table . " WHERE " . $strIDName . " = ? ";
				$stmt = $conn->prepare($strSQL);
				$stmt->execute([$strIDValue]);
				$rs = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($rs) {
					foreach ($rs as $key => $value) {
						if (!empty($value) && strlen($value) > 200) {
							$value = substr($value, 0, 200) . "...";
						} ?>
						<tr>
							<th nowrap valign="top"><?= $key ?>:</th>
							<td><?= $value ?></td>
						</tr>
				<?php
					}
				} else {
					header("Location: " . $strRedirect);
					exit();
				}
				$stmt = null;
				$rs = null;
				?>
			</table>
			<h4 class="floatRight">Not Delete: <a href="<?= $strRedirect ?>"><?= lngBackToRecodList ?></a></h4>
			<p><input class="button" type="submit" name="Action2" value="<?= lngConfirmDelete ?>">
		</form>
	</section>
</body>

</html>