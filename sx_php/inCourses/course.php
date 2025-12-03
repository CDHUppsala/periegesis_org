<?php

function sx_checkStudentCourseRegistration($sid, $cid)
{
	$conn = dbconn();
	$sql = "SELECT StudentID  
	FROM course_to_students 
	WHERE CourseID = ?
		AND StudentID = ?
		AND Approved = 1
		AND Cancelled = 0 ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$cid, $sid]);
	$rs = $stmt->fetchColumn();
	if ($rs) {
		return true;
	} else {
		return false;
	}
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
<section>
	<h1 class="head"><span><?= $str_CourseSetupTitle ?></span></h1>
	<article class="text_wraper">
		<?php
		if ($radioTemp) { ?>
			<h2 class="head"><span><?= $strTitle ?></span></h2>
			<?php
			if (!empty($strSubTitle)) { ?>
				<h3><?= $strSubTitle ?></h3>
			<?php
			}
			if (!empty($strTeacherNames)) { ?>
				<h4><?= $strTeacherNames ?></h4>
			<?php
			}


			if (!empty($dateCourseStartDate) || !empty($dateRegistrationStartDate)) { ?>
				<p>
					<?php
					if (!empty($dateCourseStartDate)) { ?>
						<b><?= lngPeriod ?></b>: <?= lngFrom . ' ' . $dateCourseStartDate . ' ' . lngTo . ' ' . $dateCourseEndDate ?><br>
					<?php
					}
					if (!empty($dateRegistrationStartDate)) { ?>
						<b><?= lngRegistrationPeriod ?></b>: <?= lngFrom . ' ' . $dateRegistrationStartDate . ' ' . lngTo . ' ' . $dateRegistrationEndDate ?>
					<?php
					} ?>
				</p>
			<?php
			}

			$radioMediaLinks = false;
			if ($radio_ShowSocialMediaInText) {
				$radioMediaLinks = true;
			}
			include PROJECT_PHP . "/basic_PrintIncludes.php";

			if (!empty($strMediaTopURL)) {
				if (strpos($strMediaTopURL, ";") > 0) {
					get_Manual_Image_Cycler($strMediaTopURL, "", $strMediaTopNotes);
				} else {
					get_Any_Media($strMediaTopURL, "Center", $strMediaTopNotes);
				}
			}
			if (!empty($strMediaRightURL)) {
				if (strpos($strMediaRightURL, ";") > 0) {
					get_Right_Images($strMediaRightURL, $strMediaRightNotes);
				} else {
					get_Any_Media($strMediaRightURL, "Right", $strMediaRightNotes);
				}
			} ?>
			<div class="text text_resizeable">
				<div class="text_max_width">
					<?php
					echo $memoCourseDescription;

					if (!empty($strFilesForDownload)) {
						sx_getDownloadableFiles($strFilesForDownload);
						//echo $str_LinksToFiles;
					}

					if (!empty($strFilesForDownloadHidden)) {
						echo '<h4>' . lngLoginToDownloadFile . '</h4>';
						if (
							isset($_SESSION["Students_" . sx_DefaultSiteLang])
							&& $_SESSION["Students_" . sx_DefaultSiteLang] == true
							&& (int) $_SESSION["Students_StudentID"] > 0
						) {
							$radioCheckRegistration = true;
							if ($radio_AccessToHiddenFilesByCourse) {
								$radioCheckRegistration = sx_checkStudentCourseRegistration($_SESSION["Students_StudentID"], $iCourseID);
							}
							if ($radioCheckRegistration) {
								echo sx_getDownloadableHiddenFiles($strFilesForDownloadHidden);
							} else {
								echo '<p>Available only for students registered for this course</p>';
							}
						} elseif ($radio_AccessToHiddenFilesByCourse) {
							echo '<p>Available only for students registered for this course</p>';
						}
					} ?>
				</div>
			</div>
		<?php
		} ?>
	</article>
</section>