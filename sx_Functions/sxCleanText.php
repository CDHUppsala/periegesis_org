<?php

/**
 * @param string $code : phone or postal code as string, 
 * @param bool $space : false removes all spaces, true keeps a single space between numbers
 * @return string : removes all characters exept numbers and optionally spaces between numbers
 */
function sx_Return_Number_Space($strText, $space = false)
{
    if(empty($strText)) {
        return '';
    }
    $CHAR = "0123456789";
    $strResult = "";
    for ($i = 0; $i < strlen($strText); $i++) {
        $strChar = substr($strText, $i, 1);
        if (strpos($CHAR, $strChar, 0) !== false) {
            $strResult .= $strChar;
        }
        if ($space && $strChar == " ") {
            $strResult .= " ";
        }
    }
    $strResult = trim($strResult);
    if ($space) {
        for ($x = 0; $x < strlen($strResult); $x++) {
            if (strpos($strResult, " ") > 0) {
                $strResult = str_replace("  ", " ", $strResult);
            } else {
                break;
            }
        }
    }
    return $strResult;
}

/**
 * @param string $str : any text/HTML string
 * @return string : replaces single quotes by apostrophs ("'" by "’")
 *   and double quotes by double apostrophs (""" by "”")
 */
function sx_Replace_Quotes($str)
{
    if (!empty($str)) {
        if (strpos($str, "'", 0) !== false) {
            $str = str_replace("'", "’", $str);
        }
        if (strpos($str, '"', 0) !== false) {
            $str = str_replace('"', "”", $str);
        }
    }
    return $str;
}

/**
 * @param string $str : any text/HTML string
 * @return string : replaces latin quotes to greek ones, if possible
 */
function sx_change_To_Greek_Quote($str)
{
    $str = str_replace("“", "«", $str);
    $str = str_replace("”", "»", $str);
    $str = sx_Replace_Quotes($str);
    return $str;
}

/**
 * Removes single and double quotes (for javascript element titles and image alts)
 * @param string $str : any text/HTML string
 * @return string : cleaned from quotes
 */
function sx_Remove_Quotes($str)
{
    $_retval = null;
    if (!empty($str)) {
        if (strpos($str, "'", 0) !== false) {
            $str = str_replace("'", "", $str);
        }
        if (strpos($str, '"', 0) !== false) {
            $str = str_replace('"', "", $str);
        }
        $_retval = $str;
    }
    return $_retval;
}

/*
===============================================
FORM PURE TEXT FROM TEXTAREAS AND TEXT INPUTS
===============================================
*/

/**
 * 1. Separate paragraphs by 2 Rows
 * @param string $txt : pure text from text area
 * @return string : Prepare the text ta add <p>...</p> between paragraphs:
 * - Removes more than 1 empty spaces,
 * - Transforms 1 or more separated rows to 2 rows
 * - removes rows from the bigining and the end of text
 */
function sx_Fix_Text_Rows($txt)
{
    if(empty($txt)) {
        return '';
    }

    $txt = stripslashes($txt);
    $txt = strip_tags($txt);
    while (strpos($txt, "\t", 0) > 0) {
        $txt = str_replace("\t", " ", trim($txt));
    }
    while (strpos($txt, "  ", 0) > 0) {
        $txt = str_replace("  ", " ", trim($txt));
    }
    while (strpos($txt, " \r\n", 0) > 0) {
        $txt = str_replace(" \r\n", "\r\n", trim($txt));
    }
    while (strpos($txt, "\r\n ", 0) > 0) {
        $txt = str_replace("\r\n ", "\r\n", trim($txt));
    }
    while (strpos(trim($txt), "\r\n\r\n", 0) > 0) {
        $txt = str_replace("\r\n\r\n", "\r\n", $txt);
    }
    $txt = str_replace("\r\n", "\r\n\r\n", $txt);
    while (strrpos(trim($txt), "\r\n", 0) > (strlen(trim($txt)) - 1)) {
        $txt = substr(trim($txt), 0, (strlen(trim($txt)) - 2));
    }
    return trim($txt);
}

/**
 * 2. Replace rows by paragraph tags (<p>...</p>)
 * @param string $txt : text where paragraphs are separated be 2 row (from sx_Fix_Text_Rows())
 * @return string : text with added paragraphs (<p>...</p>)
 */
