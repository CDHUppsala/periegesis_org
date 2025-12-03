<?php
include __DIR__ . "/functionsLanguage.php";
include __DIR__ . "/login/lockPage.php";
//include __DIR__ . "/functionsTableName.php";
include __DIR__ . "/functionsDBConn.php";


$strExport = null;
if (isset($_GET["export"])) {
    $strExport = $_GET["export"];
}
if ($strExport == "html") {
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=main_" . sx_DefaultAdminLang . ".php");
    header("Content-Type: text/xtml; ");
}

//#### Get variables from the configuration of table groups, if they are avaliable

function sx_getGroupHelp($groupName)
{
    $conn = dbconn();
    $strSQL = "SELECT GroupHelp 
		FROM sx_help_by_group 
		WHERE GroupName  = ? AND LanguageCode = ?";
    $stmt = $conn->prepare($strSQL);
    $stmt->execute([$groupName, sx_DefaultAdminLang]);
    $frs = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($frs) {
        return  $frs["GroupHelp"];
    } else {
        return  "No help available";
    }
}

function sx_getTableHelpOrComments($strName)
{
    $tempRetutn = sx_getTableComments($strName);
    if (empty($tempRetutn) || $tempRetutn == "No help available") {
        $conn = dbconn();
        $strSQL = "SELECT TableHelp 
		FROM sx_help_by_table 
		WHERE TableName  = ? AND LanguageCode = ?";
        $fstmt = $conn->prepare($strSQL);
        $fstmt->execute([$strName, sx_DefaultAdminLang]);
        $frs = $fstmt->fetchColumn();
        if (!empty($frs)) {
            $tempRetutn = $frs;
        }
        $fstmt = null;
        $frs = null;
    }
    if (empty($tempRetutn)) {
        return  "No help available";
    } else {
        return $tempRetutn;
    }
}

//#### Get variables from the configuration of table groups, if they are avaliable
//#### Get variables from the configuration of table groups, if they are avaliable
$radioConfigGroupsExist = false;
$strSQL = "SELECT AliasNameOfTables, OrderedTableGroupNames, TablesByGroupName 
	FROM sx_config_groups 
	WHERE ProjectName = ? AND LanguageCode = ?";
$stmt = $conn->prepare($strSQL);
$stmt->execute([$strSourceProjectName, sx_DefaultAdminLang]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);

if ($rs) {
    $radioConfigGroupsExist = true;
    $getAlias = $rs["AliasNameOfTables"];
    $getGroups = $rs["OrderedTableGroupNames"];
    $getTables = $rs["TablesByGroupName"];
}
$stmt = null;
$rs = null;

$arrAlias = "";
$arrGroups = "";
$arrTables = "";

if ($radioConfigGroupsExist) {
    $arrAlias = json_decode($getAlias, true);
    $arrGroups = json_decode($getGroups, true);
    $arrTables = json_decode($getTables, true);
} ?>
<!DOCTYPE html>
<html lang="<?= sx_DefaultAdminLang ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Studiox X Content Management System - Main Page Help Information</title>
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="js/jq/jquery.min.js"></script>
    <script>
        var $sx_help = jQuery.noConflict();
        $sx_help(window).on("load", function() {
            $sx_help("#nav a").click(function() {
                var $this = $sx_help(this);
                var $thisID = $this.attr("data-id");
                if ($this.attr("class") != "selected") {
                    $this.addClass("selected")
                        .siblings().removeClass("selected");
                    $sx_help("#layer" + $thisID).show(500)
                        .siblings(".js_help_main").hide(500);
                }
            });
            if (location.search != "") {
                var tabNumber = (location.search).replace(/\?intTab=/, "");
                tabNumber = (isNaN(tabNumber)) ? 0 : tabNumber
                $sx_help("#nav a[data-id=" + tabNumber + "]").click();
            }

            var headers = $sx_help('.text').find('h3');
            headers.each(function(){
                $sx_help(this).addClass('after jq_toggleNext')
                .nextUntil('h3')
                .wrapAll('<div class="overflowHidden paddingLeft display_none"></div>');
            })

            $sx_help(".jq_toggleNext").on('click', function() {
                $sx_help(this).toggleClass("selected").next().slideToggle('fast');
            });
           
        });
    </script>
</head>

<body class="body">

    <header id="header">
        <h2>Public Sphere CMS<br>Help Information for Table Groups</h2>
    </header>
    <?php
    if (is_array($arrGroups)) { ?>
        <nav id="nav">
            <h4>Help by Group</h4>
            <?php
            $iCount = count($arrGroups);
            for ($z = 0; $z < $iCount; $z++) {
                $strClass = "";
                if ($z == 0) {
                    $strClass = ' class="selected"';
                } ?>
                <a data-id="<?= $z ?>" <?= $strClass ?> href="javascript:void(0)"><?= $arrGroups[$z] ?></a>
            <?php
            } ?>
        </nav>
        <?php
    }
    if (!empty($getGroups)) {

        for ($x = 0; $x < count($arrGroups); $x++) {
            $strGroupName = trim($arrGroups[$x]);
            $radioGroupTablesExists = false;
            $strGroupTitle = "Information for the Group: ";
            if (!empty($arrTables) && array_key_exists($strGroupName, $arrTables)) {
                $radioGroupTablesExists = true;
                $strGroupTitle = "Information for the Table Group: ";
            }
            if ($x == 0) {
                $strDisplay = 'style="display: block"';
            } else {
                $strDisplay = 'style="display: none"';
            } ?>
            <div id="layer<?= $x ?>" class="maxWidth js_help_main" <?= $strDisplay ?>>
                <?php if ($x > 0) { ?>
                    <h1><?= $strGroupTitle . $strGroupName ?></h1>
                <?php
                } ?>
                <div class="text">
                    <?= sx_getGroupHelp($strGroupName) ?>
                </div>
                <?php if ($x > 0) {
                    if (!empty($arrTables) && array_key_exists($strGroupName, $arrTables)) { ?>
                        <h2>Description of Tables in Group: <?= $strGroupName ?></h2>
                <?php
                        $arrLoop = $arrTables[$strGroupName];
                        ksort($arrLoop);
                        foreach ($arrLoop as $strKey => $strName) {
                            $strTableName = $strName["name"];
                            $aliasTable = $strTableName;
                            if (!empty($arrAlias) && array_key_exists($strTableName, $arrAlias)) {
                                $aliasTable = $arrAlias[$strTableName];
                            }
                            echo '<h3 data-id="' . $strTableName . '" class="before jq_toggleNext">' . $aliasTable . '</h3>';
                            echo '<div class="text overflowHidden paddingLeft display_none">';
                            echo sx_getTableHelpOrComments($strTableName);
                            echo '</div>';
                        }
                    }
                } ?>
            </div>
    <?php
        }
    }
    include __DIR__ . "/errorMsgForClient.php";
    ?>

</body>

</html>