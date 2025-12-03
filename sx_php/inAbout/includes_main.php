<?php
if ($radio_UseTextsAbout == false) {
	Header("Location: index.php");
}
if (isset($_GET["members"])) {
	include PROJECT_PHP . "/inMembers/membersList.php";
} else {
	include __DIR__ . "/default.php";
}
