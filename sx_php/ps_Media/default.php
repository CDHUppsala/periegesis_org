<?php
include __DIR__ ."/config_MM.php";
include PROJECT_PHP . "/default_header_apps.php";
?>
</head>

<body id="body_Media">
    <header>
        <div class="row">
            <div class="left">
                <?php
                if ($strLogoReturn != "") { ?>
                    <a href="index.php" title="<?= $strSiteTitle ?>"><img src="../../images/<?= $strLogoReturn ?>" /></a>
                <?php
                } else { ?>
                    <a href="index.php"><?= $strSiteTitle ?></a>
                <?php
                } ?>
            </div>
            <div class="sxNavMarker" id="jqNavMarker">
                <svg class="sx_svg">
                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_left_right"></use>
                </svg>
            </div>
            <div class="right">
                <a href="ps_media.php"> </a>
            </div>
        </div>
    </header>

    <div class="content">
        <main>
            <?php
            if (intval(INT__ArchID) == 0) { ?>
                <div class="scroll">
                    <div class="text">
                        <h1><?= $strMediaTitle ?></h1>
                        <figure><img src="../images/<?= $strmediasetupImage ?>"></figure>
                        <?php
                        if ($strMediaNote != "") {
                            echo $strMediaNote;
                        } ?>
                    </div>
                </div>
            <?php
            } else { ?>
                <ul class="tabLinks jqTabLinks">
                    <li class="active"><?= lngPlayer ?></li>
                    <li><?= lngDescription ?></li>
                </ul>
                <ul>
                    <li class="player">
                        <div>
                        <h1><?= $strMediaTitle ?></h1>
                        <?php
                         get_Any_Media($sArchiveURL, 'Center',''); 
                        ?>
                        </div>
                    </li>
                    <li class="player_notes text" style="display:none">
                        <h1><?= $strMediaTitle ?></h1>
                        <?php
                        if ($strMediaNote != "") { ?>
                            <?= $strMediaNote ?>
                        <?php
                        } ?>
                    </li>
                </ul>
            <?php
            } ?>
        </main>
        <aside>
            <?php include __DIR__ ."/nav_MM.php"; ?>
        </aside>
    </div>
</body>

</html>