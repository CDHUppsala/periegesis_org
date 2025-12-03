<?php

/**
 * Get all Configuration Variables from the tables:
 *  - sx_config_tables
 *  - sx_help_by_table
 * ==============================================================
 */
$str_OrderByField = "";
$js_SelectedFields = "";
$js_RequiredFields = "";
$js_UpdateableFields = "";
$js_FieldRelations = "";
$js_AddUppdateRelated = "";

$js_AliasNames = "";
$js_HelpByField = "";

if (!empty($request_Table)) {
    $strSQL = "SELECT OrderByField, SelectedFields, RequiredFields,
        UpdateableFields, RelatedFields, AddUppdateRelated
        FROM sx_config_tables 
        WHERE ConfigTableName = ?";
    $stmt = $conn->prepare($strSQL);
    $stmt->execute([$request_Table]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $str_OrderByField = $rs["OrderByField"];
        $js_SelectedFields = $rs["SelectedFields"];
        $js_RequiredFields = $rs["RequiredFields"];
        $js_UpdateableFields = $rs["UpdateableFields"];
        $js_FieldRelations = $rs["RelatedFields"];
        $js_AddUppdateRelated = $rs["AddUppdateRelated"];
    }
    $rs = null;
    $stmt = null;

    $strSQL = "SELECT AliasNameOfFields, HelpByField 
        FROM sx_help_by_table 
        WHERE TableName = ?
        AND LanguageCode = ?";
    $stmt = $conn->prepare($strSQL);
    $stmt->execute([$request_Table, sx_DefaultAdminLang]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $js_AliasNames = $rs["AliasNameOfFields"];
        $js_HelpByField = $rs["HelpByField"];
    }
    $rs = null;
    $stmt = null;
}

/**
 * Decode json to array
 */
$arrAliasNames = array();
if ($js_AliasNames != "") {
    $arrAliasNames = json_decode($js_AliasNames, true);
}
$arrSelectedFields = array();
if ($js_SelectedFields != "") {
    $arrSelectedFields = json_decode($js_SelectedFields, true);
}
$arrRequiredFields = array();
if (!empty($js_RequiredFields)) {
    $arrRequiredFields = json_decode($js_RequiredFields, true);
}

$arrUpdateableFields = array();
if (!empty($js_UpdateableFields)) {
    $arrUpdateableFields = json_decode($js_UpdateableFields, true);
}
$arrFieldRelations = array();
if (!empty($js_FieldRelations)) {
    $arrFieldRelations = json_decode($js_FieldRelations, true);
}

$arrAddUppdateRelated = array();
if (!empty($js_AddUppdateRelated)) {
    $arrAddUppdateRelated = json_decode($js_AddUppdateRelated, true);
}

$arrHelpByField = array();
if (!empty($js_HelpByField)) {
    $arrHelpByField = json_decode($js_HelpByField, true);
}


if (!empty($arrFieldRelations)) {
    foreach ($arrFieldRelations as $key => $value) {
        $iRel = $value[0];
        //$sParent = $value[1];
        $sql = str_replace('""', "'", $value[2]);
        if ($iRel == 1) {
            /**
             * Replaces ID with one or two Names
             */
            $_SESSION["1_" . $key] = null;
            if (strpos($sql, " FROM " . $request_Table . " ") > 0) {
                $sql .= " " . $strLimitRecords_100;
            }
            //echo "T1: <b>" . $key . ":</b> " . $sql . "<br>";
            $rsRel = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
            if ($rsRel) {
                $_SESSION["1_" . $key] = $rsRel;
            }
            $rsRel = null;
        } elseif ($iRel == 2) {
            /**
             * Replace ID with Name, Add new record to related table
             */
            $_SESSION["2_" . $key] = null;
            //echo "T2: <b>" . $key . ":</b> " . $sql . "<br>";
            $rsRel = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
            if ($rsRel) {
                $_SESSION["2_" . $key] = $rsRel;
            }
            $rsRel = null;
            $_SESSION["2_SQL" . $key] = $sql;
        } elseif ($iRel == 3) {
            /**
             *  Gets distict values from same or related table to be added in current table record
             */
            $_SESSION["3_" . $key] = null;
            //echo "T3: <b>" . $key . ":</b> " . $sql . "<br>";
            $rsRel = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
            if ($rsRel) {
                $_SESSION["3_" . $key] = $rsRel;
            }
            $rsRel = null;
        } elseif ($iRel == 30) {
            /**
             * Get distinct values from a Selected Parent ID or Parent Fields - from the same or related table
             * to be added in current table record
             * Define Session_30_key with the array of relations it includes
             */
            $_SESSION["30_" . $key] = $value;
            /*
            echo "T30: <b>" . $key . ":</b><br>";
            print_r($value);
            echo "<br>";
            */
        } elseif ($iRel == 300) {
            /**
             * Get distinct values from a Selected Parent ID or Parent Fields - from the same or related table
             * to be added in current table record
             * Define Session_30_key with the array of relations it includes
             */
            $_SESSION["300_" . $key] = $value;
            /*
            echo "T300: <b>" . $key . ":</b><br>";
            print_r($value);
            echo "<br>";
            */
        } elseif ($iRel == 10 || $iRel == 100) {
            //  Gets subclasses of a class 
            //100 = 2 queries, one for exclusive subclasses and one (index=3) to replace IDs with Names 
            $_SESSION["10_" . $key] = null;
            //echo "T10: <b>" . $key . ":</b> " . $sql . "<br>";
            $rsRel = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
            if ($rsRel) {
                $_SESSION["10_" . $key] = $rsRel;
            }
            //print_r($rsRel);
            $rsRel = null;

            $_SESSION["100_" . $key] = null;
            if ($iRel == 100 && isset($value[3])) {
                $_SESSION["100_" . $key] = trim($value[3]);
                //echo trim($value[3]);
            }
        } elseif ($iRel == 4) {
            // Radial values 
            $_SESSION["4_" . $key] = $sql;
            //echo "T4: <b>" . $key . ":</b> " . $sql . "<br>";
        } elseif ($iRel == 40) {
            // Box values for multiple choices 
            $_SESSION["40_" . $key] = $sql;
            //echo "T40: <b>" . $key . ":</b> " . $sql . "<br>";
        }
    }
}