function sx_ParagraphBreaks($txt)
{
    $txt = str_replace("\r\n\r\n", "\r\n", $txt);
    $txt = str_replace("\r\n", "</p><p>", $txt);
    return "<p>" . trim($txt) . "</p>";
}

/**
 * Break URL paths to avoid automatic transformation to links
 * @param string $txt : text that may include URL links
 * @return string : text with unclickable URL Links
 */ function sx_SplitURL($txt)
{
    if (!empty($txt)) {
        if (strpos($txt, "://", 0) !== false) {
            $txt = str_replace("://", ": //", $txt);
        }
        if (strpos($txt, "www.", 0) !== false) {
            $txt = str_replace("www.", "www .", $txt);
        }
    }
    return $txt;
}

/**
 * Sanitize Input Text
 * @param string $str : a text/HTML string from all inuts except textarea
 * @return string : Sanitized text in the following order
 * - trim,
 * - sx_Replace_Quotes,
 * - strip_tags,
 * - htmlspecialchars,
 * - sx_SplitURL
 */

function sx_Sanitize_Input_Text($str)
{
    $str = trim($str ?? '');
    $str = strip_tags($str); 
    $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    return $str;
}
/**
 * Sanitize Search Text
 * @param string $str : a text/HTML string from all inuts except textarea
 * @return string : Sanitized text in the following order
 * - trim,
 * - sx_Remove_Quotes,
 * - strip_tags,
 * - htmlspecialchars,
 */
function sx_Sanitize_Search_Text($str)
{
    $str = trim($str ?? '');
    $str = sx_Remove_Quotes($str);
    $str = strip_tags($str);
    $str = htmlspecialchars($str);
    return $str;
}

