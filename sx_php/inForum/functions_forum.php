<?php

function forum_countItemsByTheme($id)
{
	$conn = dbconn();
	$sql = "SELECT COUNT(ForumID) AS CountArticles, MAX(InsertDate) AS MaxInsertDate 
		FROM forum_articles
		WHERE ForumID = ?
		AND Hidden = False " . str_LanguageAnd . " 
	GROUP BY ForumID ";
	$query = $conn->prepare($sql);
	$query->execute([$id]);
	$rs = $query->fetch(PDO::FETCH_NUM);
	if ($rs) {
		return $rs;
	} else {
		return NULL;
	}
}

function forum_get_active_themes() {
    $conn = dbconn();
    $aResults = null;
    $sql = "SELECT ForumID, ForumTheme" . str_LangNr . " AS ForumTheme,
		InsertDate
	FROM forum
	WHERE Publish = True AND ShowAsActual = True
	ORDER BY ForumID DESC";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResults = $rs;
    }
    return $aResults;
}

function forum_get_previous_themes() {
    $conn = dbconn();
    $aResults = null;
    $sql = "SELECT ForumID, ForumTheme" . str_LangNr . " AS ForumTheme,
		InsertDate
	FROM forum
	WHERE Publish = True 
        AND ShowAsActual = False
        AND ShowAsPrevious = True 
	ORDER BY ForumID DESC";
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $aResults = $rs;
    }
    return $aResults;
}

?>