if (!empty($arrUpdateableFields)) {
    foreach ($arrUpdateableFields as $key => $value) {
        $iRel = $value;
        // Sets boolean value to False 
        if ($iRel == 5) {
            $newKey = str_replace("_Default", "", $key);
            $_SESSION["5_" . $newKey] = true;
            //echo $key . "=" . $value . "<br>";
            //echo "T5: <b>" . $newKey . ":</b> " . boolval($_SESSION["5_" . $newKey]) . "<hr>";
        }
    }
}

/**
 * Create a Default SELECT-statement with all selected Fields
 * The statement can b change in list.php for different purposes
 */
if (!empty($arrSelectedFields)) {
    $strSelectedFields = implode(", ", $arrSelectedFields);
} else {
    $strSelectedFields = "*";
}

/*
echo "<pre>";
print_r($arrFieldRelations);
echo "</pre>";
exit;
*/

define("str_OrderByField", $str_OrderByField);
define("arr_AliasName", $arrAliasNames);
define("arr_SelectedFields", $arrSelectedFields);
define("arr_RequiredFields", $arrRequiredFields);
define("arr_UpdateableFields", $arrUpdateableFields);
define("arr_FieldRelations", $arrFieldRelations);
define("arr_AddUppdateRelated", $arrAddUppdateRelated);
define("arr_HelpByField", $arrHelpByField);

/**
 * ==============================================================
 * 1 FUNCTIONS FOR THE ADD/EDIT OF RECORDS
 * ==============================================================
 */

/**
 * To Mark required fields with an * in the form
 */
function sx_getAsterix($currentFieldName)
{
    if (!empty(arr_RequiredFields)) {
        if (in_array($currentFieldName, arr_RequiredFields)) {
            return '<span class="asterixColor">*</span>';
        } else {
            return "";
        }
    } else {
        return "";
    }
}

/**
 * Creates help information for every field
 * @param mixed $currentFieldName
 * @param mixed $help
 * @return string
 */
function sx_setHelpForJava($currentFieldName, $help)
{
    if (!empty($help)) {
        $help = trim($help);
        return '<input class="help_button jqHelpButton" title="Click to Show/Hide Help" type="button" value=" ? " data-id="help_' . $currentFieldName . '">
        	<div class="help_by_field" style="display: none" id="help_' . $currentFieldName . '">' . $help . '</div>';
    } else {
        return "";
    }
}

/**
 * Get help information for every Field and show/hide with Java Script
 * If Help exist as Column Comments in databse table ($help), giv priority to it
 */
function sx_getHelpForJava($currentFieldName, $help)
{
    $radioTemp = true;
    if (is_array(arr_HelpByField) && array_key_exists($currentFieldName, arr_HelpByField)) {
        $strHelp = arr_HelpByField[$currentFieldName];
        if (!empty($strHelp)) {
            $radioTemp = false;
            return sx_setHelpForJava($currentFieldName, $strHelp);
        }
    }

    if ($radioTemp) {
        if (!empty($help)) {
            return sx_setHelpForJava($currentFieldName, $help);
        } else {
            return "";
        }
    }
}


/**
 * Gets the field's type of relation to other (table) fields
 * @param mixed $fieldName
 * @return int
 */
