<?php

/**
 * Make searchable all visible table fields that are related to other tables
 * @$strSelectedFields: visible fields for the current table
 * @$searchFieldNameWhere: Field names for current search, if any
 */

/**
 * Functions to make relations to other tables searchable
 * Gets the relation type and the searchable variables (ID and Name)
 * Classes refers to groups, categories, etc.
 */
function sx_getSelectTypeNumber($fieldName)
{
    /**
     * Exclude some field names with type 1
     */
    if ($fieldName != 'IncludeInTextID') {
        if (is_array(@$_SESSION["10_" . $fieldName])) {
            return 10;
        } elseif (is_array(@$_SESSION["1_" . $fieldName])) {
            return 1;
        } elseif (is_array(@$_SESSION["2_" . $fieldName])) {
            return 2;
        } elseif (is_array(@$_SESSION["3_" . $fieldName])) {
            // Eclude type 3
            return 0;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

/**
 * the variables $searchFieldNameWhere and $strSelectedFields are defined in list.php
 */

$sWhereCode = "";
$searchName = "";
$searchNumber = -1;
$sLast_SelectName = "";

// to check current search fields
if (!empty($searchFieldNameWhere)) {
    $sWhereCode = str_replace(" AND ", "", $searchFieldNameWhere);
    $arrCode = explode(" = ", $sWhereCode);
    $searchName = trim($arrCode[0]);
    $searchNumber = trim($arrCode[1]);
} ?>

<form name="SelectClasses" id="jqSelectClasses">
    <?php
    /*
            echo $searchFieldNameWhere ." | ";
            echo $searchName ." | ";
            echo $searchNumber ." <br>";
            echo $strSelectedFields ."<hr>";
            */

    $arSelectFields = explode(",", $strSelectedFields);
    $iTemp = count($arSelectFields);
    for ($w = 0; $w < $iTemp; $w++) {
        /**
         * For every visible table field
         * check if it is related to other table and get its relation type
         */
        $sSelectName = trim($arSelectFields[$w]);
        $iSelectType = sx_getSelectTypeNumber($sSelectName);
        if (intval($iSelectType) > 0) {
            /**
             * Related tables and fields are already saved in sessions
             * by the type of reltion and the filed name.
             * So, just call them.
             */
            $ar_Session = $_SESSION[$iSelectType . "_" . $sSelectName];
            $iRows = count($ar_Session);
            $iColumns = count($ar_Session[0]);
            $i_Loop = -1;

            /*
            echo "<pre>";
            print_r($ar_Session);
            echo "</pre>";
            */
    ?>
            <select name="<?= $sSelectName ?>" data-name="<?= $sSelectName ?>">
                <option value="0"><?= $sSelectName ?></option>
                <?php
                //for ($r = ($iRows - 1); $r > -1; $r--) {
                for ($r = 0; $r < $iRows; $r++) {
                    $i_FieldNumer = $ar_Session[$r][0];
                    if ($iSelectType == 3) {
                        /**
                         * Type 3 relations refers to unique values of a field in the current table
                         */
                        $s_FieldName = $i_FieldNumer;
                    } else {
                        if ($iSelectType == 10 && $iColumns > 1) {
                            $i_Parentlass = trim($ar_Session[$r][2]);
                            if ($i_Loop != $i_Parentlass) {
                                if ($i_Loop > 0) {
                                    echo "</optgroup>";
                                }
                                echo '<optgroup label="' . $sLast_SelectName . $i_Parentlass . '">';
                            }
                            $i_Loop = $i_Parentlass;
                        }
                        $s_FieldName = trim($ar_Session[$r][1]);
                        if (!empty($s_FieldName) && strlen($s_FieldName) > 100) {
                            $i_pos = strpos($s_FieldName, ' ', 100);
                            $s_FieldName = substr($s_FieldName, 0, $i_pos) . '...';
                        }
                    }
                    $strSelected = "";
                    if ($searchName == $sSelectName && intval($searchNumber) == intval($i_FieldNumer)) {
                        $strSelected = "selected ";
                    }
                    echo '<option ' . $strSelected . 'value="' . $i_FieldNumer . '">' . $i_FieldNumer . ". " .  $s_FieldName . '</option>';
                }
                if ($i_Loop > 0) {
                    echo "</optgroup>";
                } ?>
            </select>
    <?php
            $sLast_SelectName = $sSelectName . ": ";
        }
    } ?>
</form>