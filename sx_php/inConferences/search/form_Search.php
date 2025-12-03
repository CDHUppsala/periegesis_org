<section>
	<h1 class="head"><span><?= lngSearch . " " . lngConferencePapers ?></span></h1>
	<form class="form_fieldsets_grid" action="search.php" method="POST" name="FormSearchText">
		<fieldset>
			<div class="flex_between flex_nowrap flex_align_start ">
				<div>
					<?php if ($strSearchPlace == "InTitle" || empty($strSearchPlace)) {
						$checkThis = " checked";
					} else {
						$checkThis = "";
					} ?>
					<label>
						<input type="radio" name="SearchPlace" value="InTitle" <?= $checkThis ?>> <span><?= (lngSearchTitle) ?></span></label>
					<?php if ($strSearchPlace == "InName") {
						$checkThis = " checked";
					} else {
						$checkThis = "";
					} ?>
					<label>
						<input type="radio" name="SearchPlace" value="InName" <?= $checkThis ?>> <span><?= (lngSearchName) ?></span></label>
					<?php if ($strSearchPlace == "InText") {
						$checkThis = " checked";
					} else {
						$checkThis = "";
					} ?>
					<label>
						<input type="radio" name="SearchPlace" value="InText" <?= $checkThis ?>> <span><?= (lngSearchText) ?></span></label>
					<?php if ($strSearchPlace == "InAll") {
						$checkThis = " checked";
					} else {
						$checkThis = "";
					} ?>
					<label>
						<input type="radio" name="SearchPlace" value="InAll" <?= $checkThis ?>> <span><?= (lngSearchAll) ?></span></label>
				</div>

				<div>
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
						<input type="radio" name="SearchType" value="Any" <?= $checkThis ?>> <span><?= (lngSearchAnyWord) ?></span></label>
				</div>
			</div>
		</fieldset>

		<fieldset>
			<input type="text" style="width: 100%;" name="SearchText" value="<?= $strSearchLow ?>" placeholder="<?= lngSearch . " - " . lngTexts ?>">

			<p class="align_right"><input type="submit" value="<?= lngSearch ?>" name="InnerSearch"></p>
		</fieldset>
	</form>
</section>