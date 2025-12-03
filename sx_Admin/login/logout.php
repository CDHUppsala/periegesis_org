<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");

$strUserFirstName = "";
if (isset($_SESSION["UserFirstName"])) {
	$strUserFirstName = $_SESSION["UserFirstName"];
}
/**
 * Just clear all data of all session variable:
 */
$_SESSION[] = array();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?= lngSiteTitle ?></title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body>
	<div style="max-width: 720px; margin: 50px auto;">
		<h1><?= lngSiteTitle ?></h1>
		<?php if (!empty($strUserFirstName)) { ?>
			<h3><?= lngByeBye ?>, <?= $strUserFirstName ?></h3>
			<p><?= lngYouAreLogouted ?></p>
		<?php } ?>
		<hr>
		<div style="float: right; font-weight: bold"><a href="../../<?= sx_DefaultSiteLang ?>">Home Page</a></div>
		<form action="login.php" target="_top" method="post">
			<p><input class="button" type="submit" value="Login" name="GoToLogin"></p>
		</form>
		<hr>
		<p><?= lngProgramName ?> <br><?= lngVersion ?> <br>Public Sphere</p>
	</div>
</body>

</html>