<?php

include __DIR__ . "/functionsLanguage.php";
include __DIR__ . "/login/lockPage.php";

/**
 * The IDs content and main are also used by jQuery in jqDefaultMenu
 * To Show/Hide the content, javscript counts 
 * the width of content and the left margin of main
 */
?>

<!DOCTYPE html>
<html lang="<?= sx_DefaultAdminLang ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Public Sphere Content Management System</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js?v=2023"></script>
    <script src="js/jq/jquery.min.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqDefaultMenu.js?v=2023"></script>

    <base target="frame">
</head>

<body>
    <div id="jqToggleContent" class="aside_show"></div>
    <div id="page">
        <div id="aside">
            <?php include "content.php"; ?>
        </div>
        <div id="main">
            <iframe name="frame" id="frame" title="Main Pages" src="main.php" frameborder="0" marginheight="0" marginwidth="0" scrolling="auto"></iframe>
        </div>
    </div>
</body>

</html>