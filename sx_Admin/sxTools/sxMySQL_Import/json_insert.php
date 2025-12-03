<?php
// Compatibility Check Before Inserting data
// include __DIR__ . "/json_check.php";


if ($radioContinue) {

    if ($radio_truncateTable) {
        $conn->exec("TRUNCATE TABLE $str_DBTableName");
    }

    $iTotalColums = count($arr_ColumnNames);
    $str_ColumnNames = implode(",", $arr_ColumnNames);
    $str_ColumnQuest = implode(",", $arr_ColumnQuests);

    // The incert statement
    $sql = "INSERT INTO $str_DBTableName ($str_ColumnNames) VALUES($str_ColumnQuest)";
    $stmt_inset = $conn->prepare($sql);

    $intImported = 0;
    $intNotImported = 0;
    $iTatalRows = count($arr_json);
    foreach ($arr_json as $json) {

        $radioInsert = true;
        $mixReturnedPKValue = NULL;
        if (isset($json[$str_PrimaryKeyName])) {
            $mixPKValue = $json[$str_PrimaryKeyName];
            $mixPKValue = !empty($mixPKValue) ? trim($mixPKValue) : '';

            if ($str_PrimaryKeyType === 'INT' && (int)$mixPKValue > 0) {
                $stmt_check->execute([$mixPKValue]);
                $mixReturnedPKValue = $stmt_check->fetchColumn();
            } elseif (!empty($mixPKValue)) {
                $stmt_check->execute([$mixPKValue]);
                $mixReturnedPKValue = $stmt_check->fetchColumn();
            } else {
                $radioInsert = false;
            }

            if ($mixReturnedPKValue) {
                $radioInsert = false;
            }
        } else {
            $radioInsert = false;
        }

        // Insert if value don't exists
        if ($radioInsert) {
            $arr_LoopValues = [];
            foreach ($json as $name => $value) {
                $s_Type = $arr_FieldNameType[$name];
                $arr_LoopValues[] = sx_getTypeCompatibleValue($s_Type, $value);
            }
            $stmt_inset->execute($arr_LoopValues);
            $intImported++;
        } else {
            $intNotImported++;
        }
    }
    unset($arr_json);
    $stmt_inset = null;
    $stmt_check = null;

    if ($intImported > 0) {
        // Reset auto increment
        $sql = "ALTER TABLE $str_DBTableName AUTO_INCREMENT = 0";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt = null;

        if ($radio_truncateTable) {
            echo '<h3>Your Table has been Trancated and Repopulated by New Data</h3>';
        } else {
            echo '<h3>Successful Insertion of New Data</h3>';
        }
        echo '<div class="msgSuccess">';
        echo "<p>Data has been Inserted for <b>$intImported Rows</b> in the Table from totaly <b>$iTatalRows Nodes</b> in the File.</p>";
        if ($intNotImported > 0) {
            echo "<p><b>$intNotImported Nodes</b> with Primary Key values that already exist in the Database Table have Not been Inserted.</p>";
        }
        echo '</div>';
    } else {
        echo '<div class="msgInfo">';
        echo '<p><b>The File has Not Been Imported</b></p>';
        echo "<p>All Nodes in the File have a <b>Prmary Key Value</b> that already exists in the Database Table. Your Table is <b>up-to-date</b>!</p>";
        echo '</div>';
    }
}
