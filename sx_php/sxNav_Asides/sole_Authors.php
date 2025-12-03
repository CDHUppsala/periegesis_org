<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

if ($radio_ShowTextsByAuthors) { ?>
	<section class="jqNavSideToBeCloned">
		<h2 class="head slide_down jqToggleNextRight"><span><?= $strTextsByAuthorsTitle ?></span></h2>
		<div class="nav_aside" style="display: none">
			<?php
			sx_GetNavAuthors("block");
			?>
		</div>
	</section>
<?php
} ?>