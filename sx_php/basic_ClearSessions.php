<?php

/**
 * Clean upp All Search sessions
 */

if (
	(isset($_SESSION["sort"]) &&
		strpos(sx_PATH, "/search.php") == 0 &&
		strpos(sx_PATH, "/search_wikidata.php") == 0) ||
	isset($_GET["clear"])
) {
	unset($_SESSION["Search"]);
	unset($_SESSION["CatWhere"]);
	unset($_SESSION["CatID"]);
	unset($_SESSION["DatumWhere"]);
	unset($_SESSION["ShowPeriod"]);
	unset($_SESSION["ShowYear"]);
	unset($_SESSION["Order"]);
	unset($_SESSION["OrderBy"]);
	unset($_SESSION["Page"]);
	unset($_SESSION["AllYears"]);
	unset($_SESSION["TypePlace"]);
	unset($_SESSION["GroupSearch"]);
	unset($_SESSION["GroupWhere"]);
	unset($_SESSION["SearchLow"]);
	unset($_SESSION["GroupID"]);
	unset($_SESSION["CategoryID"]);
	unset($_SESSION["Datum"]);
	unset($_SESSION["FirstYears"]);

	unset($_SESSION["sort"]);
	unset($_SESSION["SearchText"]);
	unset($_SESSION["SearchPlace"]);
	unset($_SESSION["SearchType"]);
	unset($_SESSION["SearchWhere"]);
	unset($_SESSION["BindSearchWhere"]);
	unset($_SESSION["OrderByColumn"]);
	unset($_SESSION["PageSize"]);
	
	unset($_SESSION['ShowPausaniasPersons']);
}

/**
 * Remove other sessions
 */

/*
contact.php
unset($_SESSION["captcha_code"]);
unset($_SESSION["EmailForm_sx_token"]);

unset($_SESSION["CommentsFormToken_sx_token"]);
unset($_SESSION["UsersLogin_sx_token"]);

conference.php
unset($_SESSION["LoginParticipants_sx_token"]);
*/
