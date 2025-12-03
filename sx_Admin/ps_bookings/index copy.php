<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsTableName.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

?>
<!DOCTYPE html>
<html lang="<?= sx_DefaultAdminLang ?>">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Public Sphere Content Management System</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
	<link rel="stylesheet" href="sx_Calendar.css">
	<script src="../js/jq/jquery.min.js"></script>
	<script src="bookings.js"></script>
	<style>

	</style>

</head>

<body class="body">
	<header id="header" class="flex_between">
		<h2>Table Reservations by Day</h2>
	</header>
	<div>
		<?php
		include __DIR__ . "/functions.php";
		?>
		<div class="row">
			<div>
				<?php
				include __DIR__ . "/calendar.php";
				?>
			</div>
			<div style="flex:2">
				<?php
				include __DIR__ . "/rums.php";
				?>
			</div>
		</div>
	</div>
</body>

</html>