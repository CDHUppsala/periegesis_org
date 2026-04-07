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
define('sx_Socket', getenv('SITE_SOCKET') ?: "https://");
define('sx_SiteURL', getenv('SITE_URL') ?: "www.periegesis.org");
define('sx_TrueSiteURL', sx_Socket . sx_SiteURL);

/**
 * In production environment, set the value of the next constant to true
 */
define('sx_CheckTrueSiteURL', getenv('CHECK_TRUE_SITE') === 'true');

define('sx_RadioMultiLang', false);
define('sx_DefaultSiteLang', "en");

$langArr = [
    ["en","English",""],
];

define("sx_LangArr", $langArr);
