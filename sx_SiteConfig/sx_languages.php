<?php
ob_start();
session_start();
/*
session_start([
    'use_only_cookies' => 1,
    'cookie_lifetime' => 0,
    'cookie_secure' => 1,
    'cookie_httponly' => 1
  ]);
*/

/**
 * Set the URL addresses for the production environment
 * Provides a canonical‑URL enforcement layer that:
 * - Ensures one single, correct, official URL, preventing SEO duplication
 * - Ensures consistency of sessions, as they exist under the same hostname
 * - Blocks reverse‑proxy or mirror access
 * - Prevents accidental or malicious hostname variations
 */
const sx_Socket = "https://";
const sx_SiteURL = "www.periegesis.org";
const sx_TrueSiteURL = 'https://www.periegesis.org';

/**
 * In production environment, set the value of the next constant to true
 * In local, development environment, set the value to false to avoid redirection to the above production URL
 * The check is pursued in sx_sitePaths.php
 */
const sx_CheckTrueSiteURL = false;

const sx_RadioMultiLang = false;
const sx_DefaultSiteLang = "en";

$langArr = [
    ["en","English",""],
];

define("sx_LangArr", $langArr);
