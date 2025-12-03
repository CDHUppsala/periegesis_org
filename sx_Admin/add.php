<?php
include __DIR__ . "/functionsLanguage.php";
include __DIR__ . "/login/lockPage.php";
include __DIR__ . "/functionsTableName.php";
include __DIR__ . "/functionsDBConn.php";
include __DIR__ . "/configFunctions.php";

if (in_array($request_Table, $arr_NotAddableTables)) {
    header("Location: main.php?msg=You+cannot+add+records+in+the+table+" . $request_Table);
    exit();
}
?>
<?php include __DIR__ . "/functionsAddEdit.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Public Sphere CMS - Add Records</title>
    <link rel="stylesheet" href="../sxCss/root_Colors.css?v=2023">
    <link rel="stylesheet" href="../sxCss/root_Gradients.css?v=2023">
    <link rel="stylesheet" href="../sxCss/root_Variables.css?v=2023">
    <link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js?v=2023"></script>
    <script src="js/jq/jquery.min.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqAjaxLoadArchives.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqLoadFormInputs.js?v=2023"></script>

    <?php if (!empty($strFormValidation)) { ?>
        <script type="text/javascript">
            // Basic form-controll: checks that required fields are not empty
            function requiredFields(form) {
                <?= "if (" . $strFormValidation . ")" ?> {
                    alert("<?= lngFillFieldsWithAsterisk ?>");
                    return false;
                }
                return true;
            }
        </script>
    <?php
    } ?>

</head>

<body class="body">
    <header id="header">
        <h2><?= lngTable . ": " . strtoupper($request_Table) ?><br><?= lngAddRecord ?></h2>
        <?php include __DIR__ . "/add_menu.php"; ?>
    </header>
    <h3><a href="list.php?RequestTable=<?= $request_Table ?>"><?= lngBackToRecodList ?></a></h3>
    <section>
        <form method="post" id="sxAddEdit" name="sxAddEdit" action="addHTML.php" <?php if (!empty($strFormValidation)) { ?>onsubmit="return requiredFields(this)" <?php } ?>>
            <table class="edit_table">
                <?php
                if ($request_Table == "books") {
                    /**
                     * Set True/False to also include Author Notes
                     */
                    sx_getBookAuthorsInput(true, true, "");
                }
                $iLoop = count(ARR_FieldNames);
                for ($i = 0; $i < $iLoop; $i++) {

                    $xName = ARR_FieldNames[$i][0];
                    $xType = ARR_FieldNames[$i][1];
                    $strHelp = ARR_FieldNames[$i][2];
                    //Add 0809 To exclude unused fields
                    if (sx_getUpdateableFieldType($xName) != 50) { ?>
                        <tr>
                            <th><?= sx_checkAsName($xName) . ": " . sx_getAsterix($xName) ?></th>

                            <?php
                            $radioMemoExists = false;
                            if ($i == 0) {
                                if ($boolIsAuto) { ?>
                                    <td>
                                        <input class="button floatRight" type="submit" name="DefaultMode" value="<?= lngEdit ?>">
                                        <input class="button floatRight" type="submit" name="AddPureText" value="<?= lngAdd ?>">
                                        <p><?= lngAutoNumber ?> <?= sx_getHelpForJava($xName, $strHelp) ?></p>
                                    </td>
                                <?php
                                } else {
                                    Header("Location: main.php?strMsg=noPK");
                                    exit;
                                }
                            } else {
                                if ($xType == "BLOB") {
                                    $radioMemoExists = true; ?>
                                    <td><textarea spellcheck id="<?= $xName ?>" name="<?= $xName ?>"></textarea>
                                        <div><?= sx_getHelpForJava($xName, $strHelp) ?></div>
                                    </td>
                                <?php
                                } elseif ($xType == "TINY") {
                                    if (isset($_SESSION["5_" . $xName])) {
                                        $strCheckYes = "";
                                        $strCheckNo = "checked";
                                    } else {
                                        $strCheckYes = "checked";
                                        $strCheckNo = "";
                                    } ?>
                                    <td><input type="radio" value="Yes" name="<?= $xName ?>" <?= $strCheckYes ?>><?= lngYes ?>
                                        <input type="radio" value="No" name="<?= $xName ?>" <?= $strCheckNo ?>><?= lngNo . " " . sx_getHelpForJava($xName, $strHelp) ?>
                                    </td>
                                <?php
                                } elseif ($xType == "DATE") { ?>
                                    <td><input type="date" name="<?= $xName ?>" autocomplete="off" malue=""><?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "DATETIME") { ?>
                                    <td><input type="datetime-local" step="60" name="<?= $xName ?>" autocomplete="off" malue=""><?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "TIME") { ?>
                                    <td><input type="time" name="<?= $xName ?>" autocomplete="off" value=""><?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "DOUBLE" || $xType == "FLOAT") { ?>
                                    <td><input type="number" step="any" name="<?= $xName ?>" value=""> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "SHORT") { ?>
                                    <td><input type="number" min="0" max="99999" step="1" maxlength="4" name="<?= $xName ?>" value=""> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "LONG" && $xName == "LoginAdminID") {
                                    $iTemp = 0;
                                    if (intval($intLoginAdminID) > 0) {
                                        $iTemp = $intLoginAdminID;
                                    } ?>
                                    <td><input type="number" title="You cannot edit this field!" name="<?= $xName ?>" value="<?= $iTemp ?>" readonly="readonly"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif (array_key_exists($xName, $arrFieldRelations)) {
                                    $strRFVAdd = "";
                                    $strRFV = ""; //Might by any type
                                ?>
                                    <td><?php sx_getRelationInputs($xName, $strRFVAdd, $strRFV) ?> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } else { ?>
                                    <td><input type="text" name="<?= $xName ?>"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
                            <?php
                                }
                            } ?>
                        </tr>
                <?php
                    }
                } ?>
            </table>
            <?php
            $strAsterixMsg = "";
            if (!empty($arrRequiredFields)) {
                $strAsterixMsg = "* " . lngAsteriskFieldsRequired;
            } ?>
            <p>
                <?= $strAsterixMsg ?>
                <input class="button" type="submit" name="AddPureText" value="<?= lngAdd ?>">
                <?php
                if ($radioMemoExists) { ?>
                    <input class="button" type="submit" name="DefaultMode" value="<?= lngEdit ?>">
                <?php
                } ?>
            </p>
        </form>
    </section>
    <div id="jqLoadArchivesWrapper">
        <div title="Toggle Show/Hide" id="jqLoadArchivesToggle" class="aside_hide"></div>
        <div title="Toggle Width between Default and 50%" id="jq_width" class="aside_show"></div>
        <div id="jqLoadArchivesLayer"></div>
    </div>
    <div id="absoluteHelp"></div>
    <?php include __DIR__ . "/errorMsgForClient.php"; ?>
</body>

</html>