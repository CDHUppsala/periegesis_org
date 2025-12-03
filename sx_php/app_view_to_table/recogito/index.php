<?php

/** 
 * SPECIAL APPLICATION FOR Periegesis to show predifined Views from RECOGITO Database
 * Can be adapted to show pridefined Views from any database
 * Include functions only once
 */
$conn = dbconn();
include_once __DIR__ . "/jq_create_table.php";
include_once __DIR__ . "/jq_query_table.php";
include_once __DIR__ . "/views_recogito_functions.php";

/**
 * The name of the Database View can be directly entered in the field Media Folder
 * However, if you enter in that field the standard name 'view_Select_Among_Views'
 *  - a select list will appear with all relevant views in the database
 *  - which all start with the prefix 'view_' and are checked by a white list
 * The visitor can select the View to be displayed
 * Can be used only once in a page, so check with $radioFirstSelect
 */

if ($strViewName == 'view_Select_Among_Views') {
    if ($radioFirstSelect === false) {
        // The database view comes from a select list
        include __DIR__ . "/select_among_views.php";
        if (isset($selected_view) && !empty($selected_view)) {
            $str_requestedRecogitoView = $selected_view;
        }
    }
} else {
    // The database view is directly entered in the field Media Folder
    $str_requestedRecogitoView = $strViewName;
}

/**
 * The variable $str_requestedRecogitoView comes from articles/default.php
 * The variable $int_Rundom defines unique tables
 * to be manipulated by unique jQuery ready functions
 */
if (isset($str_requestedRecogitoView) && !empty($str_requestedRecogitoView)) {
    if (str_contains($str_requestedRecogitoView, ';') === false) {
        $str_requestedRecogitoView .= ';';
    }
    $arr_requestedRecogitoView = explode(';', $str_requestedRecogitoView);

    foreach ($arr_requestedRecogitoView as $str_requestedRecogitoView) {
        $str_requestedRecogitoView = trim($str_requestedRecogitoView);
        if (!empty($str_requestedRecogitoView)) {
            $int_Rundom = rand(100, 1000000);
?>
            <div class="jq_tableFixed csv_tableFixed">
                <div class="jq_table_container csv_table_container">
                    <div class="jq_table_search csv_table_search text_xxsmall">
                        <div class="flex_start">
                        <button class="jq_ToggleTableView button-small button-border button-gradient" title="Switch between 2 and multiple columns">&#11134</button>
                        <div class="jq_TotalRows"></div>
                        </div>
                        <div class="csv_table_title"><?php echo sx_get_title_from_string($str_requestedRecogitoView) ?></div>
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
                        <div class="text_xsmall">Print or Export Only Visible Table Rows (max 200) to:
                            <code title="To open UTF-8 CSV files in Excel: Use “Data → Import → From Text/CSV” and select UTF-8 encoding.">[i]</code>
                        </div>
                        <a class="button-grey button-gradient jq_PrintElementToPDF" data-id="dataTable_<?php echo $int_Rundom ?>">PDF</a>
                        <a class="button-grey button-gradient jq_ExportTableToHTML" data-id="dataTable_<?php echo $int_Rundom ?>">HTML</a>
                        <a class="button-grey button-gradient jq_ExportTableToExcel" data-id="dataTable_<?php echo $int_Rundom ?>">Excel</a>
                        <a class="button-grey button-gradient jq_ExportElementToCSV" data-id="dataTable_<?php echo $int_Rundom ?>">CSV</a>
                        <a class="button-grey button-gradient jq_ExportElementToWord" data-id="dataTable_<?php echo $int_Rundom ?>">WORD</a>
                    </div>
                    <div>
                        <form method="POST" name="ExportAllRows_<?php echo $int_Rundom ?>" action="">
                            <input type="hidden" name="RecogitoView" value="<?= $str_requestedRecogitoView ?>" />
                            <div class="text_xsmall">Export All Table Rows to: 
                                <code title="To open UTF-8 CSV files in Excel: Use “Data → Import → From Text/CSV” and select UTF-8 encoding.">[i]</code>
                            </div>
                            <div class="flex_center">
                                <label><input type="radio" name="ExportType" value="csv" checked> csv</label>
                                <label><input type="radio" name="ExportType" value="xml"> xml</label>
                                <label><input type="radio" name="ExportType" value="json"> json</label>
                                <button class="button-grey button-gradient jq_ExportAllRows" type="submit">Export</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php
            include __DIR__ . "/views_recogito.php";

            /**
             * The variable $json_Data is defined in the above include
             */
            if (!empty($json_Data)) { ?>
                <script>
                    jQuery(function($) {
                        $(".jq_ExportAllRows").click(function() {
                            $(this).closest("form")
                                .attr("action", "apps/views_recogito_export.php")
                                .attr("target", "_blank");
                        });

                    });
                    jQuery(function($) {
                        sx_createViewTable($, <?php echo $int_Rundom ?>, <?php echo $json_Data ?>)
                    });
                </script>
<?php
            }
        }
    }
}
?>