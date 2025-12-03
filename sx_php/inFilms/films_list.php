<section>
	<?php

	if (!is_array($getFilms)) {
		echo "<h2>No recods available</h2>";
	} else {
		$iRows = count($getFilms);
		$iLoop = 0;
		for ($r = 0; $r < $iRows; $r++) {

			include __DIR__ . "/films_variables.php";

			if ($r == 0) { ?>
				<div class="print float_right">
					<?php
					sx_getPrintFilmList();
					?>
				</div>
				<h1 class="head"><span><?= lngDvd . " - " . $strRequestTitle ?></span></h1>
				<div class="align_right" id="jqSortTrigger">
					<?= lngSortBy ?>:
					<button data-id="id"><?= lngRecent ?></button>
					<button data-id="year"><?= lngYear ?></button>
					<button data-id="title"><?= LNG__Title ?></button>
					<button class="order_desc" disabled></button>
				</div>
				<article class="text_normal">
				<?php
			}
			if (intval($intFilmGroupID) != intval($iLoop)) {
				if ($r > 0) {
					echo "</div>";
				} ?>
					<h2 class="head"><span><?= $strFilmGroupName ?></span></h2>
					<div class="jqSortWrapper">
					<?php
				}

				$iLoop = $intFilmGroupID;
				// Sort items according to data-* criteria
					?>
					<div class="film_list" data-id="<?= $intFilmID ?>" data-title="<?= htmlspecialchars(trim(substr($strTitle, 0, 12))) ?>" data-year="<?= $strProductionYear ?>">
						<?php
						if (!empty($strFilmImage)) {
							$cWords = sx_getSanitizedText($strTitle) ?>
							<figure data-lightbox="imgList">
								<img alt="<?= $cWords ?>" src="../images/<?= $strFilmImage ?>">
								<figcaption><?php sx_getFilmStars($intFilmID, True) ?></figcaption>
							</figure>
						<?php
						}
						echo '<div>';
						echo "<h2>" . $str_Titles . "</h2>";
						echo "<p>" . $str_FilmAbstract . "</p>";

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
						echo '</div>';
						?>
					</div>
				<?php
			}
				?>
					</div>
				</article>
			<?php }
		$getFilms = null;


			?>
</section>