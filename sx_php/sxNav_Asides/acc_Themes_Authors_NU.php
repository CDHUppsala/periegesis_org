<?php
include_once __DIR__ . "/functions_Nav_Asides.php";

if ($radio_ShowTextsByThemes || $radio_ShowTextsByAuthors) {
	$displayThemes = "none";
	$displayAuthors = "none";
	$classThemes = "";
	$classAuthors = "";
	if (intval($int_ThemeID) > 0) {
		$displayThemes = "block";
		$classThemes = ' class="selected"';
	} elseif (intval($int_AuthorID) > 0) {
		$displayAuthors = "block";
		$classAuthors = ' class="selected"';
	}
?>
<div class="jqNavSideToBeCloned">
	<h2 class="head slide_down jqToggleNextRight"><span><?= lngTextArchiveAlternative ?></span></h2>
	<nav class="nav_aside jqNavAsideToggleNext" style="display: none">
	<?php
	if ($radio_ShowTextsByThemes) {?>
		<div<?=$classThemes?>><span><?=$str_TextsByThemesTitle ?></span></div>
		<?php
		sx_GetNavThemes($displayThemes);
	}
	if ($radio_ShowTextsByAuthors) {?>
		<div<?=$classAuthors?>><span><?=$strTextsByAuthorsTitle?></span></div>
		<?php
		sx_GetNavAuthors($displayAuthors);
	}?>
	</nav>
</div>
<?php
}?>
