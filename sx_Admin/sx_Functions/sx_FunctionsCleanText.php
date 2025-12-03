<?php

/**
 * ==================================
 * Clean Texts
 * ===================================
 */

/**
 * Replace 
 * single quoates: (' BY ’ OR &#700;) 
 * double quotes: (" BY ” OR &#750)
 * Replaces also quotes arround src="" and href="", så don't use doube quoates replacement
 * when adding HTML-text in database
 */
function sx_replaceQuotes($txt)
{
    $txt = sx_replaceSingleQuotes($txt);
    return  trim($txt);
}

function sx_replaceBothQuotes($txt)
{
    if (!empty($txt)) {
        $txt = sx_replaceDoubleQuotes($txt);
        $txt = sx_replaceSingleQuotes($txt);
    }
    return  $txt;
}
function sx_replaceDoubleQuotes($ctxt)
{
    if (strpos($ctxt, '"', 0) !== false) {
        $ctxt = str_replace('"', "”", $ctxt);
    }
    return  $ctxt;
}
function sx_replaceSingleQuotes($ctxt)
{
    if (strpos($ctxt, "'", 0) !== false) {
        $ctxt = str_replace("'", "’", $ctxt);
    }
    return $ctxt;
}

/**
 * Remove single and double quotes for javascripts
 */
function sx_removeQuotes($txt)
{
    if (!empty($txt)) {
        $txt = sx_removeDoubleQuotes($txt);
        $txt = sx_removeSingleQuotes($txt);
    }
    return $txt;
}
function sx_removeDoubleQuotes($rtxt)
{
    if (strpos($rtxt, '"', 0) !== false) {
        $rtxt = str_replace('"', "", $rtxt);
    }
    return  $rtxt;
}
function sx_removeSingleQuotes($rtxt)
{
    if (strpos($rtxt, "'", 0) !== false) {
        $rtxt = str_replace("'", "", $rtxt);
    }
    return $rtxt;
}

/**
 * tranform all text to a line and replace quotes - for java script
 */
function sx_makeLine($mlfix)
{
    $mlfix = str_replace("\n", "", trim($mlfix));
    $mlfix = sx_replaceQuotes($mlfix);
    return  trim($mlfix);
}


/**
 * ========================================
 * Convert PURE TEXT FROM TEXTAREAS to HTML
 * ========================================
 */

/**
 * 1.   Form Rows
 *      - Removes more than 1 empty spaces,
 *      - Transforms 1 or more separated rows to 2 rows
 *      - removes rows from the bigining and the end of text
 */

function sx_RowFix($rfix)
{
    if (!empty($rfix)) {
        $rfix = strip_tags($rfix);
        while (strpos($rfix, "\t", 0) > 0) {
            $rfix = str_replace("\t", " ", trim($rfix));
        }
        while (strpos($rfix, "  ", 0) > 0) {
            $rfix = str_replace("  ", " ", trim($rfix));
        }
        while (strpos($rfix, " \r\n", 0) > 0) {
            $rfix = str_replace(" \r\n", "\r\n", trim($rfix));
        }
        while (strpos($rfix, "\r\n ", 0) > 0) {
            $rfix = str_replace("\r\n ", "\r\n", trim($rfix));
        }
        while (strpos(trim($rfix), "\r\n\r\n", 0) > 0) {
            $rfix = str_replace("\r\n\r\n", "\r\n", $rfix);
        }
        $rfix = str_replace("\r\n", "\r\n\r\n", $rfix);
        while (strrpos(trim($rfix), "\r\n", 0) > (strlen(trim($rfix)) - 1)) {
            $rfix = substr(trim($rfix), 0, (strlen(trim($rfix)) - 2));
        }
    }
    return trim($rfix);
}

/**
 * 2. Break Paragraphs
 */
function sx_ParagraphBreaks($rbfix)
{
    $rbfix = str_replace("\r\n\r\n", "\r\n", $rbfix . "");
    $rbfix = str_replace("\r\n", "</p><p>", $rbfix . "");
    return "<p>" . trim($rbfix) . "</p>";
}

/**
 * A. Form Pure Text from Textarea Using 4 functions:
 * - Strip all Tags
 * - Replace Single and double Quotes
 * - Fix Rows
 * - Break Pargraphs
 */

function sx_formatTextarea($fixtext)
{
    return sx_ParagraphBreaks(sx_RowFix(sx_replaceQuotes($fixtext)));
}

/**
 * B. Form only Rows in case of Form errors:
 * the text returns to textarea without HTML-Formation
 */
function sx_RowFixTextarea($fixtext)
{
    return sx_RowFix($fixtext);
}


/**
 * ----------------------------------------------------
 * Various String functions: _NU = Not Used
 * ----------------------------------------------------
 */

/**
 * Get vertical text
 */
function sx_getVertical($strText)
{
    $strNew = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (!empty($strNew)) {
                $strNew .= "<br>";
            }
            $strNew .= $strChar;
        }
    }
    return  trim($strNew);
}

/**
 * For text in XML-Files 
 */

function sx_getEntityReference($str)
{
    $str = str_replace("&", "&amp;", $str . "");
    $str = str_replace("<", "&lt;", $str . "");
    $str = str_replace(">", "&gt;", $str . "");
    $str = str_replace("'", "&apos;", $str . "");
    $str = str_replace('"', "&quot;", $str . "");
    return $str;
}

/**
 * Replace html-tags 
 */

function sx_replaceHTMLTags_NU($htmlfix)
{
    $htmlfix = str_replace("<", "&lt;", trim($htmlfix));
    $htmlfix = str_replace(">", "&gt;", $htmlfix);
    return  $htmlfix;
}

/**
 * HTML Escape Before Inserting Untrusted Data into HTML Element Content 
 */
function sx_getHTMLEscaping_NU($txt)
{
    if (!empty($txt)) {
        $txt = str_replace("&", "&amp;", $txt);
        $txt = str_replace("<", "&lt;", $txt);
        $txt = str_replace(">", "&gt;", $txt);
        $txt = str_replace('"', "&quot;", $txt);
        $txt = str_replace("'", "&#x27;", $txt);
        $txt = str_replace("/", "&#x2F;", $txt);
    }
    return $txt;
}

/**
 * Using HTML encoding to encode potentially unsafe output
 * - Reviewing Code for Cross-site scripting 
 */
function sx_getHTMLEncoding_NU($txt)
{
    if (!empty($txt)) {
        $txt = str_replace("<", "&lt;", $txt);
        $txt = str_replace("<", "&gt;", $txt);
        $txt = str_replace("(", "&#40;", $txt);
        $txt = str_replace(")", "&#41;", $txt);
        $txt = str_replace("#", "&#35;", $txt);
        $txt = str_replace("&", "&amp;", $txt);
        $txt = str_replace('"', "&quot;", $txt);
    }
    return $txt;
}
