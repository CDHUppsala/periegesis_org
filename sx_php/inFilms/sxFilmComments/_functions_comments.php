<?php

function sx_GetEmailBodyFilm($intFilmID,$sxTitle,$commentID,$commentCode,$strMainText) {
	//Link to the article
	$sPath = $_SERVER["HTTP_HOST"]; 
	$commentURL = $sPath.$_SERVER["PATH_INFO"];
	$commentURL = $commentURL."?filmID=".$intFilmID."&cid=".$commentID."&cc=".$commentCode;
 
	//The header and footer of the mail
	$Header = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
	$Header = $Header.'<div style="font-family: Verdana, Arial; font-size: 1em; line-height: 140%">';
	$Header = $Header."<b>".LNG_Mail_SendingFromSite.": </b>";
	$Header = $Header. str_SiteTitle." (".$sPath.")<br><br>";
	$Footer = "</div>";
	$Footer = $Footer.'<div style="font-size: 0.8em; text-align: center; background-color: #eeeeee;">'. str_SiteInfo."</div>";
	$Footer = $Footer."</body></html>";
 
	//The content of the mail
	$sxBody = $Header;
	$sxBody = $sxBody.lngClickTheLinkToAddYourComment.":<br><br>";
	$sxBody = $sxBody.'<a href="http://'.$commentURL.'">'.$commentURL."</a><br><br>";
	$sxBody = $sxBody.'<div style="background: #eee; padding: 10px"><b>'.$sxTitle."</b><br><br>";
	$sxBody = $sxBody.$strMainText."</div><br><br>";
	$sxBody = $sxBody."<b>".lngIfNotExpectedNeglectThisEmail."</b><br><br>";
	$sxBody = $sxBody.$Footer;
	return  $sxBody;
}
 
function sx_GetNumberOfCommentsFilm($id) {
	$conn = dbconn();
	$sql = "SELECT count(*) as NumberOf FROM film_comments WHERE FilmID = ? AND Visible = True ";
	$stmtf = $conn->prepare($sql);
	$stmtf->execute([$id]);
	$rsf = $stmtf->fetch(PDO::FETCH_ASSOC);
	if ($rsf) {
		return  $rsf["NumberOf"];
	}else{
		return  0;
	}
	$rsf = null;
	$stmtf = null;
}
function sx_GetLastCommentsFilm($id) {
	$conn = dbconn();
	$sql = "SELECT CommentID FROM film_comments WHERE FilmID = ? AND Visible = True ORDER BY InsertDate DESC ";
	$stmtf = $conn->prepare($sql);
	$stmtf->execute([$id]);
	$rsf = $stmtf->fetch(PDO::FETCH_ASSOC);
	if ($rsf) {
		return  $rsf["CommentID"];
	}else{
		return  0;
	}
	$rsf = null;
	$stmtf = null;
}
?>