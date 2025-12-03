<?php

/**
 * Define here all the condition for the functioning
 *   of the Text Comment System, using veriables from
 *   the table "site_config_basic" (see sx_Config.php).
 * 
 * 1. Completely closed system: $radio_LoginToAddReadComments = true
 *      * login to both add and read comments
 * 2. Partially closed system: $radio_LoginToAddComment = true
 *      * Login to add but not to read comments
 * 3. Open system: $send_MailFor_AdminApproval = true
 *      * Read and add without login, 
 *      * Added comments require Admin approval via email
 *        to be visible in the site, but not if user is logged in.
 */

$show_Comments = false;
$show_Comment_Add_Form = false;

$message_LoginTo_AddRead = false;       // Level 1: login to add and read
$message_LoginTo_Add = false;           // Level 2: login to add but not to read
$send_MailFor_AdminApproval = false;    // Level 3: add and read without login, adding requires admin approval, if not logged in

if ($radio_UseTextComments) {

    if ($radio_LoginToAddReadComments) { // Level 1: login to both add and read
        if ($radio__UserSessionIsActive) {
            $show_Comments = true;
            $show_Comment_Add_Form = true;
        } else {
            $message_LoginTo_AddRead = true;
        }
    } elseif ($radio_LoginToAddComment) { // Level 2: Login to Add but not to read
        if ($radio__UserSessionIsActive) {
            $show_Comments = true;
            $show_Comment_Add_Form = true;
        } else {
            $show_Comments = true;
            $message_LoginTo_Add = true;
        }
    } else { // Level 3: Read and add without login, adding requires Admin approval via email
        if ($radio__UserSessionIsActive == false) {
            $send_MailFor_AdminApproval = true;
        }
        $show_Comments = true;
        $show_Comment_Add_Form = true;
    }
}
