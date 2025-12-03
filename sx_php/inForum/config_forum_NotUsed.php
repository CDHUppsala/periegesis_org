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
 * Define the default values of form inputs related to login user
 */
$i__UserID = 0;
$s__FirstName = "";
$s__LastName = "";
$s__Email = "";

/**
 * Depending on setup, by the variable $radioUseForumRegistration,
 * the user can log in from:
 * 	 1. A separate table for Forum Members registration
 * 	 2. Anather registration source, such as Members of Users of the site
 * Bot cases use the same variable for active Forum Session
 *   $radio___ForumMemberIsActive
 */
$radio___ForumMemberIsActive = false;
if ($radioUseForumRegistration) {
	/**
	 * 1. Login from Forum Members registration
	 */
	if (isset($_SESSION["Forum_" . sx_HOST]) && $_SESSION["Forum_" . sx_HOST]) {
		$radio___ForumMemberIsActive = true;
		$i__UserID = $_SESSION["Forum_UserID"];
		$s__FirstName = $_SESSION["Forum_FirstName"];
		$s__LastName = $_SESSION["Forum_LastName"];
		$s__Email = $_SESSION["Forum_UserEmail"];
	}
} elseif ($radio__UserSessionIsActive) {
	/**
	 * 2. Login from another source: here, the table Users
	 * 	  You can schange the if-condition the codes bellow to whatever login source yo use.
	 */
	$radio___ForumMemberIsActive = true;
	$i__UserID = $_SESSION["Users_UserID"];
	$s__FirstName = $_SESSION["Users_FirstName"];
	$s__LastName = $_SESSION["Users_LastName"];
	$s__Email = $_SESSION["Users_UserEmail"];
}

/**
 * If reading the forum reguires login, redirect to
 * login pages if no Forum Session is active
 */
if ($radioLoginToReadForum && $radio___ForumMemberIsActive === false) {
	if ($radioUseForumRegistration) {
		header('Location: forum_login.php');
		exit();
	} else {
		header('Location: login.php');
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
 * - The theme title and description
 * - If the theme is actual or not, used for displaying
 *   or not forms for adding articles and responses
 */
if ((int) $intForumID > 0) {
	$sql = "SELECT ShowAsActual,
		ForumTheme" . str_LangNr . " AS ForumTheme, 
		ForumNote" . str_LangNr . " AS ForumNote 
        FROM forum 
    WHERE ForumID = " . $intForumID . " 
	AND Publish = True ";
	$rs = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ($rs) {
		$radioShowAsActual = $rs["ShowAsActual"];
		$strForumTheme = $rs["ForumTheme"];
		$memoForumNote = $rs["ForumNote"];
	}
	$rs = null;
}
