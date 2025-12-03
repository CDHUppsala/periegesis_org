<?php
include __DIR__ . "/config_books.php";

?>

<body>
	<?php if ($strExport == "") { ?>
		<div style="margin: 20px">
			<div style="font-family: Verdana, Arial, helvetica; font-size: 9pt;">
				<a href="index.php"><?= lngHomePage ?></a> |
				<a target="_top" href="sx_PrintPage.php?<?= $_SERVER["QUERY_STRING"] ?>&export=print"><?= lngPrintText ?></a> |
				<a target="_top" href="sx_PrintPage.php?<?= $_SERVER["QUERY_STRING"] ?>&export=word"><?= lngSaveInWord ?></a> |
				<a target="_top" href="sx_PrintPage.php?<?= $_SERVER["QUERY_STRING"] ?>&export=html"><?= lngSaveInHTML ?></a>
			</div>
			<hr>
			<?php
		}

		if (!is_array($getBooks)) {
			echo "<h2>No recods available</h2>";
		} else {
			$iRows = count($getBooks);
			$iLoop = 0;
			$z = 0;
			for ($r = 0; $r < $iRows; $r++) {

				include __DIR__ . "/books_variables.php";

				if (empty($memoNotes)) {
					$memoNotes = $memoPromotionNotes;
				}

				if (floor($r) == 0) { ?>
					<h1><span><?= lngBibliography . " - " . $strRequestTitle ?></span></h1>
					<dl> <?php
						}

						echo "<dt>" . "\n";
						$sTitle = '<a title="' . lngViewDetails . '" href="' . sx_LANGUAGE_PATH . "books.php?bookID=" . $intBookID . '"><b>' . $strTitle . "</b></a>";
						if (intval($iBookID) > 0) {
							echo "<h2>" . $sTitle . $strSubTitle . "</h2>";
						}
						echo "<b>" . $sAuthorsName . "</b>" . $strEditors . " (" . $strPublicationYear . "), ";
						if (intval($iBookID) == 0) {
							echo $leftMark . $sTitle . $strSubTitle . $rightMark;
						}
						echo $strJournalName . $strJournalIssue . $strPublisher . $strPages;
						echo "</dt>" . "\n";
						echo "<dd>" . "\n";

						if ($memoNotes != "") {
							if ($strBookImage != "") { ?>
							<p><img alt="<?= $strTitle ?>" src="<?= sx_ROOT_HOST ?>/images/<?= $strBookImage ?>"></p>
				<?php
							}
							echo $memoNotes;
						}

						echo "<p>" . "\n";
						if ($strISBN != "") {
							echo "<b>ISBN:</b> " . $strISBN . "<br />";
						}
						if ($strExtractURL != "") {
							sx_getLinkTagsForBooks($strExtractURL, $strExtractTitle);
							echo "<br />";
						}
						if (!empty($strReviewUR)) {
							sx_getLinkTagsForBooks($strReviewURL, $strReviewTitle);
							echo "<br />";
						}
						if ($strExternalLink != "") {
							sx_getLinkTagsForBooks($strExternalLink, $strExternalLinkTitle);
							echo "<br />";
						}
						if ($strPlaceName != "") {
							echo "<b>" . lngPlaceToFind . "</b> " . $strPlaceName . $strPlaceCode . "<br />";
						}
						sx_getBookStars($intBookID, False);
						echo "</p>" . "\n";
						echo "</dd>" . "\n";
					}
					$getBooks = null;
				?>
					</dl>
				<?php
			}
			if ($strExport == "") { ?>
		</div>
	<?php } ?>
	<hr />
	<p style="text-align: center;">
		<?= lngPrintedDate . " " . date("Y-m-d") ?><br>
		<?= lngFromWebPage ?> <a href="<?= sx_HOST ?>"><?= str_SiteTitle ?></a><br>
		<?= urldecode(sx_LOCATION) ?>
	</p>
</body>

</html>
<?php

//====== EXPORT TEXT
$strSiteURL = $_SERVER["HTTP_HOST"];
$pos = strpos($strSiteURL, ".");
if ($pos > 0) {
	$strSiteURL = str_replace($strSiteURL, substr($strSiteURL, 0, $pos), "");
}
$pos = strpos($strSiteURL, ".");
if ($pos > 0) {
	$strSiteURL = substr($strSiteURL, 0, $pos - 1);
}

$strRequestTitle = str_replace("-", "", $strRequestTitle . "");
$strRequestTitle = trim($strRequestTitle);
if (strpos($strRequestTitle, ":") > 0) {
	$strRequestTitle = str_replace(":", "", $strRequestTitle);
}
if (strpos($strRequestTitle, " ") > 0) {
	$strRequestTitle = str_replace(" ", "_", $strRequestTitle);
}
if (strpos($strRequestTitle, "/") > 0) {
	$strRequestTitle = str_replace("/", "_", $strRequestTitle);
}

if (!empty($strExport)) {
	if ($strExport == "word") {
		header('Content-Description: File Transfer');
		header("Content-type: application/msword; charset=utf-8");
		header("Content-Disposition: attachment;Filename=" . $strSiteURL . "_" . $strRequestTitle . ".doc");
	}
	if ($strExport == "html") {
		header("Content-Type: text/html");
		header("Content-Disposition: attachment; filename=" . $strSiteURL . "_" . $strRequestTitle . ".html;");
		header("Content-Transfer-Encoding: binary");
	}
}

if ($strExport == "print") { ?>
	<script>
		window.print();
	</script>
<?php } ?>