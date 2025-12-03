<?php

/**
 * This file connects the text comment application to the site's 
 *   main text application. Else, they are completely independed.
 * Place it at the end of the file for reading texts (read.php)
 */
/**
 * All neccessary configuration are found in the next include
 *   where the 3 control levels of the Text Comment System are defined,
 *   based on values in the table "site_config_basic" (see sx_Config.php)
 */
include dirname(__DIR__) . "/inText_Comments/config_comments.php";

if ($show_Comments) {
    /**
     * Level 3: Open Text Comment System:
     *          Read and Add comments without login
     *          Added comments require admin approval to be visible
     */
    require dirname(__DIR__) . "/inText_Comments/default.php";

    if ($message_LoginTo_Add) {
        /**
         * Level 2: Partially closed Text Comment System:
         *          Read comments without login
         *          Login is required for adding comments
         *          With login, comments can be added without admin approval
         */
        echo '<section><div class="bg_info align_center">';
        echo '<p>' . LNG_Comments_SignUpInToAddComment . '</p>';
        echo '</div></section>';
    }
} elseif ($message_LoginTo_AddRead) {
    /**
     * Level 1: Completely closed Text Comment System:
     *          Read and Add comments requires login
     *          With login, comments can be added without admin approval
     */
    echo '<section><div class="bg_info align_center">';
    echo '<p>' . LNG_Comments_SignUpInToReadAddComment . '</p>';
    echo '</div></section>';
}
