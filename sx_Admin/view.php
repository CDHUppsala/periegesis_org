<?php
include "functionsLanguage.php";
include "login/lockPage.php";
include "functionsTableName.php";
include "functionsDBConn.php";
include "configFunctions.php";
include "functionsImages.php";

//Gets request variables
$strIDName = @$_GET["strIDName"];
$strIDValue = @$_GET["strIDValue"];
$strReturn = @$_GET["return"];

$strRedirect = "list.php";
$strRequest = null;

if (empty($strIDName) || !sx_checkTableAndFieldNames($strIDName) || !is_numeric($strIDValue)) {
    header("Location: " . $strRedirect);
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Public Sphere Content Management System - View Records</title>
    <style>
        td img {
            height: 200px !important;
            width: auto !important;
        }
    </style>
</head>
<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body class="body">
    <header id="header">
        <h2><?= lngTable ?>: <?= strtoupper($request_Table) ?> |► <?= lngViewRecord ?></h2>
    </header>
    <h3>
        <a href="<?= $strRedirect ?>"><?= lngBackToRecodList ?></a>
        <a style="margin-left: 100px" title="<?= lngEditRecord ?>" href="edit.php?strIDName=<?= $strIDName ?>&strIDValue=<?= $strIDValue . $strRequest ?>">
            <img class="sx_svg_bg" src="images/sx_svg/sx_pencil.svg"></a>
    </h3>
    <section>
        <table id="table_view" class="no_gradient">
            <?php
            $strSQL = "SELECT * FROM " . $request_Table . " WHERE " . $strIDName . " = ? ";
            $stmt = $conn->prepare($strSQL);
            $stmt->execute([$strIDValue]);
            $rs = $stmt->fetch(PDO::FETCH_ASSOC);
            $c = 0;
            if ($rs) {
                foreach ($rs as $key => $value) {
                    $meta = $stmt->getColumnMeta($c);
                    $sType = $meta["native_type"];
                    $c++;
                    if (sx_getRelationType($key) != 50 && sx_getUpdateableFieldType($key) != 50) {
                        if ($sType == "LONG" || $sType == "LONGLONG" || $sType == "SHORT") { ?>
                            <tr>
                                <th valign="top" nowrap><?= sx_checkAsName($key) ?>:</th>
                                <td><?= sx_getRelatedFieldNameForList($key, $value) ?></td>
                            </tr>
                        <?php
                        } elseif ($sType == "DOUBLE" || $sType == "FLOAT") {
                            if (!is_numeric($value)) {
                                $value = 0;
                            } ?>
                            <tr>
                                <th valign="top" nowrap><?= sx_checkAsName($key) ?>:</th>
                                <td><?= number_format($value, 2) ?></td>
                            </tr>

                        <?php
                        } elseif ($sType == "VAR_STRING" || $sType == "STRING") {
                            if (
                                !empty($value) &&
                                (strpos($value, ".gif") > 0 ||
                                    strpos($value, ".png") > 0 ||
                                    strpos($value, ".jpg") > 0 ||
                                    strpos($value, ".jpeg") > 0 ||
                                    strpos($value, ".svg") > 0)
                            ) {
                                if (strpos($value, ";") == 0) {
                                    $value .= ";";
                                }
                                $arrValue = explode(";", $value);
                                $iCount = count($arrValue);
                                $strViewImg = "";
                                for ($z = 0; $z < $iCount; $z++) {
                                    $xValue = trim($arrValue[$z]);
                                    if (!empty($xValue)) {
                                        $strViewImg .= '<a title="Open the images in a new window" target="_blank" href="view_images.php?imgURL=' .  $xValue . '" onclick="openCenteredWindow(this,\'cleanTextWin\',\'580\',\'400\');return false;">';
                                        $strViewImg .= '<img src="' . sx_getImgFolder($xValue) . '" border="0"></a>';
                                    }
                                }
                            } else {
                                $strViewImg = $value;
                            } ?>
                            <tr>
                                <th valign="top" nowrap><?= sx_checkAsName($key) ?>:</th>
                                <td><?= $strViewImg ?></td>
                            </tr>
                        <?php
                        } else { ?>
                            <tr>
                                <th valign="top" nowrap><?= sx_checkAsName($key) ?>:</th>
                                <td><?= $value ?></td>
                            </tr>
            <?php
                        }
                    }
                }
            }
            $stmt = null;
            $rs = null;
            ?>
        </table>
    </section>
</body>

</html>