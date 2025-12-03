<section>
	<?php

	if (!is_array($getBooks)) {
		echo "<h2>No recods available</h2>";
	} else {
		$iRows = count($getBooks);
		$iLoop = 0;
		for ($r = 0; $r < $iRows; $r++) {

			include __DIR__ . "/books_variables.php";

			if ($r == 0) { ?>
				<div class="print float_right">
					<?php
					//Don't return back to a Form Request
					sx_getSorting();
					sx_getPrintBookList();
					?>
				</div>
				<h1 class="head"><span><?= lngBibliography . " - " . $strRequestTitle ?></span></h1>
				<article>
					<div class="text_normal">
						<div class="text_max_width">
							<dl id="jqSortDefinitionList_Target" data-id="ASC">
						<?php
					}
					$iLoop = $intBookGroupID;

					$strTitle = '<a title="' . lngViewDetails . '" href="books.php?bookID=' . $intBookID . '">' . $leftMark . $strTitle . $rightMark . "</a>";
					echo "<dt>";
					echo  $sAuthorsName . $strEditors . $strPublicationYear;
					echo "</dt>";
					echo "<dd>";
					echo $strTitle . $strSubTitle;
					echo $strJournalName . $strJournalIssue . $strIntroduction . $strTranslator . $strPublisher . $sPages . "<br />";
					if (!empty($strISBN)) {
						echo "ISBN: " . $strISBN . "<br />";
					}
					if (!empty($strExtractURL)) {
						//True = default link to Application PDF-Archive - for numeric value
						sx_getLinkTagsForBooks($strExtractURL, $strExtractTitle, true);
						echo "<br />";
					}
					if (!empty($strReviewURL)) {
						sx_getLinkTagsForBooks($strReviewURL, $strReviewTitle);
						echo "<br />";
					}
					if (!empty($strExternalLink)) {
						//False = default link to articles - for numeric value
						sx_getLinkTagsForBooks($strExternalLink, $strExternalLinkTitle);
						echo "<br />";
					}
					if ($strPlaceName != "") {
						echo lngPlaceToFind . " " . $strPlaceName . $strPlaceCode . "<br />";
					}
					sx_getBookStars($intBookID, True);
					echo "</dd>";
				}
				$getBooks = null;
						?>
							</dl>
						</div>
					</div>
				</article>
			<?php } ?>
</section>