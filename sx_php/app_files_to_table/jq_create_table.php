<script>
    var sx_create_csv_table = function($, jq_intRundom, constant_tableData) {
        var $dataTable = $('#dataTable_' + jq_intRundom);
        var $headerObject = $dataTable.find('.jq_TableHeaders');
        var $dataTableContainer = $dataTable.parent();
        var $parent_container = $dataTableContainer.parent();
        var $paginationContainer = $parent_container.find('.jq_pagination_container');
        var $searchObject = $parent_container.find('.jq_table_search');
        var $helpObject = $parent_container.find('.jq_table_help');
        var $toggleTableView = $parent_container.find('.jq_ToggleTableView');

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

        sx_renderTable();
        sx_generatePagination();

        var isTwoColumnsTable;

        function sx_renderTable() {
            sx_renderHeaders();
            if ($(window).width() < 768) {
                isTwoColumnsTable = true;
                sx_renderTwoColumnRows();
            } else {
                isTwoColumnsTable = false;
                sx_renderRows();
            }
        }

        function sx_renderHeaders() {
            var thead = $dataTable.find('thead');
            thead.empty().css('display', 'table-header-group');
            $.each(columnNames, function(index, columnName) {
                thead.append('<th role="columnheader" aria-sort="none">' + columnName + '</th>');
            });
        }

        function sx_getPaginatedData() {
            var startIndex = (currentPage - 1) * rowsPerPage;
            return tableData.slice(startIndex, startIndex + rowsPerPage);
        }

        function sx_convert_to_link(loopValue, columnName) {
            if (typeof loopValue === 'string' && (loopValue.startsWith('http://') || loopValue.startsWith('https://'))) {
                let lastPart = loopValue.substring(loopValue.lastIndexOf('/') + 1).trim();
                if (lastPart === '') {
                    lastPart = columnName
                }
                return '<a target="_black" href="' + loopValue + '">' + lastPart + '</a>';
            }
            return loopValue
        }

        function sx_renderRows() {
            var slicedData = sx_getPaginatedData();
            var tbody = $dataTable.find('tbody');

            // Clear existing rows
            tbody.empty();

            // Append new rows
            $.each(slicedData, function(index, rowData) {
                var row = '<tr>';
                $.each(columnNames, function(index, columnName) {
                    let loopValue = sx_convert_to_link(rowData[columnName], columnName);
                    row += '<td class="' + columnName + '">' + loopValue + '</td>';
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

        function sx_renderTwoColumnRows() {
            var slicedData = sx_getPaginatedData();
            var tbody = $dataTable.find('tbody');
            $dataTable.find('thead').css('display', 'none');

            // Clear existing rows
            tbody.empty();

            // Append new rows
            var strClass = 'odd';
            $.each(slicedData, function(index, rowData) {
                var row = '';
                $.each(columnNames, function(index, columnName) {
                    var columnValue = rowData[columnName];
                    if (columnValue !== '' && columnValue !== undefined && columnValue !== null) {
                        row += '<tr class="' + strClass + '">';
                        row += '<td>' + columnName + '</td>';
                        row += '<td>' + sx_convert_to_link(columnValue, columnName) + '</td>';
                        row += '</tr>';
                    }
                });
                tbody.append(row);

                if (strClass === 'odd') {
                    strClass = 'even';
                } else {
                    strClass = 'odd';
                }
            });
            totalRows = tableData.length;
            visibleRows = slicedData.length;
            $searchObject
                .find('.jq_TotalRows')
                .html('Total Rows: ' + totalRows + '<br>Rows/Page: ' + visibleRows);
        }

        function sx_generatePagination() {
            var paginationHtml = '';
            for (var i = 1; i <= totalPages; i++) {
                paginationHtml += '<li class="' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</li>';
            }
            $paginationContainer.find('.jq_pagination').html(paginationHtml);
            if (active_SortingIndex > -1) {
                $dataTableContainer.find('.jq_TableHeaders th').eq(active_SortingIndex).addClass('active').siblings().removeClass('active');
            }
        }

        $paginationContainer.on('click', 'li', function() {
            var page = $(this).data('page');
            if (page) {
                currentPage = page;
                sx_renderTable();
                sx_generatePagination();
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
            sx_renderTable();
            sx_generatePagination();
            $dataTableContainer.stop(true, true).animate({
                    scrollTop: 0
                },
                300);

        });

        $searchObject.on('click', '.jq_SearchButton', function(e) {
            var searchText = $(this).siblings('.jq_SearchInput').val().toLowerCase();
            filterTable(searchText);
            currentPage = 1; // Reset to the first page after a new search
            sx_renderTable();
            sx_generatePagination();
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
            sx_renderTable();
            $paginationContainer.find('li').eq(0).click();
            $headerObject.find('th').eq(columnIndex).addClass('active').siblings().removeClass('active');
            active_SortingIndex = columnIndex;
        });

        function sortTable(columnIndex) {
            var columnKey = columnNames[columnIndex];
            var isAscending = sortDirection[columnKey] === 'asc';

            // Determine sorting function based on column type
            tableData.sort(function(a, b) {
                var valueA = a[columnKey];
                var valueB = b[columnKey];

                // Handle RowID specifically to restore the initial order
                if (columnKey === 'RowID') {
                    return isAscending ?
                        valueA - valueB // Ascending
                        :
                        valueB - valueA; // Descending
                }

                // Determine if values are numeric
                var isNumericA = !isNaN(valueA) && valueA !== null && valueA !== '';
                var isNumericB = !isNaN(valueB) && valueB !== null && valueB !== '';

                if (isNumericA && isNumericB) {
                    // Numeric comparison
                    return isAscending ?
                        valueA - valueB // Ascending
                        :
                        valueB - valueA; // Descending
                } else {
                    // String comparison
                    return isAscending ?
                        valueA.toString().localeCompare(valueB) :
                        valueB.toString().localeCompare(valueA);
                }
            });

            // Toggle sort direction for next sort
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

        var debounce;
        $(window).resize(function() {
            clearTimeout(debounce);
            debounce = setTimeout(function() {
                if ($(window).width() < 768) {
                    if (!isTwoColumnsTable) {
                        sx_renderTable();
                        sx_generatePagination();
                        isTwoColumnsTable = true;
                    }
                } else {
                    if (isTwoColumnsTable) {
                        sx_renderTable();
                        sx_generatePagination();
                        isTwoColumnsTable = false;
                    }
                }
            }, 200);
        });

        $toggleTableView.on('click', function() {
            if (!isTwoColumnsTable) {
                sx_renderTwoColumnRows();
                sx_generatePagination();
                isTwoColumnsTable = true;
            } else {
                sx_renderHeaders();
                sx_renderRows();
                sx_generatePagination();
                isTwoColumnsTable = false;
            }
        })

    };
</script>