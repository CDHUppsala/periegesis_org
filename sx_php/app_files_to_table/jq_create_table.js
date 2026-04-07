
// Bound the codes to the ID of every Table Instance: Comon_Prefix_jq_intRundom
function sx_create_csv_table(jq_intRundom, constant_tableData, LoadedFileName) {
    let sx_filtered_data = null;


    const data_Instance = document.getElementById('Instance_' + jq_intRundom);

    // DOM caching the data table by its distinguishing ID suffix (rundom number) 
    const dataTable = document.getElementById('dataTable_' + jq_intRundom);
    if (!dataTable) {
        console.warn('dataTable not found', jq_intRundom);
        return;
    }
    const table_Headers = dataTable.querySelector('.jq_TableHeaders');
    const table_Body = dataTable.querySelector('.jq_DataBody');



    const table_Container = data_Instance.querySelector('.jq_table_container');
    const data_TableContainer = data_Instance.querySelector('.jq_data_table_container');

    const pagination_Container = data_Instance.querySelector('.jq_pagination_container');
    const table_Search = data_Instance.querySelector('.jq_table_search');
    const toggle_TableHelp = data_Instance.querySelector('.jq_table_help');
    const toggle_TableView = data_Instance.querySelector('.jq_ToggleTableView');
    const toggle_FullScreen = data_Instance.querySelector('.jq_ToggleFullScreen');

    let rowsPerPage = 200;
    let currentPage = 1;
    let sortDirection = {};
    let totalPages;
    let active_SortingIndex = -1;

    let tableData;
    let columnNames;

    tableData = constant_tableData || [];
    if (!tableData.length) {
        console.warn('no table data');
        return;
    }
    columnNames = Object.keys(tableData[0]);


    /**
     * ===============================================
     * 1. Render the table Headers, Rows and Pagination:
     *      - Initially with the file loading
     *      - Subsequenly from event listneres for
     *          . Searching/filtering
     *          . Sorting
     *          . Pagination
     * ===============================================
     */

    var totalRows = tableData.length;
    totalPages = Math.ceil(totalRows / rowsPerPage);
    if (totalRows <= rowsPerPage && pagination_Container) {
        // hide pagination area
        pagination_Container.style.display = 'none';
    }

    var isTwoColumnsTable = false;

    sx_renderTable();
    sx_generatePagination();


    function sx_getWindowWidth() {
        return window.innerWidth || document.documentElement.clientWidth;
    }

    function sx_renderTable() {
        sx_renderHeaders();
        if (sx_getWindowWidth() < 768) {
            isTwoColumnsTable = true;
            sx_renderTwoColumnRows();
        } else {
            isTwoColumnsTable = false;
            sx_renderRows();
        }
    }

    function sx_renderHeaders() {
        var thead = dataTable.querySelector('thead');
        if (!thead) return;
        thead.innerHTML = '';
        //thead.style.display = 'table-header-group';
        for (var i = 0; i < columnNames.length; i++) {
            var columnName = columnNames[i];
            var th = document.createElement('th');
            th.setAttribute('role', 'columnheader');
            th.setAttribute('aria-sort', 'none');
            th.textContent = columnName;
            thead.appendChild(th);
        }
    }

    function sx_renderRows() {
        var slicedData = sx_getPaginatedData();
        var tbody = dataTable.querySelector('tbody');
        if (!tbody) return;

        var html = "";

        for (var r = 0; r < slicedData.length; r++) {
            var rowData = slicedData[r];
            html += "<tr>";
            for (var c = 0; c < columnNames.length; c++) {
                var columnName = columnNames[c];
                var rawValue = rowData[columnName];
                var value = sx_convert_to_link(rawValue, columnName);
                html += '<td class="' + escapeHtml(columnName) + '">' + value + '</td>';
            }
            html += "</tr>";
        }

        tbody.innerHTML = html;

        totalRows = tableData.length;
        var visibleRows = slicedData.length;
        if (table_Search) {
            var el = table_Search.querySelector('.jq_TotalRows');
            if (el) el.innerHTML = 'Total Rows: ' + totalRows + '<br>Rows/Page: ' + visibleRows;
        }
    }

    function sx_renderTwoColumnRows() {
        var slicedData = sx_getPaginatedData();
        var tbody = dataTable.querySelector('tbody');
        if (!tbody) return;

        var thead = dataTable.querySelector('thead');
        //if (thead) thead.style.display = 'none';
        thead.innerHTML = '';

        var html = "";
        var rowClass = "odd";

        for (var r = 0; r < slicedData.length; r++) {
            var rowData = slicedData[r];
            for (var c = 0; c < columnNames.length; c++) {
                var columnName = columnNames[c];
                var val = rowData[columnName];
                if (val !== "" && val !== undefined && val !== null) {
                    html += '<tr class="' + rowClass + '">';
                    html += '<th>' + escapeHtml(columnName) + '</th>';
                    html += '<td>' + sx_convert_to_link(val, columnName) + '</td>';
                    html += '</tr>';
                }
            }
            rowClass = (rowClass === 'odd') ? 'even' : 'odd';
        }

        tbody.innerHTML = html;

        totalRows = tableData.length;
        var visibleRows = slicedData.length;
        if (table_Search) {
            var el = table_Search.querySelector('.jq_TotalRows');
            if (el) el.innerHTML = 'Total Rows: ' + totalRows + '<br>Rows/Page: ' + visibleRows;
        }
    }

    function sx_generatePagination() {
        if (!pagination_Container) return;
        var ul = pagination_Container.querySelector('.jq_pagination');
        if (!ul) return;

        var paginationHtml = '';
        for (var i = 1; i <= totalPages; i++) {
            paginationHtml += '<li class="' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + i + '</li>';
        }
        ul.innerHTML = paginationHtml;

        if (active_SortingIndex > -1) {
            var headerThs = data_TableContainer.querySelectorAll('.jq_TableHeaders th');
            if (headerThs && headerThs[active_SortingIndex]) {
                headerThs[active_SortingIndex].classList.add('active');
                // remove from siblings
                for (var k = 0; k < headerThs.length; k++) {
                    if (k !== active_SortingIndex) headerThs[k].classList.remove('active');
                }
            }
        }
    }


    /**
     * ===============================================
     * 2. Render the Table Helpers
     * ===============================================
     */

    function sx_getPaginatedData() {
        var startIndex = (currentPage - 1) * rowsPerPage;
        return tableData.slice(startIndex, startIndex + rowsPerPage);
    }

    function sx_convert_to_link(loopValue, columnName) {
        const patterns = {
            wikidata: /^Q\d+$/,
            bookid: /^\d+\.\d+\.\d+$/,
        };

        if (patterns.wikidata.test(loopValue)) {
            const url = `https://www.wikidata.org/wiki/${loopValue}`;
            return `<a href="${escapeHtml(url)}" target="_blank">${escapeHtml(loopValue)}</a>`;
        } else if (patterns.bookid.test(loopValue)) {
            const safeID = escape_HTML(loopValue);
            return `<a title="Open section in Read Pausanias with Maps" href="map_periegesis.php?b=${encodeURIComponent(safeID)}" target="_blank">${safeID}</a>`;
        } else if (typeof loopValue === 'string' && (loopValue.startsWith('http://') || loopValue.startsWith('https://'))) {
            var lastPart = loopValue.substring(loopValue.lastIndexOf('/') + 1).trim();
            if (lastPart === '' || lastPart.startsWith('urn:cts') || lastPart.length > 25) lastPart = columnName;
            return '<a target="_blank" href="' + escapeHtml(loopValue) + '">' + escapeHtml(lastPart) + '</a>';
        }
        return escapeHtml(loopValue);
    }

    // simple HTML escape for safety
    function escapeHtml(v) {
        if (v === null || v === undefined) return '';
        return String(v)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }


    /**
     * ====================================================
     * 3. Event Listeners
     * ====================================================
     */

    // Pagination by clicking page number
    pagination_Container.addEventListener('click', function (ev) {
        // Case 1: direct page number <li data-page="...">
        const li = ev.target.closest('li[data-page]');
        if (li) {
            const page = parseInt(li.getAttribute('data-page'), 10);
            if (page) {
                currentPage = page;
                sx_renderTable();
                sx_generatePagination();
                if (data_TableContainer) data_TableContainer.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
            return;
        }

        // Case 2: next/prev navigation element with .jq_page
        const nav = ev.target.closest('.jq_page');
        if (nav) {
            ev.preventDefault();
            const page = nav.getAttribute('data-page');
            if (page === 'next') {
                currentPage = currentPage + 1;
                if (currentPage > totalPages) currentPage = 1;
            } else {
                currentPage = currentPage - 1;
                if (currentPage < 1) currentPage = totalPages;
            }
            sx_renderTable();
            sx_generatePagination();
            if (data_TableContainer) data_TableContainer.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    }, false);

    // Search button triggering the search process
    table_Search.querySelector('.jq_SearchButton').addEventListener('click', function (ev) {
        const input = table_Search.querySelector('.jq_SearchInput');
        const searchText = input ? input.value.toLowerCase() : '';
        filterTable(searchText);
        currentPage = 1;
        sx_renderTable();
        sx_generatePagination();
        if (data_TableContainer) data_TableContainer.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }, false);

    // Clear button that clreas the search input content
    table_Search.querySelector('.jq_ClerButton').addEventListener('click', function (ev) {
        const input = table_Search.querySelector('.jq_SearchInput');
        if (input) input.value = '';
        table_Search.querySelector('.jq_SearchButton').click();
    }, false);

    // Help toggle: Show/Hide help information
    table_Search.querySelector('.jq_SearchTableHelp').addEventListener('click', function (ev) {
        toggle_TableHelp.classList.toggle('visible');
    }, false);

    // Close table help from inside help
    if (toggle_TableHelp) {
        toggle_TableHelp.querySelector('.jq_TableHelpClose').addEventListener('click', function (ev) {
            toggle_TableHelp.classList.toggle('visible');
        }, false);
    }

    // Open Fullscreen
    toggle_FullScreen.addEventListener('click', function (ev) {
        const first_svg = this.querySelector('.svg_First');
        const second_svg = this.querySelector('.svg_Second');
        if (second_svg.style.display === 'none') {
            first_svg.style.display = 'none';
            second_svg.style.display = '';

            if (table_Container) table_Container.classList.add('csv_table_fixed');
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            data_Instance.style.height = '100vh';
        } else {
            first_svg.style.display = '';
            second_svg.style.display = 'none';
            table_Container.classList.remove('csv_table_fixed');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
            data_Instance.style.height = 'auto';
        }
    }, false);


    // Sort by clicking on Header/Column
    if (table_Headers) {
        table_Headers.addEventListener('click', function (ev) {
            const th = ev.target.closest('th');
            if (!th) return;

            // Conver the DOM of Headers to array
            const ths = Array.from(table_Headers.querySelectorAll('th'));
            // const ths = [...table_Headers.querySelectorAll('th')];

            const columnIndex = ths.indexOf(th);
            if (columnIndex === -1) return;

            // sort and re-render
            sortTable(columnIndex);
            currentPage = 1;
            sx_renderTable();
            sx_generatePagination();

            // Get a new array of headers after DOM change
            const new_Headers = Array.from(table_Headers.querySelectorAll('th'));
            new_Headers.forEach((el, index) => {
                el.classList.toggle('active', index === columnIndex);
            });

            active_SortingIndex = columnIndex;

            if (data_TableContainer) {
                data_TableContainer.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }, false);
    }


    /**
     * ===============================================
     * 4. Functions called by Event Listener
     * ===============================================
     */


    function filterTable(searchText) {
        if (searchText.trim() === "") {
            tableData = constant_tableData;
            sx_filtered_data = tableData;
        } else {
            var search = searchText.toLowerCase();
            tableData = constant_tableData.filter(function (row) {
                return Object.values(row).some(function (value) {
                    return String(value || "").toLowerCase().includes(search);
                });
            });
            sx_filtered_data = tableData;
        }
        totalPages = Math.ceil(tableData.length / rowsPerPage);
    }

    function sortTable(columnIndex) {
        var columnKey = columnNames[columnIndex];
        var isAscending = sortDirection[columnKey] === 'asc';

        tableData.sort(function (a, b) {
            var valueA = a[columnKey];
            var valueB = b[columnKey];

            if (columnKey === 'ps_Row') {
                return isAscending ? (valueA - valueB) : (valueB - valueA);
            }

            var isNumericA = typeof valueA === 'number' && !isNaN(valueA);
            var isNumericB = typeof valueB === 'number' && !isNaN(valueB);

            if (isNumericA && isNumericB) {
                return isAscending ? (valueA - valueB) : (valueB - valueA);
            } else {
                var aStr = (valueA ?? "").toString();
                var bStr = (valueB ?? "").toString();
                return isAscending ? aStr.localeCompare(bStr) : bStr.localeCompare(aStr);
            }
        });

        sortDirection[columnKey] = isAscending ? 'desc' : 'asc';
    }

    /**
     * ===============================================
     * 5 Event Listener for toggling between verticlal/horizontal table view
     * ===============================================
     */


    // Automatically toggle Vertical/Horizonta table view by window resize, with debounce
    /*
    var debounce;
    window.addEventListener('resize', function() {
        clearTimeout(debounce);
        debounce = setTimeout(function() {
            if (sx_getWindowWidth() < 768) {
                if (!isTwoColumnsTable) {
                    sx_renderTable();
                    sx_generatePagination();
                    isTwoColumnsTable = true;
                }
                    // toggle_TableView.style.display = 'none';
            } else {
                if (isTwoColumnsTable) {
                    sx_renderTable();
                    sx_generatePagination();
                    isTwoColumnsTable = false;
                }
                    // toggle_TableView.style.display = 'block';
            }
        }, 200);
    });
*/
    // Manually toggle table view button
    if (toggle_TableView) {
        toggle_TableView.addEventListener('click', function () {
            if (!isTwoColumnsTable) {
                sx_renderTwoColumnRows();
                sx_generatePagination();
                isTwoColumnsTable = true;
                this.querySelector('.svg_First').style.display = 'none';
                this.querySelector('.svg_Second').style.display = 'block';
            } else {
                sx_renderHeaders();
                sx_renderRows();
                sx_generatePagination();
                isTwoColumnsTable = false;
                this.querySelector('.svg_First').style.display = 'block';
                this.querySelector('.svg_Second').style.display = 'none';
            }
        }, false);
    }


    /**
     * ===============================================
     * 6. Convert and download original/filtered data in various formats
     * ===============================================
     */


    // To CSV
    document.getElementById("jq_FilteredToCSV_" + jq_intRundom).addEventListener("click", async function (e) {
        e.preventDefault();

        if (!sx_filtered_data || !Array.isArray(sx_filtered_data) || sx_filtered_data.length === 0) {
            alert("No filtered data available for download. Please download the entire Source File instead.");
            return;
        }

        const fieldToRemove = "ps_Row";

        // Remove added column for numeric sorting
        sx_filtered_data.forEach(row => {
            delete row[fieldToRemove];
        });

        // Extract headers
        const headers = [...new Set(sx_filtered_data.flatMap(row => Object.keys(row)))];

        // 1. Create a ReadableStream that feeds CSV rows
        const csvStream = new ReadableStream({
            start(controller) {
                // Write header
                controller.enqueue(headers.join(",") + "\n");
            },
            pull(controller) {
                if (!this.index) this.index = 0;

                if (this.index >= sx_filtered_data.length) {
                    controller.close();
                    return;
                }

                const row = sx_filtered_data[this.index++];
                const values = headers.map(h => JSON.stringify(row[h] ?? "").replace(/\\"/g, '""'));
                const line = values.join(",") + "\n";

                controller.enqueue(line);
            },
            cancel() { }
        });

        // 2. Encode text chunks
        const encoder = new TextEncoderStream();

        // 3. Pipe
        const response = new Response(csvStream.pipeThrough(encoder));
        const blob = await response.blob();

        // 4. Download the file
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `filtered_${LoadedFileName}_${new Date().toISOString().slice(0, 10)}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    });

    // To JSON
    document.getElementById("jq_FilteredToJSON_" + jq_intRundom).addEventListener("click", function (e) {
        e.preventDefault();

        if (!sx_filtered_data || !Array.isArray(sx_filtered_data) || sx_filtered_data.length === 0) {
            alert("No filtered data available for download. Please download the entire Source File instead.");
            return;
        }

        const fieldToRemove = "ps_Row";

        // Remove field from all rows
        sx_filtered_data = sx_filtered_data.map(row => {
            const clone = { ...row };
            delete clone[fieldToRemove];
            return clone;
        });

        sx_filtered_data.forEach(row => {
            delete row[fieldToRemove];
        });

        const blob = new Blob(
            [JSON.stringify(sx_filtered_data, null, 2)], // pretty printed JSON
            {
                type: "application/json"
            }
        );

        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `filtered_${LoadedFileName}_${new Date().toISOString().slice(0, 10)}.json`;
        a.click();
        URL.revokeObjectURL(url);
    });

    // To XML
    document.getElementById("jq_FilteredToXML_" + jq_intRundom).addEventListener("click", function (e) {
        e.preventDefault();

        if (!sx_filtered_data || !Array.isArray(sx_filtered_data) || sx_filtered_data.length === 0) {
            alert("No filtered data available for download. Please download the entire Source File instead.");
            return;
        }

        const fieldToRemove = "ps_Row";

        // Remove field from all rows
        sx_filtered_data.forEach(row => {
            delete row[fieldToRemove];
        });

        function escapeXML(str) {
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&apos;");
        }

        let xml = `<items>\n`;

        for (const row of sx_filtered_data) {
            xml += `  <item>\n`;
            for (const key in row) {
                const safeKey = key.replace(/[^A-Za-z0-9_\-:.]/g, "_");
                const safeValue = escapeXML(row[key] ?? "");
                xml += `    <${safeKey}>${safeValue}</${safeKey}>\n`;
            }
            xml += `  </item>\n`;
        }

        xml += `</items>`;

        const blob = new Blob([xml], {
            type: "application/xml"
        });

        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `filtered_${LoadedFileName}_${new Date().toISOString().slice(0, 10)}.xml`;
        a.click();
        URL.revokeObjectURL(url);
    });
};