function sx_getRelationTypeNumber($fieldName)
{
    if (isset($_SESSION["10_" . $fieldName])) {
        return 10;
    } elseif (isset($_SESSION["1_" . $fieldName])) {
        return 1;
    } elseif (isset($_SESSION["2_" . $fieldName])) {
        return 2;
    } elseif (isset($_SESSION["3_" . $fieldName])) {
        return 3;
    } elseif (isset($_SESSION["30_" . $fieldName])) {
        return 30;
    } elseif (isset($_SESSION["300_" . $fieldName])) {
        return 300;
    } elseif (isset($_SESSION["4_" . $fieldName]) > 0) {
        return 4;
    } elseif (isset($_SESSION["40_" . $fieldName]) > 0) {
        return 40;
    } else {
        return 0;
    }
}

/**
 * ==============================================================
 * The main function that creates form inputs for all relation types
 * ==============================================================
 * $iLoop       'Number added to JS function names to link related tables;
 * $str_RFV     'Related Field Value: The value of the carrent field name - from edit and addHTML pages, can be of any type;
 * $str_RFVAdd  'Add New Values in Relations Types 2 (e.g. Title for a New Theme) 
 *              and 3 (e.g. Distinct value, Publisher) - from addHTML-pages;
 */

/**
 * ==============================================================
 * The main function that creates form inputs for all relation types
 * ==============================================================
 * @param mixed $currentFieldName
 * @param mixed $str_RFVAdd : Add New Values in Relations Types 2 (e.g. Title for a New Theme)
 *              and 3 (e.g. Distinct value, Publisher) - from addHTML-pages;
 * @param mixed $str_RFV : Related Field Value: The value of the carrent field name - from edit and addHTML pages, can be of any type
 * @return void
 */
