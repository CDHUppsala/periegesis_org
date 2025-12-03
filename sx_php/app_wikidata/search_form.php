<section id="Search_Anchor">
	<h1 class="head"><span>Classical Antiquity Person Collection</span></h1>
	<h2>A dataset of about 24 000 Wikidata Person IDs that might prove useful for annotating persons in an ancient Greek literary text</h2>

	<form class="form_fieldsets_grid" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>#Search_Anchor" method="POST" name="SearchTextForm">
		<?php
		$strChecked = "";
		if ($radioShowPausaniasPersons == true) {
			$strChecked = " checked";
		}
		?>
		<fieldset class="align_right" style="grid-column: span 2;">Search only persons mentioned by Pausanias in Recogito Annotation 
		<input type="checkbox" name="ShowPausaniasPersons" value="Yes"<?php echo $strChecked ?>></fieldset>
		<fieldset style="grid-column: span 2;" class="flex_between">
			<?php
			$checkThis = '';
			if ($strSearchPlace == 'All' || empty($strSearchPlace)) {
				$checkThis = " checked";
			} ?>
			<label>
				<input type="radio" name="SearchPlace" value="All" <?= $checkThis ?>> <span><?= (lngSearchAll) ?>
			</label>
			<?php
			$intCount = count(ARR_SearchableFields);
			for ($i = 0; $i < $intCount; $i++) {
				$sFiledValue = ARR_SearchableFields[$i];
				$sFieldDisplay = sx_separateWordsWithCamelCase($sFiledValue);
				$sFieldDisplay = trim(str_replace('Label', '', $sFieldDisplay));
				if (strpos($sFieldDisplay, 'Description') !== false) {
					$sFieldDisplay = trim(str_replace('person', '', $sFieldDisplay));
				}
				$checkThis = "";
				if ($strSearchPlace == $sFiledValue) {
					$checkThis = " checked";
				} ?>
				<label>
					<input type="radio" name="SearchPlace" value="<?= $sFiledValue ?>" <?= $checkThis ?>> <span><?= $sFieldDisplay ?></span>
				</label>
			<?php
			} ?>
		</fieldset>
		<fieldset class="flex_between">
			<?php if ($strSearchType == "Exact" || empty($strSearchType)) {
				$checkThis = " checked";
			} else {
				$checkThis = "";
			} ?>
			<label>
				<input type="radio" name="SearchType" value="Exact" <?= $checkThis ?>> <span><?= lngSearchExactPhrase ?></span></label>
			<?php if ($strSearchType == "Every") {
				$checkThis = " checked";
			} else {
				$checkThis = "";
			} ?>
			<label>
				<input type="radio" name="SearchType" value="Every" <?= $checkThis ?>> <span><?= (lngSearchAllWords) ?></span></label>
			<?php if ($strSearchType == "Any") {
				$checkThis = " checked";
			} else {
				$checkThis = "";
			} ?>
			<label>
				<input type="radio" name="SearchType" value="Any" <?= $checkThis ?>> <span><?= (lngSearchAnyWord) ?></span>
			</label>
		</fieldset>
		<fieldset class="flex_between">
			<label><input type="text" style="width: 100%" name="SearchText" value="<?= $strSearchText ?>" placeholder="<?= lngSearchKeyWord ?>"></label>
			<label><input type="submit" value="<?= (lngSearch) ?>" name="SearchTextSubmit"></label>
		</fieldset>
	</form>
</section>