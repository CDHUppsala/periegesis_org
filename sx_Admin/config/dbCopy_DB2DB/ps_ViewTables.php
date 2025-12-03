<?php
$iRows = count($arrTables);
for ($t = 0; $t < $iRows; $t++) {
    $sTable = strtolower(trim($arrTables[$t]));
    if ($sTable != "" && @$_POST["table_" . $t] == "Yes") {
        $r = 0;
        $c = 0;
        if ($boolSourceMySQL) {
            $str_PKName = get_PrimaryKey_MySQL($sTable, $connSourceMySQL,);
            $arrSource = get_resultsMySQL($sTable, $str_PKName, $connSourceMySQL, true);
        } else {
            $str_PKName = get_PrimaryKey_Access($sTable, $connSourceAccess,);
            $arrSource = get_resultsAccess($sTable, $str_PKName, $connSourceAccess, true);
        }
        if (is_array($arrSource)) {
            echo "<h2>" . $sTable . "</h2>";
            echo '<div class="grid-container">';
            echo "<div><h3>Source Database</h3>";
            $iRecords = count($arrSource);
            for ($r = 0; $r < $iRecords; $r++) {
                echo "<hr>";
                echo 'Record ' . $r;
                echo "<hr>";
                echo "<table style='border-bottom: 1px solid #000; margin-bottom: 20px;'>";
                $iColumns = count($arrSource[0]);
                for ($c = 0; $c < $iColumns; $c++) {
                    $tempDate = "";
                    if ($arrSource[$r][$c][0] == 'timestamp' && is_int($arrSource[$r][$c][2])) {
                        $tempDate = " " . date('Y-m-d', $arrSource[$r][$c][2]);
                    }
                    echo "<tr>
									<td><b>" . $arrSource[$r][$c][0] . "</b></td>
									<td>" . $arrSource[$r][$c][1] . "</td>
									<td>" . $arrSource[$r][$c][2] . $tempDate . "</td>
									</tr>";
                }
                echo "</table>";
            }
            echo "</div>";
            $arrSource = null;

            if (!empty($strTargetDBName)) {
                echo "<div>";
                if ($boolTargetMySQL) {
                    $arrTarget = get_resultsMySQL($sTable, $str_PKName, $connTargetMySQL, true);
                } else {
                    $arrTarget = get_resultsAccess($sTable, $str_PKName, $connTargetAccess, true);
                }
                if (is_array($arrTarget)) {
                    echo "<h3>Target Database </h3>";
                    $iRecords = count($arrTarget);
                    for ($r = 0; $r < $iRecords; $r++) {
                        echo "<hr>";
                        echo 'Record ' . $r;
                        echo "<hr>";
                        echo "<table style='border-bottom: 1px solid #000; margin-bottom: 20px;'>";
                        $iColumns = count($arrTarget[0]);
                        for ($c = 0; $c < $iColumns; $c++) {
                            echo "<tr>
										<td><b>" . $arrTarget[$r][$c][0] . "</b></td>
										<td>" . $arrTarget[$r][$c][1] . "</td>
										<td>" . $arrTarget[$r][$c][2] . "</td>
										</tr>";
                        }
                        echo "</table>";
                    }
                }
                $arrTarget = null;
                echo "</div>";
            }
            echo "</div>";
        }
        $strFieldsEffected = "Fields: ";
        echo "<h4>Table: " . $t . ": " . $sTable . ", Rows: " . $r . ", " . $strFieldsEffected . $c . "</h4>";
        $i_Rows = $i_Rows + $r;
        $iFields =  $iFields + $c;
        break;
    }
}
