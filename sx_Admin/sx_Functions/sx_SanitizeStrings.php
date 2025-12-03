<?php

/**
 * replace --, ', ;, or
 */
function sx_SQLSafe($value)
{
    $value = str_replace("--", "", $value . "");
    $value = str_replace("'", "", $value . "");
    $value = str_replace(";", "", $value . "");
    return str_replace(" or ", "", $value . "");
}
/**
 * @trim
 * @stripslashes
 * @htmlspecialchars
 */
function sx_clean_input($data)
{
    if (!empty($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
    }
    return $data;
}


//###########################################
//## SANITIZE expected variable names
//## Return a value that cannot harm - so you don't need to check it
//## Used with @, if they might produce exceptions in queries
//###########################################

function sx_GetSanitizedLatinLetters($strText)
{
    $VALID_ANC = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_ANC, $strChar, 0) !== false) {
                $strResult .= $strChar;
            }
        }
    }
    return trim($strResult);
}

function sx_checkTableAndFieldNames($strText)
{
    $VALID_TABLE = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_";
    $radioResult = false;
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_TABLE, $strChar, 0) !== false) {
                $radioResult = true;
            } else {
                $radioResult = false;
                break;
            }
        }
    }
    return $radioResult;
}


//###########################################
//## SANITIZE REGISTRATION
//###########################################

//== All Phone Numbers
//============================== =====================

function sx_GetSanitizedPhone($strText)
{
    $VALID_PHONE = "0123456789+";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_PHONE, $strChar, 0) !== false) {
                $strResult .= $strChar;
            } elseif ($strChar == " " || $strChar == "/" || $strChar == "-") {
                $strResult .= " ";
            }
        }
        $strResult = trim($strResult);
        $strResult = str_replace("  ", " ", $strResult . "");
    }
    return $strResult;
}

function getSanitizedPostalCode($strText)
{
    $VALID_POSTCODE = "0123456789";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_POSTCODE, $strChar, 0) !== false) {
                $strResult .= $strChar;
            } elseif ($strChar == " ") {
                $strResult .= " ";
            }
        }
        $strResult = trim($strResult);
        $strResult = str_replace("  ", " ", $strResult . "");
    }
    return $strResult;
}


function sx_GetSanitizedCode($strText)
{
    $VALID_ANC = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_ANC, $strChar, 0) !== false) {
                $strResult .= $strChar;
            }
        }
        $strResult = str_replace("--", "", $strResult . "");
    }
    return trim($strResult);
}

function ps_getSanitizedAlphanumeric($strText)
{
    $VALID_AC = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_AC, $strChar, 0) !== false) {
                $strResult .= $strChar;
            }
        }
        $strResult = str_replace("--", "", $strResult . "");
    }
    return trim($strResult);
}

function getSanitizedCartInputs($strText)
{
    $VALID_AC = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-/.";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_AC, $strChar, 0) !== false) {
                $strResult .= $strChar;
            } elseif ($strChar == " ") {
                $strResult .= " ";
            }
        }
        $strResult = str_replace("--", "", $strResult . "");
    }
    return substr(trim($strResult), 0, 36);
}

//  All Text inputs except Search, Names and Login inputs will be sanitized here

function sx_getSanitizedText($strText)
{
    $VALID_GLTEXT = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0123456789,.«»”’-";
    $strResult = "";
    if (!empty($strText)) {
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
    }
    return trim($strResult);
}

//  Only Names will be sanitized here - Eventually replace with getSanitizedText
function sx_getSanitizedName($strText)
{
    $VALID_NAME = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ’-";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < mb_strlen($strText); $i++) {
            $strChar = mb_substr($strText, $i, 1);
            if (mb_strpos($VALID_NAME, $strChar, 0) !== false) {
                $strResult .= $strChar;
            } elseif ($strChar == "'") {
                $strResult .= "’";
            } elseif ($strChar == "\"") {
                $strResult .= "”";
            } elseif ($strChar == " ") {
                $strResult .= " ";
            }
        }
        $strResult = str_replace("--", "", $strResult);
    }
    return trim($strResult);
}

//== For User Friedly urls

function getSanitizedFriedlyURL($strText)
{
    $VALID_FRIEDLYURL = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ-";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_FRIEDLYURL, $strChar, 0) !== false) {
                $strResult .= $strChar;
            } elseif ($strChar == " ") {
                $strResult .= "-";
            }
        }
        $strResult = trim($strResult);
        $strResult = str_replace("--", "-", $strResult . "");
    }
    return trim($strResult);
}

//  Only Search inputs will be sanitized here - replace with the next function

function getSanitizedSearchText($strText)
{
    $VALID_GLTEXT = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = mb_substr($strText, $i, 1);
            if (strpos($VALID_GLTEXT, $strChar, 0) !== false) {
                $strResult .= $strChar;
            } elseif ($strChar == " ") {
                $strResult .= " ";
            }
        }
        $strResult = trim($strResult);
        $strResult = str_replace("  ", " ", $strResult . "");
        $strResult = str_replace("--", "", $strResult . "");
    }
    return $strResult;
}