function sx_getRelationInputs($currentFieldName, $str_RFVAdd, $str_RFV)
{
    // Number added to JS function names to link related tables;
    static $iLoop = 0;
    static $intSelectedMainCatIDValue = "";
    $intRelationType = sx_getRelationTypeNumber($currentFieldName);

    if (intval($intRelationType) == 0) {
        echo '<input type="text" name="' . $currentFieldName . '" value="' . htmlentities($str_RFVAdd) . '">';
    } else {
        switch ($intRelationType) {
            case 1:
                if (intval($str_RFV) == 0) {
                    $str_RFV = 0;
                }
                $strJavaScript = "";
                // == Check if Included in Type 10 as Group to Categories
                $radio = false;
                $radioInNext = false;
                foreach (arr_FieldRelations as $key => $value) {
                    if ($radio) {
                        if ($value[1] == $currentFieldName && ($value[0] == 10 || $value[0] == 100)) {
                            $radioInNext = true;
                        }
                        /*
                        echo "<pre>";
                        print_r($value);
                        echo "</pre>";
                        */
                        break;
                    }
                    if ($key == $currentFieldName) {
                        $radio = True;
                    }
                }
                if ($radioInNext) {
                    $intSelectedMainCatIDValue = $str_RFV;
                    $strJavaScript = 'onChange="javascript:sx_getRelatedOptions_' . $iLoop . '(this.options[selectedIndex].value,0)"';
                }

                echo '<select name="' . $currentFieldName . '" ' . $strJavaScript . ">";
                if (intval($str_RFV) == 0) {
                    echo '<option value="0" selected>Choose</option>';
                } else {
                    echo '<option value="0">Choose</option>';
                }

                $arrSession = $_SESSION["1_" . $currentFieldName];
                $iRows = count($arrSession);
                $iCells = count($arrSession[0]);
                for ($r = 0; $r < $iRows; $r++) {
                    $i_NumericField = $arrSession[$r][0];
                    $s_TextField = $arrSession[$r][1];
                    // In case when a record has no title, replace by its ID 
                    if (empty($s_TextField)) {
                        $s_TextField = $i_NumericField;
                    } elseif (strlen($s_TextField) > 74) {
                        $s_TextField == sx_get_Left_Part($s_TextField, 74);
                    }
                    $s_ThirdField = null;
                    $strSelected = null;
                    if ($iCells > 2) {
                        if (intval($arrSession[$r][2]) == 0) {
                            $s_ThirdField = " " . $arrSession[$r][2];
                        }
                    }
                    if (strval($i_NumericField) == strval($str_RFV)) {
                        $strSelected = " selected";
                    }
                    echo "<option" . $strSelected . ' value="' . $i_NumericField . '">' . $s_TextField . $s_ThirdField . "</option>";
                }
                echo "</select>";
                break;

            case 10:
                // == Check if Include in Next Type 10 as Category to Subcategories
                $strJavaScript = "";
                $radio = false;
                $radioInNext = false;
                foreach (arr_FieldRelations as $key => $value) {
                    if ($radio) {
                        if ($value[1] == $currentFieldName) {
                            $radioInNext = true;
                        }
                        break;
                    }
                    if ($key == $currentFieldName) {
                        $radio = True;
                    }
                    //                    $str_LastSubCat = $value[1];
                }

                if ($radioInNext) {
                    $strJavaScript = 'onChange="javascript:sx_getRelatedOptions_' . ($iLoop + 1) . '(this.options[selectedIndex].value,0)"';
                }
                echo '<select name="' . $currentFieldName . '" ' . $strJavaScript . ">";
                if (intval($str_RFV) == 0) {
                    $str_RFV = 0;
                    echo '<option value="0" selected>Choose</option>';
                } else {
                    echo '<option value="0">Choose</option>';
                }
                $arrSession = $_SESSION["10_" . $currentFieldName];
                $iRows = count($arrSession);
                $iCells = count($arrSession[0]);

                //Check if this subcategory belongs to any category
                $radio_BelongsToMainClass = false;
                if ($iCells > 2) {
                    //if (intval($arrSession[0][2]) > 0) {
                    $radio_BelongsToMainClass = true;
                    //}
                }

                //IF NOT, treat it as Type 1
                if ($radio_BelongsToMainClass == false) {
                    for ($r = 0; $r < $iRows; $r++) {
                        $i_NumericField = $arrSession[$r][0];
                        $s_TextField = sx_get_Left_Part($arrSession[$r][1], 64);
                        $strSelected = null;
                        if (strval($i_NumericField) == strval($str_RFV)) {
                            $strSelected = " selected";
                        }
                        echo "<option" . $strSelected . ' value="' . $i_NumericField . '">' . $s_TextField . "</option>";
                    }
                }
                echo "</select>";

                //If True, prepare the JS to load options according to selected Category
                if ($radio_BelongsToMainClass) {
                    echo "<script>" . "\n";
                    echo "var sx_getArrayOptions_" . $iLoop . " = new Array();" . "\n";
                    for ($r = 0; $r < $iRows; $r++) {
                        $i_NumericField = $arrSession[$r][0];
                        $s_TextField = sx_get_Left_Part($arrSession[$r][1], 54);
                        $i_ThirdField = $arrSession[$r][2];
                        echo "sx_getArrayOptions_" . $iLoop . "[" . $r . ']	= "' . $i_ThirdField . "|" . $s_TextField . "|" . $i_NumericField . '|"' . "\n";
                    }
                    echo "</script>" . "\n";

                    //Subfunction in Add and Edit pages that greates the java codes to load options related to selected Category
                    sx_getJavaForRelatedOptions($currentFieldName, $iLoop);

                    //To open selcted subcategory option when loading a page for editing
                    echo "\n" . "<script>" . "\n";
                    echo "sx_getRelatedOptions_" . $iLoop . "('" . $intSelectedMainCatIDValue . "','" . $str_RFV . "');" . "\n";
                    echo "</script>" . "\n";

                    $iLoop++;
                    /**
                     * Change the Category value only if the next loop entry is a subcategory
                     * Else, the same Category value can be used to select from other related fields 
                     * which are not related to each other.
                     */
                    if ($radioInNext) {
                        $intSelectedMainCatIDValue = $str_RFV;
                    }
                }
                break;

            case 2:
                //== Create select option from related table
                echo '<select name="' . $currentFieldName . '">';
                if (intval($str_RFV) == 0) {
                    $str_RFV = 0;
                    echo '<option value="0" selected>Choose</option>';
                } else {
                    echo '<option value="0">Choose</option>';
                }
                $arrSession = $_SESSION["2_" . $currentFieldName];
                $iRows = count($arrSession);
                $iCells = count($arrSession[0]);
                for ($r = 0; $r < $iRows; $r++) {
                    $i_NumericField = $arrSession[$r][0];
                    $s_TextField = sx_get_Left_Part($arrSession[$r][1], 74);
                    $strSelected = null;
                    if (strval($i_NumericField) == strval($str_RFV)) {
                        $strSelected = " selected";
                    }
                    echo "<option" . $strSelected . ' value="' . $i_NumericField . '">' . $s_TextField . "</option>";
                }
                echo "</select><br>";

                // To Add new input to related table - 
                // Get input details from the SQL-statement hold in session
                // SELECT ThemeID, ThemeName FROM themes WHERE Actual = True ORDER BY ThemeName ASC
                // SELECT ThemeID, ThemeName FROM themes WHERE Actual = True AND Hidden = False ORDER BY ThemeName ASC

                $sx_Str = $_SESSION["2_SQL" . $currentFieldName];

                $pos = strpos($sx_Str, "FROM ") + 4;
                $pos1 = strpos($sx_Str, "WHERE ", $pos) - 1;
                $pos2 = strpos($sx_Str, "ORDER BY", $pos) - 1;
                if ($pos1 > 0) {
                    $pos3 = $pos1 - $pos;
                    $relatedTableName = trim(substr($sx_Str, $pos, $pos3));
                } elseif ($pos2 > 0) {
                    $pos3 = $pos2 - $pos;
                    $relatedTableName = trim(substr($sx_Str, $pos, $pos3));
                } else {
                    $relatedTableName = trim(substr($sx_Str, $pos));
                }

                //## Get the WHERE-condition, if any
                if ($pos1 > 0) {
                    if ($pos2 > 0) {
                        $pos3 = $pos2 - ($pos1 + 6);
                        $relatedWhereCondition = trim(substr($sx_Str, ($pos1 + 6), $pos3));
                    } else {
                        $relatedWhereCondition = trim(substr($sx_Str, ($pos1 + 6)));
                    }
                } else {
                    $relatedWhereCondition = "";
                }

                //In case there are more than one where-condition select the first one
                if (!empty($relatedWhereCondition) && strpos($relatedWhereCondition, " AND ", 1) > 0) {
                    $sxTemp = explode(" AND ", $relatedWhereCondition);
                    $relatedWhereCondition = trim($sxTemp[0]);
                }
                if (!empty($relatedWhereCondition) && strpos($relatedWhereCondition, "=") > 0) {
                    $relatedWhereConditionArray = explode("=", $relatedWhereCondition);
                    $relatedWhereFieldName = trim($relatedWhereConditionArray[0]);
                    $relatedWhereFieldValue = trim($relatedWhereConditionArray[1]);
                } else {
                    $relatedWhereFieldName = "";
                    $relatedWhereFieldValue = "";
                }

                //## Get the related field name
                $pos = strpos($sx_Str, "SELECT", 1) + 6;
                $pos2 = strpos($sx_Str, "FROM");
                $pos3 = $pos2 - $pos;

                $relatedFieldName = trim(substr($sx_Str, $pos, $pos3));
                $pos = strpos($relatedFieldName, ",");
                $relatedFieldName = trim(substr($relatedFieldName, $pos + 1));
                /**
                 * To add a new record to related table (e.g. a new Theme)
                 */
                echo '<input type="hidden" name="hiddenRWhereName' . $currentFieldName . '" value="' . $relatedWhereFieldName . '">';
                echo '<input type="hidden" name="hiddenRWhereValue' . $currentFieldName . '" value="' . $relatedWhereFieldValue . '">';
                echo '<input type="hidden" name="hiddenRTable' . $currentFieldName . '" value="' . $relatedTableName . '">';
                echo '<input type="hidden" name="hiddenRField' . $currentFieldName . '" value="' . $relatedFieldName . '">';
                echo '<input type="text" name="Add' . $currentFieldName . '" value="' . htmlentities($str_RFVAdd) . '">';
                break;

            case 3:
                //== Get distinct values from the same or related table
                echo '<select name="' . $currentFieldName . '">';
                if (empty($str_RFV)) {
                    echo '<option value="" selected>Choose</option>';
                } else {
                    echo '<option value="">Choose</option>';
                }

                $arrSession = $_SESSION["3_" . $currentFieldName];
                $i_Rows = count($arrSession);
                for ($rr = 0; $rr < $i_Rows; $rr++) {
                    $s_TextField = $arrSession[$rr][0];
                    $strSelected = null;
                    if (strval($s_TextField) == strval($str_RFV)) {
                        $strSelected = " selected";
                    }
                    echo "<option" . $strSelected . ' value="' . $s_TextField . '">' . $s_TextField . "</option>";
                }
                echo "</select>";
                echo '<br><input type="text" name="Distinct' . $currentFieldName . '" value="' . htmlentities($str_RFVAdd) . '">';
                break;

            case 30:
                /**
                 * Prepara jQuery
                 * To Get distinct values from a Selected Parent ID or Name Field - from the same or related table
                 */

                $arrSession = $_SESSION["30_" . $currentFieldName];
                $grandFiledName = $arrSession[1];
                $cleanFiledName = $arrSession[2];
                $relationSQL = $arrSession[3];

                echo '<select name="' . $currentFieldName . '">';
                if (empty($str_RFV)) {
                    echo '<option value="" selected>Choose</option>';
                } else {
                    echo '<option value="">Choose</option>';
                    echo '<option value="' . $str_RFV . '" selected>' . $str_RFV . '</option>';
                }
                echo "</select>";
                echo '<br><input type="text" name="Distinct' . $currentFieldName . '" value="' . htmlentities($str_RFVAdd) . '">';
?>
                <script>
                    <?php
                    if (!empty($str_RFV)) { ?>
                        $sx('select[name="<?= $currentFieldName ?>"]').ready(function() {
                            sx_LoadDistinctWhere("<?= $currentFieldName ?>", "<?= $str_RFV ?>", "<?= $grandFiledName ?>", "", "<?= $relationSQL ?>");
                        });
                    <?php } ?>
                    $sx('select[name="<?= $currentFieldName ?>"]').on('change', function() {
                        var intSortin = $sx('option:selected', this).attr('data-id');
                        if ($sx('input[name="<?= $cleanFiledName ?>"]').length) {
                            if (intSortin) {
                                $sx('input[name="<?= $cleanFiledName ?>"]').val(intSortin);
                            } else {
                                $sx('input[name="<?= $cleanFiledName ?>"]').val('0');
                            }
                        }
                    });
                    $sx('select[name="<?= $grandFiledName ?>"]').on('change', function() {
                        sx_LoadDistinctWhere("<?= $currentFieldName ?>", "", "<?= $grandFiledName ?>", "", "<?= $relationSQL ?>");
                        if ($sx('input[name="<?= $cleanFiledName ?>"]').length) {
                            $sx('input[name="<?= $cleanFiledName ?>"]').val('0');
                        }
                    });
                </script>
            <?php
                break;

            case 300:
                /**
                 * Prepara jQuery
                 * To Get distinct values from a Selected Parent ID or Name Field - from the same or related table
                 */

                $arrSession = $_SESSION["300_" . $currentFieldName];
                $grandFiledName = $arrSession[1];
                $parentFiledName = $arrSession[2];
                $cleanFiledName = $arrSession[3];
                $relationSQL = $arrSession[4];

                echo '<select name="' . $currentFieldName . '">';
                if (empty($str_RFV)) {
                    echo '<option value="" selected>Choose</option>';
                } else {
                    echo '<option value="">Choose</option>';
                    echo '<option value="' . $str_RFV . '" selected>' . $str_RFV . '</option>';
                }
                echo "</select>";
                echo '<br><input type="text" name="Distinct' . $currentFieldName . '" value="' . htmlentities($str_RFVAdd) . '">';
            ?>
                <script>
                    <?php
                    if (!empty($str_RFV)) { ?>
                        $sx('select[name="<?= $currentFieldName ?>"]').ready(function() {
                            sx_LoadDistinctWhere("<?= $currentFieldName ?>", "<?= $str_RFV ?>", "<?= $grandFiledName ?>", "<?= $parentFiledName ?>", "<?= $relationSQL ?>");
                        });
                    <?php }
                    ?>
                    $sx('select[name="<?= $currentFieldName ?>"]').on("change", function() {
                        var intSortin = $sx('option:selected', this).attr('data-id');
                        if ($sx('input[name="<?= $cleanFiledName ?>"]').length) {
                            if (intSortin) {
                                $sx('input[name="<?= $cleanFiledName ?>"]').val(intSortin);
                            } else {
                                $sx('input[name="<?= $cleanFiledName ?>"]').val('0');
                            }
                        }
                    });
                    $sx('select[name="<?= $parentFiledName ?>"]').on('change', function() {
                        sx_LoadDistinctWhere("<?= $currentFieldName ?>", "", "<?= $grandFiledName ?>", "<?= $parentFiledName ?>", "<?= $relationSQL ?>");
                        if ($sx('input[name="<?= $cleanFiledName ?>"]').length) {
                            $sx('input[name="<?= $cleanFiledName ?>"]').val('0');
                        }

                    });
                    $sx('select[name="<?= $grandFiledName ?>"]').on('change', function() {
                        $sx('select[name="<?= $currentFieldName ?>"]').html('<option value="">Choose</option>');
                        if ($sx('input[name="<?= $cleanFiledName ?>"]').length) {
                            $sx('input[name="<?= $cleanFiledName ?>"]').val('0');
                        }
                    });
                </script>
    <?php
                break;

            case 4: //Get exclusive Radial values for the field
                $sx_Str = $_SESSION["4_" . $currentFieldName];
                $sx_Arr = explode(",", $sx_Str);
                $iTemp = count($sx_Arr);
                if ($iTemp < 4) {
                    for ($r = 0; $r < $iTemp; $r++) {
                        $sx_Str1 = trim($sx_Arr[$r]);
                        $strChecked = "";
                        if (empty($str_RFV)) { //When add.php' is open
                            if ($r == 0) {
                                $strChecked = " checked";
                            }
                        } else {
                            if (strval($sx_Str1) == strval($str_RFV)) {
                                $strChecked = " checked";
                            }
                        }
                        if (!empty($sx_Str1) && strlen($sx_Str1) > 6 && $iTemp > 5) {
                            if ($r == 3 || $r == 6) {
                                echo "<br>";
                            }
                        }
                        echo '<input type="radio" value="' . $sx_Str1 . '" name="' . $currentFieldName . '"' . $strChecked . '>' . $sx_Str1 . ' ';
                    }
                } else {
                    echo '<select name="' . $currentFieldName . '">';
                    for ($r = 0; $r < $iTemp; $r++) {
                        $sx_Str1 = trim($sx_Arr[$r]);
                        $strChecked = "";
                        if (empty($str_RFV)) { //When add.php' is open
                            if ($r == 0) {
                                $strChecked = " selected";
                            }
                        } else {
                            if (strval($sx_Str1) == strval($str_RFV)) {
                                $strChecked = " selected";
                            }
                        }
                        echo '<option value="' . $sx_Str1 . '"' . $strChecked . '>' . $sx_Str1 . '</option>';
                    }
                    echo '</select>';
                }

            case 40: //Get box values for the field - Added 2008
                if (isset($_SESSION["40_" . $currentFieldName])) {
                    //$sx_Arr = $_SESSION["40_" . $currentFieldName];
                    $sx_Str = $_SESSION["40_" . $currentFieldName];
                    if (strpos($sx_Str, ',') == 0) {
                        $sx_Str .= ',';
                    }
                    $sx_Arr = explode(',', $sx_Str);



                    $iTemp = count($sx_Arr);
                    for ($x = 0; $x < $iTemp; $x++) {
                        $sx_Str1 = trim($sx_Arr[$x]);
                        $strChecked = "";
                        if (empty($str_RFV)) { //When add.php' is open
                            if ($x == 0) {
                                $strChecked = "checked";
                            }
                        } else {
                            if (strpos($str_RFV, ",") > 0) {
                                $strNewSplit = explode(",", $str_RFV);
                                $iTemp = count($strNewSplit);
                                for ($v = 0; $v < $iTemp; $v++) {
                                    if ($sx_Str1 == trim($strNewSplit[$v])) {
                                        $strChecked = " checked";
                                        break;
                                    }
                                }
                            } else {
                                if (strval($sx_Str1) == strval(trim($str_RFV))) {
                                    $strChecked = " checked";
                                }
                            }
                        }
                        echo '<input type="checkbox" value="' . $sx_Str1 . '" name="' . $currentFieldName . $sx_Str1 . '"' . $strChecked . ">" . $sx_Str1;
                    }
                }
        }
    }
}

