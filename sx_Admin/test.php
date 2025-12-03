<?php
include "/sxUpload/sxConfigUpload.php";

//$skata = sx_getFolderContentsGlob("../imgMedia/", "is_dir");

echo "Only files\n";
$files = array_filter(glob("../images/*"), 'is_file');
echo "<pre>";
var_dump($files);
echo "</pre>";


echo "All files:\n";
$all = glob("../images/*");
echo "<pre>";
var_dump($all);
echo "</pre>";

echo "Only directories\n";
$dirs = glob("../images/*", GLOB_ONLYDIR);
echo "<pre>";
var_dump($dirs);
echo "</pre>";


$dirs = array_filter(glob("../imgMedia/*"), "is_dir");

echo "<pre>";
var_dump($dirs);
echo "</pre>";


$dir = preg_grep('~conf_~i', glob("../images/*", GLOB_ONLYDIR));
echo "<pre>";
var_dump($dir);
echo "</pre>";

?>