<?php
include __DIR__ . "/config_films.php";
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
		if (!is_array($getFilms)) {
			echo "<h2>No recods available</h2>";
		} else {
			$iRows = count($getFilms);
			$iLoop = 0;

			for ($r = 0; $r < $iRows; $r++) {

				include __DIR__ . "/films_variables.php";

				if (floor($r) == 0) { ?>
					<h1><?= lngDvd . " - " . $strRequestTitle ?></h1>
				<?php
				}

				if (!isset($intFilmGroupID)) {
					$intFilmGroupID = 0;
				}
				if (intval($intFilmGroupID) != intval($iLoop)) { ?>
					<h2><?= $strFilmGroupName ?></h2>
				<?php
				}
				$iLoop = $intFilmGroupID;

				if (!empty($strFilmImage)) { ?>
					<h3><?= $str_Titles ?></h3>
					<table>
						<tr>
							<td style="width: 40%">
								<img alt="<?= $strTitles ?>" src="<?= sx_ROOT_HOST ?>/images/<?= $strFilmImage ?>"></a>
							</td>
							<td style="width: 60%">
								<p><?= $str_FilmAbstract ?></p>
							</td>
						</tr>
					</table>
			<?php
				} else {
					echo "<h3>" . $str_Titles . "</h3>";
					echo "<p>" . $str_FilmAbstract . "</p>";
				}
				if (!empty($memoNotes)) {
					echo $memoNotes;
				}

				if (!empty($strReviewURL)) {
					echo "<p>";
					sx_getLinkTagsForFilms($strReviewURL, $strReviewTitle);
					echo "</p>";
				}
				if (!empty($strTrailerURL)) {
					echo "<p>";
					sx_getLinkTagsForFilms($strTrailerURL, $strTrailerTitle);
					echo "</p>";
				}
				if (!empty($strExternalLink)) {
					echo "<p>";
					sx_getLinkTagsForFilms($strExternalLink, $strExternalLinkTitle);
					echo "</p>";
				}
				if ($radioAllowSurveys) {
					echo "<p>";
					sx_getFilmStars($intFilmID, true);
					echo "</p>";
				}
			}
			$getFilms = null;
		}

		if (empty($strExport)) { ?>
		</div>
	<?php
		} ?>
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