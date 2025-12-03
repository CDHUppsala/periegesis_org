<?php
include "functionsLanguage.php";
include "login/lockPage.php";
include "functionsTableName.php";
include "functionsDBConn.php";

if (in_array($request_Table, $arr_NotCopyableTables)) {
	header("Location: main.php?msg=You+cannot+copy+records+in+table+" . $request_Table);
	exit();
}
$strIDName = "";
//The name of the ID File
if (isset($_GET["strIDName"])) {
	$strIDName = $_GET["strIDName"];
}
$strIDValue = 0;
//The value of the ID File
if (isset($_GET["strIDValue"])) {
    $strIDValue = (int) $_GET["strIDValue"];
}
$strReturn = "";
// other copy source than list.php
if (isset($_GET["return"])) {
    $strReturn = $_GET["return"];
}

$strRedirect = "list.php";
$strReturnQuery = "";
if ($strReturn == "listAdmin") {
	$strRedirect = "sxSaleFiles/listAdmin.php";
	$strReturnQuery = "&return=listAdmin";
}

if (empty($strIDName) || sx_checkTableAndFieldNames($strIDName) == false || $strIDValue == 0) {
	header("Location: " . $strRedirect);
	exit();
}

/**
 * Copy the record, if confirmed, and returns to navigation list
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["ConfirmCopy"] == "Copy") {
	$radio_Skip_Text = false;
	if (isset($_POST["SkipText"]) && $_POST["SkipText"] == "Yes") {
		$radio_Skip_Text = true;
	}
	$strFields = null;
	$strQuestionMarks = null;
	$arrValues = null;
	$strSQL = "SELECT * FROM " . $request_Table . " WHERE " . $strIDName . " = ?";
	$stmt = $conn->prepare($strSQL);
	$stmt->execute([$strIDValue]);
	$rs = $stmt->fetch(PDO::FETCH_NUM);
	$iCount = count($rs);
	for ($i = 1; $i < $iCount; $i++) {
		$meta = $stmt->getColumnMeta($i);
		$xType = $meta["native_type"];
		$sName = $meta["name"];
		$xValue = $rs[$i];
		if ($xType == "TINY") {
			if ($sName == "Hidden") {
				$xValue = 1;
			} else {
				$xValue = 0;
			}
		}
		/*
		elseif ($xType == "DATE" && !empty($xValue)) {
			$xValue =  date('Y-m-d');
		} elseif ($xType == "DATETIME") {
			$xValue =  date('Y-m-d H:i:s');
		} elseif ($radio_Skip_Text && ($xType == "BLOB" || $xType == "TEXT" || $xType == "MEDIUMTEXT" || $xType == "LONGTEXT")) {
			$xValue = null;
		}
		*/
		if (!empty($strFields)) {
			$strFields .= ",";
			$strQuestionMarks .= ",";
		}
		$strFields .= $sName;
		$strQuestionMarks .= " ?";
		$arrValues[] = $xValue;
	}
	$stmt = null;
	$rs = null;
	if (!empty($strFields) && !empty($arrValues)) {
		$sql = "INSERT INTO " . $request_Table . " (" . $strFields . ") VALUES (" . $strQuestionMarks . ")";
		$stmt = $conn->prepare($sql);

		$stmt->execute($arrValues);
		$stmt = null;
	}
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
		<h2><?= lngTable ?>: <?= strtoupper($request_Table) ?><br><?= lngCopyRecord ?></h2>
	</header>
	<section class="maxWidth">
		<h1>
			Conform the Copying of Record from <?= lngTable ?></b>: <?= $request_Table ?> -
			<?= $strIDName ?>: <?= $strIDValue ?><br>
		</h1>
		<h4>
			Not Copy: <a href="<?= $strRedirect ?>"><b><?= lngBackToRecodList ?></b></a>
		</h4>
		<form id="editForm" action="copy.php?strIDName=<?= $strIDName ?>&strIDValue=<?= $strIDValue . $strReturnQuery ?>" method="post">
			<fieldset>
				<input type="hidden" name="ConfirmCopy" value="Copy">
				<p>All <b>Boolean Fields</b> are set to False.<br>
					All Boolean Fields with teh name <b>Hidden</b> are set to True.<br>
					All <b>Dates</b> are set to Current Date.</p>
				<p>Do Not Copy the Content of Text Fields (BLOB. TEXT, MEDIUMTEXT, LONGTEXT):
					<input type="checkbox" name="SkipText" value="Yes" checked>
				</p>
			</fieldset>
			<fieldset>
				<p><input type="submit" name="Action" value="<?= lngCopyConfirm ?>"></p>
			</fieldset>
		</form>

		<h4>
			Not Copy: <a href="<?= $strRedirect ?>"><b><?= lngBackToRecodList ?></b></a>
		</h4>
	</section>
</body>

</html>