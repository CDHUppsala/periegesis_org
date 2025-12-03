<?php

$aResults = "";
$sql = "SELECT CourseID,
	CourseTitle,
	TeacherNames,
	CourseStartDate,
	CourseEndDate
FROM courses 
WHERE ShowInSite = True 
	AND ShowInArchive = True " . str_LanguageAnd . "
	ORDER BY CourseEndDate DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$aResults = $stmt->fetchAll(PDO::FETCH_NUM);
$stmt = null;

//$str_CurrentCoursesListTitle, $str_PreviousCoursesListTitle
if (is_array($aResults)) {
	$strNavPath = "courses.php?";
?>
	<section class="jqNavMainToBeCloned">
		<nav class="sxAccordionNav jqAccordionNav">
			<?php
			$iRows = count($aResults);
			$radioCurrentDate = false;
			$radioPreviousDate = false;
			if ($aResults[0][4] >= date('Y-m-d')) {
				$radioCurrentDate = true;
			}
			if ($aResults[$iRows - 1][4] < date('Y-m-d')) {
				$radioPreviousDate = true;
			}
			$radioSplit = true;
			$currentLoop = true;
			$previousLoop = true;

			for ($r = 0; $r < $iRows; $r++) {
				$iCourseID = $aResults[$r][0];
				$sCourseTitle = $aResults[$r][1];
				$sTeacherNames = $aResults[$r][3];
				$dCourseStartDate = $aResults[$r][3];
				$dCourseEndDate = $aResults[$r][4];

				if ($r == 0 && ($radioPreviousDate == false || $radioCurrentDate == false)) {
					$strHeader = $str_CurrentCoursesListTitle;
					if ($radioCurrentDate == false) {
						$strHeader = $str_PreviousCoursesListTitle;
					} ?>
					<h3><span><?= $strHeader ?></span></h3>
					<ul>
						<?php
						$radioSplit = false;
					}
					if ($radioSplit) {
						if ($dCourseEndDate >= date('Y-m-d') && $currentLoop) { ?>
							<h3 class="slide_up jqToggleNextRight"><span><?= $str_CurrentCoursesListTitle ?></span></h3>
							<ul>
							<?php
							$currentLoop = false;
						}

						if ($dCourseEndDate < date('Y-m-d') && $previousLoop) { ?>
							</ul>
							<h3 class="slide_up jqToggleNextRight"><span><?= $str_PreviousCoursesListTitle ?></span></h3>
							<ul>
						<?php
							$previousLoop = false;
						}
					}
					$strClass = "";
					if ($intCourseID == $iCourseID) {
						$strClass = 'class="open" ';
					} ?>
						<li><a <?= $strClass ?>href="<?= $strNavPath ?>courseid=<?= $iCourseID ?>"><?= $sCourseTitle ?></a></li>
					<?php
				} ?>
							</ul>

		</nav>
	</section>
<?php
}
$aResults = null;
?>