//====================================================
//==== 2 FUNCTIONS FOR THE LIST OF RECORDS
//====================================================

//## Replace the Number Values of Related Table Records with Name Values
//===============================================
function sx_getRelatedFieldNameForList($currentFieldName, $str_RFV)
{
    if (empty($currentFieldName) || intval($str_RFV) == 0) {
        return $str_RFV;
    }
    $arrSession = "";
    $strAdd = "";
    if (isset($_SESSION["1_" . $currentFieldName])) {
        $arrSession = $_SESSION["1_" . $currentFieldName];
    } elseif (isset($_SESSION["2_" . $currentFieldName])) {
        $arrSession = $_SESSION["2_" . $currentFieldName];
    } elseif (isset($_SESSION["100_" . $currentFieldName])) {
        $guerySession = $_SESSION["100_" . $currentFieldName];
    } elseif (isset($_SESSION["10_" . $currentFieldName])) {
        $arrSession = $_SESSION["10_" . $currentFieldName];
    } else {
        return $str_RFV;
    }
    if (isset($guerySession)) {
        $conn = dbconn();
        $stmt = $conn->prepare($guerySession);
        $stmt->execute([$str_RFV]);
        return $stmt->fetchColumn();
    } elseif (is_array($arrSession)) {
        $iTemp = count($arrSession);
        $iCulums = count($arrSession[0]);
        for ($r = 0; $r < $iTemp; $r++) {
            if (strval($arrSession[$r][0]) == strval($str_RFV)) {
                if ($iCulums > 2) { // Separate First and Last Name
                    if (intval($arrSession[$r][2]) == 0) {
                        $strAdd = " " . $arrSession[$r][2];
                    }
                }
                if (!empty($arrSession[$r][1])) {
                    return trim($arrSession[$r][1]) . $strAdd;
                } else {
                    return $str_RFV;
                }
            }
        }
    } else {
        return $str_RFV;
    }
}

