<?php
include realpath(dirname(dirname(__DIR__)) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/login/adminLevelPages.php";

include "config.php";
include "functions.php";

?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Validat XML-Files aggainst XSD-Files</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">

	<script src="../../js/jq/jquery.min.js"></script>
	<script>
		$(document).ready(function() {
			$('fieldset').on("click", function() {
				$('#message').hide();
			});
			$('#validation').on('submit', function(event) {
				event.preventDefault();

				$.ajax({
					url: "validate_ajax.php",
					type: "POST",
					data: new FormData(this),
					cache: false,
					contentType: false,
					processData: false,
					success: function(result) {
						$('#message').html(result).show(300);
					}
				})
			});
		});
	</script>

</head>

<body class="body">
	<header id="header">
		<h2>Validat XML-Files aggainst XSD-Files</h2>
	</header>

	<h2>Select in pare an XML-File and its corresponding XSD-File</h2>
	<div class="maxWidth">
		<form action="validate_ajax.php" method="post" name="validation" id="validation" enctype="multipart/form-data">
			<h4>Files from Server Folder</h4>
			<fieldset class="row">
				<div>
					<?php
					if ($s_ImportFolder != "") {
						$arrFiles = sx_getFolderFilesByExtention($s_FolderPath, "xml");
						if (!is_array($arrFiles)) { ?>
							<p><?= lngTheRequestedFolderDoesNotExist ?></p>
						<?php
						} else { ?>
							<label>Select an XML File:</label><br>
							<select Name="XMLFile">
								<option VALUE="">Select an XML File</option>
								<?php
								$iCount = count($arrFiles);
								for ($sx = 0; $sx < $iCount; $sx++) {
									$loopFile = trim($arrFiles[$sx]); ?>
									<option value="<?= $loopFile ?>"><?= $loopFile ?></option>
								<?php
								} ?>

							</select>
				</div>
				<div>
				<?php
						}

						$arrFiles = sx_getFolderFilesByExtention($s_FolderPath, "xsd");
						if (!is_array($arrFiles)) { ?>
					<p><?= lngTheRequestedFolderDoesNotExist ?></p>
				<?php
						} else { ?>
					<label>Select an XSD File:</label><br>
					<select Name="XSDFile">
						<option VALUE="">Select an XSD File</option>
						<?php
							$iCount = count($arrFiles);
							for ($sx = 0; $sx < $iCount; $sx++) {
								$loopFile = trim($arrFiles[$sx]); ?>
							<option value="<?= $loopFile ?>"><?= $loopFile ?></option>
						<?php
							} ?>
					</select>
				</div>
		<?php
						}
					}
		?>
			</fieldset>
			<h4>Files from Local Folder</h4>
			<fieldset class="row">
				<div>
					<label>Select XML File</label><br>
					<input type="file" name="XML_File" id="XML_File" />
				</div>
				<div>
					<label>Select XSD File</label><br>
					<input type="file" name="XSD_File" id="XSD_File" /><br>
				</div>
			</fieldset>
			<fieldset class="row" style="justify-content: space-between">
				<input type="reset" value="Reset" name="reset">
				<input type="submit" value="Validate" name="SelectFileTable">
			</fieldset>
		</form>
	</div>
	<div id="message">
	</div>
</body>

</html>