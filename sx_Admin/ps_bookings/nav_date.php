<?php
if (isset($date_RequestedDate) && sx_isDate($date_RequestedDate)) {
    $requestedDate = $date_RequestedDate;
} else {
    $requestedDate = date("Y-m-d");
}

$requestedDate = new DateTime($requestedDate);
$intThisYear = $requestedDate->format("Y");
$intThisMonth = $requestedDate->format("n");

?>
<table class="calendar_nav">
    <tr>
        <th><a href="<?php echo $strCurrentURL . "?month=" . (int)$iPrevMonth . "&year=" . $iPrevYear; ?>">&#x25C0;</a></th>
        <th><strong><?php echo lng_MonthNames[$iThisMonth - 1] . ' ' . $iThisYear; ?></strong></th>
        <th><a href="<?php echo $strCurrentURL . "?month=" . (int)$iNextMonth . "&year=" . $iNextYear; ?>">&#x25B6;</a> </th>
    </tr>
</table>

<form style="margin-left: auto" action="<?= $_SERVER['PHP_SELF'] ?>" name="calendarForm" method="post">
    <select name="month">
        <?php
        for ($i = 1; $i < 13; $i++) {
            $strSelected = "";
            if ($i == $intThisMonth) {
                $strSelected = " selected";
            } ?>
            <option value="<?= $i ?>" <?= $strSelected ?>><?= lng_MonthNames[$i - 1] ?></option>
        <?php
        } ?>
    </select>
    <select name="year">
        <?php
        $iYear = (int) date('Y');
        for ($r = $iYear -  1; $r < $iYear + 3; $r++) {
            $strSelected = "";
            if ($r == $intThisYear) {
                $strSelected = " selected";
            } ?>
            <option value="<?= $r ?>" <?= $strSelected ?>> <?= $r ?> </option>
        <?php
        } ?>
    </select>
    <input style="margin-top: 0" type="Submit" value="&#x25BA;" name="go">
</form>