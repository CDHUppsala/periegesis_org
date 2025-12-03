<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

$radiActive = true;
if (isset($_GET['active'])) {
    $radiActive = false;
}
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
        <?php
        $strLink = '<a href="active_newsletters.php?active=no">Get Inactve Newsletters</a>';
        if ($radiActive) {
            echo '<h2>Active Newsletters</h2>';
        } else {
            echo '<h2>Inactive Newsletters</h2>';
            $strLink = '<a href="active_newsletters.php">Get Actve Newsletters</a>';
        } ?>
    </header>
    <div class="alignRight margin row flex_justify_end">
        <span style="display: inline-block; margin-right: auto;"><?php echo $strLink ?></span>
        <input type="button" value="Copy to Clipboard" class="button jq_CopyToClipboard" data-id="TableList">
        <input type="button" value="Print as PDF" class="button jq_PrintDivElement" data-id="TableList">
        <input type="button" value="Export into Excel" class="button jq_ExportTableIntoExcel" data-id="TableList">
    </div>

    <section>

        <?php

        $aResults = "";
        $intActive = 0;
        if ($radiActive) {
            $intActive = 1;
        }
        $sql = "SELECT l.LetterID, l.LanguageID, l.GroupID, g.GroupName, l.Active, l.Email, l.FullName, l.Notes, l.BlacklistedIP
        FROM newsletters AS l
        LEFT JOIN newsletter_groups AS g ON g.GroupID = l.GroupID
        WHERE l.Active = $intActive
        ORDER BY l.GroupID DESC, l.Email ASC";
        $rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if ($rs) {
            $aResults = $rs;
        }
        $rs = null;


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
                    if (!empty($value)) {
                        if (strpos($value, '@') == 0) {
                            echo "<td>" . mb_convert_case($value, MB_CASE_TITLE, 'UTF-8') . "</td>";
                        } else {
                            echo "<td>" . mb_strtolower($value) . "</td>";
                        }
                    } else {
                        echo "<td></td>";
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