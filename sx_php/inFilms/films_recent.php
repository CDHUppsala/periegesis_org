<section>
	<?php
	if ($strFilmFirstPageTitle == "") {
		$strFilmFirstPageTitle = $str_FilmMenuTitle;
	} ?>
	<h1 class="head"><span><?= $strFilmFirstPageTitle ?></span></h1>
	<?php
	if ($memoFilmNote != "") { ?>
		<div class="bg_grey"><?= $memoFilmNote ?></div>
	<?php
	} ?>
	<div class="tabs jqTabs">
		<?php
		if (is_array($getFilms)) {
			$iRows = count($getFilms) ?>
			<ul class="tabs_options">
				<?php
				$i = 0;
				$iLoop = 0;
				for ($r = 0; $r < $iRows; $r++) {
					$i_FilmGroupID = $getFilms[$r][1];
					if (intval($iLoop) != intval($i_FilmGroupID)) {
						if ($i == 0) {
							$strTabClass = "selected";
						} else {
							$strTabClass = "";
						} ?>
						<li class="<?= $strTabClass ?>"><?= $getFilms[$r][2] ?></li>
				<?php
						$i++;
					}
					$iLoop = $i_FilmGroupID;
				} ?>
			</ul>
			<ul class="tabs_content">
				<?php
				$iLoop = 0;
				for ($r = 0; $r < $iRows; $r++) {

					include __DIR__ . "/films_variables.php";

					if (intval($intFilmGroupID) != intval($iLoop)) {
						if ($r == 0) {
							$strDisplay = "block";
						} else {
							$strDisplay = "none";
						}
						if ($r > 0) { ?>
							</li>
						<?php } ?>
						<li class="text_normal" style="display: <?= $strDisplay ?>">
						<?php
					}
					$iLoop = $intFilmGroupID ?>
						<article>
							<?php
							if ($strFilmImage != "") {
								$cWords = sx_getSanitizedText($strTitle) ?>
								<figure class="image_left" data-lightbox="img_<?= $iLoop ?>">
										<img alt="<?= $cWords ?>" src="../images/<?= $strFilmImage ?>">
									<figcaption><?php sx_getFilmStars($intFilmID, True) ?></figcaption>
								</figure>
							<?php
							}
							echo "<h2>" . $str_Titles . "</h2>";
							echo "<p>" . $str_FilmAbstract . "</p>";

							if (!empty($memoNotes)) {
								echo $memoNotes;
							}
							if ($strReviewURL != "") {
								echo "<p>";
								sx_getLinkTagsForFilms($strReviewURL, $strReviewTitle);
								echo "</p>";
							}
							if ($strTrailerURL != "") {
								echo "<p>";
								sx_getLinkTagsForFilms($strTrailerURL, $strTrailerTitle);
								echo "</p>";
							}
							if ($strExternalLink != "") {
								echo "<p>";
								sx_getLinkTagsForFilms($strExternalLink, $strExternalLinkTitle);
								echo "</p>";
							}
							if ($radioAllowSurveys) {
								echo "<p>";
								sx_getFilmStars($intFilmID, true);
								echo "</p>";
							}
							?>
						</article>
					<?php
				} ?>
						</li>
			</ul>
		<?php }
		$getFilms = null;
		?>
	</div>
</section>