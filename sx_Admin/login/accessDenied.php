<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
?>
<!DOCTYPE html>
<html id="studiox">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere - DB Content Management</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body>
	<div style="width: 720px; margin: 50px auto;">
		<h1><?= lngSiteTitle ?></h>
			<h3><?= lngOnlyForWebMasters ?></h3>
			<?php if (@$_GET["sx"] == "sx") { ?>
				<p class="errMsg">You must activate sessions from your browser in order to login!</p>
			<?php } ?>
			<hr>
			<p style="float: right; font-weight: bold"><a href="../../<?= sx_DefaultSiteLang ?>/index.php">Home Page</a></p>
			<form action="login.php" target="_top" method="POST">
				<p><input class="button" type="submit" value="Login" name="GoToLogin"></p>
			</form>
			<hr>
			<p><?= lngProgramName ?> | <?= lngVersion ?> |
				<!--a target=_blank href="http://www.publicsphere.net"-->Piblic Sphere
			</p>
	</div>
</body>

</html>