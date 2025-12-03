<meta property="og:type" content="<?= $str_PropertyType ?>">
<meta property="og:title" content="<?= $str_MetaTitle ?>">
<meta property="og:url" content="<?= sx_LOCATION ?>">
<meta property="og:site_name" content="<?= $str_SiteTitle ?>">
<meta property="og:description" content="<?= $str_MetaDescription ?>">
<?php if (!empty($str_PropertyImage)) { ?>
    <meta property="og:image" content="<?= sx_ROOT_HOST . "/" . $str_PropertyImage ?>">
    <?php
    $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/" . $str_PropertyImage;
    $imagePath = mb_convert_encoding($imagePath, 'UTF-8', 'auto');
    if (file_exists($imagePath) && is_readable($imagePath)) {
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo) { ?>
            <meta property="og:image:width" content="<?php echo $imageInfo[0] ?>" />
            <meta property="og:image:height" content="<?php echo $imageInfo[1] ?>" />
            <meta property="og:image:type" content="<?php echo $imageInfo[2] ?>" />
<?php
        }
    }
} ?>
<link rel="canonical" href="<?= sx_LOCATION ?>">
<link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">