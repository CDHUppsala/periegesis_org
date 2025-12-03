<?php
function sx_getVisits(string $whereClause, string $interval): array
{
    $conn = dbconn();

    $intervalMap = [
        'y'  => 'DAYOFYEAR(Datum)',
        'ww' => 'WEEK(Datum,1)',
        'm'  => 'MONTH(Datum)',
        'q'  => 'EXTRACT(QUARTER FROM Datum)',
    ];

    $selectPart = '';
    $groupByPart = '';
    $orderByPart = '';

    if (isset($intervalMap[$interval])) {
        $expr = $intervalMap[$interval];
        $selectPart   = "$expr AS dbPeriodDate, ";
        $groupByPart  = ", $expr";
        $orderByPart  = ", $expr ASC";
    }

    $sql = "
        SELECT YEAR(Datum) AS dbYearDate,
               $selectPart
               SUM(Visits) AS countResult
        FROM visits
        $whereClause
        GROUP BY YEAR(Datum) $groupByPart
        ORDER BY YEAR(Datum) ASC $orderByPart
    ";

    return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}


function sx_showVisits(int $maxVisits, string $strDI, float $iRatio, array $aResults): void
{
    global $writePeriod, $intVisitsTotal;

    $ratio = $iRatio / max($maxVisits, 1); // avoid /0

    // Labels
    switch ($strDI) {
        case "yyyy":
            $writeStrDI = lngByYear;
            $periodStrDI = lngTotal;
            break;
        case "q":
            $writeStrDI = lngByQuarter;
            $periodStrDI = lngQuarter;
            break;
        case "m":
            $writeStrDI = lngByMonth;
            $periodStrDI = lngMonth;
            break;
        case "ww":
            $writeStrDI = lngByWeek;
            $periodStrDI = lngWeek;
            break;
        case "y":
            $writeStrDI = lngByYearDay;
            $periodStrDI = lngDay;
            break;
        default:
            $writeStrDI = lngByDate;
            $periodStrDI = lngPeriod;
    }

    $iRows = count($aResults);

    // Bar width logic (same rules as before, but centralized)
    if ($iRows <= 13) {
        $imgWidth = 28;
    } elseif ($iRows <= 26) {
        $imgWidth = 20;
    } elseif (strlen((string)$maxVisits) > 3) {
        $imgWidth = 20;
    } else {
        $imgWidth = 15;
    }
?>
    <h2><?= lngVisitsByDate ?></h2>
    <table class="tableBorders">
        <tr>
            <td><b><?= lngDivision ?></b>:</td>
            <td><?= $writeStrDI ?></td>
        </tr>
        <tr>
            <td><b><?= lngPeriod ?></b>:</td>
            <td><?= $writePeriod ?></td>
        </tr>
        <tr>
            <td><b><?= lngTotalVisits ?></b>:</td>
            <td><?= number_format($intVisitsTotal, 0, ",", " ") ?></td>
        </tr>
    </table>
    <div class="scroll">
        <table>
            <tr>
                <td class="alignRight">%:</td>
                <?php foreach ($aResults as $row):
                    $intSumVisits = (int)$row['countResult'];
                    $currentPercent = $intVisitsTotal > 0
                        ? number_format(($intSumVisits / $intVisitsTotal) * 100, 0)
                        : 0;
                ?>
                    <td class="alignCenter" valign="bottom">
                        <div class="rotate"><?= sx_getVertical($intSumVisits) ?></div>
                        <img src="../images/bar.png" alt="chart"
                            style="height: <?= floor($intSumVisits * $ratio) + 1 ?>px; width: <?= $imgWidth ?>px"><br>
                        <?= $currentPercent ?>
                    </td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td class="alignRight"><?= $periodStrDI ?>:</td>
                <?php foreach ($aResults as $row):
                    $intPeriod = $row['dbPeriodDate'] ?? '';
                    if ($strDI === 'yyyy') {
                        $intPeriod = number_format((int)$intPeriod, 0, ',', ' ');
                    }
                ?>
                    <td class="gray alignCenter td_border_right"><?= $intPeriod ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td class="alignRight"><?= lngYear ?>:</td>
                <?php
                $checkYear = null;
                $colspan = 0;

                foreach ($aResults as $row) {
                    $year = (int)($row['dbYearDate'] ?? 0);

                    if ($checkYear !== null && $checkYear !== $year) {
                        // Output previous year cell
                ?>
                        <td class="color alignCenter td_border_right" colspan="<?= $colspan ?>"><?= $checkYear ?></td>
                    <?php
                        $colspan = 0;
                    }

                    $colspan++;
                    $checkYear = $year;
                }

                if ($colspan > 0) { ?>
                    <td class="color" colspan="<?= $colspan ?>"><?= $checkYear ?></td>
                <?php
                } ?>
            </tr>
        </table>
    </div>
<?php
}


function sx_showVisitsForm(string $dInterval, string $dPeriod, int $intMinVisitYear): void
{
    $intervalOptions = [
        'yyyy' => lngByYear,
        'q'    => lngByQuarter,
        'm'    => lngByMonth,
        'ww'   => lngByWeek,
        'y'    => lngByYearDay,
    ];

    $periodOptions = [
        '0'  => lngAllDates,
        '1'  => lngLastMonth,
        '3'  => lngLastQuarter,
        '6'  => lngLastSixMonths,
        '12' => lngLastYear,
    ];

    $currentYear = sx_getYear(date('Y-m-d'));

    if ($intMinVisitYear > 0) {
        for ($y = $currentYear; $y >= $intMinVisitYear; $y--) {
            $periodOptions[(string)$y] = (string)$y;
        }
    }
?>
    <hr>
    <form action="default.php?pg=v" method="POST" name="strDefineDate">
        <div class="row" style="justify-content: flex-start;">

            <select size="1" name="dateInterval">
                <?php foreach ($intervalOptions as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($dInterval === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>

            <select size="1" name="datePeriod">
                <?php foreach ($periodOptions as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ((string)$dPeriod === (string)$value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>

            <input class="button" type="submit" value="»»»" name="SubmitDates">
        </div>
    </form>

    <div class="text maxWidth">
        <a href="#" onclick="showBox('CountNote')"><?= lngHowAccountVisitors ?> &gt;&gt;&gt;</a>
        <div id="CountNote" style="display: none">
            <?= lngHowAccountVisitorsNote ?> [<a href="#" onclick="showBox('CountNote')"><?= lngHide ?></a>]
        </div>
    </div>
<?php
}
?>