<?php
include __DIR__ . "/config_gallery.php";
include PROJECT_PHP . "/default_header_apps.php";

?>
<script>
    var int_PhotoID = 0;
</script>
</head>

<body>
    <header>
        <div class="row">
            <div class="left">
                <?php
                if (!empty($strLogoReturn)) { ?>
                    <a href="index.php" title="<?= $strSiteTitle ?>"><img border="0" src="../images/<?= $strLogoReturn ?>" /></a>
                <?php
                } else { ?>
                    <a href="index.php"><?= $strSiteTitle ?></a>
                <?php
                } ?>
            </div>
            <div class="middle">
                <div class="sxNavMarker sxNavInfo" id="jqNavLeft"></div>
                <div class="sxNavMarker sxNavMenu" id="jqNavRight"></div>
            </div>
            <div class="right">
                <a title="Reload Default Page" href="ps_gallery.php"></a>
            </div>
        </div>
    </header>
    <main>
        <div class="left">
            <?php include "main_Info.php"; ?>
        </div>
        <div class="middle">
            <?php include "main_Photo.php"; ?>
        </div>
        <div class="right">
            <?php include "main_Nav.php"; ?>
        </div>
    </main>
    <footer>
        <?php include "footer_Thumps.php"; ?>
    </footer>
</body>

</html>