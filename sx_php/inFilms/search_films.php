<section class="jqNavSideToBeCloned">
	<h2 class="head"><span><?= lngSearch ?></span></h2>
	<form method="get" name="selectAuthor" class="formFilmSearch align_center">
		<?php

		$arrDirector = array();
		$arrScriptwriter = array();
		$arrActors = array();
		$arrYear = array();
		$sql = "SELECT Director, Scriptwriter, Actors, ProductionYear FROM films ";
		$stmt = $conn->query($sql);

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$Director = $result['Director'];
			$Scriptwriter = $result['Scriptwriter'];
			$Actors = $result['Actors'];
			$arrYear[] = $result['ProductionYear'];

			if (strpos($Director, ",") > 0) {
				$aDirector = explode(",", $Director);
				$count = count($aDirector);
				for ($i = 0; $i < $count; $i++) {
					$arrDirector[] = trim($aDirector[$i]);
				}
			} else {
				$arrDirector[] = trim($Director);
			}

			if (strpos($Scriptwriter, ",") > 0) {
				$aScriptwriter = explode(",", $Scriptwriter);
				$count = count($aScriptwriter);
				for ($i = 0; $i < $count; $i++) {
					$arrScriptwriter[] = trim($aScriptwriter[$i]);
				}
			} else {
				$arrScriptwriter[] = trim($Scriptwriter);
			}

			if (strpos($Actors, ",") > 0) {
				$aActors = explode(",", $Actors);
				$count = count($aActors);
				for ($i = 0; $i < $count; $i++) {
					$arrActors[] = trim($aActors[$i]);
				}
			} else {
				$arrActors[] = trim($Actors);
			}
		}
		$stmt = null;
		$arrDirector = array_unique($arrDirector, SORT_STRING);
		sort($arrDirector);

		$arrScriptwriter = array_unique($arrScriptwriter, SORT_STRING);
		sort($arrScriptwriter);

		$arrActors = array_unique($arrActors, SORT_STRING);
		sort($arrActors);

		$arrYear = array_unique($arrYear);
		rsort($arrYear);


		if (!empty($arrDirector)) { ?>
			<select class="jqSubmitSelectChange" name="Director" style="width: 100%; margin: 2px 0" size="1">
				<option selected="selected" value=""><?= lngSelectDirector ?></option>
				<?php
				foreach ($arrDirector as $key => $value) {
					if (!empty($value)) { ?>
						<option value="films.php?director=<?= urlencode($value) ?>"><?=$value?></option>
				<?php
					}
				} ?>
			</select>
		<?php
		}
		$arrDirector = null;
		if (!empty($arrScriptwriter)) { ?>
			<select class="jqSubmitSelectChange" name="Scriptwriter" style="width: 100%; margin: 2px 0" size="1">
				<option selected="selected" value=""><?= lngSelectScriptwriter ?></option>
				<?php
				foreach ($arrScriptwriter as $key => $value) {
					if (!empty($value)) { ?>
						<option value="films.php?writer=<?= urlencode($value) ?>"><?=$value?></option>
				<?php
					}
				} ?>
			</select>
		<?php
		}
		$arrScriptwriter = null;
		if (!empty($arrActors)) { ?>
			<select class="jqSubmitSelectChange" name="Actor" style="width: 100%; margin: 2px 0" size="1">
				<option selected="selected" value=""><?= lngSelectActor ?></option>
				<?php
				foreach ($arrActors as $key => $value) {
					if (!empty($value)) { ?>
						<option value="films.php?actor=<?= urlencode($value) ?>"><?=$value?></option>
				<?php
					}
				} ?>
			</select>
		<?php
		}
		$arrActors = null;
		if (!empty($arrYear)) { ?>
			<select class="jqSubmitSelectChange" name="ProductionYear" style="width: 100%; margin: 2px 0" size="1">
				<option selected="selected" value=""><?= lngSelect . " " . mb_strtolower(lngPublicationYear) ?></option>
				<?php
				foreach ($arrYear as $key => $value) {
					if (!empty($value)) { ?>
						<option value="films.php?year=<?= trim($value) ?>"><?=$value?></option>
				<?php
					}
				} ?>
			</select>
		<?php
		}
		$arrYear = null;

		$sql = "SELECT PlaceID, PlaceCode, PlaceName" . str_LangNr . " AS PlaceName 
	FROM film_place ORDER BY Sorting DESC, PlaceCode ";
		$stmt = $conn->query($sql);
		$rs = $stmt->fetchAll(PDO::FETCH_NUM);
		if ($rs) {
			$aResults = $rs;
		}
		$rs = null;
		$stmt = null;

		if (is_array($aResults)) {
			$iRows = count($aResults) ?>
			<select class="jqSubmitSelectChange" name="Place" style="width: 100%; margin: 2px 0" size="1">
				<option selected="selected" value="0"><?= lngSelectPlace ?></option>
				<?php
				for ($r = 0; $r < $iRows; $r++) { ?>
					<option value="films.php?placeID=<?= $aResults[$r][0] ?>"><?= $aResults[$r][2] . " (" . $aResults[$r][1] . ")" ?></option>
				<?php
				} ?>
			</select>
		<?php
		}
		$aResults = null;
		?>
	</form>
	<form class="fieldset_flex" method="POST" name="FilmTitle" action="films.php">
		<input type="text" name="title" value="" placeholder="<?= lngSearchTitle ?>"><input type="submit" value="&#10095;&#10095;&#10095;">
	</form>
</section>