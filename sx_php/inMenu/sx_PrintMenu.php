<?php
include __DIR__ . "/config_menu.php";

if ($strExport == "word") {
    $radio_UseLunchImages = false;
}
?>
<style>
    td {
        vertical-align: top;
    }
    td h3 {
        padding-top: 0;
        margin-top: 0;
    }

    td:nth-last-child(2) {
        white-space: nowrap;
    }

    td:last-child {
        text-align: right;
    }
</style>

<body>
    <?php
    if (empty($strExport)) { ?>
        <div style="margin: 12px 20px;">
        <?php
    }
    echo '<h3><a href="' . sx_ROOT_HOST . '">' . str_SiteTitle . '</a></h3>';

    if ($sPrint == "dinnermenu") {
        include __DIR__ . "/print_dinner_menu.php";
    } elseif ($sPrint == "lunchmenu") {
        include __DIR__ . "/print_lunch_menu.php";
    }

    if (empty($strExport)) { ?>
        </div>
    <?php
    } ?>
    <hr>
    <p style="text-align: center;">
        <?= lngPrintedDate ?>: <?= Date("Y-m-d") ?><br>
        <?= lngFromWebPage ?>: <a href="<?= sx_ROOT_HOST ?>"><?= str_SiteTitle ?></a><br>
        <?= sx_LOCATION ?>
    </p>
</body>

</html>
<?php
if ($strExport == "print") { ?>
    <script>
        window.print();
    </script>
<?php
} ?>