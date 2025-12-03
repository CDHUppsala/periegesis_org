<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere CMS - Groups of Tables</title>
	<style>
		pre {
            position: absolute;
			top: 0; right: 0; bottom: 0; left: 0;
			padding: 40px;
            overflow: scroll;
		}
	</style>
</head>

<body>
<?php
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

?>
</body>
</html>

