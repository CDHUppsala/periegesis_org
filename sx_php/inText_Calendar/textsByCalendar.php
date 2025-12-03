<?php
function sx_GetTextRowsInCalendar($firstDay, $lastDay)
{
    $conn = dbconn();
    $sql = "SELECT DISTINCT t.PublishedDate 
        FROM " . sx_TextTableVersion . " AS t 
            INNER JOIN text_groups AS g ON t.GroupID = g.GroupID 
        WHERE t.Publish = True 
            AND g.Hidden = False 
            AND (PublishedDate >= ?) 
            AND (PublishedDate <= ?)
        LIMIT 33";
    //echo "$sql";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$firstDay, $lastDay]);
    $a_Results = $stmt->fetchAll(PDO::FETCH_NUM);
    $stmt = null;
    if (is_array($a_Results)) {
        return $a_Results;
    } else {
        return "";
    }
}

$strCurrentURL = "texts.php";

/**
 * Variable $date_SearchByDate is defined in sx_config.php
 * It already includes the first of month of the searched date
 */

if (return_Is_Date($date_SearchByDate)) {
    $sxCurrDate = $date_SearchByDate;
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

if (empty($str_TextsByCalenderTitle)) {
    $str_TextsByCalenderTitle = lngTextCalendar;
} ?>
<section class="texts_calendar_block" style="display: <?= $displayCalendar ?>">
    <table class="calendar_table">
        <tr class="month_nav">
            <td title="<?php echo lngPreviousMonth ?>"><a href="<?php echo sx_PATH . "?month=" . $iPrevMonth . "&year=" . $iPrevYear; ?>">&#x25C0;</a></td>
            <td colspan="6"><span><?php echo lng_MonthNames[$iThisMonth - 1] . ' ' . $iThisYear; ?></span></td>
            <?php
            if (new DateTime($iNextYear . '-' . $iNextMonth . '-01') < new DateTime(date('Y-m-d'))) { ?>
                <td title="<?php echo lngNextMonth ?>"><a href="<?php echo sx_PATH . "?month=" . $iNextMonth . "&year=" . $iNextYear; ?>">&#x25B6;</a> </td>
            <?php
            } else { ?>
                <td></td>
            <?php
            } ?>
        </tr>
        <tr>
            <th>W</th>
            <?php for ($i = 0; $i < 7; $i++) { ?>
                <th><?= mb_substr(lng_DayNames[$i], 0, 1) ?></th>
            <?php } ?>
        </tr>
        <?php

        $aRows = sx_GetTextRowsInCalendar($objMonthFirstDay->format("Y-m-d"), $objMonthLastDay->format("Y-m-d"));

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
                    <td class="week"><a href="<?= $strCurrentURL . "?week=" . $iWeek . "&month=" . $iThisMonth . "&year=" . $iThisYear ?>"><?= $iWeek ?></a></td>
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
                if ($LoopDate == date("Y-m-d")) {
                    $Class = ' class="thisDay"';
                }
                if ($iDay > $iMonthDays) {
                    $iDay -= $iMonthDays;
                    $Class = ' class="notDay"';
                } elseif (is_array($aRows)) {
                    if (array_search($LoopDate, array_column($aRows, 0)) !== false) {
                        $LinkLeft = '<a href="' . $strCurrentURL . '?day=' . $iDay . '&month=' . $iThisMonth . '&year=' . $iThisYear . '">';
                        $LinkRight = "</a>";
                    }
                } ?>
                    <td<?= $Class ?>><?= $LinkLeft . $iDay . $LinkRight ?></td>
                <?php
            }
            if (($i % 7) == 6) {
                echo "</tr>\n";
            }
        }
                ?>
    </table>
    <form action="texts.php" name="calendarForm" method="post">
        <div>
            <select class="jqSelectMonth" name="month">
                <?php
                for ($i = 1; $i < 13; $i++) {
                    $strSelected = "";
                    if ($i == $iThisMonth) {
                        $strSelected = " selected";
                    } ?>
                    <option value="<?= $i ?>" <?= $strSelected ?>><?= lng_MonthNames[$i - 1] ?></option>
                <?php
                } ?>
            </select><select class="jqSelectTextYear" name="year">
                <?php
                $sql = "SELECT DISTINCT Year(PublishedDate) AS AsYear FROM " . sx_TextTableVersion . " ORDER BY Year(PublishedDate) DESC ";
                $stmt = $conn->query($sql);
                $rs = $stmt->fetchAll(PDO::FETCH_NUM);
                $stmt = null;
                if ($rs) {
                    $rows = count($rs);
                    for ($r = 0; $r < $rows; $r++) {
                        $strSelected = "";
                        if ($rs[$r][0] == $iThisYear) {
                            $strSelected = " selected";
                        } ?>
                        <option value="<?= $rs[$r][0] ?>" <?= $strSelected ?>> <?= $rs[$r][0] ?> </option>
                <?php
                    }
                } ?>
            </select>
        </div>
        <input title="<?= lngListMonthsArticles ?>" type="Submit" value="&#x25BA;" name="go">
    </form>
</section>

<script>
    $sx(document).ready(function() {
        // Get anly the months of a year that contain a text
        $sx(".jqSelectTextYear").change(function() {
            var $Data = "year=" + $sx(this).val();
            $sx.ajax({
                url: "ajax_Texts_SelectMonths.php",
                cache: false,
                data: $Data,
                dataType: "html",
                scriptCharset: "utf-8",
                type: "GET",
                success: function(result) {
                    $sx(".jqSelectMonth").html(result);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        });
    })
</script>