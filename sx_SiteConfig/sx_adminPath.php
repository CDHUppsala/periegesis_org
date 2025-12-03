<?php

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

define("sx_ROOT_HOST", $protocol . $_SERVER["HTTP_HOST"]);
define("sx_ROOT_HOST_ADMIN", $protocol . $_SERVER["HTTP_HOST"] .'/dbAdmin/');

/**
 * To link to common sources of CSS and JavaScript files during development:
 * Change the path between development site ana real production site
 */
CONST sx_ADMIN_DEV = sx_ROOT_HOST_ADMIN;
