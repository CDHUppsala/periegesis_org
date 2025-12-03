<?php
require realpath(dirname($_SERVER['DOCUMENT_ROOT'])."/sx_SiteConfig/sx_design.php");

/**
 * This is a backup file
 * You can use it, or use the corresponding file 
 * in the public administration folder (/dbAdmin)
 */

// To show configuration database tools in content manu
CONST sx_radioShowConfigDB = true;

//Used Text Table: texts, text_news, texts_blog or news (for Shopping)
//=================================
CONST sx_radioIncludeTextComments = false;
CONST sx_radioIncludeTextToAuthors = false;
const sx_radioIncludeBookComments = false;
CONST sx_RadioUseFirstPageTexts = true;

// For Products with accessories
CONST sx_radioUseAccessories = False;
 
// For text lists to restrict Huge list to the last X Months 
CONST sx_radioUseLastPublishedMonths = False;
CONST sx_LastPublishedMonths = 3;

// For site maps
CONST sx_radioUseTextMaps = True;
CONST sx_radioUseTextMapsByYear = True;
CONST sx_radioUseAboutMaps = True;
CONST sx_radioUseAuthorMaps = True;
CONST sx_radioUseThemesMaps = True;
CONST sx_radioUseEventMaps = false;
CONST sx_radioBookMaps = False;
CONST sx_radioUseConferneceMaps = false;
CONST sx_radioUseProductMaps = False;
 
//For sending email - Newsltters
//=================================
CONST sx_radioIncludeNewslettersByGroup = True;
CONST sx_radioIncludeUsersList = false;
CONST sx_radioIncludeStudentsList = true;
CONST sx_radioIncludeStudentsListByCourse = true;
CONST sx_radioIncludeMembersLists = false;
CONST sx_includeConferenceParticipants = true;
CONST sx_radioParticipantsByConference = false;
CONST sx_radioIncludeCustomersList = False;

 
//For statistics
//=================================
CONST sx_IncludeProductStats = False;
CONST sx_ProductTable = "products"; // OR gallaries
