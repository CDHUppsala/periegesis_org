<?php

/**
 * Check conditions for accessing forum
 */
if ($radio_UseForum == False) {
	header('Location: index.php');
	exit();
}

/**
 * Get Forum setup from the database
 */
$sql = "SELECT UseForumRegistration, UseAdministrationControl, 
    LoginToReadForum, LoginToParticipate,
	MaxArticleCharacters, MaxResponseCharacters, 
    LoginTitle, LoginNote, 
    RegistrationTitle, RegistrationNote, 
    WelcomeTitle, WelcomeNote, 
    ConditionsTitle, ConditionsNote 
    FROM forum_setup " . str_LanguageWhere;
$rs = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$radioUseForumRegistration = $rs["UseForumRegistration"];
	$radioUseAdministrationControl = $rs["UseAdministrationControl"];
	$radioLoginToReadForum = $rs["LoginToReadForum"];
	$radioLoginToParticipate = $rs["LoginToParticipate"];
	$intMaxArticleCharacters = (int) $rs["MaxArticleCharacters"];
	$intMaxResponseCharacters = (int) $rs["MaxResponseCharacters"];
	$strLoginTitle = $rs["LoginTitle"];
	$memoLoginNote = $rs["LoginNote"];
	$strRegistrationTitle = $rs["RegistrationTitle"];
	$memoRegistrationNote = $rs["RegistrationNote"];
	$strWelcomeTitle = $rs["WelcomeTitle"];
	$memoWelcomeNote = $rs["WelcomeNote"];
	$strConditionsTitle = $rs["ConditionsTitle"];
	$memoConditionsNote = $rs["ConditionsNote"];
}
$rs = null;

if (intval($intMaxArticleCharacters) == 0) {
	$intMaxArticleCharacters = 10000;
}

if (intval($intMaxResponseCharacters) == 0) {
	$intMaxResponseCharacters = 2500;
}

/**
 * The file confirms User Registration and is placed here because
 * - it closes the window, if request comes from administrator
 * - it redirects to the login page, if it comes from the user
 * The fiel is placed in /login/ but is also included in /inForum/
 *  $str_PG is used only here, for the above purpose
 */

 $sPG = $_GET['pg'] ?? '';
 if ($sPG === "allow") {
	 include __DIR__ . "/approve_forum_member.php";
 }
 

/**
 * Define the default values of form inputs related to login user
 */
$i__UserID = 0;
$s__FirstName = "";
$s__LastName = "";
$s__Email = "";

/**
 * Check if a forum member is logged in
 */
$radio___ForumMemberIsActive = false;
if (isset($_SESSION["Forum_" . sx_HOST]) && $_SESSION["Forum_" . sx_HOST]) {
	$radio___ForumMemberIsActive = true;
	$i__UserID = $_SESSION["Forum_UserID"];
	$s__FirstName = $_SESSION["Forum_FirstName"];
	$s__LastName = $_SESSION["Forum_LastName"];
	$s__Email = $_SESSION["Forum_UserEmail"];
}

/**
 * If reading the forum reguires login, 
 *  - redirect to login pages if no Forum Session is active
 *  - if online registration is not allowed, redirect to index page
 */
if ($radioLoginToReadForum && $radio___ForumMemberIsActive === false) {
	if ($radioUseForumRegistration) {
		header('Location: forum_login.php');
		exit();
	} else {
		// A completely closed forum system
		header('Location: /');
		exit();
	}
}

/**
 * Get reguest variables
 */
$intForumID = isset($_GET['forumID']) ? (int) $_GET["forumID"] : 0;
$intArticleID = isset($_GET['articleID']) ? (int) $_GET['articleID'] : 0;
$intAnchor = isset($_GET['anchor']) ? (int) $_GET['anchor'] : 0;

/**
 * Get 3 field values from the currently Open Forum Theme
 * which are used in 2 pages:
 * - The theme title and and the theme description
 * - If the theme is actual or not, if YES, display the forms for 
 *   adding articles and responses for login members
 */
if ((int) $intForumID > 0) {
	$sql = "SELECT ShowAsActual,
		ForumTheme{$str_LangNr} AS ForumTheme, 
		ForumNote{$str_LangNr} AS ForumNote 
    FROM forum 
    WHERE ForumID = ? 
		AND Publish = True ";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$intForumID]);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radioShowAsActual = $rs["ShowAsActual"];
		$strForumTheme = $rs["ForumTheme"];
		$memoForumNote = $rs["ForumNote"];
	}
	$rs = null;
}
