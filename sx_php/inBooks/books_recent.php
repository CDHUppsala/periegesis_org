<section>
	<h1 class="head"><span><?= $str_BooksLinkTitle ?></span></h1>
	<?php
	if ($memoBibliographyNote != "") { ?>
		<div class="bg_grey"><?= $memoBibliographyNote ?></div>
	<?php
	} ?>
	<div class="tabs jqTabs">
		<h2><span><?= $strBooksFirstPageTitle ?></span></h2>
		<?php
		if (is_array($getBooks)) {
			$iRows = count($getBooks) ?>
			<ul class="tabs_options">
				<?php
				$i = 0;
				$iLoop = 0;
				for ($r = 0; $r < $iRows; $r++) {
					$i_BookGroupID = $getBooks[$r][2];
					if (intval($iLoop) != intval($i_BookGroupID)) {
						if ($i == 0) {
							$strTabClass = "selected";
						} else {
							$strTabClass = "";
						} ?>
						<li class="<?= $strTabClass ?>"><?= $getBooks[$r][3] ?></li>
				<?php
						$i++;
					}
					$iLoop = $i_BookGroupID;
				} ?>
			</ul>

			<ul class="tabs_content">
				<?php
				$iLoop = 0;
				for ($r = 0; $r < $iRows; $r++) {

					include __DIR__ . "/books_variables.php";

					if (empty($memoPromotionNotes)) {
						$memoPromotionNotes = $memoNotes;
					}

					if (intval($intBookGroupID) != intval($iLoop)) {
						if ($r == 0) {
							$strDisplay = "block";
						} else {
							$strDisplay = "none";
						}
						if ($r > 0) {
							echo '</li>';
						} ?>
						<li style="display: <?= $strDisplay ?>">
						<?php
					}
					$iLoop = $intBookGroupID
						?>
						<article class="text_normal text_small grid_12">
							<?php
							if ($strBookImage != "") {
								$altToImage = sx_getSanitizedText($strAuthorNames . " " . $strTitle);
								$altToImage = str_replace("-", " ", $altToImage);
							?>
								<figure class="image_left" data-lightbox="img_Books">
									<img alt="<?= $altToImage ?>" src="../images/<?= $strBookImage ?>">
									<figcaption><?php sx_getBookStars($intBookID, True) ?></figcaption>
								</figure>
							<?php
							} ?>
							<div>
								<h3><a title="<?= lngViewDetails ?>" href="books.php?bookID=<?= $intBookID ?>"><?= $strTitle . $strSubTitle ?></a></h3>
								<p><b><?= $sAuthorsName . $strEditors ?></b> <?= $strJournalName . $strJournalIssue . $strPublisher . $strPublicationYear . $sPages ?></p>
								<?php
								if (!empty($memoPromotionNotes)) {
									echo $memoPromotionNotes;
								}

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
								sx_getBookStars($intBookID, True);
								?>
							</div>
						</article>
					<?php
				} ?>
						</li>
			</ul>
		<?php
		}
		$getBooks = null;
		?>
	</div>
</section>