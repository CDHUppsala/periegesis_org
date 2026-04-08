<?php

$protocol = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    ($_SERVER['SERVER_PORT'] == 443) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
) ? "https://" : "http://";

define("sx_ROOT_HOST", $protocol . $_SERVER["HTTP_HOST"]);
define("sx_ROOT_HOST_ADMIN", $protocol . $_SERVER["HTTP_HOST"] . '/dbAdmin/');

/**
 * Load CSS and JavaScript files from the root directory
 * In developement environment, change to the common, local
 *   dbAdmin directory for developing CSS and JS files 
 */
const sx_ADMIN_DEV = sx_ROOT_HOST_ADMIN;
