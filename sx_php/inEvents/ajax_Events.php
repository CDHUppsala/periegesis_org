<?php

include dirname(__DIR__) . "/sx_config.php";
include dirname(__DIR__) . "/basic_MediaFunctions.php";
include dirname(__DIR__) . "/basic_PrintFunctions.php";

include __DIR__ . "/functions_queries.php";
include __DIR__ . "/config_dates.php";

$strLoad = "";
if (isset($_GET["load"])) {
    $strLoad = $_GET["load"];
}

$radioWeek = false;
$radioMonth = false;
$radioCalendar = false;

if (return_Is_Date($date_ThisMonday) && $strLoad == "week") {
    $radioWeek = true;
    include __DIR__ . "/events_by_week.php"
?>
	<script>
		sxLoadWeekTabsFunction();
	</script>
<?php
} elseif (return_Is_Date($dDate) && $strLoad == "month") {
    $radioMonth = true;
    include __DIR__ . "/events_by_month.php";
} elseif (return_Is_Date($dDate) && $strLoad == "calendar") {
    $radioCalendar = true;
    include __DIR__ . "/events_by_calendar.php"; ?>
	<script>
		sxLoadAbsoluteTD();
	</script>
<?php
} else {
        echo "<h3>The page cannot be displayed!</h3>";
    }
$conn = null;
?>
<script>
	sxLoadUniversalAjax();
</script>