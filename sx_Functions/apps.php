<?php
/**
 * @param string $str : Used to break long Titles into lines (for Charts in chart.js)
 * @param int $interval : number of characters per line
 * @return array : an array of lines
 */
function get_Lines_From_Title($str, $interval = 50)
{
    if (empty($str)) {
        return [];
    }
    $pos = 0;
    $pos2 = 0;
    $pos3 = 0;
    $length = 0;
    $length1 = 0;
    if (mb_strlen($str) > $interval) {
        $pos = mb_strpos($str, " ", $interval);
    }
    if (mb_strlen($str) > $interval * 2) {
        $pos2 = mb_strpos($str, " ", $interval * 2);
        $length = $pos2 - $pos;
    }
    if (mb_strlen($str) > $interval * 3) {
        $pos3 = mb_strpos($str, " ", $interval * 3);
        $length1 = $pos3 - $pos2;
    }
    $arr = [];
    if ($pos3 > 0) {
        $arr[] = mb_substr($str, 0, $pos);
        $arr[] = mb_substr($str, $pos, $length);
        $arr[] = mb_substr($str, $pos2, $length1);
        $arr[] = mb_substr($str, $pos3);
    } elseif ($pos2 > 0) {
        $arr[] = mb_substr($str, 0, $pos);
        $arr[] = mb_substr($str, $pos, $length);
        $arr[] = mb_substr($str, $pos2);
    } elseif ($pos > 0) {
        $arr[] = mb_substr($str, 0, $pos);
        $arr[] = mb_substr($str, $pos);
    } else {
        $arr[] = $str;
    }
    return $arr;
}


function sx_transform_csv_to_table($csv)
{
    $csvToRead = fopen($csv, 'r');
    $x = 0;
    echo '<table class="csv_table jq_SortTableByColumn">';
    $arrColumn = "";
    $intColums = 0;

    while (!feof($csvToRead)) {
        $csvArray = fgetcsv($csvToRead, 1000, ',');
        if (!empty($csvArray) && $csvArray !== array(null) && !empty($csvArray[0])) {
            if ($x == 0) {
                $intColums = count($csvArray);
                $arrColumn = $csvArray;
                echo '<div class="align_right">
                    <label>Filter by a word or phrase: <input type="text" class="filter_table"></label>
                    </div>';
                echo '<thead><tr>';
                for ($i = 0; $i < $intColums; $i++) {
                    $strLoop = $csvArray[$i];
                    echo '<th>' . sx_separateWordsWithCamelCase($strLoop) . '</th>';
                }
                echo "</tr></thead>";
                echo "<tbody";
                $x = 1;
            } else {
                echo "<tr>";
                for ($j = 0; $j < $intColums; $j++) {
                    $strLoop = $csvArray[$j];
                    if (strpos($strLoop, '<a ') !== false) {
                        if (strpos($strLoop, 'target=') === false) {
                            $sLeft = substr($strLoop, 0, 2) . ' target="_blank"';
                            $sRight = substr($strLoop, 2);
                            $strLoop = $sLeft . $sRight;
                        }
                    } elseif (strpos($strLoop, 'http://') !== false || strpos($strLoop, 'https://') !== false) {
                        $strLoop = '<a title="Link to External Source" target="_blank" href="' . $strLoop . '">' .  sx_separateWordsWithCamelCase($arrColumn[$j]) . '</a>';
                    } else {
                        $strLoop = (string)($strLoop);
                        if (strpos($strLoop, 'T00:00:00Z') !== false) {
                            $strLoop = str_replace('T00:00:00Z', '', $strLoop);
                        }
                    }
                    if ($j == 0) {
                        $strLoop = $strLoop . ' ' . $x;
                    }
                    echo "<td>$strLoop</td>";
                }
                echo '</tr>';
                $x++;
            }
        }
    }
    echo '</tbody></table>';
    fclose($csvToRead);
}

