<?php
include realpath(dirname(__DIR__) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/login/adminLevelPages.php";
include PROJECT_ADMIN ."/functionsDBConn.php";
include "config_upload.php";
/**
 * View the conference files uploaded by login participents
 */
function sx_GetParicipant($id)
{
	$conn = dbconn();
	$_retval = "";
	if (intval($id) > 0) {
		$sxSQL = "SELECT CONCAT(LastName, ' ', FirstName) AS ParticipantName FROM conf_participants WHERE ParticipantID = " . $id;
		$sxrs = $conn->query($sxSQL);
		$_retval = $sxrs->fetch(PDO::FETCH_COLUMN);
		$sxrs = null;
	}
	return $_retval;
}

$levelsBack = "../../";
$strSelectedFolder = "images";

/**
 * Get the folder number (as number in the array of uploadable folder)
 * Show the first folder by default
 */

$intFolder = 0;
if (isset($_POST["SelectedFolder"])) {
	if (is_numeric($_POST["SelectedFolder"])) {
		$intFolder = intval($_POST["SelectedFolder"]);
		$_SESSION["FolderIndex"] = $intFolder;
	}
} elseif (isset($_SESSION["FolderIndex"])) {
	$intFolder = $_SESSION["FolderIndex"];
}
if (intval($intFolder) == 0) {
	$intFolder = 0;
}

/**
 * Get the folder name (from the folder number above)
 */

$arrConferenceFolders = array_values(preg_grep('~/conf_~i', ARR_UploadableFolders));

$iCount = count($arrConferenceFolders);
if ($intFolder >= 0) {
	$strSelectedFolder  = trim($arrConferenceFolders[$intFolder]);
} else {
	for ($x = 0; $x < $iCount; $x++) {
		if ($strSelectedFolder == trim($arrConferenceFolders[$x])) {
			$intFolder = $x;
			break;
		}
	}
}
?>

<div id="bodyUpload" class="jqInsertImages">
	<h3><?= lngCopyImages ?></h3>
	<p><?= lngMarkToCopyImages ?></p>
	<form action="sxUpload/ajax_view_conference_files.php" method="post" name="LoadSelectForm" id="jqLoadSelectForm">
		<p><b><?= lngSelectFolder ?></b>:<br>
			<select name="SelectedFolder" style="width: 50%">
				<?php
				$strLast = "";
				for ($f = 0; $f < $iCount; $f++) {
					$strSelected = "";
					$strLoop = str_replace(sx_RootPath, "", trim($arrConferenceFolders[$f]));
					$strCurr = explode("/", $strLoop)[0];
					if ($strCurr != $strLast) {
						if ($f > 0) {
							echo "</optgroup>";
						}
						echo '<optgroup label="' . $strCurr . '">';
					}
					if ($f == intval($intFolder)) {
						$strSelected = " selected";
					} ?>
					<option VALUE="<?= $f ?>" <?= $strSelected ?>><?= $strLoop ?></option>
				<?php
					$strLast = $strCurr;
				} ?>
			</select>
			<input type="submit" value="<?= lngOpenFolder ?>" name="viewThisFolder">
		</p>
	</form>
	<table class="no_bg">
		<?php

		/**
		 * The selected folder ($strSelectedFolder) is a physical path:
		 * 		c:\xxx\htdocs/imageFolder/imageSubFolder
		 * Remove the Root Directory to get relative paths for showing of images and image sizes
		 * 		../../imageFolder/imageSubFolder/image.jpg
		 * Remove the MAIN (first) image folder for copying images to table fields in database
		 * 		image.jpg or imageSubFolder/image.jpg
		 */
		if (!empty($strSelectedFolder)) {
			if (!is_dir($strSelectedFolder)) { ?>
				<tr>
					<td><b><?= lngTheRequestedFolderDoesNotExist ?></b></td>
				</tr>
				<?php
			} else {
				$arrImages = sx_getFolderContents($strSelectedFolder, "is_file");
				if (is_array($arrImages)) {
					$iCount = count($arrImages);
					$i_LastID = 0;
					$i_ParticipantID = 0;
					$s_ParticipantName = "";
					for ($z = 0; $z < $iCount; $z++) {
						$strPathToCopy = "";
						$strShortFolderPath = str_replace(sx_RootPath, "", $strSelectedFolder);
						if ($pos = strpos($strShortFolderPath, "/")) {
							$strPathToCopy = substr($strShortFolderPath, $pos + 1) . "/";
						}
						$strFileName = $arrImages[$z];
						$strPathToCopy .= $strFileName;
						$strImagPath = $levelsBack . $strShortFolderPath . "/" . $strFileName;

						if (strpos($strFileName, "pid_") !== false) {
							$i_ParticipantID = explode("_", $strFileName)[1];
							if (intval($i_ParticipantID) > 0 && $i_ParticipantID != $i_LastID) {
								$s_ParticipantName = sx_GetParicipant($i_ParticipantID);
							}
						} else {
							$i_ParticipantID = 0;
							$s_ParticipantName = "";
						}
						$bgClass = "";
						if(($z +1) % 2) {
							$bgClass = ' class="bg_gray"';
						}
						?>
						<tr<?=$bgClass?>>
							<td colspan="2">
								<input title="<?= $strPathToCopy ?>" type="text" value="<?= $strPathToCopy ?>" name="<?= $z ?>">
							</td>
						</tr>
						<tr<?=$bgClass?>>
							<td>
								<input type="checkbox" name="<?= $z ?>" value="<?= $strPathToCopy ?>">
								<span><?= lngSize ?>: <?= number_format((filesize($strImagPath) / 1024), 0, ",", " ") ?> KB</span><br>
								<span title="Date Created"><?= date("Y-m-d H:i:s",filectime($strImagPath)) ?> C</span><br>
								<span title="Last Modified"><?= date("Y-m-d H:i:s",filemtime($strImagPath)) ?> M</span><br>
								<?php
								if ($i_ParticipantID > 0) { ?>
									<span><b>ID: <?= $i_ParticipantID . "</b> " . $s_ParticipantName ?></span>
								<?php } ?>
							</td>
							<td><img src="<?= $strImagPath ?>"></td>
						</tr>
					<?php
						$i_LastID = $i_ParticipantID;
					}
				} else { ?>
					<tr>
						<td><b><?= lngTheFolderIsEmpty ?></b></td>
					</tr>
		<?php
				}
			}
		}
		?>
	</table>
</div>
<script>
	sxAjaxLoadArchives();
</script>