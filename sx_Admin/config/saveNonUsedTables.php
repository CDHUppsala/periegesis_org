<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

$radioConfigGroupsExist = false;
$strSQL = "SELECT TablesByGroupName FROM sx_config_groups 
	WHERE ProjectName = ?
	AND LanguageCode = ? ";
$stmt = $conn->prepare($strSQL);
$stmt->execute([$strSourceProjectName, sx_DefaultAdminLang]);
$getTables = $stmt->fetchColumn();

$arrTables = "";

if (!empty($getTables)) {
    $arrTables = json_decode($getTables, true);
}


$arrNoGrouped = $arrTables['noGrouped'];
$arrNonUsedTables = [];
foreach ($arrNoGrouped as $row) {
    $arrNonUsedTables[] = $row['name'];
}

$jsonNonUsedTables = json_encode($arrNonUsedTables, JSON_UNESCAPED_UNICODE);

const STR_ColumnName = 'NonUsedTables';
$saveMessage = "";

$sql = "SELECT CachedData FROM data_caching WHERE CachingName = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([STR_ColumnName]);
$strCachedData = $stmt->fetchColumn();

if (empty($strCachedData)) {
    $sql = "INSERT INTO data_caching (CachingName, CachedData) VALUES (?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([STR_ColumnName, $jsonNonUsedTables]);
    $saveMessage = "The Non Used Tables have been saved in the Database";
} else {
    $sql = "UPDATE data_caching SET CachedData = ? WHERE CachingName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$jsonNonUsedTables, STR_ColumnName]);
    $saveMessage = "The Non Used Tables have been updated in the Database";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Public Sphere CMS - Config Table Fields</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body class="body">
    <header id="header">
        <h2>Save Non Used Tables in th Database</h2>
    </header>
    <section>

        <?php
        if (!empty($saveMessage)) {
            echo "<h2>$saveMessage</h2>";
        }
        echo "<h3>Non Used Tables</h3>";
        echo '<pre>';
        print_r(json_decode($jsonNonUsedTables, true));
        echo '</pre>';

        ?>
    </section>
</body>

</html>