<?php

if ($radio_UseForum == False || ($radio_LoginToReadForum && $radio___ForumMemberIsActive === false)) {
	header("Location: index.php");
	exit();
}
$intArticleID = $_GET["articleID"] ? (int) $_GET["articleID"] : 0;

if (intval($intArticleID) == 0) {
	header("Location: index.php");
	exit();
}

if (empty($strExport)) { ?>
	<div style="margin: 20px;">
		<div style="font-size: 0.9em; background: #eee; padding: 5px;">
			<a target="_top" href="sx_PrintPage.php?articleID=<?= $intArticleID ?>&export=print"><?= lngSavePrintInPDF ?></a> |
			<a target="_top" href="sx_PrintPage.php?articleID=<?= $intArticleID ?>&export=word"><?= lngSaveInWord ?></a> |
			<a target="_top" href="sx_PrintPage.php?articleID=<?= $intArticleID ?>&export=html"><?= lngSaveInHTML ?></a>
		</div>
	<?php
} ?>

<h4><?= lngForum ?> - <?= str_SiteTitle ?></h4>
	<?php
	$sql = "SELECT fa.ForumID, f.ForumTheme{$str_LangNr} AS ForumTheme, 
		fa.FirstName, fa.LastName, fa.InsertDate, 
		fa.Title, fa.TextBody 
	FROM forum AS f 
	INNER JOIN forum_articles AS fa ON f.ForumID = fa.ForumID 
	WHERE fa.InsertID = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intArticleID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) { ?>
		<h1><?= LNG_Forum_Theme . ": " . $rs["ForumID"] . ": " . $rs["ForumTheme"] ?></h1>
		<h2><?= lngInitialArticle . ": " . $rs["Title"] ?></h2>
		<h3><?= $rs["FirstName"] . " " . $rs["LastName"] . ", " . $rs["InsertDate"] ?></h3>
		<?= $rs["TextBody"] ?>
	<?php }
	$stmt = null;
	$rs = null;

	$sql = "SELECT FirstName, LastName, InsertDate, Title, TextBody 
	FROM forum_articles 
	WHERE ResponseID = ? ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intArticleID]);
	$r = 1;
	while ($rs = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
		<h2><?= lngForumResponse ?>: <?= $r ?> - <?= $rs["Title"] ?></h2>
		<h3><?= $rs["FirstName"] . " " . $rs["LastName"] ?>, <?= $rs["InsertDate"] ?></h3>
		<?= $rs["TextBody"] ?>
	<?php
		$r++;
	}
	$rs = null; ?>
	<hr>
	<p style="text-align: center">
		<?= lngPrintedDate ?>: <?= Date("Y-m-d") ?><br>
		<?= lngFromWebPage ?>: <b><?= str_SiteTitle ?></b><br>
		<?= sx_LOCATION ?>
	</p>

	<?php if ($strExport == "") { ?>
	</div>
<?php
	} ?>
</body>

</html>
<?php
if ($strExport == "print") { ?>
	<script>
		window.print();
	</script>
<?php
}
