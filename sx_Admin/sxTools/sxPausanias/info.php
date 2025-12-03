
    <?php
    $peakMemoryUsageBytes = memory_get_peak_usage();
    $peakMemoryUsageMB = $peakMemoryUsageBytes / 1048576;

    echo "<p><b>Peak Memory Usage</b>: " . $peakMemoryUsageBytes . " Bytes or " . round($peakMemoryUsageMB, 2) . " MB</p>";
    ?>

    <h2>Information</h2>
    <div class="maxWidth">
        <h3 class="before jq_toggleNext">The usages of this Application</h3>
        <ul class="display_none">
            <li>This application is to be used for <b>single</b> database tables.
                <ul>
                    <li>If you want to be able to <b>truncate</b> multiple Database Tables and entirely restore them by <b>Saved Backups</b>, please use the application <b>Backup Small/Big Database</b>.</li>
                </ul>
            </li>
            <li>You can Import a CSV, XML or JSON File from your Local computer (Local Files) or from a Default Folder in the private part of the Remote Server and use it for the following purposes:
                <ul>
                    <li>To <b>update</b> all or parts of existing table records and all or parts of their columns.</li>
                    <li>To <b>insert</b> new records in a table.</li>
                    <li>To <b>truncate</b> (delete the data of) a table to <b>Restor</b> a previous version that you have saved using <b>Export from MySQL</b>.</li>
                    <li>To <b>truncate</b> a table to insert <b>New Data</b>.</li>
                </ul>
            </li>
            <li>Since the Import file is converted to a <b>PHP Associative Array</b>, which consumes server memory, the <b>size</b> of the Import File must not exceed about <?php echo number_format((int)(ini_get('memory_limit'))/10,1); ?>M. Otherwise, you must splitt it into two or more files.</li>
                <li>Before importing the file, the program checks:
                    <ul>
                    <li>The uniqueness of all <b>Primary Keys</b>.</li>
                    <li>The identity of the file's <b>Field Names</b> to the table's <b>Column Names</b>.</li>
                    <li>The compatibility of field values of all file rows with the <b>Data Type</b> of the corresponding table columns.
                    </li>
                </ul>
            </li>
        </ul>
        <h3 class="before jq_toggleNext">For Import of Type: Truncate and Insert</h3>
        <ul class="display_none">
            <li><b>Obs!</b> If you truncate a table, your site will generate errors during the insert process. To avoid that, set your site in <b>Update Mode</b> before you start the process:
                <ul>
                    <li>To do that, open the Group <b>Initial Settings</b> and click on <b>Change Site Mode</b>.</li>
                </ul>
            </li>
            <li>It is also recommended to <b>Back Up</b> the table you are going to truncate from <b>Backup Small Database</b>.</li>
        </ul>

        <h3 class="before jq_toggleNext">For Import of Type: Insert</h3>
        <ul class="display_none">
            <li>All <b>Primary Keys</b> (PK) in all file rows must have a unique value, usually but not necessarily an auto-incremented integer.</li>
            <li>Only PK values that <b>does Not exist</b> in the Database Table will be inserted. Existing PK value in the database table are not affected.</li>
            <li>If you wish to update old records and add new ones, Update first and Insert thereafter.</li>
        </ul>
        <h3 class="before jq_toggleNext">For Import of Type: Update</h3>
        <ul class="display_none">
            <li>Only the records in the Import File that have the <b>same</b> Primary Key (PK) value with that of the Database Table will be updated. New records, with a PK value that not exist in the Database Table, will not be added.</li>
            <li>If you wish to update old records and add new ones, Update first and Insert thereafter.</li>
        </ul>
    </div>
