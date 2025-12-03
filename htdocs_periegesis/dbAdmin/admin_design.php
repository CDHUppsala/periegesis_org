<?php
/*  ===================
    Show/Hide errors
    ===================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

*/
// Folder where you upload document files downloadable only for login members
CONST sx_PrivateArchivesFolder = "sxLoginArchives";
// Folder where you uploa CSV, XML or JSON files used to update tables
CONST SX_PrivateInportExportFilesFolder = "annotations";

/**
 * Redefine some constants from the site design
 */

CONST sx_TextTableVersion = "articles";
CONST sx_includeConferences = false;

CONST sx_ConfigProjectName = "Research Community";
CONST sx_DefaultAdminLang = "en";

// To show configuration database tools in content manu
CONST sx_radioShowConfigDB = true;
CONST sx_radioShowConfigTools = true;
CONST sx_radioShowDesignTools = true;
CONST SX_allowSingleFolderCreation = true;

// Text-related Includes
CONST sx_ShowByPublishInFirstPage = false;
CONST sx_includeAsideTexts = false;
CONST sx_includeSlider = false;
CONST sx_radioIncludeTextComments = false;
CONST sx_radioIncludeTextToAuthors = false;
CONST sx_radioIncludeBookComments = false;

// For text lists to restrict Huge list to the last X Months 
CONST sx_radioUseLastPublishedMonths = false;
CONST sx_LastPublishedMonths = 3;

// For site maps
CONST sx_radioUseArticleMaps = true;
CONST sx_radioUseTextMaps = false;
CONST sx_radioUseTextMapsByYear = false;
CONST sx_radioUseAboutMaps = true;
CONST sx_radioUseAuthorMaps = false;
CONST sx_radioUseThemesMaps = false;
CONST sx_radioUseEventMaps = false;
CONST sx_radioBookMaps = false;
CONST sx_radioUseConferneceMaps = false;
CONST sx_radioUseProductMaps = false;
 
// For sending email - Newsltters
CONST sx_radioIncludeNewslettersByGroup = false;
CONST sx_radioIncludeUsersList = false;
CONST sx_radioIncludeStudentsList = false;
CONST sx_radioIncludeStudentsListByCourse = false;
CONST sx_radioIncludeMembersLists = false;
CONST sx_includeConferenceParticipants = false;
CONST sx_radioParticipantsByConference = false;
CONST sx_radioIncludeCustomersList = false;

// For Products
CONST sx_radioUseAccessories = false;
CONST sx_IncludeProductStats = false;

CONST SX_radioTestEnvironment = false;
