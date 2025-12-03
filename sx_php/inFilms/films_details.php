<?php
if (intval($iFilmID) == 0) {
	header("Location: index.php");
	exit();
}

if (!is_array($getFilms)) {
	echo "<section><h2>No recods available</h2></section>";
} else {
	/**
	 * Set the Value for the Record Roe to 0 to get the vaues of the first (and only) record
	 */
	$r = 0;

	include __DIR__ . "/films_variables.php";

?>
	<section>
		<div class="print float_right">
			<?php
			sx_getBackArrow();
			sx_getPrintFilmList(); ?>
		</div>
		<h1 class="head"><span><?= lngDvd . " / " . $strRequestTitle ?></span></h1>
		<article>
			<h2><?= $str_Titles ?></h2>
			<?php if (!empty($strFilmImage)) {
				$cWords = sx_getSanitizedText($strTitle)
			?>
				<figure class="image_left" data-lightbox="img_<?= $intFilmID ?>">
					<img alt="<?= $cWords ?>" src="../images/<?= $strFilmImage ?>">
				</figure>
			<?php
			} ?>
			<div class="text">
				<div class="text_max_width">
					<?php
					echo "<p>" . $str_FilmAbstract . "</p>";
					if (!empty($memoNotes)) {
						echo $memoNotes;
					} ?>
					<div class="align_right">
						<?php
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
						?>
					</div>
				</div>
			</div>
		</article>
	</section>
	<?php

	$getFilms = null;

	if ($radioAllowSurveys || $radioAllowComments) { ?>
		<div class="comments" id="comment">
			<?php
			if ($radioAllowSurveys) {
				include "sxFilmSurveys/survey.php";
			}
			if ($radioAllowComments) {
				include "sxFilmComments/_include_comments.php";
			} ?>
		</div>
	<?php
	} ?>
<?php
} ?>