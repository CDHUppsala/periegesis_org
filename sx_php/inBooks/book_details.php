<?php
if (intval($iBookID) == 0) {
	header("Location: index.php");
	exit();
}

if (!is_array($getBooks)) {
	echo "<section><h2>No recods available</h2></section>";
} else {
	/**
	 * Set the Value for the Record Row to 0
	 *   to get the values of the first (and only) record
	 */
	$r = 0;

	include  __DIR__ . "/books_variables.php";

	if (empty($memoNotes)) {
		$memoNotes = $memoPromotionNotes;
	} ?>
	<section>
		<div class="print float_right">
			<?php
			sx_getBackArrow();
			sx_getPrintBookList(); ?>
		</div>
		<h1 class="head"><span><?= lngBibliographyIn . " / " . $strRequestTitle ?></span></h1>
		<article>
			<h2><?= $strTitle . $strSubTitle ?></h2>
			<h3><?= $sAuthorsName . $strEditors . $strPublicationYear . $strJournalName . $strJournalIssue . $strPublisher . $sPages ?></h3>
			<?php if (!empty($strBookImage)) {
				$altToImage = sx_getSanitizedText($strAuthorNames . " " . $strTitle);
				$altToImage = str_replace("-", " ", $altToImage);
			?>
				<figure class="image_left" data-lightbox="img_<?= $intBookID ?>">
					<img alt="<?= $altToImage ?>" src="../images/<?= $strBookImage ?>">
				</figure>
			<?php
			} ?>
			<div class="text">
				<div class="text_max_width">
					<?php
					if ($memoNotes != "") {
						echo $memoNotes;
					} ?>
					<div class="align_right">
						<?php
						if ($strISBN != "") {
							echo "<p><b>ISBN:</b> " . $strISBN . "</p>";
						}
						if ($strExtractURL != "") {
							echo "<p>";
							sx_getLinkTagsForBooks($strExtractURL, $strExtractTitle);
							echo "</p>";
						}
						if ($strReviewURL != "") {
							echo "<p>";
							sx_getLinkTagsForBooks($strReviewURL, $strReviewTitle);
							echo "</p>";
						}
						if ($strExternalLink != "") {
							echo "<p>";
							sx_getLinkTagsForBooks($strExternalLink, $strExternalLinkTitle);
							echo "</p>";
						}
						if ($strPlaceName != "") {
							echo "<p><b>" . lngPlaceToFind . "</b> " . $strPlaceName . $strPlaceCode . "</p>";
						}
						sx_getBookStars($intBookID, true); ?>
					</div>
				</div>
			</div>
		</article>
	</section>
	<?php
	$getBooks = null;
	?>
<?php
} ?>