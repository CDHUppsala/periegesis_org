<body>
<?php

function sx_getReport($id)
{
	$return = null;
    $conn = dbconn();
    $sql = "SELECT
	r.ProjectID,
	p.ProjectName" . str_LangNr . " AS ProjectName,
	p.ProjectSubName" . str_LangNr . " AS ProjectSubName,
	r.ChapterName,
	r.SubChapterName,
	r.Title,
	r.SubTitle,
	r.InsertDate,
	r.MediaTopURL,
	r.ImagesFromFolder,
	r.MediaTopNotes,
	r.MediaRightURL,
	r.MediaRightNotes,
	r.PDFArchiveID,
	r.FilesForDownload,
	r.ReportNotes
	FROM report_projects AS p 
		INNER JOIN reports as r
		ON p.ProjectID = r.ProjectID
	WHERE r.ReportID = ? AND r.Hidden = False " . str_LanguageAnd;
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);
    $return = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt = null;
    return $return;
}

if (empty($strExport)) {?>
<div style="margin: 12px 20px;">
	<a href="reports.php"><?=lngHomePage?></a> |
	<a target="_top" href="sx_PrintPage.php?reportid=<?= $int_ReportID ?>&export=print"><?=lngSavePrintInPDF?></a> |
	<a target="_top" href="sx_PrintPage.php?reportid=<?= $int_ReportID ?>&export=word"><?=lngSaveInWord?></a> |
	<a target="_top" href="sx_PrintPage.php?reportid=<?= $int_ReportID ?>&export=html"><?=lngSaveInHTML?></a>
<hr>
<?php }

$rsReport = sx_getReport($int_ReportID);
$radioTemp = false;
if (is_array($rsReport)) {
	$radioTemp = true;
	$int_ProjectID = $rsReport["ProjectID"];
	$strProjectName = $rsReport["ProjectName"];
	$strProjectSubName = $rsReport["ProjectSubName"];
	$strChapterName = $rsReport["ChapterName"];
	$strSubChapterName = $rsReport["SubChapterName"];
	$strTitle = $rsReport["Title"];
	$strSubTitle = $rsReport["SubTitle"];
	$dateInsertDate = $rsReport["InsertDate"];
	$strMediaTopURL = $rsReport["MediaTopURL"];
	$strMediaTopNotes = $rsReport["MediaTopNotes"];
	$strMediaRightURL = $rsReport["MediaRightURL"];
	$strMediaRightNotes = $rsReport["MediaRightNotes"];

	$strFilesForDownload = $rsReport["FilesForDownload"];
	$memoReportNotes = $rsReport["ReportNotes"];
}
$rsReport = null;


if(!empty($strSubChapterName)) {$strSubChapterName = "/". $strSubChapterName;}

if ($radioTemp) { ?>
	<h4><?=str_SiteTitle ?></h4>
	<hr>
	<h1><?= $strProjectName ?></h1>
	<?php
	if (!empty($strProjectSubName)) { ?>
		<h2><?= $strProjectSubName ?></h2>
	<?php
	} ?>
	<h3><?= lngChapter .": ". $strChapterName . $strSubChapterName ?></h3>
    <hr>
	<h2><?= $strTitle ?></h2>
	<?php
	if (!empty($strSubTitle)) { ?>
		<h4><?= $strSubTitle ?></h4>
    <?php }
    if ($dateInsertDate != "") {?>
        <h4><?=$dateInsertDate ?></h4>
    <?php }
	echo $memoReportNotes;
	if (!empty($strMediaTopURL)) {
		get_Images_To_Print($strMediaTopURL, $strMediaTopNotes);
        echo "<hr>";
	}
 	if (!empty($strMediaRightURL)) {
		get_Images_To_Print($strMediaRightURL, $strMediaRightNotes);
        echo "<hr>";
    } ?>
	<p style="text-align: center;">
		<?=lngPrintedDate?>&nbsp;<?=Date("Y-m-d")?><br>
		<?=lngFromWebPage?>&nbsp;<b><?=str_SiteTitle?></b><br>
		<?= $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>
	</p>
<?php
}else{?>
	<h3><?=lngTextDoesNotExist?></h3>
	<?=lngCloseWindowReturnToSite?>
<?php
}
 
if (empty($strExport)) {?>
</div>
<?php }?>
</body>
</html>
<?php

if ($strExport == "print") {?>
<script>
	window.print();
</script>
<?php }?>
