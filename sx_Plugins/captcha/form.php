<?php

$type = "";
if (!empty($_GET['type'])) {
    $type = "_" . $_GET['type'];
}

if ($_POST) {
    $visitor_name = "";
    $visitor_email = "";
    $email_title = "";
    $concerned_department = "";
    $visitor_message = "";
    $email_body = "<div>";

    if (isset($_POST['visitor_name'])) {
        $visitor_name = htmlspecialchars($_POST['visitor_name']);
        $email_body .= "<div>
                           <label><b>Visitor Name:</b></label>&nbsp;<span>" . $visitor_name . "</span>
                        </div>";
    }

    if (isset($_POST['visitor_email'])) {
        $visitor_email = str_replace(array("\r", "\n", "%0a", "%0d"), '', $_POST['visitor_email']);
        $visitor_email = filter_var($visitor_email, FILTER_VALIDATE_EMAIL);
        $email_body .= "<div>
                           <label><b>Visitor Email:</b></label>&nbsp;<span>" . $visitor_email . "</span>
                        </div>";
    }

    if (isset($_POST['email_title'])) {
        $email_title = htmlspecialchars($_POST['email_title']);
        $email_body .= "<div>
                           <label><b>Reason For Contacting Us:</b></label>&nbsp;<span>" . $email_title . "</span>
                        </div>";
    }

    if (isset($_POST['concerned_department'])) {
        $concerned_department = htmlspecialchars($_POST['concerned_department']);
        $email_body .= "<div>
                           <label><b>Concerned Department:</b></label>&nbsp;<span>" . $concerned_department . "</span>
                        </div>";
    }

    if (isset($_POST['visitor_message'])) {
        $visitor_message = htmlspecialchars($_POST['visitor_message']);
        $email_body .= "<div>
                           <label><b>Visitor Message:</b></label>
                           <div>" . $visitor_message . "</div>
                        </div>";
    }

    if ($concerned_department == "billing") {
        $recipient = "billing@domain.com";
    } else if ($concerned_department == "marketing") {
        $recipient = "marketing@domain.com";
    } else if ($concerned_department == "technical support") {
        $recipient = "tech.support@domain.com";
    } else {
        $recipient = "contact@domain.com";
    }

    $email_body .= "</div>";

    $headers  = 'MIME-Version: 1.0' . "\r\n"
        . 'Content-type: text/html; charset=utf-8' . "\r\n"
        . 'From: ' . $visitor_email . "\r\n";

    if (mail($recipient, $email_title, $email_body, $headers)) {
        echo "<fieldset>Thank you for contacting us, $visitor_name. You will get a reply within 24 hours.</fieldset>";
    } else {
        echo '<fieldset>We are sorry but the email did not go through.</fieldset>';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>Untitled 1</title>
    <link rel="stylesheet" href="../../sxCss/sx_Structure.css">
    <link rel="stylesheet" href="../../sxCss/sx_Texts.css">
    <link rel="stylesheet" href="../../sxCss/sx_Images.css">
    <link rel="stylesheet" href="../../sxCss/sx_Buttons.css">
    <link rel="stylesheet" href="../../sxCss/sx_Forms.css">
    <link rel="stylesheet" href="../../sxCss/sx_svg.css">
</head>

<body style="margin: 40px;">
    <a href="form.php"> Default </a> |
    <a href="form.php?type=0"> 0 </a> |
    <a href="form.php?type=1"> 1 </a> |
    <a href="form.php?type=2"> 2 </a> |
    <a href="form.php?type=3"> 3 </a> |
    <a href="form.php?type=4"> 4 </a> |
    <a href="form.php?type=5"> 5 </a> |
    <a href="form.php?type=6"> 6 </a>
    <form action="form.php" method="post">
        <fieldset>
            <input type="text" id="name" name="visitor_name" placeholder="Your Name" pattern=[A-Z\sa-z]{3,20} required>
            <br>
            <input type="email" id="email" name="visitor_email" placeholder="Your E-mail" required>
            <br>
            <select id="department-selection" name="concerned_department" required>
                <option value="">Select a Department</option>
                <option value="billing">Billing</option>
                <option value="marketing">Marketing</option>
                <option value="technical support">Technical Support</option>
            </select>
        </fieldset>
        <fieldset>
            <input type="text" id="title" name="email_title" required placeholder="Reason For Contacting Us" pattern=[A-Za-z0-9\s]{8,60}>
            <br>
            <textarea id="message" name="visitor_message" placeholder="Write your message." required></textarea>
        </fieldset>
        <fieldset>
            <div class="row_flex" style="justify-content: left; align-items:flex-end;">
            <img src="captcha<?= $type ?>.php" alt="CAPTCHA" class="captcha-image"> 
            <a href="javascript:void(0)" class="refresh-captcha"><img style="width: 50px; height: auto; display: block; background: #06c" title="Refresh Captcha" src="../../imgPG/sx_svg/sx_svg/sx_reload.svg" alt="Refresh" /></a>
            </div>
            <input type="text" id="captcha" name="captcha_challenge" placeholder="Please Enter the Captcha Text" pattern="[A-Z]{6}">
            
        </fieldset>
        <input class="button" type="submit" value="Send Message" />
    </form>
    <script>
        var refreshButton = document.querySelector(".refresh-captcha");
        refreshButton.onclick = function() {
            document.querySelector(".captcha-image").src = 'captcha<?= $type ?>.php?' + Date.now();
        }
    </script>
</body>

</html>