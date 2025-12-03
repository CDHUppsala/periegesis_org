<?php
// Opens in a function, so redefine connection to db
$conn = dbconn();
$arrDataGroup = "";
$arr = "";
if (!empty($int_DataGroupID) && (int) $int_DataGroupID > 0) {
    $sql = "SELECT 
    GroupName,
    GroupDataDisplayForm,
    ShowSectionTitle,
    ShowSectionNotes,
    ShowDataTitle,
    ShowDataNotes
    FROM multi_data_groups
    WHERE GroupID = ? AND Publish = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_DataGroupID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $arrDataGroup = $rs;
    }
    $rs = NULL;


    //   $arrDataGroup = get_data_group_variables($int_DataGroupID);
    if ($arrDataGroup) {
        $strGroupName = $arrDataGroup["GroupName"];
        $strGroupDataDisplayForm = $arrDataGroup["GroupDataDisplayForm"];
        $radioShowSectionTitle = $arrDataGroup["ShowSectionTitle"];
        $radioShowSectionNotes = $arrDataGroup["ShowSectionNotes"];
        $radioShowDataTitle = $arrDataGroup["ShowDataTitle"];
        $radioShowDataNotes = $arrDataGroup["ShowDataNotes"];


        $sql = "SELECT 
        d.Title,
        d.MediaURL,
        d.MediaFolder,
        d.Notes,
        s.SectionID,
        s.SectionTitle" . str_LangNr . " AS SectionTitle,
        s.Notes" . str_LangNr . " AS SectionNotes
    FROM
        multi_data AS d 
            INNER JOIN 
        multi_data_groups AS g ON d.GroupID = g.GroupID
            LEFT JOIN
        multi_data_sections AS s ON d.SectionID = s.SectionID
    WHERE
        d.GroupID = ? AND d.Publish = 1" . str_LanguageAnd . " 
            AND g.Publish = 1 
            AND (s.Publish = 1 OR s.Publish IS NULL)
    ORDER BY s.Sorting DESC, s.SectionID, d.Sorting DESC, DataID ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$int_DataGroupID]);
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rs) {
            $arr = $rs;
        }
        $rs = NULL;
    }
}

/*
echo '<pre>';
print_r($arr);
echo '</pre>';
exit;
*/

if (is_array($arr)) {
    if ($strGroupDataDisplayForm == 'Gallery') {
        include  __DIR__ . "/data_to_gallery.php";
    } elseif ($strGroupDataDisplayForm == 'Table') {
        include  __DIR__ . "/data_to_table.php";
    } elseif ($strGroupDataDisplayForm == 'Cards') {
        include  __DIR__ . "/data_to_cards.php";
    } elseif ($strGroupDataDisplayForm == 'Cycler') {
        include  __DIR__ . "/data_to_cycler.php";
    }
}
unset($arrDataGroup);
unset($arr);
