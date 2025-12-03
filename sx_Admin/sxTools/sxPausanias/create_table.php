<?php

/**
 * Converts CSV, XML and JSON files to HTML table
 */
include_once __DIR__ . "/functions_files.php";
include_once __DIR__ . "/jq_create_table.php";
include __DIR__ . "/array_get_book_sections.php";


function sx_get_bookID($key, $arr)
{
    if (array_key_exists($key, $arr)) {
        return $arr[$key];
    }
    return '';
}

function extract_Wiki_QID($text)
{
    // Check if 'Q' exists in the string
    $pos = strpos($text, 'Q');
    if ($pos === false) {
        return null;
    }

    $arr = explode('Q', $text);
    if (!isset($arr[1])) {
        return null;
    }

    $numericPart = (int) $arr[1];
    if ($numericPart === 0) {
        return null;
    }

    // Return the Wiki ID with the 'Q' prefix
    return "https://www.wikidata.org/wiki/Q{$numericPart}";
}

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
                $bookKey = '';
                $anchoreKey = '';
                $indexTAG = array_search('TAGS', $arrHeaders);
                $indexCOM = array_search('COMMENTS', $arrHeaders);

                while (($row = fgetcsv($csvReader, 0, $fieldSeparator,'"','\\')) !== false) {
                    $rowData = [];
                    if ($radioAddRow) {
                        $rowData['RowID'] = $loopRows;
                    }

                    $linkWikiID = extract_Wiki_QID($row[$indexTAG] . ' ' . $row[$indexCOM]);

                    foreach ($arrHeaders as $index => $header) {
                        if ($header === 'GROUP_ID' || $header === 'GROUP_ORDER' || $header === 'VOCAB_TEMPORAL_BOUNDS') {
                            continue;
                        }
                        if ($header === 'QUOTE_TRANSCRIPTION') {
                            $header = 'QUOTE';
                        }
                        if ($header === 'VERIFICATION_STATUS') {
                            $header = 'STATUS';
                        }
                        if ($header === 'URI') {
                            if (empty($row[$index]) && !empty($linkWikiID)) {
                                $rowData[$header] = $linkWikiID;
                                continue;
                            }
                        }

                        if ($header === 'FILE') {
                            $bookKey = $row[$index];
                            if (str_contains($bookKey, 'Book')) {
                                $bookKey = str_replace('Book', '', $bookKey);
                                $bookKey = str_replace('.xml', '', $bookKey);
                            } else {
                                $bookKey = explode(':', $bookKey)[0];
                            }
                            continue;
                        }
                        if ($header === 'ANCHOR') {
                            $anchoreKey = $row[$index];
                            $anchoreKey = explode('/p', $anchoreKey)[0];
                            $anchoreKey = str_replace('from=/TEI[1]/text[1]/body[1]/', '', $anchoreKey);
                            $anchoreKey = str_replace('from=/tei/text/body/', '', $anchoreKey);
                            $bookKey = $bookKey . '_' . $anchoreKey;
                            $strBookID = sx_get_bookID($bookKey, $arrayBookIDs);
                            if (!empty($strBookID)) {
                                $intBookSections++;
                            }
                            $rowData['BookID'] = $strBookID;
                            $strBookID = '';
                            $bookKey = '';
                            continue;
                        }
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
            $xmlContent = simplexml_load_file($import_FileName);
            // Clean UTF-8 BOM, if any
            $xmlContent = removeBOM($xmlContent);

            $arrData = [];
            if (isset($xmlContent->row)) {
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

    <div class="maxWidthWide">
        <ol>
            <?php

            // Check for unique values for the Primary Key
            $radioImportable = true;
            if (!empty($arrData) && is_array($arrData) && !empty($str_PrimaryKeyName)) {
                // Extract values for the key
                $values = array_column($arrData, $str_PrimaryKeyName);
                $intCountValues = count($values);

                // Check if all rows in the file have identified book sections
                if($intCountValues !== $intBookSections) {
                    $radioImportable = false;
                    echo '<li><div class="msgError">'. $intCountValues - $intBookSections . ' rows in the file have <b>Non-Identified</b> Book Sections.</div></li>'; 
                }else{
                    echo "<li><div class=\"msgSuccess\">All $intBookSections lines in the file have <b>Identified</b> Book Sections.</div></li>"; 
                }
                // Check for uniqueness
                if ($intCountValues == 0 || count(array_unique($values)) == 0) {
                    $radioImportable = false;
                    echo "<li><div class=\"msgError\">The Primary Key $str_PrimaryKeyName of the Database Table does not exist in the File.</div></li>";
                } elseif ($intCountValues === count(array_unique($values))) {
                    echo "<li><div class=\"msgSuccess\">All $intCountValues values of the <b>Primary Key {$str_PrimaryKeyName}</b> in all <b>File Lines</b> are <b>Unique</b>.</div></li>";
                } else {
                    $radioImportable = false;
                    $duplicates = array_diff_key($values, array_unique($values));
                    if (!empty($duplicates)) {
                        echo "<li><div class=\"msgError\">The Primary Key $str_PrimaryKeyName includes duplicate values: " . implode(', ', $duplicates) . "</div></li>";
                    }
                }
                $values = null;
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