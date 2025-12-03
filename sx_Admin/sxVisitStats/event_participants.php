<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

$aResults = "";

$sql = "SELECT distinct 
    LastName, FirstName, CONCAT(FirstName, ' ', LastName) AS FullName, 
    Email
FROM event_participants 
WHERE Verified = 1
ORDER BY LastName";
$rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
if ($rs) {
    $aResults = $rs;
}
$rs = null;

?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>SX Statistics</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SXCMS List of Records</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js"></script>
    <script src="../js/jq/jquery.min.js"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js?v=4"></script>

</head>

<body id="bodyStats" class="body">
    <header id="header">
        <h2>Unique Event Participants</h2>
    </header>
    <div class="alignRight margin row flex_justify_end">
        <input type="button" value="Copy to Clipboard" class="button jq_CopyToClipboard" data-id="TableList">
        <input type="button" value="Print as PDF" class="button jq_PrintDivElement" data-id="TableList">
        <input type="button" value="Export into Excel" class="button jq_ExportTableIntoExcel" data-id="TableList">
    </div>

    <section>

        <?php
        $r = 1;
        if (is_array(($aResults))) { ?>

            <table id="TableList" class="jqTableList">

            <?php
            foreach ($aResults as $row) {
                if ($r == 1) {
                    echo "<tr>";
                    echo "<th>Count</th>";
                    foreach ($row as $key => $value) {
                        echo "<th>$key</th>";
                    }
                    echo "</tr>";
                }

                echo "<tr>";
                echo "<td>$r</td>";
                foreach ($row as $key => $value) {
                    if (strpos($value, '@') == 0) {
                        echo "<td>" . mb_convert_case($value, MB_CASE_TITLE, 'UTF-8') . "</td>";
                    } else {
                        echo "<td>" . mb_strtolower($value) . "</td>";
                    }
                }
                echo "</tr>";
                $r++;
            }
        }
        $aResults = null;
            ?>
            </table>
    </section>
</body>

</html>