//## Get the alias name of the current field from the assiciative array
//===============================================
function sx_checkAsName($currentFieldName)
{
    if (is_array(arr_AliasName) && array_key_exists($currentFieldName, arr_AliasName)) {
        return arr_AliasName[$currentFieldName];
    } else {
        // If the name is all uppercase, return it as-is (after replacing underscores)
        if (strtoupper($currentFieldName) === $currentFieldName) {
            return str_replace('_', ' ', $currentFieldName);
        }

        // Otherwise, split before uppercase letters and clean up
        $CleanedFieldName = trim(implode(' ', preg_split('/(?<!^)(?=[A-Z])/', $currentFieldName)));
        $CleanedFieldName = str_replace('_', ' ', $CleanedFieldName);

        // Fix common acronyms
        $CleanedFieldName = str_replace('I D', 'ID', $CleanedFieldName);
        $CleanedFieldName = str_replace('U R L', 'URL', $CleanedFieldName);
        $CleanedFieldName = str_replace('A P I', 'API', $CleanedFieldName);
        $CleanedFieldName = str_replace('J S O N', 'JSON', $CleanedFieldName);
        $CleanedFieldName = str_replace('H T M L', 'HTML', $CleanedFieldName);
        $CleanedFieldName = str_replace('C S S', 'CSS', $CleanedFieldName);
        $CleanedFieldName = str_replace('S Q L', 'SQL', $CleanedFieldName);
        $CleanedFieldName = str_replace('X M L', 'XML', $CleanedFieldName);
        $CleanedFieldName = str_replace('I P', 'IP', $CleanedFieldName);
        $CleanedFieldName = str_replace('D N S', 'DNS', $CleanedFieldName);
        $CleanedFieldName = str_replace('U R I', 'URI', $CleanedFieldName);

        return $CleanedFieldName;
    }
}

