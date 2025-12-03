<?php
require realpath(dirname($_SERVER['DOCUMENT_ROOT'])."/sx_SiteConfig/sx_languages.php");
function Redirect($url, $permanent = false) {
    header('Location: '. $url, true, $permanent ? 301 : 302);
    exit();
}

Redirect(sx_TrueSiteURL ."/". sx_DefaultSiteLang ."/", false);
