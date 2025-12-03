<?php
/*
	The file config_articles.php, which inludes initial variables,
	is placed in the HEAD Element of the articles.php
*/
$radio_UseTextsArticles = true;
if ($radio_UseTextsArticles == false) {
    Header("Location: index.php");
    exit;
}
include  dirname(__DIR__) . "/functions_pagination.php";

if (intval($int_ArticleID) > 0) {
    include  dirname(__DIR__) . "/apps_functions.php";
    include  dirname(__DIR__) . "/index.php";
} else {
    include  dirname(__DIR__) . "/read_more.php";
}
