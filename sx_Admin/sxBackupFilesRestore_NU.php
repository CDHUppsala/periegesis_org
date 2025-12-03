<?php
include "functionsLanguage.php";
include "login/lockPage.php";
include "login/adminLevelPages.php";
include "functionsDBConn.php";

$copyMsg = "";
$strFromFolder = "dbBackup/";
$toPath = $_SERVER['DOCUMENT_ROOT'] . "/../private/";
$fromPath = $toPath . $strFromFolder;

if (!is_dir($fromPath)) {
    echo "<h3>Error in the definition of paths.</h3>";
    exit();
}

/**
 * Create the source folder, if not exists
 */
if (!is_dir($fromPath)) {
    mkdir($fromPath);
}

/**
 * Copy Source files from the Backup Folder
 */
if (!empty(@$_POST["dbRestore"])) {
    /**
     * Copy only checked files
     */
    $err = false;
    foreach ($_POST as $name => $value) {
        if (strpos($name, "sxSel__") === false) {
            if ($value == "ON") {
                $file = $_POST["sxIn__" . $name];
                $Temp = substr($file, 2);
                if (!copy($fromPath . $file, $toPath . $Temp)) {
                    $err = true;
                }
            }
        }
    }
    if ($err) {
        $copyMsg = "Error: one or more files have not been restored!";
        $classMsg = "errMsg";
    } else {
        $copyMsg = "The files have been restored successfully!";
        $classMsg = "msgSuccess";
    }
}
?>
<!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Public Sphere Content Management System - Restore Backup Databases</title></head>
<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
<style>
td {padding: 6px 20px;}
</style>
</head>
<body class="body">

<header id="header">
	<h2><?=lngRestoreBackupedDatabasesFromFolder?> <span><?=$strFromFolder?></span></h2>
</header>
<div class="maxWidthWide">
	<?php if ($copyMsg != "") {?>
	<div class="<?=$classMsg?>"><?=$copyMsg?></div>
	<?php } else {?>
	<div class="errMsg">
		<?=lngWarningAboutDatabaseRestoring?>
	</div>
	<?php }?>
	<h3><?=lngChooseTheDatabasesToRestore?></h3>
	<form action="sxBackupFilesRestore.php" method="post" name="restoreFiles">
		<table style="width: 100%">
			<tr>
				<th><?=lngArchive?></th>
				<th><?=lngDate?></th>
				<th><?=lngSize?></th>
			</tr>
		<?php
if (!empty($fromPath)) {
    $arrFromFiles = scandir($fromPath);

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
				<td><?=date('Y-m-d H:i:s', filemtime($fromPath . $FromFile))?></td>
				<td class="alignRight"><?=number_format(filesize($fromPath . $FromFile), 0, ",", " ")?> kb</td>
		<?php }?>
			</tr>
	<?php }
    $arrFromFiles = null;

}?>
		</table>
        <p class="floatRight">
			<input type="submit" value="Restore Databases" name="dbRestore">
		</p>
	<p><a href="sxBackupFiles.php"><?=lngReturnToBackupDatabases?></a></p>
	</form>
</div>
</body>
</html>
