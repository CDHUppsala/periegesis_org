<?php

$strCurrentURL = "index.php";

/**
 * Variable $dateSearchByDate is defined in sx_config.php
 * It already includes the first of month of the searched date
 * day, week, month, year
 */

if (isset($date_RequestedDate) && sx_isDate($date_RequestedDate)) {
    $sxCurrDate = $date_RequestedDate;
} else {
    $sxCurrDate = date("Y-m-d");
}

$date_time = new DateTime($sxCurrDate);
$iThisYear = $date_time->format("Y");
$iThisMonth = $date_time->format("n");

$iPrevYear = $iThisYear;
$iNextYear = $iThisYear;
$iPrevMonth = $iThisMonth - 1;
$iNextMonth = $iThisMonth + 1;

if ($iPrevMonth == 0) {
    $iPrevMonth = 12;
    $iPrevYear = $iThisYear - 1;
}
if ($iNextMonth == 13) {
    $iNextMonth = 1;
    $iNextYear = $iThisYear + 1;
}

/**
 * Just incase: get the searched date with the first day of the searched month
 */
$objMonthFirstDay = new DateTime($iThisYear . "-" . $iThisMonth . "-01");
$iMonthDays = $objMonthFirstDay->format('t');
$iFirstMonthWeek = intval($objMonthFirstDay->format('W'));

$objMonthLastDay = new DateTime($iThisYear . "-" . $iThisMonth . "-" . $iMonthDays);



/**
 * To transform to monday = 0
 */
$iWeekStartDay = $objMonthFirstDay->format('w') - 1;
if ($iWeekStartDay < 0) {
    $iWeekStartDay = 6;
}

$objPrevMonthFirstDay = new DateTime($iPrevYear . "-" . $iPrevMonth . "-01");
$iPrevMonthDays = $objPrevMonthFirstDay->format('t');

$objNextMonthFirstDay = new DateTime($iNextYear . "-" . $iNextMonth . "-01");
$iWeekStartDayNext = $objNextMonthFirstDay->format('w') - 1;

if ($iWeekStartDayNext < 0) {
    $iWeekStartDayNext = 6;
}
if ($iWeekStartDayNext == 0) {
    $iWeekStartDayNext = 7;
}

$iTotalCalendarDays = $iMonthDays + $iWeekStartDay + (7 - $iWeekStartDayNext);
?>
<section class="calendar_bg">
    <table class="calendar_table month_calendar">
        <caption>
            <span>Select Month and Day</span>
        </caption>
        <tr>
            <th><a href="<?php echo $strCurrentURL . "?month=" . $iPrevMonth . "&year=" . $iPrevYear; ?>">&#x25C0;</a></th>
            <th colspan="6"><strong><?php echo lng_MonthNames[$iThisMonth - 1] . ' ' . $iThisYear; ?></strong></th>
            <th><a href="<?php echo $strCurrentURL . "?month=" . $iNextMonth . "&year=" . $iNextYear; ?>">&#x25B6;</a> </th>
        </tr>
        <tr>
            <th>W</th>
            <?php for ($i = 0; $i < 7; $i++) { ?>
                <th><?= mb_substr(lng_DayNames[$i], 0, 1) ?></th>
            <?php } ?>
        </tr>
        <?php

        for ($i = 0; $i < ($iTotalCalendarDays); $i++) {
            if (($i % 7) == 0) {
                $iWeek = ($iFirstMonthWeek++);
                if ($iFirstMonthWeek > 52 && $iThisMonth == 1) {
                    $iFirstMonthWeek = 1;
                }
                if ($iWeek == 53 && $iThisMonth == 12 && $iWeekStartDayNext < 4) {
                    $iWeek = 1;
                } ?>
                <tr>
                    <th><a href="<?= $strCurrentURL . "?week=" . $iWeek . "&month=" . $iThisMonth . "&year=" . $iThisYear ?>"><?= $iWeek ?></a></th>
                <?php
            }
            if ($i < $iWeekStartDay) {
                $iDay = ($iPrevMonthDays - ($iWeekStartDay - $i) + 1); ?>
                    <td class="notDay"><?= $iDay ?></td>
                <?php
            } else {
                $iDay = ($i - $iWeekStartDay + 1);
                $LoopDate = date("Y-m-d", mktime(0, 0, 0, $iThisMonth, $iDay, $iThisYear));
                $LinkLeft = "";
                $LinkRight = "";
                $Class = "";
                if ($LoopDate == $date_RequestedDate) {
                    $Class = ' class="selected_day"';
                } elseif ($LoopDate == date("Y-m-d")) {
                    $Class = ' class="thisDay"';
                }
                if ($iDay > $iMonthDays) {
                    $iDay -= $iMonthDays;
                    $Class = 'class="notDay"';
                } else {
                    $LinkLeft = '<a href="' . $strCurrentURL . '?day=' . $iDay . '&month=' . $iThisMonth . '&year=' . $iThisYear . '">';
                    $LinkRight = "</a>";
                } ?>
                    <td <?= $Class ?>><?= $LinkLeft . $iDay . $LinkRight ?></td>
            <?php
            }
            if (($i % 7) == 6) {
                echo "</tr>\n";
            }
        }
            ?>
    </table>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" name="calendarForm" method="post">
        <div>
            <select name="month">
                <?php
                for ($i = 1; $i < 13; $i++) {
                    $strSelected = "";
                    if ($i == $iThisMonth) {
                        $strSelected = " selected";
                    } ?>
                    <option value="<?= $i ?>" <?= $strSelected ?>><?= lng_MonthNames[$i - 1] ?></option>
                <?php
                } ?>
            </select><select name="year">
                <?php
                $iYear = (int) date('Year');
                for ($r = $iYear -  1; $r < $iYear + 2; $r++) {
                    $strSelected = "";
                    if ($r == $iThisYear) {
                        $strSelected = " selected";
                    } ?>
                    <option value="<?= $r ?>" <?= $strSelected ?>> <?= $r ?> </option>
                <?php
                } ?>
            </select>
        </div>
        <input type="Submit" value="&#x25BA;" name="go">
    </form>
</section>