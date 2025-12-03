<title><?= $str_MetaTitle ?></title>
<meta name="Creator" content="Public Sphere" />
<meta name="title" content="<?= $str_MetaTitle ?>" />
<meta name="description" content="<?= $str_MetaDescription ?>" />
<?php if (strpos(sx_HOST_PATH, "/fr/") > 0) { ?>
    <meta name="Language" content="fr" />
    <meta property="og:locale" content="fr_FR" />
<?php } elseif (strpos(sx_HOST_PATH, "/en/") > 0) { ?>
    <meta name="Language" content="en" />
    <meta property="og:locale" content="en_GB" />
<?php } elseif (strpos(sx_HOST_PATH, "/sv/") > 0) { ?>
    <meta name="Language" content="sv" />
    <meta property="og:locale" content="sv_SE" />
<?php } elseif (strpos(sx_HOST_PATH, "/fi/") > 0) { ?>
    <meta name="Language" content="fi" />
    <meta property="og:locale" content="fi_FI" />
<?php } else { ?>
    <meta name="Language" content="el" />
    <meta property="og:locale" content="el_GR" />
<?php }
include __DIR__ . "/metainc.php";
?>
