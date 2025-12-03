<?php
include "functionsLanguage.php";
include "login/lockPage.php";
include "login/adminLevelPages.php";
include "functionsDBConn.php";

$copyMsg = "";
$strToFolder = "dbBackup/";
$fromPath = $_SERVER['DOCUMENT_ROOT'] . "/../private/";
$toPath = $fromPath . $strToFolder;

if (!is_dir($fromPath)) {
    echo "<h3>Error in the definition of paths.</h3>";
    exit();
}

/**
 * Create the destination folder, if not exists
 */
if (!is_dir($toPath)) {
    mkdir($toPath);
}

/**
 * Copy Source files to the Backup Folder
 */
if (!empty(@$_POST["dbFiles"])) {
    /**
     * Copy only checked files
     */
    $err = false;
    foreach ($_POST as $name => $value) {
        if (strpos($name, "sxSel__") === false) {
            if ($value == "ON") {
                $prefix = $_POST["sxSel__" . $name];
                $file = $_POST["sxIn__" . $name];
                if (!copy($fromPath . $file, $toPath . $prefix . "_" . $file)) {
                    $err = true;
                }
            }
        }
    }
    if ($err) {
        $copyMsg = "Error: one or more files have not been coped!";
        $classMsg = "errMsg";
    } else {
        $copyMsg = "The files have been copied successfully!";
        $classMsg = "msgSuccess";
    }
}
?>
<!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Public Sphere Content Management System - Back up Databases</title>
<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>
<body class="body">
	<header id="header">
		<h2><?=lngBackupDatabasesToTheFolder . ": <span>" . $strToFolder?></span></h2>
	</header>
    <div class="maxWidthWide">
	<?php
if (!empty($copyMsg)) {?>
	<div class="<?=$classMsg?>"><?=$copyMsg?></div>
	<?php }?>
	<h3><?=lngChooseTheDatabasesToBackup?>:</h3>
	<form action="sxBackupFiles.php" method="post" name="copyFiles">
        <table style="width: 100%">
			<tr>
				<th><?=lngDatabase?></th>
				<th><?=lngSetPrefix?></th>
				<th><?=lngExisingBackupFiles?></th>
				<th><?=lngDate?></th>
				<th><?=lngSize?></th>
			</tr>
	<?php
if (!empty($fromPath)) {
    $arrFromFiles = scandir($fromPath);
    $arrToFiles = scandir($toPath);

    $num = count($arrFromFiles);
    for ($i = 2; $i < $num; $i++) {
        $FromFile = $arrFromFiles[$i];
        if (is_file($fromPath . $FromFile)) {
            /**
             * Send the file name as value in a hidden input, as PHP changes Dot (.) in names to underscore (_)
             */
            ?>
			<tr>
				<td>
					<input type="hidden" name="sxIn__<?=$FromFile?>" value="<?=$FromFile?>">
					<input type="checkbox" name="<?=$FromFile?>" value="ON"> <?=$FromFile?>
				</td>
				<td>
					<select name="sxSel__<?=$FromFile?>" size="1">
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					</select>
				</td>
				<?php

            if (is_array($arrToFiles)) {
                $count = count($arrToFiles);
                $radioTemp = true;
                $iLoop = 0;
                for ($z = 2; $z < $count; $z++) {
                    $ToFile = $arrToFiles[$z];
                    if (strpos($ToFile, $FromFile) !== false && is_file($toPath . $ToFile)) {
                        $radioTemp = false;
                        if ($iLoop > 0) {
                            echo "</tr><tr><td></td><td></td>";
                        }?>
						<td><?=$ToFile?></td>
						<td><?=date('Y-m-d H:i:s', filemtime($toPath . $ToFile))?></td>
						<td class="alignRight"><?=number_format(filesize($toPath . $ToFile), 0, ",", " ")?> kb</td>
						<?php $iLoop = $iLoop + 1;
                    }
                }
                if ($radioTemp) {
                    echo "<td></td><td></td><td></td>";
                }
            } else {
                echo "<td></td><td></td><td></td>";
            }
            ?>
			</tr>
			<?php }
    }
    $arrFromFiles = null;
    $arrToFiles = null;

}?>
    	</table>
        <p class="floatRight">
    		<input type="submit" value="Copy Databases" name="dbFiles">
        </p>
    <p><a href="sxBackupFilesRestore.php"><?=lngRestoreBackupedDatabases?></a></p>
    </form>
</div>
</body>
</html>