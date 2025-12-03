<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";
include "functions.php";

$strDBTable = "";
if (!empty($_POST["DBTable"])) {
    $strDBTable = $_POST["DBTable"];
}
// From selected DB Table, as hidden input
if (isset($_POST["HiddenDBTable"])) {
    $strDBTable = $_POST["HiddenDBTable"];
} ?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Public Sphere - Creation XML Files from Database</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="../../js/jq/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#select").click(function() {
                $(this).closest("form")
                    .attr("action", "index.php")
                    .attr("target", "");
            });

            $("#export__NotUsed").click(function(event) {
                event.preventDefault(); // Prevent the default form submit behavior

                var formData = $(this).closest("form").serialize();

                // AJAX request cannot handle downloading a file using header(...)
                $.ajax({
                    url: 'export.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        alert('Your export request was successful!');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('There was an error processing your request: ' + textStatus);
                    }
                });
            });

            $("#export").click(function(event) {
                event.preventDefault();
                var checkedValue = $("input[name='ExportType']:checked").val();

                var i_frame = $('<iframe name="download-iframe" style="display: none;"></iframe>');
                $('body').append(i_frame);

                var form = $(this).closest("form");
                form.attr("action", "export.php");
                form.attr("target", "download-iframe");
                form.submit();

                form.attr("action", "");
                form.attr("target", "");

                i_frame.on('load', function() {
                    $(this).remove();
                });
                $('#SaveMessage').text('The '+ checkedValue.toUpperCase() +' File has been saved on the Remote Server.').css('display','block');
            });



            $(".jq_toggleNext").on('click', function() {
                $(this).toggleClass("selected").next().slideToggle('fast');
            });

        });
    </script>
</head>

<?php
// Show only Used Table
$arrNonUsedTables = "";
$strDataMarkNotes = return_NonUsedTables();
if (!empty($strDataMarkNotes)) {
    $arrNonUsedTables = json_decode($strDataMarkNotes, true);
}
?>

<body class="body">
    <header id="header">
        <h2>Public Sphere: - Export Database Tables</h2>
    </header>
    <section class="maxWidth">
        <h1>Export Database Tables to XML, JSON and CSV Files</h1>
        <form method="POST" name="chooseTable" action="index.php">
            <fieldset class="row" style="justify-content: flex-end; align-items: center;">
                <label>Select Table from Database:</label>
                <select size="1" name="DBTable">
                    <option value="">Select Table</option>
                    <?php

                    $result = $conn->query("SHOW TABLES");
                    $rs = $result->fetchAll(PDO::FETCH_NUM);
                    foreach ($rs as $table) {
                        $loopTable = (string) $table[0];
                        if (empty($arrNonUsedTables) || !in_array($loopTable, $arrNonUsedTables)) {
                            $strSelected = "";
                            if (!empty($strDBTable) && $loopTable == $strDBTable) {
                                $strSelected = "selected ";
                            } ?>
                            <option <?= $strSelected ?>value="<?= $loopTable ?>"><?= $loopTable ?></option>
                    <?php
                        }
                    }
                    $rs = null;
                    ?>
                </select>
                <input type="submit" value="Select Table" name="SelectTable">
            </fieldset>
        </form>
        <?php
        if (!empty($strDBTable)) {
            include "select_fields.php";
        } ?>
    </section>
    <?php
    include __DIR__ . '/info.html';
    ?>

</body>

</html>