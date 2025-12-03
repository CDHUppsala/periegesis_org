<?php
include __DIR__ . "/config_events.php";
?>
<style>
    th,
    td {
        vertical-align: top;
        text-align: left;
        padding: 4px;
        border: 1px solid #999999
    }

    th {
        white-space: nowrap;
    }

    figure {
        padding: 0;
        margin: 0;
    }
</style>

<body>
    <?php
    if ($strExport == "") { ?>
        <div style="margin: 20px;">
        <?php
    }

    if (!isset($_GET["pg"]) || empty($_GET["pg"])) {
        $conn = null;
        echo "<h2>No Records Found</h2>";
        exit();
    } else {
        $sPage = $_GET["pg"];
    }

    $radioCalendar = false;
    $radioMonth = false;
    $radioWeek = false;
    $radioList = false;

    if ($sPage == "event") {
        $sx_url = $intEventID;
        include __DIR__ . "/prints/print_event.php";
    } elseif ($sPage == "calendar") {
        $sx_url = "calendar_" . $date_FirstMonthDate;
        $radioCalendar = true;
        include __DIR__ . "/prints/print_calendar.php";
    } elseif ($sPage == "month") {
        $sx_url = "month_" . $date_FirstMonthDate;
        $radioMonth = true;
        include __DIR__ . "/prints/print_month.php";
    } elseif ($sPage == "week") {
        $sx_url = "week_" . $date_ThisMonday;
        $radioWeek = true;
        include __DIR__ . "/prints/print_week.php";
    } elseif ($sPage == "list") {
        $sx_url = "list_" . date('Y-m-d');
        $radioList = true;
        include __DIR__ . "/prints/print_list.php";
    }

    if ($strExport == "") { ?>
        </div>
    <?php
    } ?>
    <hr>
    <p style="text-align: center;">
        <?= lngPrintedDate ?>: <?= Date("Y-m-d") ?><br>
        <?= lngFromWebPage ?>: <b><?= str_SiteTitle ?></b><br>
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