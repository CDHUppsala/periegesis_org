<?php
include __DIR__ . '/config_PDF.php';
include PROJECT_PHP . "/default_header_apps.php";
?>
</head>

<body id="body_PDF">
    <header>
        <div class="row">
            <div class="left">
                <?php
                if (empty($strLogoReturn)) {
                    $strLogoReturn = $str_LogoImageSmall;
                }
                if (!empty($strLogoReturn)) { ?>
                    <a href="index.php"><img alt="<?php echo $strSiteTitle ?>" src="../images/<?= $strLogoReturn ?>" /></a>
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
                <a href="ps_PDF.php" aria-label="Read the Page"></a>
            </div>
        </div>
    </header>

    <div class="content">
        <main>
            <?php
            if (intval($intArchID) == 0) { ?>
                <div class="scroll text">
                    <h1><?= $strPDFTitle ?></h1>
                    <?php
                    if ($strPDFImage != "") { ?>
                        <div class="image_center"><img alt="<?= $strPDFTitle ?>" src="../../images/<?= $strPDFImage ?>"></div>
                    <?php
                    }
                    if ($memoNote != "") {
                        echo $memoNote;
                    } ?>
                </div>
            <?php
            } elseif ($radioLoginToRead && !empty($strHiddenFilesName)) { ?>
                <h1><?= $strArchiveName ?></h1>
                <div class="text"><?= $strMetaNote ?></div>
            <?php
                sx_LoadRequestedFile($strHiddenFilesName);
            } elseif (!empty($strArchiveURL)) {
                get_Any_Media($strArchiveURL, 'Center', '');
            } else {
                echo '<h1>Error!</h1><p>The file does not exist in the server!</p>';
            } ?>
        </main>
        <aside>
            <?php
            include "sxNav_Includes.php";
            ?>
        </aside>
    </div>
</body>

</html>