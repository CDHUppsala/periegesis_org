<script>
    var sx_createViewTable = function($, jq_intRundom, constant_tableData) {

        var $dataTable = $('#dataTable_' + jq_intRundom);
        var $headerObject = $dataTable.find('.jq_TableHeaders');
        var $dataTableContainer = $dataTable.parent();
        var $parent_container = $dataTableContainer.parent();
        var $paginationContainer = $parent_container.find('.jq_pagination_container');
        var $searchObject = $parent_container.find('.jq_table_search');
        var $helpObject = $parent_container.find('.jq_table_help');

        var rowsPerPage = 200;
        var currentPage = 1;
        var sortDirection = {}; // Keep track of sorting direction for each column
        var totalPages;
        var active_SortingIndex = -1;
        /**
         * The variables js_object_view_data and js_object_view_data_2 (if any)
         * are defines as constand JSON text rendered in the HTML page
         * the are used to define and restore tableData
         */
        var tableData;
        var columnNames;

        tableData = constant_tableData;
        columnNames = Object.keys(tableData[0]);

        // Initial rendering
        totalRows = tableData.length;
        totalPages = Math.ceil(totalRows / rowsPerPage);
        if (totalRows <= rowsPerPage) {
            $($paginationContainer).hide();
        }

        renderTable();
        generatePagination();

        function renderTable() {
            renderHeaders();
            renderRows();
        }

        function renderHeaders() {
            var thead = $dataTable.find('thead');
            thead.empty();
            $.each(columnNames, function(index, columnName) {
                thead.append("<th>" + columnName + "</th>");
            });
        }

        function renderRows() {
            var startIndex = (currentPage - 1) * rowsPerPage;
            var endIndex = startIndex + rowsPerPage;
            var slicedData = tableData.slice(startIndex, endIndex);
            var tbody = $dataTable.find('tbody');

            // Clear existing rows
            tbody.empty();

            // Append new rows
            $.each(slicedData, function(index, rowData) {
                var row = '<tr>';
                $.each(columnNames, function(_, columnName) {
                    row += '<td class="' + columnName + '">' + rowData[columnName] + '</td>';
                });
                row += '</tr>';
                tbody.append(row);
            });
            totalRows = tableData.length;
            visibleRows = slicedData.length;
            $searchObject
                .find('.jq_TotalRows')
                .html('Total Rows: ' + totalRows + '<br>Rows/Page: ' + visibleRows);
        }

        function generatePagination() {
            var paginationHtml = '';
            for (var i = 1; i <= totalPages; i++) {
                paginationHtml += '<li class="' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</li>';
            }
            $paginationContainer.find('.jq_pagination').html(paginationHtml);
            if (active_SortingIndex > -1) {
                $dataTableContainer.find('.jq_TableHeaders th').eq(active_SortingIndex).addClass('active').siblings().removeClass('active');
            }
            if (typeof sx_activate_book_chapters_maps === 'function') {
                sx_activate_book_chapters_maps($, $dataTable);
            }
        }

        $paginationContainer.on('click', 'li', function() {
            var page = $(this).data('page');
            if (page) {
                currentPage = page;
                renderTable();
                generatePagination();
                $dataTableContainer.stop(true, true).animate({
                        scrollTop: 0
                    },
                    300);
            }
        });

        $paginationContainer.on('click', '.jq_page', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            if (page == 'next') {
                currentPage = currentPage + 1;
                if (currentPage > totalPages) {
                    currentPage = 1;
                }
            } else {
                currentPage = currentPage - 1;
                if (currentPage < 1) {
                    currentPage = totalPages;
                }
            }
            renderTable();
            generatePagination();
            $dataTableContainer.stop(true, true).animate({
                    scrollTop: 0
                },
                300);
        });

        $searchObject.on('click', '.jq_SearchButton', function(e) {
            var searchText = $(this).siblings('.jq_SearchInput').val().toLowerCase();
            filterTable(searchText);
            currentPage = 1; // Reset to the first page after a new search
            renderTable();
            generatePagination();
            $dataTableContainer.animate({
                    scrollTop: 0
                },
                'fast');
        });

        $searchObject.on('click', '.jq_ClerButton', function() {
            $(this).siblings('.jq_SearchInput').val('');
            $(this).siblings('.jq_SearchButton').click();
        });

        function filterTable(searchText) {
            if (searchText.trim() === "") {
                tableData = constant_tableData;
            } else {
                tableData = constant_tableData.filter(function(row) {
                    return Object.values(row).some(value =>
                        value.toString().toLowerCase().includes(searchText)
                    );
                });
            }
            totalPages = Math.ceil(tableData.length / rowsPerPage);
        }

        $headerObject.on('click', 'th', function() {
            var columnIndex = $(this).index();
            var firstColumnKey = columnNames[0];
            sortTable(columnIndex);
            renderTable();
            $paginationContainer.find('li').eq(0).click();
            $headerObject.find('th').eq(columnIndex).addClass('active').siblings().removeClass('active');
            active_SortingIndex = columnIndex;
        });

        function sortTable(columnIndex) {
            var columnKey = columnNames[columnIndex];
            var isAscending = sortDirection[columnKey] === 'asc';
            if (columnKey != 'RowID') {
                tableData.sort(function(a, b) {
                    var valueA = a[columnKey];
                    var valueB = b[columnKey];

                    if (isAscending) {
                        return valueA.toString().localeCompare(valueB);
                    } else {
                        return valueB.toString().localeCompare(valueA);
                    }
                });
            } else {
                if (isAscending) {
                    sortDirection[columnKey] = isAscending ? 'desc' : 'asc';
                    return tableData.sort((a, b) => (a.RowID > b.RowID) ? 1 : -1);
                } else {
                    sortDirection[columnKey] = isAscending ? 'desc' : 'asc';
                    return tableData.sort((a, b) => (a.RowID > b.RowID) ? -1 : 1);
                }
            }
            sortDirection[columnKey] = isAscending ? 'desc' : 'asc';
        }

        $searchObject.on('click', '.jq_SearchTableHelp', function() {
            $helpObject.slideToggle(300);
        })
        $helpObject.on('click', '.jq_TableHelpClose', function() {
            $helpObject.slideUp(300);
        })

        $searchObject.on('click', '.jq_full_screen', function() {
            $(this).hide().next('a').show();
            $parent_container.addClass('csv_table_fixed')
                .parent().css({
                    'height': '100vh',
                    'widht': '100vw'
                });
            $('html, body').css('overflow', 'hidden');
        })
        $searchObject.on('click', '.jq_full_screen_close', function() {
            $(this).hide().prev('a').show();
            $parent_container.removeClass('csv_table_fixed')
                .parent().css('height', 'auto');

            $('html, body').css('overflow', 'visible');
        })
    }
</script>