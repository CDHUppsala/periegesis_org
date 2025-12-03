<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

if ($radio_ShowTextsByThemes) { ?>
	<section class="jqNavSideToBeCloned">
		<h2 class="head slide_up jqToggleNextRight"><span><?= $str_TextsByThemesTitle ?></span></h2>
		<nav class="nav_aside jqAccordionNav">
			<?php
			sx_GetNavThemes("block");
			?>
		</nav>
	</section>
<?php
} ?>