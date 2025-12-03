<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

$str_iFrameSource = "exportMainPage.php";
if (isset($_GET["relaod"]) && $_GET["relaod"] == "yes") {
	$str_iFrameSource = "configTableGroups.php";
}
?>
<!DOCTYPE html>
<html lang="<?= sx_DefaultAdminLang ?>">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Public Sphere CMS - Groups of Tables</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<style>
		#menuBG {
			bottom: 0;
			padding-bottom: 40px;
		}
	</style>
	<script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js"></script>
	<script src="../js/jq/jquery.min.js"></script>
	<script src="<?php echo sx_ADMIN_DEV ?>config/js/jqConfigFunctions.js"></script>
	<base target="frame">
</head>

<body>
	<div id="jqToggleContent" class="aside_show"></div>
	<div id="page">
		<div id="aside">
			<?php include __DIR__ . "/dbTables.php"; ?>
		</div>
		<div id="main">
			<iframe name="frame" id="frame" src="<?= $str_iFrameSource ?>" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
		</div>
	</div>

</body>

</html>