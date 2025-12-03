<section>
	<h1 class="head"><span><?= lngSearch ?></span></h1>
	<form class="form_fieldsets_grid" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" name="FormSearchText">
		<fieldset>
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
					$sql = "SELECT YEAR(InsertDate) AS FirstYear 
						FROM articles $str_LanguageWhere 
						ORDER BY ArticleID ASC LIMIT 1";
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
			<select size="1" name="GroupID" id="jq_SelectArticleGroupID">
				<option value="0"><?= lngSelectAllGroups ?></option>
				<?php
				$sql = "SELECT ArticleGroupID, GroupName" . str_LangNr . " AS GroupName 
						FROM article_groups 
						WHERE Hidden = False
						ORDER BY GroupName";
				$stmt = $conn->query($sql);
				$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if ($rs) {
					$iRows = count($rs);
					for ($r = 0; $r < $iRows; $r++) {
						$iTemp = intval($rs[$r]["ArticleGroupID"]);
						$selectThis = "";
						if (intval($intGroupID) == $iTemp) {
							$selectThis = " selected";
						} ?>
						<option value="<?= $iTemp ?>" <?= $selectThis ?>><?= ($rs[$r]["GroupName"]) ?></option>
				<?php
					}
				}
				$rs = null;
				$stmt = null;
				?>
			</select>
			<select size="1" name="CategoryID" id="SelectCategoryID">
				<option value="0"><?= lngSelectAllCategories ?></option>
				<?php
				if (intval($intGroupID) > 0) {
					$aResults = null;
					$sql = "SELECT ArticleCategoryID, CategoryName" . str_LangNr . " AS CategoryName 
    					FROM article_categories 
    					WHERE ArticleGroupID = ? AND Hidden = False
					    ORDER BY CategoryName";
					$stmt = $conn->prepare($sql);
					$stmt->execute([$intGroupID]);
					$rs = $stmt->fetchAll(PDO::FETCH_NUM);
					if ($rs) {
						$aResults = $rs;
					}
					$stmt = null;
					$rs = null;
					if (is_array($aResults) && !empty($aResults)) {
						$iRows = count($aResults);
						for ($r = 0; $r < $iRows; $r++) {
							$iTemp = intval($aResults[$r][0]);
							$selectThis = "";
							if (intval($intCategoryID) == $iTemp) {
								$selectThis = " selected";
							} ?>
							<option value="<?= $iTemp ?>"<?php echo $selectThis ?>><?= $aResults[$r][1] ?></option>
				<?php
						}
					}
				}
				?>

			</select>

		</fieldset>
		<fieldset>
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
		</fieldset>
		<fieldset>
			<p><input type="text" name="SearchText" value="<?= $strSearch ?>" placeholder="<?= lngSearchKeyWord ?>"></p>
			<p class="align_right"><input type="submit" value="<?= (lngSearch) ?>" name="InnerSearch"></p>
		</fieldset>
	</form>
</section>

<script>
	// Get anly the months of a year that contain an article
	$sx(document).ready(function() {
		$sx("#jq_SelectArticleGroupID").on("change", function() {
			var $Data = "groupID=" + $sx(this).val();
			$sx.ajax({
				url: "ajax_Select_Article_Categoris.php",
				cache: false,
				data: $Data,
				dataType: "html",
				scriptCharset: "utf-8",
				type: "GET",
				success: function(result) {
					$sx("#SelectCategoryID").html(result).focus();
				},
				error: function(xhr, status, error) {
					alert(xhr.responseText);
				}
			});
		});

	});
</script>