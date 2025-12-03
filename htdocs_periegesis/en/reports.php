<?php
include __DIR__ . "/siteLang/sxLang.php";
include PROJECT_PHP ."/sx_config.php";
include PROJECT_PHP ."/inReports/config.php";
include PROJECT_PHP ."/defaultHeader.php";
?>
</head>

<body id="body_reports">
    <?php require PROJECT_PHP ."/sx_Header.php"; ?>
    <div class="page">
        <div class="content">
            <main class="main">
                <?php
                include PROJECT_PHP ."/inReports/includes_main.php";
                ?>
            </main>
            <aside class="aside">
                <?php
                include PROJECT_PHP ."/inReports/includes_aside.php";
                ?>
            </aside>
        </div>
    </div>
    <?php
    include PROJECT_PHP ."/sx_Footer.php";
    $conn = null;
    ?>
</body>

</html>