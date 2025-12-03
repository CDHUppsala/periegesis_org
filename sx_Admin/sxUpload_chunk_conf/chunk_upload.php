<?php

/**
 * Full security presupposes full control of 
 * subfolder name
 * file name
 */

session_start();
ob_start();

// (A) FUNCTION TO FORMULATE SERVER RESPONSE
function sx_getError($ok = 1, $info = "")
{
	// THROW A 400 ERROR ON FAILURE
	if ($ok == 0) {
		http_response_code(400);
	}
	die(json_encode(["ok" => $ok, "info" => $info]));
}


if(!isset($_SESSION["ParticipantID"]) || intval($_SESSION["ParticipantID"]) == 0) {
	sx_getError(0, "No active session!.");
}

// (B) INVALID UPLOAD
if (empty($_FILES) || $_FILES['file']['error']) {
	sx_getError(0, "Failed to move uploaded file!");
}

/**
 * Obs!, Obs!, Obs! The check bellow is very specific to this application
 * You must sanitize both GET query parameters
 * Here, I'm waiting for very special values
 * destin = conf_integer (e.g conf_4)
 * prefix = pid_integer_ (e.g. pid_13_)
 */
$sDestinationURL = "/imgMedia/";
if (isset($_GET["destin"])) {
	$tmpDestin = $_GET["destin"];

	if (sx_checkTableAndFieldNames($tmpDestin) === false) {
		sx_getError(0, "Unidentified error 1.");
	}
	if (strpos($tmpDestin, "_") == 0) {
		sx_getError(0, "Unidentified error 2.");
	}
	$arrDestin = explode("_", $tmpDestin);
	if ($arrDestin[0] !== "conf") {
		sx_getError(0, "Unidentified error 3.");
	}
	if (!is_numeric($arrDestin[1]) || intval($arrDestin[1]) == 0) {
		sx_getError(0, "Unidentified error 4.");
	}
	$sDestinationURL .= $tmpDestin;
} else {
	sx_getError(0, "Unidentified error 5.");
}

$sFilePrefix = "";
if (isset($_GET["prefix"])) {
	$tmpPrefix = $_GET["prefix"];
	if (sx_checkTableAndFieldNames($tmpPrefix) === false) {
		sx_getError(0, "Unidentified error 10.");
	}
	if (strpos($tmpPrefix, "_") == 0) {
		sx_getError(0, "Unidentified error 20.");
	}

	$arrPrefix = explode("_", $tmpPrefix);
	if ($arrPrefix[0] !== "pid") {
		sx_getError(0, "Unidentified error 30.");
	}
	if (intval($arrPrefix[1]) == 0 || intval($arrPrefix[1]) != $_SESSION["ParticipantID"]) {
		sx_getError(0, "Unidentified error 40.");
	}
	if (!empty($arrPrefix[2])) {
		sx_getError(0, "Unidentified error 50.");
	}

	$sFilePrefix = $tmpPrefix;
} else {
	sx_getError(0, "Unidentified error 60.");
}


/**
 * (C) UPLOAD DESTINATION
 * Do Not create folder, since the last subfolder cannot be fully controlled
 * so multiple folders con be created on attack!
 */
$filePath = realpath($_SERVER['DOCUMENT_ROOT'] . $sDestinationURL);
if (!file_exists($filePath)) {
	sx_getError(0, "Failed to create $filePath");
}

$filePath = $filePath . DIRECTORY_SEPARATOR;

$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];

$fileName = sx_getSanitizedFileNames($fileName);
$filePathName = $filePath . $fileName;

// (D) DEAL WITH CHUNKS
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
$out = @fopen("{$filePathName}.part", $chunk == 0 ? "wb" : "ab");
if ($out) {
	$in = @fopen($_FILES['file']['tmp_name'], "rb");
	if ($in) {
		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}
	} else {
		sx_getError(0, "Failed to open input stream");
	}
	@fclose($in);
	@fclose($out);
	@unlink($_FILES['file']['tmp_name']);
} else {
	sx_getError(0, "Failed to open output stream");
}

// (E) CHECK IF FILE HAS BEEN UPLOADED
if (!$chunks || $chunk == $chunks - 1) {
	rename("{$filePathName}.part", $filePath . $sFilePrefix . $fileName);
}
sx_getError(1, "Upload OK");
