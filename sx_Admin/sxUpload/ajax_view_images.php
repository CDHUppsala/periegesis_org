<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include __DIR__ . "/config_upload.php";

$levelsBack = "../../";

$strTbl = "";
if (!empty($_GET["tbl"])) {
	$strTbl = $_GET["tbl"];
}
if ($strTbl == "Products") {
	$strSelectedFolder = "imgProducts";
} elseif ($strTbl = "MediaArchives") {
	$strSelectedFolder = "imgMedia";
} else {
	$strSelectedFolder = "images";
}


/**
 * Get the folder number (as number in the array of uploadable folder)
 * Show the first folder by default
 */

$intFolder = 0;
if (isset($_POST["SelectedFolder"])) {
	if (is_numeric($_POST["SelectedFolder"])) {
		$intFolder = (int) $_POST["SelectedFolder"];
		$_SESSION["FolderIndex"] = $intFolder;
	}
} elseif (isset($_SESSION["FolderIndex"])) {
	$intFolder = (int) $_SESSION["FolderIndex"];
}
if (intval($intFolder) == 0) {
	$intFolder = 0;
}

/**
 * Get the folder name (from the folder number above)
 */

$iCount = count(ARR_UploadableFolders);
if ($intFolder >= 0) {
	$strSelectedFolder  = trim(ARR_UploadableFolders[$intFolder]);
	if (substr($strSelectedFolder, -1) != "/") {
		$strSelectedFolder = $strSelectedFolder . "/";
	}
} else {
	for ($x = 0; $x < $iCount; $x++) {
		if ($strSelectedFolder == trim(ARR_UploadableFolders[$x])) {
			if (substr($strSelectedFolder, -1) != "/") {
				$strSelectedFolder = $strSelectedFolder . "/";
				//exit;
			}
			$intFolder = $x;
			break;
		}
	}
}  ?>

<div id="bodyUpload" class="jqInsertImages">
	<h3><?= lngCopyImages ?></h3>
	<p><?= lngMarkToCopyImages ?></p>
	<form action="sxUpload/ajax_view_images.php" method="post" name="LoadSelectForm" id="jqLoadSelectForm">
		<p><b><?= lngSelectFolder ?></b>:<br>
			<select name="SelectedFolder" style="width: 50%">
				<?php
				$strLast = "";
				for ($f = 0; $f < $iCount; $f++) {
					$strSelected = "";
					$strLoop = trim(ARR_UploadableFolders[$f]);

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
		 * Add the Root Directory to the selected folder ($strSelectedFolder) to get a physical path:
		 * 		c:\xxx\htdocs/imageFolder/imageSubFolder
		 * Remove the Root Directory to get relative paths for showing of images and image sizes
		 * 		../../imageFolder/imageSubFolder/image.jpg
		 * Remove the MAIN (Parent) folder for copying images to table fields in database
		 * 		image.jpg or imageSubFolder/image.jpg
		 */
		$strSelectedFolderPath = sx_RootPath . $strSelectedFolder;
		if (!empty($strSelectedFolderPath)) {
			if (!is_dir($strSelectedFolderPath)) { ?>
				<tr>
					<td><b><?= lngTheRequestedFolderDoesNotExist ?></b></td>
				</tr>
				<?php
			} else {
				$arrFiles = sx_getFolderContents($strSelectedFolderPath, "is_file");
				if (is_array($arrFiles)) {
					$jsonFiles = json_encode($arrFiles, JSON_UNESCAPED_UNICODE);
					$arrFiles = json_decode($jsonFiles, true);
					$z = 0;

					foreach ($arrFiles as $strFileName) {
						$strPathToCopy = "";
						if (($pos = strpos($strSelectedFolder, "/")) !== false) {
							$strPathToCopy = substr($strSelectedFolder, $pos + 1);
						}
						$strPathToCopy .= $strFileName;
						$strFilePath = $levelsBack . $strSelectedFolder . $strFileName;

						if (file_exists($strFilePath) && is_readable($strFilePath)) {
							$imageInfo = getimagesize($strFilePath);
							$isImage = sx_check_image_suffix($strFileName);
							$bgClass = (($z + 1) % 2) ? 'class="bg_gray"' : 'class="bg_image"';
				?>
							<tr <?= $bgClass ?>>
								<td colspan="2">
									<input title="<?= $strPathToCopy ?>" type="text" value="<?= $strPathToCopy ?>" name="<?= $z ?>">
								</td>
							</tr>
							<tr <?= $bgClass ?>>
								<td style="white-space: norap">
									<input type="checkbox" name="<?= $z ?>" value="<?= $strPathToCopy ?>">
									<span><?= lngSize ?>: <?= number_format(filesize($strFilePath), 0, ",", " ") ?></span>
									<?php if ($imageInfo) {
										echo "<br>WxH:  $imageInfo[0]x$imageInfo[1]";
									} ?>
								</td>
								<?php
								if ($imageInfo === false && $isImage === false) {
									echo "<td></td>";
								} else {
									echo '<td><img src="' . $strFilePath . '" alt="Image Preview"></td>';
								} ?>

							</tr>
					<?php
						}
						$z++;
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