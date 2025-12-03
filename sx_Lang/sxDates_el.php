<?php
function sx_getCapitals($strText) {
    $sxGR = array (
        array("ά","Ά","Α"),
        array("έ","Έ","Ε"),
        array("ή","Ή","Η"),
        array("ί","Ί","Ι"),
        array("ό","Ό","Ο"),
        array("ύ","Ύ","Υ"),
        array("ώ","Ώ","Ω")
    );
    for ($r = 0; $r < 7; $r++) {
        for ($c = 0; $c < 2; $c++) {
            if (strpos($strText,$sxGR[$r][$c],0) > 0) {
                $strText = str_replace($sxGR[$r][$c],$sxGR[$r][2],$strText);
            }
        }
    }
    if (strpos($strText,"ς",0) > 0) {
        $strText=str_replace("ς","Σ",$strText);
    }
    return mb_strtoupper($strText, "UTF-8");
}

$lng_MonthNames = array(
    "Ιανουάριος",
    "Φεβρουάριος",
    "Μάρτιος",
    "Απρίλιος",
    "Μάιος",
    "Ιούνιος",
    "Ιούλιος",
    "Αύγουστος",
    "Σεπτέμβριος",
    "Οκτώβριος",
    "Νοέμβριος",
    "Δεκέμβριος");
define("lng_MonthNames", $lng_MonthNames);

$lng_MonthNamesGen = array(
    "Ιανουαρίου",
    "Φεβρουαρίου",
    "Μαρτίου",
    "Απριλίου",
    "Μαϊου",
    "Ιουνίου",
    "Ιουλίου",
    "Αυγούστου",
    "Σεπτεμβρίου",
    "Οκτωβρίου",
    "Νοεμβρίου",
    "Δεκεμβρίου");
define("lng_MonthNamesGen", $lng_MonthNamesGen);

$lng_DayNames = array(
    "Δευτέρα",
    "Τρίτη",
    "Τετάρτη",
    "Πέμπτη",
    "Παρασκευή",
    "Σάββατο",
    "Κυριακή"
);
define("lng_DayNames", $lng_DayNames);

$sxEuDays=array();
$sxEuDays[0]="Δευτέρα";
$sxEuDays[1]="Τρίτη";
$sxEuDays[2]="Τετάρτη";
$sxEuDays[3]="Πέμπτη";
$sxEuDays[4]="Παρασκευή";
$sxEuDays[5]="Σάββατο";
$sxEuDays[6]="Κυριακή";

$sxMonths=array();
$sxMonths[0]="Ιανουάριος";
$sxMonths[1]="Φεβρουάριος";
$sxMonths[2]="Μάρτιος";
$sxMonths[3]="Απρίλιος";
$sxMonths[4]="Μάιος";
$sxMonths[5]="Ιούνιος";
$sxMonths[6]="Ιούλιος";
$sxMonths[7]="Αύγουστος";
$sxMonths[8]="Σεπτέμβριος";
$sxMonths[9]="Οκτώβριος";
$sxMonths[10]="Νοέμβριος";
$sxMonths[11]="Δεκέμβριος";

$sxMonthsGen=array();
$sxMonthsGen[0]="Ιανουαρίου";
$sxMonthsGen[1]="Φεβρουαρίου";
$sxMonthsGen[2]="Μαρτίου";
$sxMonthsGen[3]="Απριλίου";
$sxMonthsGen[4]="Μαϊου";
$sxMonthsGen[5]="Ιουνίου";
$sxMonthsGen[6]="Ιουλίου";
$sxMonthsGen[7]="Αυγούστου";
$sxMonthsGen[8]="Σεπτεμβρίου";
$sxMonthsGen[9]="Οκτωβρίου";
$sxMonthsGen[10]="Νοεμβρίου";
$sxMonthsGen[11]="Δεκεμβρίου";

?>