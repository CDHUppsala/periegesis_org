<?php
$intStudentID = 0;
if (
	!isset($_SESSION["Students_" . sx_DefaultSiteLang])
	|| !isset($_SESSION["Students_StudentID"])
	|| !isset($_SESSION["Students_Email"])
) {
	header('Location: courses_login.php');
	exit();
} else {
	$intStudentID = (int) $_SESSION["Students_StudentID"];
}

if ($intStudentID == 0) {
	header('Location: courses_login.php');
	exit();
}

/**
 * Just in case where someone is logged in while  
 * the account has been deleted or dissactivated
 * Unset sessions in that case
 */
$sql = "SELECT FirstName 
 FROM course_students 
 WHERE StudentID = ?
	 AND AllowAccess = 1 ";
$stmtf = $conn->prepare($sql);
$stmtf->execute([$intStudentID]);
$FirstName = $stmtf->fetchColumn();
if (empty($FirstName)) {
	$_SESSION[] = array();
	session_destroy();
	header('Location: courses_login.php');
	exit();
}

function sx_checkStudentExists($cid, $sid)
{
	$conn = dbconn();
	$sql = "SELECT CourseID  
	FROM course_to_students 
	WHERE CourseID = ?
		AND StudentID = ?
		AND Cancelled = 0 ";
	$stmtf = $conn->prepare($sql);
	$stmtf->execute([$cid, $sid]);
	$rsf = $stmtf->fetch(PDO::FETCH_NUM);
	if ($rsf) {
		return true;
	} else {
		return false;
	}
	$stmtf = null;
	$rsf = null;
}

function sx_checkCourseExists($cid)
{
	$conn = dbconn();
	$sql = "SELECT CourseTitle 
		FROM courses 
		WHERE CourseID = ?
			AND ShowInSite = 1 
			AND RegistrationEndDate >= ? ";
	$stmtf = $conn->prepare($sql);
	$stmtf->execute([$cid, date("Y-m-d")]);
	$title = $stmtf->fetchColumn();
	if ($title) {
		return $title;
	} else {
		return null;
	}
	$stmtf = null;
	$title = null;
}

$strSubmitMsg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$intCourseID = 0;
	if (isset($_POST["CourseID"])) {
		$intCourseID = (int) $_POST["CourseID"];
	}

	$radioSentEmail = false;
	if (intval($intCourseID) > 0) {
		/**
		 * Check if requested Course exists and get its Title to send with email
		 * Check if the student has already applied for the requested course
		 * Use the result to Update or Insert
		 */
		$str__CourseTitle = sx_checkCourseExists($intCourseID);
		$radioStudentExists = sx_checkStudentExists($intCourseID, $intStudentID);
		if ($str__CourseTitle) {
			$radioTeacher = 0;
			if (isset($_POST["Teacher"]) && $_POST["Teacher"] == "Yes") {
				$radioTeacher = 1;
			}
			if ($radioStudentExists) {
				$radioCancel = 0;
				if (isset($_POST["Cancel"]) && $_POST["Cancel"] == "Yes") {
					$radioCancel = 1;
					$radioTeacher = 0;
				}
				$sql = "UPDATE course_to_students SET
						IsTeacher = ?,
						Cancelled = ?
					WHERE CourseID = ? AND StudentID = ? ";
				$stmt = $conn->prepare($sql);
				$stmt->execute([$radioTeacher, $radioCancel, $intCourseID, $intStudentID]);
				$strSubmitMsg = "Requested updates have been successfully pursued";
			} else {
				$radioSentEmail = true;
				$sApprovalCode = return_Random_Alphanumeric(72);
				$sql = "INSERT INTO course_to_students
						(CourseID, StudentID, IsTeacher, ApprovalCode)
					VALUES (?, ?, ?, ?)";
				$stmt = $conn->prepare($sql);
				$stmt->execute([$intCourseID, $intStudentID, $radioTeacher, $sApprovalCode]);
				$strSubmitMsg = "Your application has been sent. Please check your email for further information.";
			}
		}
	} else {
		$strSubmitMsg = "Undefined error, please contact the administration of the site.";
	}

	if ($radioSentEmail) {
		include __DIR__ . '/apply_course_email.php';
	}
}

/**
 * 
 * Information for the Form
 * 
 */

/**
 * Get the applications of the current student
 * for all ACTUAL courses  
 */
$arrApplicants = null;
$sql = "SELECT cts.CourseID,
		cts.Approved,
		cts.IsTeacher
	FROM course_to_students AS cts
		INNER JOIN courses AS c ON cts.CourseID = c.CourseID 
	WHERE cts.Cancelled = 0
		AND cts.StudentID = ?
		AND c.RegistrationEndDate >= ? ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intStudentID, date("Y-m-d")]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (($rs)) {
	$arrApplicants = $rs;
}
$stmt = null;
$rs = null;

/**
 * Get the array of all ACTUAL courses
 */
