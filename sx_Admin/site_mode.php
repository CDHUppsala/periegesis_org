<?php

/**
 * During magior updates, you set your site to Update Mode.
 */
include __DIR__ . "/functionsLanguage.php";
include __DIR__ . "/login/lockPage.php";
include __DIR__ . "/functionsTableName.php";
include __DIR__ . "/functionsDBConn.php";

const STR_ColumnName = 'SiteMode';
$saveMessage = "";
$strUpdateMode = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["UpdateMode"])) {
        $strUpdateMode = $_POST["UpdateMode"];


        $sql = "SELECT CachedData FROM data_caching WHERE CachingName = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([STR_ColumnName]);
        $strCachedData = $stmt->fetchColumn();

        if (empty($strCachedData)) {
            $sql = "INSERT INTO data_caching (CachingName, CachedData) VALUES (?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([STR_ColumnName, $strUpdateMode]);
            $saveMessage = "<p>The Site has been set in $strUpdateMode Mode</p>";
        } else {
            $sql = "UPDATE data_caching SET CachedData = ? WHERE CachingName = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$strUpdateMode, STR_ColumnName]);
            $saveMessage = "<p>The Site has been changes to $strUpdateMode Mode</p>";
        }
    } else {
        $saveMessage = "<p>You must choose a Site Mode</p>";
    }
}

$sql = "SELECT CachedData FROM data_caching WHERE CachingName = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([STR_ColumnName]);
$strCachedData = $stmt->fetchColumn();

$strUpdateChecked = '';
$strRunningChecked = '';
if (!empty($strCachedData)) {
    if ($strCachedData === 'Update') {
        $strUpdateChecked = 'checked ';
    } elseif ($strCachedData === 'Running') {
        $strRunningChecked = 'checked ';
    }
    $saveMessage = "<p>Currently, the Site is set in $strCachedData Mode</p>";
} else {
    $saveMessage .= '<p>The Site Mode is not set yet.</p>';
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
        <h2>Change the Display Mode of the Site - Between Update and Running</h2>
    </header>
    <section>
        <div class="maxWidth">
            <h1>Change the display mode of the Site Between Running and Update</h1>
            <p>When you pursue major updates in the database, or, mainly, when you truncate (clean up) a table to import new data,
                set the site in Update Mode to avoid the appearance of errors in the site.</p>
            <p>Visitors will then be redirected to a page informing them that the site is in Udate Mode
                and ask them to return later.</p>

            <div class="msgSuccess"><?php echo $saveMessage ?></div>
            <form method="POST" name="ChangeUpdateMode" action="site_mode.php">
                <fieldset>
                    <label><input type="radio" name="UpdateMode" value="Running" <?php echo $strRunningChecked ?> /> Running Mode</label>
                    <label><input type="radio" name="UpdateMode" value="Update" <?php echo $strUpdateChecked ?> /> Update Mode</label>
                </fieldset>
                <fieldset>
                    <label><input type="submit" name="SubmitMode" value="Change Mode" />
                </fieldset>
            </form>
        </div>
    </section>
    <section>
        <div class="maxWidth">
            <h1>How it works</h1>
            <p>This form adds in the database table <b>data_caching</b> the key name <b>SiteMode</b> and its value: <b>Running</b> or <b>Update</b>.</p>
            <p>The table <b>data_caching</b> is checked from the top of every page in the site.
                If the value is <b>Update</b>, the visitor is redirected to an information page <b>update.html</b>,</p>
        </div>
    </section>
</body>

</html>