//  Search ancient Greek Text Will replace the above by using the parameter radioUK 
function sx_getSanitizedSearchText_ClassicGreek($strText, $radioCG)
{
    $VALID_GLTEXT = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0123456789-.";
    $VALID_AncientUC = "ἀἁἂἃἄἅἆἇἈἉἊἋἌἍἎἏἐἑἒἓἔἕἘἙἚἛἜἝἠἡἢἣἤἥἦἧἨἩἪἫἬἭἮἯἰἱἲἳἴἵἶἷἸἹἺἻἼἽἾἿὀὁὂὃὄὅὈὉὊὋὌὍὐὑὒὓὔὕὖὗὙὛὝὟὠὡὢὣὤὥὦὧὨὩὪὫὬὭὮὯὰάὲέὴήὶίὸόὺύὼώᾀᾁᾂᾃᾄᾅᾆᾇᾈᾉᾊᾋᾌᾍᾎᾏᾐᾑᾒᾓᾔᾕᾖᾗᾘᾙᾚᾛᾜᾝᾞᾟᾠᾡᾢᾣᾤᾥᾦᾧᾨᾩᾪᾫᾬᾭᾮᾯᾰᾱᾲᾳᾴᾶᾷᾸᾹᾺΆᾼῂῃῄῆῇῈΈῊΉῌῐῑῒΐῖῗῘῙῚΊῠῡῢΰῤῥῦῧῨῩῪΎῬῲῳῴῶῷῸΌῺΏῼ";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = mb_substr($strText, $i, 1);
            if (strpos($VALID_GLTEXT, $strChar, 0) !== false) {
                $strResult .= $strChar;
            } elseif ($strChar == " ") {
                $strResult .= " ";
            } elseif ($radioCG) {
                if (strpos($VALID_AncientUC, $strChar, 0) !== false) {
                    $strResult .= $strChar;
                }
            }
        }
        $strResult = trim($strResult);
        $strResult = str_replace("  ", " ", $strResult . "");
        $strResult = str_replace("--", "", $strResult . "");
    }
    return $strResult;
}


//  Sanitized Where and Order by

function getSanitizedWhere($strText)
{
    $VALID_GLTEXT = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789, ";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_GLTEXT, $strChar, 0) !== false) {
                $strResult .= $strChar;
            }
        }
    }
    return $strResult;
}

//  Only URLs will be sanitized here

function getSanitizedURL($strText)
{
    $VALID_URL = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789:/-_.";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_URL, $strChar, 0) !== false) {
                $strResult .= $strChar;
            }
        }
        $strResult = str_replace("--", "", $strResult . "");
    }
    return trim($strResult);
}

//  CHECK INPUTS - FALSE OR TRUE
//  For registration: Emails, usernames and passwords will not be sanitized but checked

function sx_CheckSanitizedEmail($strText)
{
    $VALID_EMAIL = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.@";
    $radioResult = false;
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_EMAIL, $strChar, 0) !== false) {
                $radioResult = true;
            } else {
                $radioResult = false;
                break;
            }
        }
    }
    if ($radioResult) {
        if (strpos($strText, ".") == 0 || strpos($strText, "@") == 0 || strlen($strText) < 8) {
            $radioResult = false;
        }
    }
    return $radioResult;
}
function sx_GetSanitizedEmail($strText)
{
    $VALID_EMAIL = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.@";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_EMAIL, $strChar, 0) !== false) {
                $strResult .= $strChar;
            }
        }
        $strResult = str_replace("--", "", $strResult . "");
    }
    return $strResult;
}

function sx_getSanitizedFileNames($strText)
{
    $VALID_GLTEXT = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0123456789.-_";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < mb_strlen($strText); $i++) {
            $strChar = mb_substr($strText, $i, 1);
            if (mb_strpos($VALID_GLTEXT, $strChar, 0) !== false) {
                $strResult .= $strChar;
            } elseif ($strChar == " ") {
                $strResult .= "_";
            }
        }
        $strResult = trim($strResult);
        $strResult = str_replace("--", "", $strResult . "");
    }
    return trim($strResult);
}

function sx_checkSanitizedPW($strText)
{
    $VALID_PW = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";
    $radioResult = false;
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = mb_substr($strText, $i, 1);
            if (strpos($VALID_PW, $strChar, 0) !== false) {
                $radioResult = true;
            } else {
                $radioResult = false;
                break;
            }
        }
    }
    return $radioResult;
}

function checkSanitizedQString($strText)
{
    $VALID_QUERY = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $radioResult = false;
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = mb_substr($strText, $i, 1);
            if (strpos($VALID_QUERY, $strChar, 0) !== false) {
                $radioResult = true;
            } else {
                $radioResult = false;
                break;
            }
        }
    }
    return $radioResult;
}


//  SANITIZE LOGIN
//  Emails, usernames and passwords will be sanitized before posting



function loginSanitizedPW($strText)
{
    $VALID_PW = "αάβγδεέζηήθιίϊκλμνξοόπρστυύφχψώωςΆΑΒΓΔΈΕΖΉΗΘΊΙΚΛΜΝΞΌΟΠΡΣΤΎΥΦΧΨΏΩABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = mb_substr($strText, $i, 1);
            if (strpos($VALID_PW, $strChar, 0) !== false) {
                $strResult .= $strChar;
            }
        }
        $strResult = str_replace("--", "", $strResult . "");
    }
    return $strResult;
}

function cleanSMSReturn($strText)
{
    $VALID_SMS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.";
    $strResult = "";
    if (!empty($strText)) {
        for ($i = 0; $i < strlen($strText); $i++) {
            $strChar = substr($strText, $i, 1);
            if (strpos($VALID_SMS, $strChar, 0) !== false) {
                $strResult .= $strChar;
            }
        }
    }
    return $strResult;
}
