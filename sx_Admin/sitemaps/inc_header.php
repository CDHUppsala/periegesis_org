<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Create Sitemap XML</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body class="body">
    <header id="header">
        <h2><?= lngCreateXMLSiteMaps ?></h2>
        <div>
            <a class="button" href="create_xml.php">Root Sitemap</a>
            <?php if (sx_radioUseTextMaps) { ?>
                <a class="button" href="create_xml.php?type=Texts">For Texts</a>
            <?php }
            if (sx_radioUseArticleMaps) { ?>
                <a class="button" href="create_xml.php?type=Articles">For Articles</a>
            <?php }
            if (sx_radioUseProductMaps) { ?>
                <a class="button" href="create_xml.php?type=Products">For Products</a>
            <?php }
            if (sx_radioUseAboutMaps) { ?>
                <a class="button" href="create_xml.php?type=About">For About</a>
            <?php }
            if (sx_radioUseAuthorMaps) { ?>
                <a class="button" href="create_xml.php?type=Authors">For Authors</a>
            <?php }
            if (sx_radioUseThemesMaps) { ?>
                <a class="button" href="create_xml.php?type=Themes">For Themes</a>
            <?php }
            if (sx_radioUseEventMaps) { ?>
                <a class="button" href="create_xml.php?type=Events">For Events</a>
            <?php }
            if (sx_radioUseConferneceMaps) { ?>
                <a class="button" href="create_xml.php?type=Conferences">For Conferences</a>
                <a class="button" href="create_xml.php?type=Sessions">For Sessions</a>
                <a class="button" href="create_xml.php?type=Papers">For Papers</a>
            <?php }
            ?>
        </div>
    </header>