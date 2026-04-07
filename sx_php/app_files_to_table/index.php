<?php
//ini_set('memory_limit', '256M');

/**
 * Converts CSV, XML and JSON files to HTML table
 */
include_once __DIR__ . "/php_functions.php";
echo '<script>';
include_once __DIR__ . "/jq_create_table.js";
echo '</script>';

/**
 * The variable $str_SourceFileNames comes from the function get_apps_files_to_table() 
 *      that also includes all pages of this application (including this one)
 *  - The function can be called from anywhere, but usually from the default page for articles
 */

if (empty($str_SourceFileNames)) {
    header("Location: index.php");
    exit();
}
if (str_contains($str_SourceFileNames, ';') === false) {
    $str_SourceFileNames .= ';';
}
$arrSourceFileNames = explode(';', $str_SourceFileNames);

foreach ($arrSourceFileNames as $strSourceFileName) {
    $strSourceFileName = trim($strSourceFileName);

    if (!empty($strSourceFileName)) {
        $strSourceFilePath = "../imgMedia/$strSourceFileName";
        if (file_exists($strSourceFilePath)) {

            /**
             * The variable $int_Rundom defines unique tables
             * to be manipulated by unique jQuery Ready functions
             */
            $int_Rundom = rand(100, 1000000); ?>
            <div class="csv_tableFixed" id="Instance_<?php echo $int_Rundom ?>">
                <div class="jq_table_container csv_table_container">
                    <div class="jq_table_search csv_table_search">
                        <button class="jq_ToggleTableView title="Switch between 2 and multiple columns">
                            <svg id="sx_share_vertical_25" class="svg_First" viewBox="0 0 32 32">
                                <path
                                    d="M3 1 q-2 0 -2 2 v26 q0 2 2 2 h3 q2 0 2 -2 v-26 q0-2 -2-2z M12 1 q-2 0 -2 2 v26 q0 2 2 2 h17 q2 0 2 -2 v-26 q0-2 -2-2z">
                                </path>
                            </svg>
                            <svg id="sx_shared_horizontal_25" class="svg_Second" style="display: none" viewBox="0 0 32 32">
                                <path
                                    d="M3 1 q-2 0 -2 2 v3 q0 2 2 2 h26 q2 0 2 -2 v-3 q0-2 -2-2z M3 10 q-2 0 -2 2 v17 q0 2 2 2 h26 q2 0 2 -2 v-17 q0-2 -2-2z">
                                </path>
                            </svg>
                        </button>
                        <div class="jq_TotalRows"></div>
                        <div class="csv_table_title"><?php echo sx_get_title_from_string($strSourceFileName) ?></div>
                        <div class="csv_flex">
                            <input class="jq_SearchInput" type="text" name="searchInput">
                            <button class="jq_SearchButton">Search</button>
                            <button class="jq_ClerButton">Clear</button>
                            <button class="jq_SearchTableHelp title="View Help">
                                <svg id="sx_info_square" viewBox="0 0 32 32">
                                    <path d="M 14 1 q-1 0 -1 1 v4 q0 1 1 1 h4 q1 0 1 -1 v-4 q0 -1 -1 -1z M14 12 q-1 0 -1 1 v17 q0 1 1 1 h4 q1 0 1 -1 v-17 q0 -1 -1 -1z"> </path>
                                </svg>
                            </button>
                            <button class="jq_ToggleFullScreen title="View in Full Screen">
                                <svg class="svg_First">
                                    <path d="m1 1 v8 l 3-3 7 7 2-2 -7-7 3-3z M1 31 h8 l-3-3 7-7 -2-2 -7 7 -3-3z M31 31 v-8 l-3 3 -7-7 -2 2 7 7 -3 3z M31 1 h-8 l 3 3 -7 7 2 2 7-7 3 3z"></path>
                                </svg>
                                <svg class="svg_Second" style="display: none;">
                                    <path d="M5 3 a1 1 0 0 0 -2 2 l11 11 -11 11 a1 1 0 0 0 2 2 l11 -11 11 11 a1 1 0 0 0 2 -2 l-11 -11 11 -11 a1 1 0 0 0 -2-2 l-11 11z"> </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <?php
                    include __DIR__ . "/help.html";
                    ?>
                    <div class="jq_data_table_container csv_data_table_container">
                        <table class="jq_data_table csv_data_table" id="dataTable_<?php echo $int_Rundom ?>">
                            <thead class="jq_TableHeaders"></thead>
                            <tbody class="jq_DataBody"></tbody>
                        </table>
                    </div>

                    <div class="jq_pagination_container csv_pagination_container">
                        <button class="button-grey button-gradient csv_page jq_page" data-page="prev">&#9664;</button>
                        <ul class="jq_pagination csv_pagination"></ul>
                        <button class="button-grey button-gradient csv_page jq_page" data-page="next">&#9654;</button>
                    </div>
                </div>

                <div class="flex_center csv_export">
                    <div title="Use for Presentation Purposes only, Not in production.">
                        <div class="text_xsmall">Print only Visible Table Rows (max 200) to:</div>
                        <button class="button-grey button-gradient jq_PrintElementToPDF" data-id="dataTable_<?php echo $int_Rundom ?>">PDF</button>
                        <button class="button-grey button-gradient jq_ExportTableToHTML" data-id="dataTable_<?php echo $int_Rundom ?>">HTML</button>
                        <button class="button-grey button-gradient jq_ExportTableToExcel" data-id="dataTable_<?php echo $int_Rundom ?>">Excel</button>
                        <!--button class="button-grey button-gradient jq_ExportElementToCSV" data-id="dataTable_<?php //echo $int_Rundom ?>">CSV</button-->
                        <button class="button-grey button-gradient jq_ExportElementToWord" data-id="dataTable_<?php echo $int_Rundom ?>">WORD</button>
                    </div>
                    <div>
                        <div class="text_xsmall">Download Filtered File to:</div>
                        <button class="button-grey button-gradient" id="jq_FilteredToCSV_<?php echo $int_Rundom ?>">CSV</button>
                        <button class="button-grey button-gradient" id="jq_FilteredToJSON_<?php echo $int_Rundom ?>">JSON</button>
                        <button class="button-grey button-gradient" id="jq_FilteredToXML_<?php echo $int_Rundom ?>">XML</button>
                    </div>
                    <div>
                        <div class="text_xsmall">Download Source File:</div>
                        <a class="button-grey button-gradient" target="_blank" href="<?= $strSourceFilePath ?>">Entire Source File</a>
                    </div>
                </div>
            </div>

            <?php

            $arrData = [];

            // used for export/downloads
            $str_LoadedFileName = return_file_name($strSourceFilePath);

            $strFileExtension = return_file_extension($strSourceFilePath);
            switch ($strFileExtension) {
                case 'csv':
                    if (($csvReader = fopen($strSourceFilePath, 'r')) !== false) {
                        // Read the first row to get the separator
                        $fields = fgets($csvReader);
                        $fieldSeparator = strpos($fields, ",") !== false ? "," : ";";
                        $fields = null;

                        // Back to first row
                        rewind($csvReader);

                        // Read the first row to get headers
                        $arrHeaders = fgetcsv($csvReader, 0, $fieldSeparator, '"', '\\');
                        $arrHeaders = array_map('trim', $arrHeaders);
                        $encoding = mb_detect_encoding($arrHeaders[0]);

                        // To remove BOM encoding - is effectively removed by mb_substr()
                        $arrHeaders[0] = mb_substr($arrHeaders[0], 0, null, $encoding);

                        $radioAddRow = !in_array('ps_Row', $arrHeaders);

                        $loopRows = 1;
                        // Loop Reading from the second row
                        while (($row = fgetcsv($csvReader, 0, $fieldSeparator, '"', '\\')) !== false) {
                            $rowData = [];
                            if ($radioAddRow) {
                                $rowData['ps_Row'] = $loopRows;
                            }
                            foreach ($arrHeaders as $index => $header) {
                                $rowData[$header] = str_replace('","', '", "', $row[$index]);
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
                    $xmlContent = simplexml_load_file($strSourceFilePath);
                    // Clean UTF-8 BOM, if any
                    $xmlContent = removeBOM($xmlContent);

                    if ($xmlContent !== false) {
                        // Convert the SimpleXMLElement object to JSON and then decode to associative array
                        $jsonData = json_encode($xmlContent);
                        $xmlContent = null;

                        $arrData = json_decode($jsonData, true);
                        $jsonData = null;
                    }

                    // Move to the second dimension: table > row > columns
                    $arrData = reset($arrData) ? $arrData[key($arrData)] : [];
                    if (!empty($arrData) && is_array($arrData)) {
                        // Clean empty fields and normalize
                        $arrData = cleanEmptyFields($arrData);
                        $arrData = normalizeArrayStructure($arrData);
                    }

                    foreach ($arrData as $index => &$row) {
                        // Prepend ps_Row as the first key
                        $row = array_merge(
                            ['ps_Row' => $index + 1], // new key:value pair
                            $row                        // existing data
                        );
                    }
                    unset($row); // break reference

                    //echo 'Final memory usage xml: ' . memory_get_usage() . ' bytes<br>';
                    break;

                case 'json':
                    $jsonContent = file_get_contents($strSourceFilePath);
                    // Clean UTF-8 BOM, if any
                    $jsonContent = removeBOM($jsonContent);

                    if ($jsonContent !== false) {
                        $arrData = json_decode($jsonContent, true);
                        $jsonContent = null;

                        if (!empty($arrData) && is_array($arrData)) {
                            $arrData = normalizeArrayStructure($arrData);
                        }
                    }

                    foreach ($arrData as $index => &$row) {
                        // Prepend ps_Row as the first key
                        $row = array_merge(
                            ['ps_Row' => $index + 1], // new key:value pair
                            $row                        // existing data
                        );
                    }
                    unset($row); // break reference

                    //echo 'Final memory usage json: ' . memory_get_usage() . ' bytes<br>';
                    break;
            }

            /**
             * Convert $arrData to a javascript object for the following jQuery ready function
             */

            if (!empty($arrData) && is_array($arrData)) { ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        //var str_LoadedFileName = "<?php echo $str_LoadedFileName ?>";
                        sx_create_csv_table(
                            <?php echo (int)$int_Rundom ?>,
                            <?php echo json_encode($arrData, JSON_HEX_TAG | JSON_HEX_QUOT) ?>,
                            "<?php echo $str_LoadedFileName ?>"
                        );
                    });
                </script>
<?php
            }
            unset($arrData);
        }
    }
}
?>