<?php
$aResults = "";
$sql = "SELECT ProjectID, ProjectName" . str_LangNr . " AS ProjectName, MenuImages
	FROM report_projects
	WHERE Hidden = False
	ORDER BY Sorting DESC, InsertDate ";
$stmt = $conn->prepare($sql);
$stmt->execute();
$aResults = $stmt->fetchAll(PDO::FETCH_NUM);
$stmt = null;

$strMenuImages = null;
if (is_array($aResults)) {
    $strNavPath = "reports.php?";
    if (empty($strReportNavigationTitle)) {
        $strReportNavigationTitle = "Research Reports bb";
    } ?>
	<section class="jqNavMainToBeCloned">
		<h2 class="head slide_up jqToggleNextRight"><span><?= $strReportNavigationTitle ?></span></h2>
		<div class="sxAccordionNav">
			<ul>
			<?php
            $iRows = count($aResults);
    for ($r = 0; $r < $iRows; $r++) {
        $intProjectID = $aResults[$r][0];
        $strProjectName = $aResults[$r][1];
        $strClass = "";
        if ($intProjectID == $int_ProjectID) {
            $strClass = 'class="open" ';
            $strMenuImages = $aResults[$r][2];
        } ?>
					<li><a <?=$strClass?>href="<?= $strNavPath ?>projectid=<?= $intProjectID ?>"><?= $strProjectName ?></a></li>
				<?php
    } ?>
			</ul>
        </div>
	</section>
<?php
}
$aResults = null;

if (intval($int_ProjectID) > 0 && !empty($strMenuImages)) {
    echo '<section>';
    if (strpos($strMenuImages, ";") == 0) {
        get_Any_Media($strMenuImages, "Center", "");
    } else {
		$arrTemp = explode(";",$strMenuImages);
        for ($p = 0; $p < count($arrTemp); $p++) {
            get_Any_Media(trim($arrTemp[$p]), "Center", "");
        }
    }
    echo '</section>';
}
?>