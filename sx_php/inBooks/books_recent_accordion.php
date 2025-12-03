<section>
	<?php

	if ($memoBibliographyNote != "") {
		if ($str_BooksNavTitle == "") {
			$str_BooksNavTitle = lngBibliography;
		}
	?>
		<h1 class="head"><span><?= $str_BooksNavTitle ?></span></h1>
		<div class="text"><?= $memoBibliographyNote ?></div>
	<?php
	} ?>

	<div class="accordion jqAccordion">
		<h2 class="head"><span><?= $strBooksFirstPageTitle ?></span></h2>
		<?php

		if (!is_array($getBooks)) {
			echo "<h3>No results found!</h3>";
		} else { ?>
			<dl>
				<?php
				$iRows = count($getBooks);
				$iLoop = 0;
				for ($r = 0; $r < $iRows; $r++) {

					include __DIR__ . "/books_variables.php";

					if (empty($memoPromotionNotes)) {
						$memoPromotionNotes = $memoNotes;
					}

					if (intval($intBookGroupID) != intval($iLoop)) {
						$strDisplay = "none";
						$strSlected = "";
						if ($r == 0) {
							$strDisplay = "block";
							$strSlected = ' class="selected"';
						}
						if ($r > 0) {
							echo "</dd>";
						} ?>
						<dt<?= $strSlected ?>><?= $strBookGroupName ?></dt>
							<dd style="display: <?= $strDisplay ?>">
							<?php
						}
						$iLoop = $intBookGroupID;
							?>
							<article>
								<?php
								if ($strBookImage != "") {
									$altToImage = sx_getSanitizedText($strAuthorNames . " " . $strTitle);
									$altToImage = str_replace("-", " ", $altToImage);
								?>
									<figure class="image_left" data-lightbox="img_<?= $r ?>">
										<img alt="<?= $altToImage ?>" src="../images/<?= $strBookImage ?>">
										<figcaption>
											<?php sx_getBookStars($intBookID, True) ?>
										</figcaption>
									</figure> <?php
											} ?>
								<h3><a title="<?= lngViewDetails ?>" href="books.php?bookID=<?= $intBookID ?>"><?= $strTitle . $strSubTitle ?></a></h3>
								<p><b><?= $sAuthorsName . $strEditors ?></b> <?= $strJournalName . $strJournalIssue . $strPublisher . $strPublicationYear . $sPages ?></p>
								<?php
								if ($memoPromotionNotes != "") {
									echo $memoPromotionNotes;
								} ?>
								<div class="align_right marginTB">
									<?php
									if ($strISBN != "") {
										echo "<p><b>ID:</b> " . $strISBN . "</p>";
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
									sx_getBookStars($intBookID, True);
									?>
								</div>
							</article>
						<?php
					} ?>
							</dd>
			</dl>
		<?php }
		$getBooks = null;
		?>
	</div>
</section>