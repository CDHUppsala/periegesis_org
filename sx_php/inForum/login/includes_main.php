<?php
$strPG = $_GET['pg'] ?? '';

if ($strPG == "message") {
    include __DIR__ . "/messages.php";
} elseif ($strPG == "join") {
    include __DIR__ . "/join.php";
} elseif ($strPG == "logout") {
    include __DIR__ . "/logout.php";
} elseif ($strPG == "forgot") {
    include __DIR__ . "/password_send.php";
} elseif ($strPG == "reset") {
    include __DIR__ . "/password_reset.php";
} elseif ($strPG == "edit") {
    include __DIR__ . "/edit.php";
} elseif ($strPG == "leave") {
    include __DIR__ . "/leave.php";
} elseif ($strPG == "conditions") {
    include dirname(__DIR__) . "/conditions.php";
} else {
    include __DIR__ . "/login.php";
}
