<?php
function views_getLinkTitle($url)
{
    $arrUrl = explode('/', $url);
    if (count($arrUrl) > 2) {
        return 'Open in ' . $arrUrl[2] . ', ID: ' . end($arrUrl);
    } else {
        return '';
    }
}


function add_classToBookSections($json, $radioList = false, $break = true)
{
    $data = json_decode($json, true);
    $intCount = count($data);
    $strList = '';
    $intLoop = 1;
    if ($intCount > 1 && $radioList) {
        $strList = '<ol>';
        foreach ($data as $book) {
            $strList .= '<li><span class="jq_book_section">' . $book . '</span></li>';
        }
        $strList .= '</ol>';
    } else {
        if ($break) {
            foreach ($data as $book) {
                $strList .= '<span title="' . $intLoop . '" class="jq_book_section">' . $book . '</span><br>';
                $intLoop++;
            }
        } else {
            foreach ($data as $book) {
                $strList .= '[<span title="' . $intLoop . '" class="jq_book_section">' . $book . '</span>] ';
                $intLoop++;
            }
        }
    }
    return $strList;
}

function get_cleaned_TagsAndComments($link, $desc)
{
    if (!empty($desc)) {
        /*
        */
        if (str_contains($desc, ';')) {
            $desc = str_replace('; ', ';', $desc);
            $desc = str_replace(' ;', ';', $desc);
        }
        if (!empty($link) && str_contains($link, '/')) {
            $arrLinks = explode('/', $link);
            $id = end($arrLinks);
            if (str_contains($desc, $id)) {
                $desc = str_replace($id . '|', '', $desc);
                $desc = str_replace('|' . $id, '', $desc);
                $desc = str_replace($id, '', $desc);
                $desc = str_replace('()', '', $desc);
                $desc = str_replace('|)', ')', $desc);
                $desc = str_replace('(|', '(', $desc);
            }
        }
        $desc = str_replace('|', ' | ', $desc);
    }
    return $desc;
}

function convertQuoteToLinks($jsonData)
{
    $linksArray = json_decode($jsonData, true);

    $result = '<ol>';
    if (count($linksArray) >= 4) {
        $result = '<ol class="grid_list">';
    }
    if (count($linksArray) === 1) {
        $result = '<ul>';
    }

    foreach ($linksArray as $link) {
        $quote = $link[0];
        $uri = $link[1];

        if (!empty($uri)) {
            $result .= '<li><a href="' . htmlspecialchars($uri) . '" target="_blank">' . htmlspecialchars($quote) . '</a></li>';
        } else {
            $result .= '<li>' . htmlspecialchars($quote) . '</li>';
        }
    }
    $result .= '</ol>';
    if (count($linksArray) === 1) {
        $result .= '</ul>';
    }

    return $result;
}

function convertMapsToLinks($jsonData)
{
    $linksArray = json_decode($jsonData, true);

    $result = '<ol>';
    if (count($linksArray) >= 8) {
        $result = '<ol class="grid_list">';
    }
    foreach ($linksArray as $link) {
        $quote = $link[0];
        $lat = $link[1];
        $lng = $link[2];

        if (!empty($lat)) {
            $result .= '<li><span class="jq_LoadMap" data-place="' . $quote . '" data-lat="' . $lat . '" data-lng="' . $lng . '" title="Lat: ' . $lat . ', Lng: ' . $lng . '">' . $quote . '</span></li>';
        } else {
            $result .= '<li>' . htmlspecialchars($quote) . '</li>';
        }
    }

    return $result;
}


function processTagsAndComments($json, $strLinks)
{
    $data = json_decode($json, true);

    if (!is_array($data) || empty($data)) {
        return "";
    }
    $intCount = count($data);
    $htmlList = '';
    $listItem = '';

    foreach ($data as $subArray) {
        if (
            !is_array($subArray) || count($subArray) === 0
            || (empty($subArray[0]) && empty($subArray[1]))
        ) {
            continue;
        }

        $tags = $subArray[0] ?? '';
        $comments = $subArray[1] ?? '';
        if (!empty($tags)) {
            $tags = get_cleaned_TagsAndComments($strLinks, $tags);
        }
        if (!empty($comments)) {
            $comments = get_cleaned_TagsAndComments($strLinks, $comments);
        }

        $listItem = htmlspecialchars($tags);
        if (!empty($comments)) {
            $listItem .= " <b>[" . htmlspecialchars($comments) . "]</b>";
        }

        if (strpos($listItem, 'Q') !== false) {
            $listItem = extract_wiki_QID($listItem);
        }
        if (strpos($listItem, 'pleiades:') !== false) {
            $listItem = extract_pliades_ID($listItem);
        }

        $htmlList .= "<li>{$listItem}</li>";
        $listItem = '';
    }
    if ($intCount > 1) {
        $htmlList = "<ol>{$htmlList}</ol>";
    } else {
        $htmlList = "<ul>{$htmlList}</ul>";
    }


    return $htmlList;
}

function processSingleTagsAndComments($json, $strLinks)
{
    $data = json_decode($json, true);

    if (!is_array($data) || empty($data)) {
        return "";
    }

    $tags = $data[0] ?? '';
    $comments = $data[1] ?? '';
    if (!empty($tags)) {
        $tags = get_cleaned_TagsAndComments($strLinks, $tags);
    }
    if (!empty($comments)) {
        $comments = get_cleaned_TagsAndComments($strLinks, $comments);
    }

    $listItem = htmlspecialchars($tags);
    if (!empty($comments)) {
        $listItem .= " <b>[" . htmlspecialchars($comments) . "]</b>";
    }

    return extract_wiki_QID($listItem);
}

function extract_wiki_QID($text)
{
    // Check if 'Q' exists in the string
    $pos = strpos($text, 'Q');
    if ($pos === false) {
        return $text;
    }

    $arr = explode('Q', $text);
    if (!isset($arr[1])) {
        return $text;
    }

    $numericPart = (int) $arr[1];
    if ($numericPart === 0) {
        return $text;
    }

    // Return the whole text with the Wiki ID as a link
    return str_replace("Q{$numericPart}", "<a target=\"_black\" href=\"https://www.wikidata.org/wiki/Q{$numericPart}\">Q{$numericPart}</a>", $text);
}

function extract_pliades_ID($text)
{
    // Check if 'pleiades:' exists in the string
    $pos = strpos($text, 'pleiades:');
    if ($pos === false) {
        return $text;
    }

    $arr = explode('pleiades:', $text);
    if (!isset($arr[1])) {
        return $text;
    }

    $numericPart = (int) $arr[1];
    if ($numericPart === 0) {
        return $text;
    }

    // Return the whole text with the Pleiades ID as a link
    return str_replace("pleiades:{$numericPart}", "<a target=\"_black\" href=\"https://pleiades.stoa.org/places/{$numericPart}\">pleiades:{$numericPart}</a>", $text);
}
