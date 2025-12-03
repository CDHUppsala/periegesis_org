<?php
/**
 * The use of music is defined by design
 * but also dynamically from the site-config_apps table.
 * Finally, it is also defined in music_setup table
 */
if (sx_includeMusic == false || $radio_IncludeMusic == false) {
    header("location: index.php");
    exit();
}
$sql = "SELECT UseMusic,
    MusicMenuTitle" . str_LangNr . " AS MusicMenuTitle, 
    MusicNavTitle" . str_LangNr . " AS MusicNavTitle, 
    MusicMiddleTitle" . str_LangNr . " AS MusicMiddleTitle, 
    MusicImgURL,
    MusicNotes" . str_LangNr . " AS MusicNotes
FROM music_setup " . str_LanguageWhere;
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $radioUseMusic = $rs["UseMusic"];
    $str_MusicMenuTitle = $rs["MusicMenuTitle"];
    $str_MusicNavTitle = $rs["MusicNavTitle"];
    $str_MusicMiddleTitle = $rs["MusicMiddleTitle"];
    $str_MusicImgURL = $rs["MusicImgURL"];
    $memo_MusicNotes = $rs["MusicNotes"];
}
$stmt = null;
$rs = null;

if (!isset($radioUseMusic) || $radioUseMusic == False) {
    header("location: index.php");
    exit();
}

$str_SiteTitle = $str_MusicMiddleTitle;
if (!empty($memo_MusicNotes)) {
    $str_MetaDescription = return_Left_Part_FromText(strip_tags($memo_MusicNotes), 120);
}

$int_TrackID = 0;
if (isset($_GET["trackID"])) {
    $int_TrackID = (int) $_GET["trackID"];
}

$int_RequestAlbumID = 0;
if (isset($_GET["albumID"])) {
    $int_RequestAlbumID = (int) $_GET["albumID"];
}

if(intval($int_TrackID) > 0) {
    /*
     * Get here an array with all track information with inner join
     * to neccassery album information
     */
    /*
    */
    $sql = "SELECT
        t.TrackNumber,
        t.TrackDate,
        t.TrackTitle" . str_LangNr . " AS TrackTitle, 
        t.TrackURL,
        t.FreeDownload,
        t.TrackImage,
        t.TrackNotes" . str_LangNr . " AS TrackNotes,
        a.AlbumTitle" . str_LangNr . " AS AlbumTitle,
        a.SellingSiteTitle" . str_LangNr . " AS SellingSiteTitle,
        a.SellingSiteURL
    FROM music_tracks AS t
        INNER JOIN music_albums AS a
            ON t.AlbumID = a.AlbumID
    WHERE t.TrackID = ?
        AND t.Hidden = False
        AND a.Hidden = False ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_TrackID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $int_TrackNumber = $rs["TrackNumber"];
        $str_TrackTitle = $rs["TrackTitle"];
        $str_TrackURL = $rs["TrackURL"];
        $radio_FreeDownload = $rs["FreeDownload"];
        $str_TrackImage = $rs["TrackImage"];
        $memo_TrackNotes = $rs["TrackNotes"];
        $str_AlbumTitle = $rs["AlbumTitle"];
        $str_SellingSiteTitle = $rs["SellingSiteTitle"];
        $str_SellingSiteURL = $rs["SellingSiteURL"];
    } else{
        $int_TrackID = 0;
    }
    $stmt = null;
    $rs = null;

    $str_SiteTitle = $str_TrackTitle;
    if (!empty($memo_TrackNotes)) {
        $str_MetaDescription = return_Left_Part_FromText(strip_tags($memo_TrackNotes), 120);
    }

}elseif(intval($int_RequestAlbumID) > 0) {
    /*
    Get here an array with all information about the album
    and get the album tracks in the relevant page
    */
    $sql = "SELECT
        AlbumDate,
        AlbumTitle" . str_LangNr . " AS AlbumTitle,
        AlbumImage,
        AlbumImagePlace,
        AlbumImageNotes,
        GalleryID,
        FilesForDownload,
        SellingSiteTitle" . str_LangNr . " AS SellingSiteTitle,
        SellingSiteURL,
        AlbumNote" . str_LangNr . " AS AlbumNote
    FROM music_albums 
    WHERE AlbumID = ?
        AND Hidden = False";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$int_RequestAlbumID]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $str_AlbumDate = $rs["AlbumDate"];
        $str_AlbumTitle = $rs["AlbumTitle"];
        $str_AlbumImage = $rs["AlbumImage"];
        $str_AlbumImagePlace = $rs["AlbumImagePlace"];
        $memo_AlbumImageNotes = $rs["AlbumImageNotes"];
        $int_GalleryID = $rs["GalleryID"];
        $str_FilesForDownload = $rs["FilesForDownload"];
        $str_SellingSiteTitle = $rs["SellingSiteTitle"];
        $str_SellingSiteURL = $rs["SellingSiteURL"];
        $memo_AlbumNote = $rs["AlbumNote"];
    } else{
        $int_RequestAlbumID = 0;
    }
    $stmt = null;
    $rs = null;

    $str_SiteTitle = $str_AlbumTitle;
    if (!empty($memo_AlbumNote)) {
        $str_MetaDescription = return_Left_Part_FromText(strip_tags($memo_AlbumNote), 120);
    }
}

$str_MetaTitle = $str_SiteTitle;
