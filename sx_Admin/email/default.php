<?php
include realpath(dirname(__DIR__) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

include_once __DIR__ . "/get_functions.php";

$iSecondsDelay = 620;


/**
 * GET THE CONTENT OF REQUESTED TEXT, EVENT, OR Product 
 * TO POPULATE THE TEXTAREA OF THE FORM
 */
$str_Table = null;
$intCurrentID = 0;
$radioCheck = false;
if (isset($_GET["tbl"])) {
	$str_Table = $_GET["tbl"];
	$radioCheck = sx_checkTableAndFieldNames($str_Table);
}

if ($radioCheck && isset($_GET["cid"])) {
	$intCurrentID = (int) $_GET["cid"];
	if (intval($intCurrentID) == 0) {
		$intCurrentID = 0;
	}
}

 $str_TextArea = "";
if (intval($intCurrentID) > 0) {
	if (($str_Table == "news" || $str_Table == "texts" || $str_Table == "articles" || $str_Table == "posts" || $str_Table == "blog")) {
		$str_TextArea = sx_getText($str_Table, $intCurrentID);
	} elseif ($str_Table == "events") {
		$str_TextArea = sx_getEvent($intCurrentID);
	} elseif ($str_Table == "conferences") {
		$str_TextArea = sx_getConference($intCurrentID);
	} elseif ($str_Table == "products") {
		$str_TextArea = sx_getProduct($intCurrentID);
	}
}

if (empty($str_TextArea)) {
    $str_TextArea = '<h2>Your Title Here</h2>
	<p>Your text here!</p>
	<p><b>Delete this information:</b> Click on the above icons to insert Lists, 
	Tables and links to Image and Multimedia files.
	Click on the icon with an eye symbol to preview your mail.</p>';
}


// CREATE AND POPULATE THE FORM
// The list of emails will be added by ajax query
//==========================================================================
?>
<!DOCTYPE html>
<html lang="<?= sx_DefaultSiteLang ?>">

<head>
	<meta charset="utf-8">
	<title>Public Sphere CMS - Send Emails</title>
	<style>
		#count {
			margin-right: 12px;
		}

		#count b {
			font-size: 2em;
			color: #09c;
		}

		pre {
			background: #fff;
			padding: 16px;
		}

		form {
			clear: both;
			overflow: Hidden;
		}
	</style>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2024">
	<script src="../js/jq/jquery.min.js"?v=2024></script>
	<script src="<?php echo sx_ADMIN_DEV ?>js/jqAjaxLoadArchives.js?v=2024"></script>
	<script src="../tinymce/tinymce.min.js?v=2024"></script>
	<script src="../tinymce/config/email.js?v=2024"></script>
</head>

<body>
	<div id="page">
		<div id="leftCol">
			<?php
			require_once("sx_NewsletterForm.php");
			?>
		</div>

		<div id="rightCol">
			<?php
			require_once("sx_MailSources.php");
			require_once("sx_NewsletterHelp.php");
			?>

		</div>
	</div>
</body>

</html>
<?php $connClose ?>