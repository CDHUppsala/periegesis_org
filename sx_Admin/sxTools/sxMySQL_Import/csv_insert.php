<?php
// Compatibility Check Before inserting data
// include __DIR__ . "/csv_check.php";

if ($radioContinue) {

    if ($radio_truncateTable) {
        $conn->exec("TRUNCATE TABLE $str_DBTableName");
    }

    $str_ColumnNames = implode(",", $arr_ColumnNames);
    $str_ColumnQuest = implode(",", $arr_ColumnQuests);

    // The incert statement
    $sql = "INSERT INTO  $str_DBTableName  ($str_ColumnNames) VALUES($str_ColumnQuest)";
    $stmt_insert = $conn->prepare($sql);

    $line = 1;
    $intImported = 0;
    $intNotImported = 0;

    // Reset the file pointer to the beginning of the file
    rewind($data);

    // The fgetcsv() reads the CSV file by row and creates an array of fields
    while (($arrLineData = fgetcsv($data, 0, $columnSeparator, '"', '\\')) !== false) {

        // Jumb to line 2
        if ($line ===  1) {
            $line++;
            continue;
        }

        $intIndexPK = array_search($str_PrimaryKeyName, $arr_Headers);

        $radioInsert = true;
        $mixReturnedPKValue = NULL;
        if ($intIndexPK !== false) {
            $mixPKValue = $arrLineData[$intIndexPK];
            $mixPKValue = !empty($mixPKValue) ? trim($mixPKValue) : '';
            if ($str_PrimaryKeyType === 'INT' && (int)$mixPKValue > 0) {
                $stmt_check->execute([$mixPKValue]);
                $mixReturnedPKValue = $stmt_check->fetchColumn();
            } elseif (!empty($mixPKValue)) {
                $stmt_check->execute([$mixPKValue]);
                $mixReturnedPKValue = $stmt_check->fetchColumn();
            }else{
                $radioInsert = false;
            }

            if ($mixReturnedPKValue) {
                $radioInsert = false;
            }
        }else{
            $radioInsert = false;
        }

        if ($radioInsert) {
            $arr_LoopValues = [];
            for ($c = 0; $c < $int_Columns; $c++) {
                $value = $arrLineData[$c];
                $s_Type = $arr_ColumnTypes[$c];
                $arr_LoopValues[] = sx_getTypeCompatibleValue($s_Type, $value);
            }
            $stmt_insert->execute($arr_LoopValues);
            $intImported++;
        } else {
            $intNotImported++;
        }
        $line++;
    }
    $stmt_insert = null;
    $stmt_check = null;
    fclose($data);

    if ($intImported > 0) {
        // Reset auto increment
        $sql = "ALTER TABLE $str_DBTableName AUTO_INCREMENT = 0";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt = null;

        $line = $line - 2;
        if ($radio_truncateTable) {
            echo '<h3>Your Table has been Trancated and Repopulated by New Data</h3>';
        } else {
            echo '<h3>Successful Insertion of New Data</h3>';
        }
        echo '<div class="msgSuccess">';
        echo "<p>Data has been Inserted for <b>$intImported Rows</b> (and $int_Columns Colmns) in the Table from totaly <b>$line Lines</b> in the CSV File.</p>";
        if ($intNotImported > 0) {
            echo "<p><b>$intNotImported Lines</b> with Primary Key values that already exist in the Database Table have Not been Inserted.</p>";
        }
        echo '</div>';
    } else {
        echo '<div class="msgInfo">';
        echo '<p><b>The CSV File has Not Been Imported</b></p>';
        echo "<p>All Lines in the CSV File have a <b>Prmary Key Value</b> that already exists in the Database Table. Your Table is <b>up-to-date</b>!</p>";
        echo '</div>';
    }
}
