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

const sx_Socket = "https://";
//const sx_SiteURL = "www.periegesis.org";
const sx_SiteURL = "www.periegesis.abm.uu.se";

/*
    Set sx_radioCheckTrueSiteURL = false for test domains
    False is Not valid for root directory
*/
const sx_radioCheckTrueSiteURL = true;

define("sx_TrueSiteURL", sx_Socket . sx_SiteURL);

const sx_RadioMultiLang = false;
const sx_DefaultSiteLang = "en";

$langArr = array(
    array("en", "English", "")
);

define("sx_LangArr", $langArr);
