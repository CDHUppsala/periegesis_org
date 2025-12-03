<?php
CONST SX_radioTestEnvironment = false;

// TEXT VARSION TABLE: texts_blog, texts_news, texts
CONST sx_includeTextTables = false;
CONST sx_TextTableVersion = "articles";
CONST sx_ReplaceListImage = "logo/Digital_Periegesis_bg.svg";
CONST SX_Inclued_Statistics_Periegesis = true;

/**
 * Path to hidden files - accessible only for login users
 * must be subfolder to /private 
 */
CONST sx_PrivateArchivesPath = "sxLoginArchives/";

CONST sx_usedCurrency = "€";

CONST sx_DefaultImgFolder = "../images/";
CONST sx_UseLightBox = true;

CONST sx_showPaginationInFirstPage = true;
CONST sx_includeFormPaginationInFirstPage = false;

CONST sx_includeAsideTexts = false;
//CONST sx_arrayTextGroupsByAside = array(1,2);

CONST sx_radioUseHyphenator = true;

// Database version: to be removed
CONST sx_radioMySQLDatabase = true;

// Multilinqual Texts
CONST sx_includeMultilinqual = true;

/**
 * Special variables - or to be removed
 */

//if external link to top media also will be used for First Page Media
CONST sx_includeExternalLinkInFirstPageImage = false;

/**
 * Initial settings for the use of Applications
 */
CONST sx_includeSlider = true;
CONST sx_includeForum = false;
CONST sx_includeUsersLogin = false;
CONST sx_includeUsersLogin_OnTop = true;
CONST sx_includeSpotLights = false;
CONST sx_IncludeWeekProgram = false;
CONST sx_IncludeQuiz = false;
CONST sx_IncludeSurveys = false;
CONST sx_includeFAQ = false;
CONST sx_includeMenu = false;
CONST sx_includeMusic = false;

CONST sx_IncludePromotion = true;
CONST sx_includeAnchoreInPromotion = true;

CONST sx_IncludeFirstPageSections = true;
CONST sx_includeAnchoreInFirstPageSections = true;
CONST sx_ButtonSectionClass_1 = "button-grey buton-big button-arrow button-gradient";
CONST sx_ButtonSectionClass_2 = "button-grey buton-big button-gradient";

CONST SX_ButtonPrintClass = "button-border button-border-black button-big";

CONST sx_includeEvents = false;
CONST sx_includeBooks = true;
CONST sx_includeGallery = false;
CONST sx_includeFolderGallery = false;
CONST sx_includeMMGallery = false;
CONST sx_includePDFArchive = false;
CONST sx_includePDFArchive_OnTop = true;
CONST sx_includeReports = true;
CONST sx_includeMembersArea = false;
CONST sx_includeFilms = false;
CONST sx_includeFilms_OnTop = false;

CONST sx_intNumberOfStars = 5;

/**
 * For organization of couses with student registration
 */
CONST sx_IncludeCourses = true;
CONST sx_IncludeCoursesInTop = false;
CONST sx_radioRegRequiresActiveCourse = false;

/**
 * Any set of Text Groups (including their categories, subcatagories and articles) can 
 *   be defined to be accessible only for logged in users.
 * When the user is logged in, these groups can be displayed i 2 ways:
 * 1.   In the ordinary menus of the site (by default)
 * 2.   Both in the ordinary menu and in a separate aside menu
 *          set sx_IncludeLoginGroupsIn_SeparateMenu = true
 */
CONST sx_IncludeLoginGroupsIn_SeparateMenu = true;

//  Max Search results
CONST sx_intMaxTopSearch = 100;

//  For big databases with frequent texts, set Search from last 3 months as default
CONST sx_radioSearchFromLastThreeMonths = false;

CONST sx_ShowSocialMediaOnTop = false;
CONST sx_IncludeTopContact = false;
CONST sx_IncludeTopSearch = false;
CONST sx_IncludeNavSearch = true;
CONST sx_IncludeTopLinks = true;
CONST sx_IncludeNavLinks = false;