//===============================================
//==== 3 FUNCTIONS USED IN MANY FILES
//===============================================

//## Get the Relation Type for current radio field
//===============================================
function sx_getUpdateableFieldType($currFieldName)
{
    if (is_array(arr_UpdateableFields) && array_key_exists($currFieldName, arr_UpdateableFields)) {
        return arr_UpdateableFields[$currFieldName];
    } else {
        return 0;
    }
}

//## Get the Relation Type of the current field from the arr_FieldRelations
//===============================================
function sx_getRelationType($currFieldName)
{
    if (is_array(arr_FieldRelations) && array_key_exists($currFieldName, arr_FieldRelations)) {
        return arr_FieldRelations[$currFieldName][0];
    } else {
        return 0;
    }
}

//===============================================
//==== 4 FUNCTIONS ONLY FOR ADD and Update
//===============================================

/**
 * Is called for the relation Type 10 above
 * It creates javascript for selecting options for related tables
 * Groups - Categories - SubCategories - SubSubCategories
 */

function sx_getJavaForRelatedOptions($catIDName, $sx_RO)
{ ?>
    <script>
        //	SX - Get select option for subset (categories) in a set (group)
        function sx_getRelatedOptions_<?= $sx_RO ?>(iGroupIDvalue, iCatIDValue) {
            select = window.document.sxAddEdit<?= "." . $catIDName ?>;
            strSplit = "";
            count = 1;
            select.options.length = count;
            arrayOptions = sx_getArrayOptions_<?= $sx_RO ?>;
            iLength = arrayOptions.length;
            for (i = 0; i < iLength; i++) {
                strSplit = arrayOptions[i].split("|");
                if (strSplit[0] == iGroupIDvalue) {
                    var strOption = select.options[count++] = new Option(strSplit[1]);
                    strOption.value = strSplit[2];
                    if (strSplit[2] == iCatIDValue) {
                        strOption.selected = true;
                    }
                }
            }
            // Clear/Reset/Remove select options for all descending levels of subsets when the option of a set is changed
            if (iCatIDValue == 0) {
                if (typeof sx_getRelatedOptions_<?= ($sx_RO + 1) ?> === "function") {
                    sx_getRelatedOptions_<?= ($sx_RO + 1) ?>(iGroupIDvalue, 0);
                }
            }
        }
    </script>
<?php }


