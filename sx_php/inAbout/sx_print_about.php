<body>
	<?php
	if (intval($intAboutID) == 0) {
		$intAboutID = 0;
	}

	if (empty($strExport)) { ?>
		<div style="margin: 12px 20px;">
			<a href="default.php"><?= lngHomePage ?></a> |
			<a target="_top" href="sx_PrintPage.php?aboutid=<?= $intAboutID ?>&export=print"><?= lngSavePrintInPDF ?></a> |
			<a target="_top" href="sx_PrintPage.php?aboutid=<?= $intAboutID ?>&export=word"><?= lngSaveInWord ?></a> |
			<a target="_top" href="sx_PrintPage.php?aboutid=<?= $intAboutID ?>&export=html"><?= lngSaveInHTML ?></a>
			<hr>
		<?php }
	$radioTemp = False;
	$sql = "SELECT Title, SubTitle, InsertDate, MediaTopURL, MediaTopNotes, MediaRightURL, MediaRightNotes, AboutNotes
	FROM about
	WHERE Hidden = False AND AboutID = ? " . str_LanguageAnd;
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intAboutID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radioTemp = True;
		$sTitle = $rs["Title"];
		$sSubTitle = $rs["SubTitle"];
		$dInsertDate = $rs["InsertDate"];
		$strMediaTopURL = $rs["MediaTopURL"];
		$strMediaTopNotes = $rs["MediaTopNotes"];
		$strMediaRightURL = $rs["MediaRightURL"];
		$strMediaRightNotes = $rs["MediaRightNotes"];
		$memoAboutNotes = $rs["AboutNotes"];
	}
	$rs = null;
	$stmt = null;

	if (!empty($str_AboutGroupName)) {
		$sTitle = $str_AboutGroupName . ": " . $sTitle;
	}
	if ($radioTemp) { ?>
			<h4><?= str_SiteTitle ?></h4>
			<h1><?= $str_TextsAboutTitle ?></h1>
			<h2><?= $sTitle ?></h2>
			<?php
			if ($sSubTitle != "") { ?>
				<h3><?= $sSubTitle ?></h2>
				<?php }
			if ($dInsertDate != "") { ?>
					<h4><?= $dInsertDate ?></h4>
				<?php }
			echo "<hr>";
			echo $memoAboutNotes;
			if (!empty($strMediaTopURL)) {
				get_Images_To_Print($strMediaTopURL, $strMediaTopNotes);
			}
			if (!empty($strMediaRightURL)) {
				get_Images_To_Print($strMediaRightURL, $strMediaRightNotes);
			} ?>
				<hr>
				<p style="text-align: center;">
					<?= lngPrintedDate ?>&nbsp;<?= Date("Y-m-d") ?><br>
					<?= lngFromWebPage ?>&nbsp;<b><?= str_SiteTitle ?></b><br>
					<?= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ?>
				</p>
			<?php
		} else { ?>
				<h3><?= lngTextDoesNotExist ?></h3>
				<?= lngCloseWindowReturnToSite ?>
			<?php
		}

		if (empty($strExport)) { ?>
		</div>
	<?php } ?>
</body>

</html>
<?php
if ($strExport == "print") { ?>
	<script>
		window.print();
	</script>
<?php } ?>