/**
 * Design First Page, Classification Lists and Archives of texts
 * sx_HighlightFirstText : different css for the first text
 * sx_UseTwoColumnsInFirstPage : set to false if sx_UseTwoColumns is true and you use Aside Texts 
 * sx_showTopPagination : Hides pagination information (Breadcrumbs) on the top of list
 */
CONST sx_HighlightFirstText = true;
CONST sx_UseTwoColumns = true;
CONST sx_UseTwoColumnsInFirstPage = true;
CONST sx_showTopPagination = false;

// ADVERTISE FUNCTION
CONST sx_includeHeaderAds = true;
CONST sx_includeFooterSlider = true;
CONST sx_includeFooterMain = true;
CONST sx_includeFooterAds = true;
CONST sx_IncludeDialogAds = true;
CONST sx_includeLogoAds = true;

/**
 * Alternative uses of Text Themes:
 * 	1 Use Groups of Themes
 * 	2 Use the InsertDate to Group Themes by Year
 * Both alternative cannot be true, so the first has priority to the second one
 */
CONST sx_includeThemeListsByGroup = true;
CONST sx_includeThemeListsByYear = false;
CONST sx_ShowThemeYearInList = true;

/**
 * Related texts will be shown only in First Page
 * - Not in Pagination of Archive List
 * If false, a new field must be created in Texts Table to prohibit text 
 * to be published when open archive lists of groups, etc.
 */
CONST sx_ShowRelatedOnlyInFirstPage = false;

/**
 * Groups of About texts can be open separately on Top Menu 
 * - No effect if About Text are not classified in Groups
 */
CONST sx_ShowAboutHeaderMenuAtStart = true;
CONST sx_AboutHeaderMenuByGroups = false;
CONST sx_AboutHeaderMenuByGroupsInList = false;

// If the About menu is shown by Groups, you can define here
// if the aside menu should show All Groupsa or only the currently opened one
CONST sx_ShowAboutAsideMenuByCurrentGroup = false;

// Show selected articles and about texts in footer
CONST SX_IncludeAboutTextsInFooter = true;
CONST SX_IncludeArticlesInFooter = true;
// In index.php show recent articles in the aside section
CONST SX_IncludeArticlesInIndexAside = true;

/**
 * Show all language flags, even that of the current langauge
 */
CONST sx_includeLanguageFlagsOnTop = false;
CONST sx_ShowCurrentLangFlag = true;
CONST sx_ShowLangByFlag = false;

/**
 * ASIDE MENUW
 * Menus NOT Defined in Text Configuration Table
 */
CONST sx_ShowRecentMost_BothTextsAndComments_Tabs = true;
CONST sx_ShowRecentMost_BothTextsAndComments_Accordion = true;
CONST sx_IncludeTextByCalender = true;
CONST sx_IncludeTextByYearMonth = false;

/**
 * Do not use captcha for login pages with Tokens
 * Can be activated to prevent brute force attacks
 */
CONST sx_radio_UseUsersLoginCaptcha = false;
CONST sx_radio_UseUserRegistratioCaptcha = true;
CONST sx_radio_UseStudentsLoginCaptcha = false;
CONST sx_radio_UseStudentRegistratioCaptcha = true;
CONST sx_radio_UseForumLoginCaptcha = false;
CONST sx_radio_UseForumRegistratioCaptcha = true;
CONST sx_radio_UseContactCaptcha = false;
CONST SX_countMessageLength = true;
/**
 * Text in the Footer of the articles.php page
 * Set sx_include_FooterText to true if anny of the other options is true
 */

CONST sx_include_FooterText = true;

CONST sx_include_RelatedTextsInFooter = true;
CONST sx_RelatedTextsByCards = true;
CONST sx_include_SelectedThemeInFooter = true;
CONST sx_SelectedThemeByCards = true;
CONST sx_include_SelectedClassInFooter = true;
CONST sx_SelectedClassByCards = true;
/* Set to false if images are portraits */
CONST SX_radioAbsolutCardImages = false;

CONST SX_radioShowIngressInTextCards = true;
CONST SX_TextIngressLength = 100;