$arrCourses = null;
$sql = "SELECT CourseID, CourseTitle, CourseSubTitle, TeacherNames,
		CourseStartDate, CourseEndDate,
		RegistrationStartDate, RegistrationEndDate 
	FROM courses 
	WHERE ShowInSite = 1
		AND RegistrationStartDate <= ? 
		AND RegistrationEndDate >= ? 
	ORDER BY RegistrationEndDate ASC ";
$stmt = $conn->prepare($sql);
$stmt->execute([date("Y-m-d"), date("Y-m-d")]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (($rs)) {
	$arrCourses = $rs;
}
$stmt = null;
$rs = null;
?>

<h1><?= $str_CourseRegistrationTitle  ?></h1>
<?php
if ($radio_UseTeacherRegistration) { ?>
	<p><?= $memo_TeacherRegistrationNotes  ?></p>
<?php
}
if ($strSubmitMsg != "") { ?>
	<div class="bg_success"><?= $strSubmitMsg ?></div>
	<?php
}

/**
 * For every ACTUAL course, 
 * check the status of the current student's application, if any.
 */
if (is_array($arrCourses)) {
	$iRows = count($arrCourses);
	for ($r = 0; $r < $iRows; $r++) {
		$radioApplied = false;
		$radioApproved = false;
		$strDisabled = "";
		$strChecked = "";
		$strCheckTeacher = "";
		$intCourseID = intval($arrCourses[$r]['CourseID']);
		// Check eventual application from current student
		if (is_array($arrApplicants)) {
			foreach ($arrApplicants as $record) {
				if ($record['CourseID'] == $intCourseID) {
					$radioApplied = true;
					$radioApproved = $record['Approved'];
					if ($radioApproved) {
						$radioApplied = false;
						$strDisabled = "disabled";
					}
					$strChecked = "checked";
					if ($record['IsTeacher']) {
						$strCheckTeacher = "checked";
					}
					break;
				}
			}
		}
		$strCourse = $arrCourses[$r]['CourseTitle'];
		if (!empty($arrCourses[$r]['CourseSubTitle'])) {
			$strCourse .= '<br>' . $arrCourses[$r]['CourseSubTitle'];
		} ?>
		<form name="RegisterForCourse_<?= $intCourseID ?>" action="<?= sx_PATH ?>?pg=course" method="post">
			<input type="hidden" name="CourseID" value="<?= $intCourseID ?>" />
			<fieldset>
				<div class="fieldset_flex">
					<div>
						<?php
						/**
						 * CHANGE: The applicant might be able to cancel the registration even after its approval.
						 * In that case, add the checkbox input in both cases beloww
						 */
						if ($radioApplied) { ?>
							<input type="checkbox" name="Cancel" value="Yes"> Check the box to cancel your application
						<?php
						} ?>
						<div>
							<h4><?= $strCourse ?></h4>
							<p>
								<?php
								if (!empty($arrCourses[$r]['TeacherNames'])) {
									echo '<b>' . lngTeacher . ':</b> ' . $arrCourses[$r]['TeacherNames'];
									echo "<br>";
								} ?>
								<b><?= lngPeriod ?>:</b> <?= $arrCourses[$r]['CourseStartDate'] . " <b>" . lngTo . "</b> " . $arrCourses[$r]['CourseEndDate'] ?>
								<br><b><?= lngRegistrationPeriod ?>:</b> <?= $arrCourses[$r]['RegistrationStartDate'] . " <b>" . lngTo . "</b> " . $arrCourses[$r]['RegistrationEndDate'] ?>
							</p>
						</div>
					</div>
					<?php
					if ($radio_UseTeacherRegistration) { ?>
						<div class="align_right white_space_nowrap">
							<?= lngTeacher ?>:<input type="checkbox" name="Teacher" value="Yes" <?= $strCheckTeacher . ' ' . $strDisabled ?>>
						</div>
					<?php
					} ?>
				</div>
				<div class="fieldset_flex">
					<?php
					/**
					 * CHANGE: The applicant might be able to cancel the registration even after its approval.
					 * In that case, add the submit input in both cases beloww
					 */
					if ($radioApproved) { ?>
						<p><span class="text_success">Your application has been approved.</span><br>You are registered for this course.
							If you want to change or cancel your registration, please, send an email to the administration
							by using the Contact Link on the top of this page.</p>
						<?php
					} else {
						if ($radioApplied) { ?>
							<p><span class="text_success">Your application has been sent.</span><br>Please wait for an email about its approval.
								You can make available changes and update your application.</p>
							<div class="align_right">
								<input type="Submit" name="Update" value="<?= lngUpdate ?>">
							</div>
						<?php
						} else { ?>
							<div> </div>
							<div class="align_right">
								<input type="Submit" name="Applay" value="<?= LNG_Form_Submit ?>">
							</div>
						<?php
						} ?>
					<?php
					} ?>
				</div>
			</fieldset>
		</form>
	<?php
	}
	$arrCourses = null;
	$arrApplicants = null;
} else { ?>
	<p>There is no announcement for a New Course.</p>
<?php
} ?>

<article>
	<div class="text">
		<div class="text_max_width">
			<?= $memo_CourseRegistrationNotes ?>
		</div>
	</div>
</article>