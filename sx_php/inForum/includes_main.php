<?php
include __DIR__ . "/functions_forum.php";
$strPG = $_GET['pg'] ?? '';

if ($strPG == "conditions") {
    include __DIR__ . "/conditions.php";
} elseif ((int) $intArticleID > 0) {
    include __DIR__ . "/view_article.php";
} elseif ((int) $intForumID > 0) {
    include __DIR__ . "/view_theme_articles.php";
} else {
    include __DIR__ . "/view_themes.php";
}