/**
 * For conference sites
 */
CONST sx_includeConferences = false;
CONST sx_includeParticipantsLogin = false;

/**
 * Transforms Footer Cycler to Card Advertising
 * This makes it possible to have 3 Cart Advertises simulteneously
 * Cycler is disfunctional in mobiles!
 */
CONST sx_radioUseFooterCycler = true;

/**
 * Places Logo backgroun image in the Flex part of Header instead of the Header
 */
CONST sx_setLogoBackgroundInFlex = true;

CONST sx_ListAuthorsByAlphabet = true;

/**
 * FORM OF ACCORDION EFFECTS
 * Class jqAccordionNav means that opening of one level closese all other levels
 * Class jqAccordionNavNext means that evry level is opened and closed separately.
 */
CONST sx_jqAccordionForm = "jqAccordionNav";

CONST sx_showDatesInMainMenu = true;

/**
 * General DESIGN ALTERNATIVES
 */
CONST SX_IncludeLogoInHeader = true;
CONST SX_includeAppsNavigation = true;
CONST SX_appsNavigationVisibleInMobiles = false;
CONST SX_includeTextPrintFunctions = true;
// if external downloads (PDF) will open in iFrame, showing the external file
CONST SX_includeExternalDownloadsInFrame = false;
// just replace "Download Document XX" with "Download my CV"
CONST SX_downloadExternalDocumentIsCV = false;
// If in inline gallery image names will be transformed to titles and shown in figcaption
CONST SX_showCaptionInGallery = true;
// if any image name will be transformed to title and shown in figcaption
CONST SX_radioCreateCaptionByMediaName = true;

/**
 * DESIGN ALTERNATIVES for Articles
 */
/**
 * Articles menu can be shown by Group and Categoryor or by 
 * Goup and Article (when articles are few and no categories are used)
 */
CONST sx_radioUseArticles = true;
CONST sx_radioArticleMenuByArticle = false;
CONST SX_radioShowArticleDate = true;

// If pagination of articles will incude images and inroductory text
CONST SX_IncludeArticlePagingInFirstPage = false;
CONST SX_IncludeImagesInArticlePaging = true;
CONST SX_IncludeMediaInArticlePaging = false;
CONST SX_IncludeIntroductionInArticlePaging = true;
// the number of articles by page, in pagination of articles
CONST SX_pageSizeForArticles = 12;

// If Article Groups in Menue will inklude a link the opens the entire group
CONST SX_useLinksInArticleGroups = false;
CONST SX_includeClassArticlesInCards = true;
CONST SX_SetPrefixInReadMoreArtticles = false;

CONST sx_radioUsePosts = false; //Delete
CONST sx_radioUseItems = false;

CONST SX_inludeLinkToLogtypsInFooter = true;

CONST SX_shwoWideSliderInIndexPage = false;

CONST SX_includeSubjectInContactForm = true;

CONST SX_includeCSVToTableFunctions = true;

CONST SX_includePlaceMaps = true;

/**
 * If images have different dimentions, order them with a common first letter (H,V,W)
 *  - different first letters are displayed in different flex-cards
 */
CONST SX_newFlexCardByNewFirstLetter = false;

CONST SX_RequireNameAndPostalCodeInNewsLeters = false;

/**
 * Special CONSTants
 */
CONST SX_showSpecialFooter = false;

/**
 * Adapted Application constants, to be included on the top menu
 */
CONST SX_IncludeWikidataSearch = true;

CONST SX_IncludeAppTextToMaps = true;
CONST SX_IncludeAppTextToMapsTitle = "Read Pausanias with Maps";

CONST SX_IncludeAppSearchMaps = true;
CONST SX_IncludeAppSearchMapsTitle = "Search in Maps";

CONST SX_IncludeExternalLink = true;
CONST SX_IncludeExternalLinkTitle = "Nodegoat Visualizations";
CONST SX_IncludeExternalLinkURL = "https://nodegoat.abm.uu.se/viewer.p/5/4263/scenario/6/geo/";

