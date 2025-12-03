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
include __DIR__ . "/functionsAddEdit.php";

/**
 * Add new records
 */
if (!empty($_POST["strAddHTML"]) || !empty($_POST["AddPureText"])) {
    /**
     * Get form values from function: returns an array of thre set av values
     * array($arrAddFields,$arrAddPrepareValues,$arrAddValues);
     */
    $arrInsertRecords = sx_getInsertUpdateRecords("add");

    $sql = "INSERT INTO " . $request_Table . " (" . $arrInsertRecords[0] . ") VALUES (" . $arrInsertRecords[1] . ")";
    /*
    echo $sql;
    echo "<pre>";
    print_r($arrInsertRecords[2]);
    echo "</pre>";
    exit();
     */
    $stmt = $conn->prepare($sql);
    $stmt->execute($arrInsertRecords[2]);
    $int_BookID = $conn->lastInsertId();
    $stmt = null;

    if ($request_Table == "books" && !empty($_POST["BookToAuthors"])) {
        $int_BookID = intval($int_BookID);
        if (intval($int_BookID) > 0) {
            $strToAthors = $_POST["BookToAuthors"];
            if (strpos($strToAthors, ",") == 0) {
                $strToAthors .= ",";
            }
            $arrA = explode(",", $strToAthors);
            $iCount = count($arrA);
            for ($r = 0; $r < $iCount; $r++) {
                $iTemp = trim($arrA[$r]);
                if (intval($iTemp) > 0) {
                    $iTemp = intval($iTemp);
                    $sql = "INSERT INTO book_to_authors (BookID, AuthorID, AuthorOrdinal) Values(?,?,?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$int_BookID, $iTemp, $r]);
                }
            }
        }
    }

    header("Location: list.php?RequestTable=" . $request_Table);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Public Sphere Content Management System - Add HTML Records</title>
</head>
<link rel="stylesheet" href="../sxCss/root_Colors.css?v=2024">
<link rel="stylesheet" href="../sxCss/root_Gradients.css?v=2024">
<link rel="stylesheet" href="../sxCss/root_Variables.css?v=2024">
<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2024">
<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
    <script src="<?php echo sx_ADMIN_DEV ?>js/jsFunctions.js?v=2023"></script>
    <script src="js/jq/jquery.min.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqFunctions.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqAjaxLoadArchives.js?v=2023"></script>
    <script src="<?php echo sx_ADMIN_DEV ?>js/jqLoadFormInputs.js?v=2023"></script>


<script src="tinymce/tinymce.min.js?v=2024"></script>
<script src="tinymce/config/custom.js?v=2024"></script>
<?php if (!empty($strFormValidation)) { ?>
    <script>
        // Public Sphere - Basic form-controll: checks that required fields are not empty
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
        <h2><?= lngTable . ": " . strtoupper($request_Table) ?> <br><?= lngAddRecord ?></h2>
        <?php include __DIR__ . "/add_menu.php"; ?>
    </header>

    <h3><a href="list.php?RequestTable=<?= $request_Table ?>"><?= lngBackToRecodList ?></a></h3>
    <section>
        <form method="post" id="sxAddEdit" name="sxAddEdit" action="addHTML.php" <?php if (!empty($strFormValidation)) { ?>onsubmit="return requiredFields(this)" <?php } ?>>
            <table class="edit_table">
                <?php
                if ($request_Table == "books") {
                    $strToAthors = @$_POST["BookToAuthors"];
                    /**
                     * Set True/False to also include Author Notes
                     */
                    sx_getBookAuthorsInput(false, true, $strToAthors);
                }

                $iLoop = count(ARR_FieldNames);
                for ($i = 0; $i < $iLoop; $i++) {
                    $xName = ARR_FieldNames[$i][0];
                    $xType = ARR_FieldNames[$i][1];
                    $strHelp = ARR_FieldNames[$i][2];
                    $xValue = "";
                    if (!empty($_POST[$xName])) {
                        $xValue = $_POST[$xName];
                    }

                    /**
                     * Add 0809 To exclude unused fields
                     */
                    if (sx_getUpdateableFieldType($xName) != 50) { ?>
                        <tr>
                            <th><?= sx_checkAsName($xName) . ": " . sx_getAsterix($xName) ?></th>
                            <?php
                            if ($i == 0) {
                                if ($boolIsAuto) { ?>
                                    <td>
                                        <input class="button floatRight" type="submit" value="<?= lngAdd ?>" name="strAddHTML">
                                        <p><?= lngAutoNumber . " " . sx_getHelpForJava($xName, $strHelp) ?></p>
                                    </td>
                                <?php
                                } else {
                                    Header("Location: main.php?strMsg=noPK");
                                    exit;
                                }
                            } else {
                                if ($xType == "BLOB") {
                                    if (!empty($xValue)) { //== To exclude empty strings from the function rowFix
                                        $xValue = sx_formatTextarea($xValue);
                                    } ?>
                                    <td><textarea spellcheck id="<?= $xName ?>" name="<?= $xName ?>"><?= $xValue ?></textarea>
                                        <div><?= sx_getHelpForJava($xName, $strHelp) ?></div>
                                    </td>
                                <?php
                                } elseif ($xType == "TINY") { //Yes/No
                                ?>
                                    <td><input type="radio" value="Yes" <?php if ($xValue == "Yes") { ?>checked<?php } ?> name="<?= $xName ?>"> <?= lngYes ?>
                                        <input type="radio" value="No" <?php if ($xValue == "No") { ?>checked<?php } ?> name="<?= $xName ?>"> <?= lngNo . " " . sx_getHelpForJava($xName, $strHelp) ?>
                                    </td>
                                <?php
                                } elseif ($xType == "DATE") {
                                ?>
                                    <td><input type="date" name="<?= $xName ?>" autocomplete="off" maxlength="10" value="<?= $xValue ?>"><?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "DATETIME") {
                                ?>
                                    <td><input type="datetime-local" step="60" name="<?= $xName ?>" autocomplete="off" value="<?= $xValue ?>"><?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "TIME") { ?>
                                    <td><input type="time" name="<?= $xName ?>" autocomplete="off" maxlength="8" value="<?= $xValue ?>"><?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "DOUBLE" || $xType == "FLOAT") {
                                    if (!is_numeric($xValue)) {
                                        $xValue = 0;
                                    } ?>
                                    <td><input type="number" step="any" name="<?= $xName ?>" value="<?= $xValue ?>"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "SHORT") {
                                    if (!is_numeric($xValue)) {
                                        $xValue = 0;
                                    } elseif (intval($xValue) > 9999) {
                                        $xValue = 9999;
                                    } ?>
                                    <td><input type="number" min="0" max="99999" step="1" maxlength="4" name="<?= $xName ?>" value="<?= $xValue ?>"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif ($xType == "LONG" && $xName == "LoginAdminID") { //Disable and set the ID of the administrator that is loged in
                                    if (intval($xValue) == 0) {
                                        $xValue = 0;
                                    }
                                    if (intval($intLoginAdminID) > 0) {
                                        $xValue = $intLoginAdminID;
                                    } ?>
                                    <td><input type="number" title="You cannot edit this field!" name="<?= $xName ?>" value="<?= $xValue ?>" readonly="readonly"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } elseif (array_key_exists($xName, $arrFieldRelations)) {
                                    if (@$_POST["Add" . $xName . ""] != "") {
                                        $strRFVAdd = trim(@$_POST["Add" . $xName . ""]);
                                        $strRFV = "";
                                    } else {
                                        $strRFVAdd = "";
                                        $strRFV = $xValue;
                                        if (!empty($strRFV)) {
                                            if (is_numeric($strRFV)) {
                                                $strRFV = intval($strRFV);
                                            } else {
                                                $strRFV = strval($strRFV);
                                            }
                                        } else {
                                            $strRFVAdd = "";
                                            $strRFV = "";
                                        }
                                    } ?>
                                    <td><?php sx_getRelationInputs($xName, $strRFVAdd, $strRFV) ?> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
                                <?php
                                } else { 
                                    if(!empty($xValue)) {
                                        $xValue = htmlspecialchars($xValue);
                                    } ?>
                                    <td><input type="text" name="<?= $xName ?>" value="<?= $xValue ?>" size="58"> <?= sx_getHelpForJava($xName, $strHelp) ?></td>
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
            <p><?= $strAsterixMsg ?> <input class="button" type="submit" value="<?= lngAdd ?>" name="strAddHTML"></p>

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