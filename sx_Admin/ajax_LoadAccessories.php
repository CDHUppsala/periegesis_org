<?php
include "functionsLanguage.php";
include "functionsTableName.php";
include "login/lockPage.php";
include "functionsDBConn.php";

$i_GroupID = @$_POST["selectedGroupID"];
if (intval($i_GroupID) == 0) {
    $i_GroupID = 0;
}?>
<div id="bodyUpload" class="jqAccessories">
	<h3><?=lngAccessoryGroups?></h3>
	<p><?=lngMarkToCopyImages?></p>
	<form action="ajax_LoadAccessories.php" method="post" name="LoadSelectForm" id="jqLoadSelectForm">
		<select name="selectedGroupID">
			<option value="0"><?=lngSelectGroup?></option>
<?php
$sql = "SELECT GroupID, GroupName
	FROM accessorygroups
	WHERE Hidden = False ";
$stmt = $Conn->query($sql);
while ($rs = $stmt->fetch()) {
    $iTemp = $rs["GroupID"];
    $strSelected = "";
    if (intval($iTemp) == intval($i_GroupID)) {
        $strSelected = " selected";
    }?>
			<option value="<?=$iTemp?>" <?=$strSelected?>><?=$rs["GroupName"]?></option>
		<?php
}
$rs = null;
$stmt = null;
?>
		</select>
		<input type="submit" value="»»»" name="viewThisFolder"></td>
	</form>
	<h3><?=lngAccessoryCategories?></h3>
<?php
if (intval($i_GroupID) >= 0) {
    $s_Where = "";
    if (intval($i_GroupID) > 0) {
        $s_Where = " WHERE c.GroupID = " . $i_GroupID;
    }
    $sql = "SELECT c.CategoryID, c.CategoryName, a.AccessoryName, a.AccessoryPrice
		FROM accessorycategories AS c
		INNER JOIN accessories AS a
		ON c.CategoryID = a.CategoryID
		" . $s_Where;
    $rs = $Conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $accResults = $rs;
    }
    $rs = null;

    if (is_array($accResults)) {
        $iLoop = 0;
        $iRows = count($accResults);
        for ($x = 0; $x < $iRows; $x++) {
            $i_CategoryID = $accResults[$x][0];
            $s_CategoryName = $accResults[$x][1];
            $s_AccessoryName = $accResults[$x][2];
            $i_AccessoryPrice = $accResults[$x][3];
            if ($iLoop != $i_CategoryID) {
                if (floor($iLoop) > 0) {echo "</ul>";}?>
				<input type="checkbox" name="<?=$x?>" value="<?=$i_CategoryID?>"> <?=$i_CategoryID . ": " . $s_CategoryName?>
				<ul>
			<?php }?>
				<li><?=$s_AccessoryName . " (€" . $i_AccessoryPrice . ")"?></li>
			<?php
$iLoop = $i_CategoryID;
        }
        echo "</ul>";
    }
    $accResults = null;
}?>
</div>
<script>
	sxAjaxLoadArchives();
</script>
