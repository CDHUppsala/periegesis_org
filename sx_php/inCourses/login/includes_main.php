<?php
if ($radio_UseStudentsLogin == False) {
	header('Location: index.php');
	exit();
} ?>
<section>
    <?php
    $strPG = "";
    if (isset($_GET["pg"])) {
        $strPG = $_GET["pg"];
    }

    if ($strPG == "message") {
        include __DIR__ ."/messages.php";
    } elseif ($strPG == "join") {
        include __DIR__ ."/join.php";
    } elseif ($strPG == "course") {
        include __DIR__ ."/apply_course.php";
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
        include __DIR__ ."/join_allow.php";
    } elseif ($strPG == "approve") {
        include __DIR__ ."/apply_course_approve.php";
    } else {
        include __DIR__ ."/login.php";
    } ?>
</section>