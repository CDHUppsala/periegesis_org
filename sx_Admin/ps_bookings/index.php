<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsTableName.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include __DIR__ . "/functions.php";

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
	<script src="reservations.js"></script>
</head>

<body class="body">
	<header id="header">
		<h2>Rooms Reservations by Month</h2>
		<?php
		include __DIR__ . "/nav_date.php";
		?>
	</header>
	<div>
		<?php
		include __DIR__ . "/rooms.php";
		include __DIR__ . "/booking_form.php";
		?>
	</div>
</body>

</html>