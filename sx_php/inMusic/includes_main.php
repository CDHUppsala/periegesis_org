<?php
if (intval($int_TrackID) > 0) {
    include __DIR__ . "/music_track.php";
} elseif (intval($int_RequestAlbumID) > 0) {
    include __DIR__ . "/music_album.php";
} else {
    include __DIR__ . "/music_welcome.php";
}
