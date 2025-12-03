<?php
include realpath(dirname(dirname(__DIR__)) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/login/adminLevelPages.php";
include PROJECT_ADMIN ."/functionsDBConn.php";

include "functions.php";
include "functions_upload.php";
//include "config.php";

const PATH_ToImportFolder = PROJECT_PRIVATE . "/import_export_files/";


$radioEnableCheckAction = false;
$radioEnableUploadAction = false;

?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>SX CMS - Update Database from XML Files</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js"></script>
	<script src="../../js/jq/jquery.min.js"></script>
	<script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js"></script>
</head>

<body id="bodyUplodXML" class="body">

	<header id="header">
		<h2>Upload XML Files to the Database</h2>
	</header>
	<h2><?= lngSelectTableAndUploadFile ?></h2>
	<div class="row">
		<div style="flex: 1 1 60%">
			<?php
			include "sxGetXMLFile.php";
			if (!empty($request_Table)) {
				include "sxOpenXMLFile.php";
			} ?>
		</div>
		<div style="flex: 1 1 40%">
			<?php include "sxCheckXMLFile.php"; ?>
		</div>
	</div>
</body>

</html>