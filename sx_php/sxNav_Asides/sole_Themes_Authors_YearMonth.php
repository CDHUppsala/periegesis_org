<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

if ($radio_ShowTextsByAuthors) { ?>
	<section class="jqNavSideToBeCloned">
		<h2 class="head slide_down jqToggleNextRight"><span><?= $strTextsByAuthorsTitle ?></span></h2>
		<div class="nav_aside jqAccordionNav" style="display: none">
			<?php
			sx_GetNavAuthors("block");
			?>
		</div>
	</section>
<?php
}
if ($radio_ShowTextsByThemes) { ?>
	<section class="jqNavSideToBeCloned">
		<h2 class="head slide_up jqToggleNextRight"><span><?= $str_TextsByThemesTitle ?></span></h2>
		<nav class="nav_aside jqAccordionNav" style="display: none">
			<?php
			sx_GetNavThemes("block");
			?>
		</nav>
	</section>
<?php
}
if ($radio_ShowTextsByYearMonth) { ?>
	<section class="jqNavSideToBeCloned">
		<h2 class="head slide_up jqToggleNextRight"><span><?= $str_TextsByYearMonthTitle ?></span></h2>
		<nav class="nav_aside jqAccordionNav" style="display: none">
			<?php
			include dirname(__DIR__) . "/inText_Calendar/textsByYearMonth.php";
			?>
		</nav>
	</section>
<?php
} ?>