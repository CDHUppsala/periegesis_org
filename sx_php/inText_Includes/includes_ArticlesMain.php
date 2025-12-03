<?php
include_once dirname(__DIR__) . "/inText_Archives/archives_TextsPagingQuery.php";

/**
 * ==================================================
 * If the Text Comment application is used, 
 *   and adding a comment reqires administration approval
 *   place the file for the approval here, to close the window
 *   without loading text and coments
 * ==================================================
 */
if ($radio_UseTextComments) {
    require dirname(__DIR__) . "/inText_Comments/approve_comment.php";
}

if ($radio_UseRelatedTexts) {
    // Functions are called from default.php and read.php - if $radio_UseRelatedTexts == True
    require dirname(__DIR__) . "/inTexts/functions_texts_related.php";
}
if (intval($int_TextID) > 0) {
    require dirname(__DIR__) . "/inTexts/read.php";

    /**
     * ==================================================
     * Connect the Text application to the Text Comment application
     * Get Comments: 
     * - if they are generally used in the site and
     * - if they are allowed for the article with ID $int_TextID, opened in read.php
     * ==================================================
     */
    if ($radio_UseTextComments && isset($radio__AllowTextComments) && $radio__AllowTextComments) {
        require dirname(__DIR__) . "/inText_Comments/includes_comments.php";
    }

    if (sx_include_FooterText) {
        require dirname(__DIR__) . "/inText_Cards/cards_Functions.php";
        // Opens Related Texts
        if ($radio_UseRelatedTexts && sx_include_RelatedTextsInFooter) {
            require dirname(__DIR__) . "/inText_Cards/cards_TextsRelatedByID.php";
        }
        // Opens Texts from a Theme
        if (sx_include_SelectedThemeInFooter) {
            require dirname(__DIR__) . "/inText_Cards/cards_TextsByTheme.php";
        }
        // Opens Recent Text from the same classification level as the current text
        if (sx_include_SelectedClassInFooter) {
            require dirname(__DIR__) . "/inText_Cards/cards_TextsByClassLevel.php";
        }
    }
} else {
    echo '<section id="jqLoadPageNav" aria-label="Introduction of Articles">';
    require dirname(__DIR__) . "/inTexts/default.php";
    echo '</section>';
}
