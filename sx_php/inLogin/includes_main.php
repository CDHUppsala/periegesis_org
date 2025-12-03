<?php

if ($radio_UseUsersLogin == False) {
    header('Location: index.php');
    exit();
}
?>
<section>
    <?php
    $strPG = "";
    if (isset($_GET["pg"])) {
        $strPG = $_GET["pg"];
    }
    include __DIR__ ."/basic_queries.php";

    if ($strPG == "message") {
        include __DIR__ ."/messages.php";
    } elseif ($strPG == "join") {
        include __DIR__ ."/join.php";
    } elseif ($strPG == "logout") {
        include __DIR__ ."/logout.php";
    } elseif ($strPG == "forgot") {
        include __DIR__ ."/password_send_mail.php";
    } elseif ($strPG == "reset") {
        include __DIR__ ."/password_reset.php";
    } elseif ($strPG == "edit") {
        include __DIR__ ."/edit.php";
    } elseif ($strPG == "leave") {
        include __DIR__ ."/leave.php";
    } elseif ($strPG == "allow") {
        include __DIR__ ."/allow_access.php";
    } else {
        include __DIR__ ."/login.php";
    } ?>
</section>