<?php

$sql = "SELECT 
    UseCourses,
    CurrentCoursesListTitle,
    PreviousCoursesListTitle,
    CourseSetupTitle,
    CourseSetupMedia,
    CourseSetupDescription,
        StudentsAreaMenuTitle,
    UseStudentsLogin,
    AllowOnlineRegistration,
    UseAdministrationControl,
    UseRegistrationCode,
    RegistrationCode,
    AccessToHiddenFilesByCourse,
    StudentsLoginTitle, StudentsLoginNotes,
    StudentsAreaRegistrationTitle, StudentsAreaRegistrationNotes,
    StudentsWelcomeTitle, StudentsWelcome,
            CourseRegistrationTitle,
            CourseRegistrationNotes,
    GeneralConditionsTitle, GeneralConditions,
    UseTeacherRegistration,
    TeacherRegistrationNotes
FROM course_students_setup " . str_LanguageWhere . "
LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if (is_array($rs)) {
    $rdioUseCourses = $rs["UseCourses"];
    $str_CurrentCoursesListTitle = $rs["CurrentCoursesListTitle"];
    $str_PreviousCoursesListTitle = $rs["PreviousCoursesListTitle"];
    $str_CourseSetupTitle = $rs["CourseSetupTitle"];
        $str_CourseSetupMedia = $rs["CourseSetupMedia"];
        $memo_CourseSetupDescription = $rs["CourseSetupDescription"];
    $str_StudentsAreaMenuTitle = $rs["StudentsAreaMenuTitle"];
        $radio_UseStudentsLogin = $rs["UseStudentsLogin"];
    	$radio_AllowOnlineRegistration = $rs["AllowOnlineRegistration"];
	    $radio_UseAdministrationControl = $rs["UseAdministrationControl"];
    	$radio_UseRegistrationCode = $rs["UseRegistrationCode"];
	    $str_RegistrationCode = $rs["RegistrationCode"];
    $radio_AccessToHiddenFilesByCourse = $rs["AccessToHiddenFilesByCourse"];
	$str_StudentsLoginTitle = $rs["StudentsLoginTitle"];
		$memo_StudentsLoginNotes = $rs["StudentsLoginNotes"];
	$str_StudentsAreaRegistrationTitle = $rs["StudentsAreaRegistrationTitle"];
		$memo_StudentsAreaRegistrationNotes = $rs["StudentsAreaRegistrationNotes"];
	$str_StudentsWelcomeTitle = $rs["StudentsWelcomeTitle"];
		$memo_StudentsWelcome = $rs["StudentsWelcome"];
    $str_CourseRegistrationTitle = $rs["CourseRegistrationTitle"];
        $memo_CourseRegistrationNotes = $rs["CourseRegistrationNotes"];
	$str_GeneralConditionsTitle = $rs["GeneralConditionsTitle"];
		$memo_GeneralConditions = $rs["GeneralConditions"];
	$radio_UseTeacherRegistration = $rs["UseTeacherRegistration"];
		$memo_TeacherRegistrationNotes = $rs["TeacherRegistrationNotes"];
}
$stmt = null;
$rs = null;

if ($rdioUseCourses == false) {
    header('Location: index.php');
    exit();
}

/**
 * Meta tags
 */
$str_SiteTitle = $str_CourseSetupTitle;
$str_MetaTitle = $str_SiteTitle;
$str_MetaDescription = return_Left_Part_FromText(strip_tags($memo_CourseSetupDescription), 120);


$intCourseID = 0;
if (isset($_GET["courseid"])) {
    $intCourseID = (int) $_GET["courseid"];
}
$arr_Course = "";
if (intval($intCourseID) > 0) {
    $sql = "SELECT CourseID,
    CourseTitle,
    CourseSubtitle,
    TeacherNames,
    CourseStartDate,
    CourseEndDate,
    RegistrationStartDate,
    RegistrationEndDate,
    MediaTopURL,
    MediaTopNotes,
    MediaRightURL,
    MediaRightNotes,
    FilesForDownload,
    FilesForDownloadHidden,
    CourseDescription
	FROM courses
	WHERE ShowInSite = True
		AND ShowInArchive = True 
		AND CourseID = ? " . str_LanguageAnd . "
	LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$intCourseID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if (is_array($rs)) {
        $arr_Course = $rs;
    }
    $stmt = null;
    $rs = null;
}
if (is_array($arr_Course)) {
    $str_SiteTitle = $arr_Course['CourseTitle'];
    $str_MetaTitle = $str_SiteTitle;
    $str_MetaDescription = return_Left_Part_FromText(strip_tags($arr_Course['CourseDescription']), 120);
}
