<?php

/**
 * Approval from the administration of the site
 * The link is sent from email as GET request
 */
if (isset($_GET["tid"]) && isset($_GET["cc"]) && isset($_GET["cc"])) {
    $textID = return_Filter_Integer($_GET["tid"]);
    $insertID = return_Filter_Integer($_GET["cid"]);
    $requestCode = $_GET["cc"];

    if (intval($textID) > 0 && intval($insertID) > 0) {
        $sql = "SELECT CommentCode FROM text_comments WHERE InsertID = ? AND TextID = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$insertID, $textID]);
        $savedCode = $stmt->fetchColumn();
        $stmt = null;

        if ($savedCode == $requestCode) {
            $sql = "UPDATE text_comments 
            SET Visible = 1
            WHERE InsertID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$insertID]);
        }
    }
    /**
     * Uncomment in real site
     */
    echo "<script>window.close();</script>";
}
