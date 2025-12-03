<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

/**
 * Texts by Theme, Author and Year/Month
 */
if ($radio_ShowTextsByThemes || $radio_ShowTextsByAuthors || $radio_ShowTextsByYearMonth) {

	$displayThemes = "none";
	$displayAuthors = "none";
	$displayYearMonth = "none";
	$classThemes = "";
	$classAuthors = "";
	$classYearMonth = "";
	$radioSectionDisplay = true;
	if (intval($int_ThemeID) > 0) {
		$displayThemes = "block";
		$classThemes = ' open"';
		$radioSectionDisplay = true;
	} elseif (intval($int_AuthorID) > 0) {
		$displayAuthors = "block";
		$classAuthors = ' open"';
		$radioSectionDisplay = true;
	} elseif (intval($int_Month) > 0) {
		$displayYearMonth = "block";
		$classYearMonth = ' open"';
		$radioSectionDisplay = true;
	}
	$strSlide =  "slide_down";
	$strDesplay = "none";
	if ($radioSectionDisplay) {
		$strSlide =  "slide_up";
		$strDesplay = "block";
	}
?>
	<section class="jqNavSideToBeCloned">
		<h2 class="head <?= $strSlide ?> jqToggleNextRight"><span><?= $str_ByTextsMenuTitle ?></span></h2>
		<nav class="sxAccordionNav jqAccordionNav" style="display: <?= $strDesplay ?>">
			<ul class="common_lists">
				<?php
				if ($radio_ShowTextsByThemes) { ?>
					<li>
						<div class="<?= $classThemes ?>"><span><?= $str_TextsByThemesTitle ?></span></div>
					<?php
					sx_GetNavThemes($displayThemes, $int_ThemeID);
					echo "</li>";
				}
				if ($radio_ShowTextsByAuthors) { ?>
					<li>
						<div class="<?= $classAuthors ?>"><span><?= $strTextsByAuthorsTitle ?></span></div>
					<?php
					sx_GetNavAuthors($displayAuthors, $int_AuthorID);
					echo "</li>";
				}
				if ($radio_ShowTextsByYearMonth) { ?>
					<li>
						<div class="<?= $classYearMonth ?>"><span><?= $str_TextsByYearMonthTitle ?></span></div>
					<?php
					include dirname(__DIR__) . "/inText_Calendar/textsByYearMonth.php";
					echo "</li>";
				} ?>
			</ul>
		</nav>
	</section>
<?php
} ?>