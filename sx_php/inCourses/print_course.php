<body>
	<?php
	include __DIR__ . "/config_courses.php";

	if (intval($intCourseID) == 0) {
		$intCourseID = 0;
	}

	if (empty($strExport)) { ?>
		<div style="margin: 12px 20px;">
			<a href="default.php"><?= lngHomePage ?></a> |
			<a target="_top" href="sx_PrintPage.php?courseid=<?= $intCourseID ?>&export=print"><?= lngSavePrintInPDF ?></a> |
			<a target="_top" href="sx_PrintPage.php?courseid=<?= $intCourseID ?>&export=word"><?= lngSaveInWord ?></a> |
			<a target="_top" href="sx_PrintPage.php?courseid=<?= $intCourseID ?>&export=html"><?= lngSaveInHTML ?></a>
			<hr>
		<?php
	}

	if (is_array($arr_Course)) {
		$radioTemp = true;
		$iCourseID = $arr_Course["CourseID"];

		$dateCourseStartDate = $arr_Course["CourseStartDate"];
		$dateCourseEndDate = $arr_Course["CourseEndDate"];
		$dateRegistrationStartDate = $arr_Course["RegistrationStartDate"];
		$dateRegistrationEndDate = $arr_Course["RegistrationEndDate"];

		$strTitle = $arr_Course["CourseTitle"];
		$strSubTitle = $arr_Course["CourseSubtitle"];
		$strTeacherNames = $arr_Course["TeacherNames"];
		$strMediaTopURL = $arr_Course["MediaTopURL"];
		$strMediaTopNotes = $arr_Course["MediaTopNotes"];
		$strMediaRightURL = $arr_Course["MediaRightURL"];
		$strMediaRightNotes = $arr_Course["MediaRightNotes"];
		$strFilesForDownload = $arr_Course["FilesForDownload"];
		$strFilesForDownloadHidden = $arr_Course["FilesForDownloadHidden"];
		$memoCourseDescription = $arr_Course["CourseDescription"];
	}
	$arr_Course = null;

	if (empty($str_CourseSetupTitle)) {
		$str_CourseSetupTitle = $str_CoursesLinkTitle;
	} ?>
		<h1><?= $str_CourseSetupTitle ?></h1>
		<?php
		if ($radioTemp) { ?>
			<h2><?= $strTitle ?></h2>
			<?php
			if (!empty($strSubTitle)) { ?>
				<h3><?= $strSubTitle ?></h3>
			<?php
			}
			if (!empty($strTeacherNames)) { ?>
				<h4><?= $strTeacherNames ?></h4>
				<?php
			}
			if (!empty($dateCourseStartDate) || !empty($dateRegistrationStartDate)) {
				if (!empty($dateCourseStartDate)) { ?>
					<p><b><?= lngPeriod ?></b>: <?= lngFrom . ' ' . $dateCourseStartDate . ' ' . lngTo . ' ' . $dateCourseEndDate ?></p>
				<?php
				}
				if (!empty($dateRegistrationStartDate)) { ?>
					<p><b><?= lngRegistrationPeriod ?></b>: <?= lngFrom . ' ' . $dateRegistrationStartDate . ' ' . lngTo . ' ' . $dateRegistrationEndDate ?></p>
			<?php
				}
			}
			echo $memoCourseDescription;

			if (strlen($strMediaTopURL) > 0) {
				get_Images_To_Print($strMediaTopURL, $strMediaTopNotes);
			}
			if (strlen($strMediaRightURL) > 0) {
				get_Images_To_Print($strMediaRightURL, $strMediaRightNotes);
			} ?>
			<hr>
			<p style="text-align: center;">
				<?= lngPrintedDate ?>: <?= Date("Y-m-d") ?><br>
				<?= lngFromWebPage ?>: <b><?= str_SiteTitle ?></b><br>
				<?= sx_LOCATION ?>
			</p>
		<?php
		} else { ?>
			<h3><?= lngTextDoesNotExist ?></h3>
			<?= lngCloseWindowReturnToSite ?>
		<?php
		}

		if (empty($strExport)) { ?>
		</div>
	<?php
		} ?>
</body>

</html>
<?php
if ($strExport == "print") { ?>
	<script>
		window.print();
	</script>
<?php
} ?>