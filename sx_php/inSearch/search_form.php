<section>
	<h1 class="head"><span><?= lngSearch . " - " . lngTexts ?></span></h1>
	<form class="form_fieldsets_grid" action="search.php" method="POST" name="FormSearchText">
		<fieldset>
			<?php if ($strSearchPlace == "InTitle" || strlen($strSearchPlace) == 0) {
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
			<input type="radio" name="SearchPlace" value="InAll" <?= $checkThis ?>> <span><?= (lngSearchAll) ?></label>
		</fieldset>
		<fieldset>
			<select size="1" name="Datum">
				<option value="0"><?= (lngSearchAllDates) ?></option>
				<?php if (intval($intDatum) == 1) {
					$selectThis = " selected";
				} else {
					$selectThis = "";
				} ?>
				<option value="1" <?= $selectThis ?>><?= (lngSearchLastMonth) ?></option>
				<?php if (intval($intDatum) == 3) {
					$selectThis = " selected";
				} else {
					$selectThis = "";
				} ?>
				<option value="3" <?= $selectThis ?>><?= (lngSearchlastQuarter) ?></option>
				<?php if (intval($intDatum) == 6) {
					$selectThis = " selected";
				} else {
					$selectThis = "";
				} ?>
				<option value="6" <?= $selectThis ?>><?= (lngSearchLastSexMonth) ?></option>
				<?php if (intval($intDatum) == 12) {
					$selectThis = " selected";
				} else {
					$selectThis = "";
				} ?>
				<option value="12" <?= $selectThis ?>><?= (lngSearchLastYear) ?></option>
				<?php
				if (!isset($_SESSION["FirstYears"])) {
					$intFirstYear = return_Year(date('Y-m-d'));
					$sql = "SELECT YEAR(PublishedDate) AS FirstYear 
						FROM " . sx_TextTableVersion . " " . str_LanguageWhere . "
						ORDER BY TextID ASC LIMIT 1";
					$stmt = $conn->query($sql);
					$rs = $stmt->fetch(PDO::FETCH_ASSOC);
					if ($rs) {
						$iFirstYear = $rs["FirstYear"];
					}
					$rs = null;
					$stmt = null;
					if (intval($iFirstYear) > 0) {
						$intFirstYear = $iFirstYear;
					}
					$_SESSION["FirstYears"] = $intFirstYear;
				} else {
					$intFirstYear = $_SESSION["FirstYears"];
				}

				$iTemp = return_Year(date('Y-m-d'));
				for ($i = $iTemp; $i > ($intFirstYear - 1); $i--) {
					if (intval($intDatum) == intval($i)) {
						$selectThis = " selected";
					} else {
						$selectThis = "";
					}
				?>
					<option value="<?= $i ?>" <?= $selectThis ?>><?= (lngYear . " " . $i) ?></option>
				<?php } ?>
			</select><br>
			<select size="1" name="GroupID">
				<option value="0"><?= lngSelectAllGroups ?></option>
				<?php
				$sql = "SELECT GroupID, GroupName" . str_LangNr . " AS GroupName 
						FROM text_groups 
						WHERE Hidden = False " . str_LoginToReadAnd . "
						ORDER BY GroupName";
				$stmt = $conn->query($sql);
				$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if ($rs) {
					$iRows = count($rs);
					for ($r = 0; $r < $iRows; $r++) {
						$iTemp = intval($rs[$r]["GroupID"]);
						if (intval($intGroupID) == $iTemp) {
							$selectThis = " selected";
						} else {
							$selectThis = "";
						} ?>
						<option value="<?= $rs[$r]["GroupID"] ?>" <?= $selectThis ?>><?= ($rs[$r]["GroupName"]) ?></option>
				<?php
					}
				}
				$rs = null;
				$stmt = null;
				?>
			</select><br>
			<input type="text" name="SearchText" value="<?= $strSearch ?>" placeholder="<?= lngSearch . " - " . lngTexts ?>">
		</fieldset>
		<fieldset>
			<?php if ($strSearchType == "Exact" || strlen($strSearchType) == 0) {
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
		</fieldset>
		<fieldset>
			<p class="align_right"><input type="submit" value="<?= (lngSearch) ?>" name="InnerSearch"></p>
		</fieldset>
	</form>
</section>