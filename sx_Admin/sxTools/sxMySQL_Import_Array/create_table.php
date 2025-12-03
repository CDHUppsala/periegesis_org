<?php

ini_set('memory_limit', '256M');
/**
 * Converts CSV, XML and JSON files to HTML table
 */
include_once __DIR__ . "/functions_files.php";
include_once __DIR__ . "/jq_create_table.php";

/**
 * The variable $import_FileName comes from index.php 
 */

if (file_exists($import_FileName)) {


    $arrData = [];
    $intBookSections = 0;
    switch ($file_extension) {
        case 'csv':
            if (($csvReader = fopen($import_FileName, 'r')) !== false) {
                // Read the first row to get the separator
                $fields = fgets($csvReader);
                $fieldSeparator = strpos($fields, ",") !== false ? "," : ";";
                $fields = null;

                // Back to first row
                rewind($csvReader);

                // Read the first row to get headers
                $arrHeaders = fgetcsv($csvReader, 0, $fieldSeparator,'"','\\');
                $arrHeaders = array_map('trim', $arrHeaders);
                $encoding = mb_detect_encoding($arrHeaders[0]);

                // To remove BOM encoding - is effectively removed by mb_substr()
                $arrHeaders[0] = mb_substr($arrHeaders[0], 0, null, $encoding);
                $radioAddRow = !in_array('RowID', $arrHeaders);
                $radioAddRow = false;

                $loopRows = 1;
                // Loop Reading from the second row
                while (($row = fgetcsv($csvReader, 0, $fieldSeparator, '"', '\\')) !== false) {
                    $rowData = [];
                    if ($radioAddRow) {
                        $rowData['RowID'] = $loopRows;
                    }
                    foreach ($arrHeaders as $index => $header) {
                        $rowData[$header] = $row[$index];
                    }
                    $arrData[] = $rowData;
                    $loopRows++;
                }
                $rowData = null;

                fclose($csvReader);
            }

            // Normalize array structure
            if (!empty($arrData) && is_array($arrData)) {
                $arrData = normalizeArrayStructure($arrData);
            }
            //echo 'Final memory usage csv: ' . memory_get_usage() . ' bytes<br>';
            break;

        case 'xml':

            libxml_use_internal_errors(true);
            $xmlContent = simplexml_load_file($import_FileName);

            if ($xmlContent === false) {
                echo "Failed to load XML file. Errors:\n";
                foreach (libxml_get_errors() as $error) {
                    echo $error->message . "\n";
                }
                libxml_clear_errors();
                break;
            }

            $xmlContent = removeBOM($xmlContent);
            
            if (!empty($xmlContent->children())) {
                foreach ($xmlContent->children() as $row) {
                    // Convert each <row> node to an associative array
                    $arrData[] = convertSimpleXmlToArray($row);
                }
            }

            break;

        case 'json':
            $jsonContent = file_get_contents($import_FileName);
            // Clean UTF-8 BOM, if any
            $jsonContent = removeBOM($jsonContent);

            if ($jsonContent !== false) {
                $arrData = json_decode($jsonContent, true);
                $jsonContent = null;

                if (!empty($arrData) && is_array($arrData)) {
                    $arrData = normalizeArrayStructure($arrData);
                }
            }
            //echo 'Final memory usage json: ' . memory_get_usage() . ' bytes<br>';

            break;
    } ?>

    <?php

    if (empty($arrData)) {

        echo '<h2>The Import File is empty or cannot be read</h2>';
    } else { ?>

        <div class="maxWidthWide">
            <ol>
                <?php

                // Check for unique values for the Primary Key
                $radioImportable = true;
                if (!empty($arrData) && is_array($arrData) && !empty($str_PrimaryKeyName)) {
                    // Extract values for the key
                    $arrPKValues = array_column($arrData, $str_PrimaryKeyName);
                    $intCountPKValues = count($arrPKValues);
                    $intCountUniquePKValues = count(array_unique($arrPKValues));

                    // Check for uniqueness
                    if ($intCountPKValues === 0 || $intCountUniquePKValues === 0) {
                        $radioImportable = false;
                        echo "<li><div class=\"msgError\">The Primary Key $str_PrimaryKeyName of the Database Table does not exist in the File.</div></li>";
                    } elseif ($intCountPKValues === $intCountUniquePKValues) {
                        echo "<li><div class=\"msgSuccess\">All $intCountPKValues values of the <b>Primary Key {$str_PrimaryKeyName}</b> in all <b>File Lines</b> are <b>Unique</b>.</div></li>";
                    } else {
                        $radioImportable = false;
                        $duplicates = array_diff_key($arrPKValues, array_unique($arrPKValues));
                        if (!empty($duplicates)) {
                            echo "<li><div class=\"msgError\">The Primary Key $str_PrimaryKeyName includes duplicate values: " . implode(', ', $duplicates) . "</div></li>";
                        }
                    }
                    $arrPKValues = null;
                    $duplicates = null;
                }

                // Check if file fields have the same name as the table columns
                $arrFirstRow = $arrData[0];
                $keyErr = [];
                foreach ($arrFirstRow as $key => $value) {
                    if (!in_array($key, $arr_FieldNames)) {
                        $keyErr[] = $key;
                    }
                }
                if (!empty($keyErr)) {
                    $radioImportable = false;
                    echo "<li><div class=\"msgError\">Following File Fields do NOT exist as Table Columns: " .  implode(', ', $keyErr) . " </div></li>";
                } else {
                    echo "<li><div class=\"msgSuccess\">All <b>File Fields</b> have the same name as the corresponding <b>Table Columns</b>.</div></li>";
                }

                // Check for compatible value types - but only if names are equal
                $intLine = 1;
                $arrErrType = [];
                if ($radioImportable) {
                    foreach ($arrData as $line) {
                        foreach ($line as $key => $value) {
                            if (!sx_checkTypeCompatibility($arr_FieldNameType[$key], $value)) {
                                $arrErrType[] = "{$intLine}: {$key}";
                            }
                        }
                        $intLine++;
                    }
                    if (!empty($arrErrType)) {
                        $radioImportable = false;
                        echo "<li><div class=\"msgError\">Following File Lines have values NOT Compatable to the Table Column Type: <br>" . implode('<br>', $arrErrType) . "</div></li>";
                    } else {
                        echo "<li><div class=\"msgSuccess\">All <b>File Lines</b> have <b>Field Values</b> of the same <b>Type</b> as the corresponding <b>Columns</b> of the Table.</div></li>";
                    }
                }

                // Final message
                if ($radioImportable) { ?>
                    <li>
                        <div class="msgSuccess"><b>You can now safely import the file!</b></div>
                    </li>
                    <script>
                        // Enable the submit button
                        document.getElementById('SubmitImport').disabled = false;
                    </script>
                <?php
                } ?>
            </ol>
        </div>

    <?php
    }
    // Create table only if import is not submited
    if (!$radioImportSubmited) { ?>
        <div class="jq_table_container csv_table_container">
            <div class="jq_table_search csv_table_search">
                <div class="row">
                    <button class="jq_ToggleTableView" title="Switch between 2 and multiple columns">&#11134</button>
                    <div class="jq_TotalRows"></div>
                </div>
                <div><?php echo basename($import_FileName) ?></div>
                <div class="row">
                    <input class="jq_SearchInput" type="text" name="searchInput">
                    <button class="jq_SearchButton">Search</button>
                    <button class="jq_ClerButton">Clear</button>
                </div>
            </div>
            <?php
            ?>
            <div class="jq_data_table_container csv_data_table_container">
                <table class="jq_data_table csv_data_table" id="dataTable">
                    <thead class="jq_TableHeaders"></thead>
                    <tbody class="jq_DataBody"></tbody>
                </table>
            </div>

            <div class="jq_pagination_container csv_pagination_container">
                <a class="button-grey button-gradient csv_page jq_page" data-page="prev" href="javascript:void(0)">&#9664;</a>
                <ul class="jq_pagination csv_pagination"></ul>
                <a class="button-grey button-gradient csv_page jq_page" data-page="next" href="javascript:void(0)">&#9654;</a>
            </div>
        </div>

        <?php
        /**
         * Convert $arrData to a javascript object for the following jQuery ready function
         */
        /*
        echo '<pre>';
        print_r($arrData);
        echo '</pre>';
        */
        if (!empty($arrData) && is_array($arrData)) { ?>
            <script>
                jQuery(function($) {
                    sx_create_csv_table($, <?php echo json_encode($arrData, JSON_HEX_TAG | JSON_HEX_QUOT) ?>);
                });
            </script>
<?php
        }
        unset($arrData);
    }
}
?>