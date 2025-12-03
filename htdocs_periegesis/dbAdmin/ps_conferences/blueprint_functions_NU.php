<?php

function sx_getLastConferenceID()
{
	$conn = dbconn();
	static $last_id = -1;
	if ($last_id < 0) {
		$sql = "SELECT ConferenceID FROM conferences ORDER BY ConferenceID DESC LIMIT 1";
		$stmt = $conn->query($sql);
		$_retval = $stmt->fetch(PDO::FETCH_COLUMN);
		$stmt = null;
		if ($_retval > 0) {
			$last_id = $_retval;
		}
	}
	return $last_id;
}
function sx_getLastSessionID()
{
	$conn = dbconn();
	static $last_id = -1;
	if ($last_id < 0) {
		$sql = "SELECT SessionID FROM conf_sessions ORDER BY SessionID DESC LIMIT 1";
		$stmt = $conn->query($sql);
		$_retval = $stmt->fetch(PDO::FETCH_COLUMN);
		$stmt = null;
		if ($_retval > 0) {
			$last_id = $_retval;
		}
	}
	return $last_id;
}
function sx_getLastPaperID()
{
	$conn = dbconn();
	static $last_id = -1;
	if ($last_id < 0) {
		$sql = "SELECT PaperID FROM conf_papers ORDER BY PaperID DESC LIMIT 1";
		$stmt = $conn->query($sql);
		$_retval = $stmt->fetch(PDO::FETCH_COLUMN);
		$stmt = null;
		if ($_retval > 0) {
			$last_id = $_retval;
		}
	}
	return $last_id;
}
?>