function sx_html_sc(?string $str): string {
    $str = trim($str ?? '');
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize Text Area
 * @param string $str : a text/HTML string from textarea
 * @return string : Sanitized text in the following order
 * - sx_Sanitize_Input_Text() same as Input Text,
 * - sx_Fix_Text_Rows() inserts 2 rows between paragraphs,
 * - sx_ParagraphBreaks() replaces rows by paragraph tags (<p>...</p>)
 *      before uploading to the database
 */
function sx_Sanitize_Text_Area($str)
{
    return sx_ParagraphBreaks(sx_Fix_Text_Rows(sx_Sanitize_Input_Text($str)));
}

/**
 * Sanitize Text Area to be reused in case of form submit error
 * Fix rows between paragraphs
 * Do not Break (add) paragraphs
 * @param string $str : a text/HTML string from textarea
 * @return string : Sanitized text, without adding paragraphs:
 * - sx_Sanitize_Input_Text() same as Input Text,
 * - sx_Fix_Text_Rows() inserts 2 rows between paragraphs,
 */
function sx_Sanitize_Text_Area_Rows($str)
{
    return sx_Fix_Text_Rows(sx_Sanitize_Input_Text($str));
}

function sx_Check_Greek_Language($txt)
{
    if(empty($txt)) {
        return false;
    }
    $_retval = false;
    $LGR = "ΑαΆάΕεΈέΗηΉήΙιΊίΟοΌόΥυΎύΩωΏώ";
    for ($i = 1; $i <= strlen($txt); $i++) {
        $strChar = substr($txt, $i - 1, 1);
        if (strpos($LGR, $strChar, 1) !== false) {
            $_retval = true;
            break;
        }
    }
    return $_retval;
}

function sx_GetSanitizedLatinLetters($strText)
{
    if(empty($strText)) {
        return '';
    }
    $VALID_ANC = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $strResult = "";
    for ($i = 0; $i < strlen($strText); $i++) {
        $strChar = substr($strText, $i, 1);
        if (strpos($VALID_ANC, $strChar, 0) !== false) {
            $strResult .= $strChar;
        }
    }
    return trim($strResult);
}

function sx_checkTableAndFieldNames($strText)
{
    if(empty($strText)) {
        return '';
    }
    $VALID_TABLE = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_";
    $radioResult = false;
    for ($i = 0; $i < strlen($strText); $i++) {
        $strChar = substr($strText, $i, 1);
        if (strpos($VALID_TABLE, $strChar, 0) !== false) {
            $radioResult = true;
        } else {
            $radioResult = false;
            break;
        }
    }
    return $radioResult;
}

function sx_GetSanitizedPhone($strText) {
    if (empty($strText)) {
        return '';
    }
    $VALID_PHONE = "0123456789";
    $strResult = '';

    for ($i = 0; $i < strlen($strText); $i++) {
        $strChar = substr($strText, $i, 1);
        if ($strChar == '+' && $i == 0) {
                $strResult .= $strChar;
        }elseif (strpos($VALID_PHONE, $strChar) !== false) {
            $strResult .= $strChar;
        }
    }
    return trim($strResult);
}

function sx_return_numbers($str) {
    if (empty($str)) {
        return '';
    }
    $VALID_NUMBERS = "0123456789";
    $strResult = '';

    for ($i = 0; $i < strlen($str); $i++) {
        $strChar = substr($str, $i, 1);
        if (strpos($VALID_NUMBERS, $strChar) !== false) {
            $strResult .= $strChar;
        }
    }
    return trim($strResult);
}

function sx_getSanitizedText($strText)
{
    if(empty($strText)) {
        return '';
    }
    $VALID_GLTEXT = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0123456789,.«»”’-";
    $strResult = "";
    $strText = str_replace("\r\n", "|", $strText . "");
    for ($i = 0; $i < mb_strlen($strText); $i++) {
        $strChar = mb_substr($strText, $i, 1);
        if (mb_strpos($VALID_GLTEXT, $strChar, 0) !== false) {
            $strResult .= $strChar;
        } elseif ($strChar == "'") {
            $strResult .= "’";
        } elseif ($strChar == "\"") {
            $strResult .= "”";
        } elseif ($strChar == " ") {
            $strResult .= " ";
        } elseif ($strChar == "|") {
            $strResult .= "\r\n";
        }
    }
    $strResult = trim($strResult);
    $strResult = str_replace("  ", " ", $strResult . "");
    $strResult = str_replace("--", "", $strResult . "");
    return trim($strResult);
}

function sx_RemovetGreekAcents($letter)
{
    if(empty($letter)) {
        return '';
    }
    $search = array('/ά/', '/έ/', '/ή/', '/ί/', '/ϊ/', '/ό/', '/ύ/', '/ώ/', '/Ά/', '/Έ/', '/Ή/', '/Ί/', '/Ό/', '/Ύ/', '/Ώ/');
    $replace = array('α', 'ε', 'η', 'ι', 'ι', 'ο', 'υ', 'ω', 'Α', 'Ε', 'Η', 'Ι', 'Ο', 'Υ', 'Ω');
    $new_letter = preg_replace($search, $replace, $letter);
    return $new_letter;
}

function sx_get_sanitized_random_code($strText)
{
    if(empty($strText)) {
        return '';
    }
    $VALID_ANC = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $strResult = "";
    for ($i = 0; $i < strlen($strText); $i++) {
        $strChar = substr($strText, $i, 1);
        if (strpos($VALID_ANC, $strChar, 0) !== false) {
            $strResult .= $strChar;
        }
    }
    return $strResult;
}

function sx_checkFileName($strText)
{
    if(empty($strText)) {
        return '';
    }
    $VALID_TABLE = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz.";
    for ($i = 0; $i < strlen($strText); $i++) {
        $strChar = substr($strText, $i, 1);
        if (strpos($VALID_TABLE, $strChar, 0) === false) {
            return false;
        }
    }
    return true;
}

function sx_checkQueryString($strText)
{
    if(empty($strText)) {
        return '';
    }
    $VALID_TABLE = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789=";
    for ($i = 0; $i < strlen($strText); $i++) {
        $strChar = substr($strText, $i, 1);
        if (strpos($VALID_TABLE, $strChar, 0) === false) {
            return false;
        }
    }
    return true;
}

/**
 * Get the a checked querystring (with parameter and value), 
 * but only if there is just one parameter
 */
function sx_getCheckedQueryString(string $url): string {
    if(str_contains($url, '&')) {
        $url = explode('&',$url)[0];
    }
    return sx_checkQueryString($url) ? $url : '';
}
