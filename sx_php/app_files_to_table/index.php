<?php
ini_set('memory_limit', '256M');
/**
 * Converts CSV, XML and JSON files to HTML table
 */
include_once __DIR__ . "/php_functions.php";
include_once __DIR__ . "/jq_create_table.php";


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
        $strSourceFilePath = "../imgPDF/$strSourceFileName";
        if (file_exists($strSourceFilePath)) {

            /**
             * The variable $int_Rundom defines unique tables
             * to be manipulated by unique jQuery Ready functions
             */
            $int_Rundom = rand(100, 1000000); ?>
            <div class="csv_tableFixed">
                <div class="jq_table_container csv_table_container">
                    <div class="jq_table_search csv_table_search text_xxsmall">
                        <div class="flex_start">
                            <button class="jq_ToggleTableView button-small button-border button-gradient" title="Switch between 2 and multiple columns">&#11134</button>
                            <div class="jq_TotalRows"></div>
                        </div>
                        <div class="csv_table_title"><?php echo sx_get_title_from_string($strSourceFileName) ?></div>
                        <div class="flex_end">
                            <input class="jq_SearchInput" type="text" name="searchInput">
                            <button class="jq_SearchButton button-small button-grey button-gradient">Search</button>
                            <button class="jq_ClerButton button-small button-grey button-gradient">Clear</button>

                            <a class="jq_SearchTableHelp" title="View Help" href="javascript:void(0)"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_question_bold"></use>
                                </svg></a>
                            <a class="jq_full_screen" title="View in Full Screen" href="javascript:void(0)"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_full_screen"></use>
                                </svg></a>
                            <a class="jq_full_screen_close" style="display: none;" title="Close Full Screen" href="javascript:void(0)"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_clear_bold"></use>
                                </svg></a>
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
                        <a class="button-grey button-gradient csv_page jq_page" data-page="prev" href="javascript:void(0)">&#9664;</a>
                        <ul class="jq_pagination csv_pagination"></ul>
                        <a class="button-grey button-gradient csv_page jq_page" data-page="next" href="javascript:void(0)">&#9654;</a>
                    </div>
                </div>

                <div class="flex_center csv_export">
                    <div>
                        <div class="text_xsmall">Print or Export Only Visible Table Rows (max 200) to:</div>
                        <a class="button-grey button-gradient jq_PrintElementToPDF" data-id="dataTable_<?php echo $int_Rundom ?>">PDF</a>
                        <a class="button-grey button-gradient jq_ExportTableToHTML" data-id="dataTable_<?php echo $int_Rundom ?>">HTML</a>
                        <a class="button-grey button-gradient jq_ExportTableToExcel" data-id="dataTable_<?php echo $int_Rundom ?>">Excel</a>
                        <a class="button-grey button-gradient jq_ExportElementToCSV" data-id="dataTable_<?php echo $int_Rundom ?>">CSV</a>
                        <a class="button-grey button-gradient jq_ExportElementToWord" data-id="dataTable_<?php echo $int_Rundom ?>">WORD</a>
                    </div>
                    <div>
                        <div class="text_xsmall">Download the entire Source File:</div>
                        <a class="button-grey button-gradient" target="_blank" href="<?= $strSourceFilePath ?>">Download Source File</a>
                    </div>
                </div>
            </div>

            <?php

            $arrData = [];

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
                        $arrHeaders = fgetcsv($csvReader, 0, $fieldSeparator,'"','\\');
                        $arrHeaders = array_map('trim', $arrHeaders);
                        $encoding = mb_detect_encoding($arrHeaders[0]);

                        // To remove BOM encoding - is effectively removed by mb_substr()
                        $arrHeaders[0] = mb_substr($arrHeaders[0], 0, null, $encoding);
                        $radioAddRow = !in_array('RowID', $arrHeaders);

                        $loopRows = 1;
                        // Loop Reading from the second row
                        while (($row = fgetcsv($csvReader, 0, $fieldSeparator,'"','\\')) !== false) {
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
                    //echo 'Final memory usage json: ' . memory_get_usage() . ' bytes<br>';
                    break;
            }

            /**
             * Convert $arrData to a javascript object for the following jQuery ready function
             */

            if (!empty($arrData) && is_array($arrData)) { ?>
                <script>
                    jQuery(function($) {
                        sx_create_csv_table($, <?php echo (int)$int_Rundom ?>, <?php echo json_encode($arrData, JSON_HEX_TAG | JSON_HEX_QUOT) ?>);
                    });
                </script>
<?php
            }
            unset($arrData);
        }
    }
}
?>