//===============================================
//==== 5 FUNCTIONS ONLY FOR THE CONFIGURATION FILE
//===============================================

//## Check if the current field is selected to be shown in the list of records
//===============================================
function sx_checkSelected($currentFieldName)
{
    if (is_array(arr_SelectedFields)) {
        if (in_array($currentFieldName, arr_SelectedFields)) {
            return "checked";
        } else {
            return "";
        }
    } else {
        return "checked";
    }
}

//## Check the field for descend order
//===============================================
function sx_checkOrderBy($currentFieldName)
{
    if (!empty(str_OrderByField)) {

        if ($currentFieldName == trim(str_replace("DESC", "", str_OrderByField))) {
            return "checked";
        } else {
            return "";
        }
    } else {
        return "";
    }
}

//## Check if the current field is required - from the arrRequiredFields
function sx_checkRequiredFields($currentFieldName)
{
    if (!empty(arr_RequiredFields)) {
        if (in_array($currentFieldName, arr_RequiredFields)) {
            return  "checked";
        } else {
            return  "";
        }
    } else {
        return  "";
    }
}

//## Get the Relations of the current field from the arr_FieldRelations
function sx_getRelatedFields($currFieldName)
{
    if (is_array(arr_FieldRelations) && array_key_exists($currFieldName, arr_FieldRelations)) {
        $strTemp = arr_FieldRelations[$currFieldName][2];
        if (isset(arr_FieldRelations[$currFieldName][3])) {
            $strTemp .= '; ' . arr_FieldRelations[$currFieldName][3];
        }
        return $strTemp;
    } else {
        return null;
    }
}

//## Get 2 Relation parts of the current field from the arr_FieldRelations
function sx_getRelatedFieldsSlice($currFieldName)
{
    if (is_array(arr_FieldRelations) && array_key_exists($currFieldName, arr_FieldRelations)) {
        $arrTemp = array_slice(arr_FieldRelations[$currFieldName], 1);
        $strTemp = "";
        foreach ($arrTemp as $key) {
            if (!empty($strTemp)) {
                $strTemp .= "; ";
            }
            $strTemp .= ($key);
        }
        return $strTemp;
    } else {
        return null;
    }
}

//## Get the Unbounded Field Relations 
function sx_getUnboundedRelations($currFieldName)
{
    if (is_array(arr_AddUppdateRelated) && array_key_exists($currFieldName, arr_AddUppdateRelated)) {
        return arr_AddUppdateRelated[$currFieldName][1];
    } else {
        return null;
    }
}

?>