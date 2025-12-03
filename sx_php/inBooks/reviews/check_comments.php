<?php

/**
 * Book Review are written by login Users,
 *   so they are published imediately.
 * By this page, the administrator of the site
 *   can HIDE a Book Review, if it is consedered as inappropriate.
 */

$i__BookID = 0;
if(isset($_GET["bookID"]) && (int) $_GET["bookID"] > 0) {
    $i__BookID = (int) $_GET["bookID"];
}

$i__CommentID = 0;
if(isset($_GET["cid"]) && (int) $_GET["cid"] > 0) {
    $i__CommentID = (int) $_GET["cid"];
}

$s__CommentCode = "";
if(!empty($_GET["cc"]) && strlen($_GET["cc"]) > 48) {
    $s__CommentCode = sx_Sanitize_Input_Text($_GET["cc"]);
}

if (intval($i__CommentID) > 0 && intval($i__BookID) > 0 && !empty($s__CommentCode) && strlen($s__CommentCode) >= 9) {
	$sql = "UPDATE book_comments 
		SET Visible = 0
		WHERE CommentID = ? AND BookID = ? AND CommentCode = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$i__CommentID, $i__BookID, $s__CommentCode]);
}
/**
 * Uncomment in real site
 */
echo "<script>window.close();</script>";
