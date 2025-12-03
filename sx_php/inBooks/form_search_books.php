<section class="jqNavSideToBeCloned">
	<h2 class="head"><span><?= lngSearch ?></span></h2>
	<form method="get" name="selectAuthor" class="formBookSearch align_center">
		<?php
		$aResults = null;
		$sql = "SELECT AuthorID, LastName, FirstName 
			FROM book_authors 
			ORDER BY LastName, FirstName ";
		$stmt = $conn->query($sql);
		$rs = $stmt->fetchAll(PDO::FETCH_NUM);
		if ($rs) {
			$aResults = $rs;
		}
		$rs = null;
		$stmt = null;

		if (is_array($aResults)) {
			$iRows = count($aResults) ?>
			<select class="jqSubmitSelectChange" name="selectedWriters" style="width: 100%; margin: 2px 0" size="1">
				<option selected="selected" value=""><?= lngSelectWriter ?></option>
				<?php
				for ($r = 0; $r < $iRows; $r++) { ?>
					<option value="books.php?wid=<?= $aResults[$r][0] ?>&wn=<?= urlencode($aResults[$r][1] . " " . $aResults[$r][2]) ?>"><?= $aResults[$r][1] . " " . $aResults[$r][2] ?></option>
				<?php
				} ?>
			</select>
		<?php
		}
		$aResults = null;

		$sql = "SELECT DISTINCT Publisher 
			FROM books 
			ORDER BY Publisher ";
		$stmt = $conn->query($sql);
		$rs = $stmt->fetchAll(PDO::FETCH_NUM);
		if ($rs) {
			$aResults = $rs;
		}
		$rs = null;
		$stmt = null;

		if (is_array($aResults)) {
			$iRows = count($aResults) ?>
			<select class="jqSubmitSelectChange" name="selectedPublisher" style="width: 100%; margin: 2px 0" size="1">
				<option selected="selected" value=""><?= lngSelectPublisher ?></option>
				<?php
				for ($r = 0; $r < $iRows; $r++) { ?>
					<option value="books.php?publisher=<?= $aResults[$r][0] ?>"><?= $aResults[$r][0] ?></option>
				<?php
				} ?>
			</select>
		<?php
		}
		$aResults = null;

		$sql = "SELECT DISTINCT PublicationYear 
			FROM books 
			ORDER BY PublicationYear DESC ";
		$stmt = $conn->query($sql);
		$rs = $stmt->fetchAll(PDO::FETCH_NUM);
		if ($rs) {
			$aResults = $rs;
		}
		$rs = null;
		$stmt = null;

		if (is_array($aResults)) {
			$iRows = count($aResults) ?>
			<select class="jqSubmitSelectChange" name="selectedYear" style="width: 100%; margin: 2px 0" size="1">
				<option selected="selected" value="books.php?year=9999"><?= lngSelect . " " . mb_strtolower(lngPublicationYear) ?></option>
				<?php
				for ($r = 0; $r < $iRows; $r++) { ?>
					<option value="books.php?year=<?= $aResults[$r][0] ?>"><?= $aResults[$r][0] ?></option>
				<?php
				} ?>
			</select>
		<?php
		}
		$aResults = null;

		$sql = "SELECT PlaceID, PlaceCode, PlaceName" . str_LangNr . " AS PlaceName 
			FROM book_place 
			ORDER BY Sorting DESC, PlaceCode ";
		$stmt = $conn->query($sql);
		$rs = $stmt->fetchAll(PDO::FETCH_NUM);
		if ($rs) {
			$aResults = $rs;
		}
		$rs = null;
		$stmt = null;

		if (is_array($aResults)) {
			$iRows = count($aResults) ?>
			<select class="jqSubmitSelectChange" name="selectedPlace" style="width: 100%; margin: 2px 0" size="1">
				<option selected="selected" value="0"><?= lngSelectPlace ?></option>
				<?php
				for ($r = 0; $r < $iRows; $r++) { ?>
					<option value="books.php?placeID=<?= $aResults[$r][0] ?>"><?= $aResults[$r][2] . " (" . $aResults[$r][1] . ")" ?></option>
				<?php
				} ?>
			</select>
		<?php
		}
		$aResults = null;
		?>
	</form>
	<form class="fieldset_flex" method="POST" name="selectBookTitle" action="books.php">
		<input type="text" name="title" value="" placeholder="<?= lngSearchTitle ?>"><input type="submit" value="&#10095;&#10095;&#10095;">
	</form>
</section>