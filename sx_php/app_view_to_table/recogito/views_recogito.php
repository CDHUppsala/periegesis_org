<?php

/**
 * The variabl $str_requestedRecogitoView is defined in the page that
 * includes this application (e.g. default.php for articles)
 */

switch ($str_requestedRecogitoView) {
    case 'views_books_grouped_by_chapter_sections':
        $sql = "SELECT Book, Chapter, Sections
            FROM views_books_grouped_by_chapter_sections";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $strSections = $row["Sections"];

            if (!empty($strSections)) {
                $row["Sections"] = add_classToBookSections($strSections, false, false);
            }
            $rows[] = $row;
        }
        break;
    case 'views_wiki_persons':
        $sql = "SELECT Wikidata, PersonInWikidata, PersonInPausanias AS InPausanias, Description, Entity, Wikipedia, FatherLabel AS Father, FatherURL, MotherLabel AS Mother, MotherURL
            FROM views_wiki_persons";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strWikidata = $row["Wikidata"];
            $strPersonInWikidata = $row["PersonInWikidata"];
            $strInPausanias = $row["InPausanias"] ?? '';
            $strWikipedia = $row["Wikipedia"];

            $strFatherLabel = $row["Father"];
            $strFatherURL = $row["FatherURL"];
            $strMotherLabel = $row["Mother"];
            $strMotherURL = $row["MotherURL"];

            unset($row['Wikidata']);
            unset($row['FatherURL']);
            unset($row['MotherURL']);

            if (!empty($strWikidata)) {
                $title = views_getLinkTitle($strWikidata);
                $row["PersonInWikidata"] = '<a target="_blank" title="' . $title . '" href="' . $strWikidata . '">' . $strPersonInWikidata . '</a>';
            }
            $row["InPausanias"] = $strInPausanias;
            if (!empty($strWikipedia)) {
                $title = views_getLinkTitle($strWikipedia);
                $row["Wikipedia"] = '<a target="_blank" title="' . $title . '" href="' . $strWikipedia . '">Article</a>';
            }

            if (!empty($strFatherLabel) && str_contains($strFatherLabel, 'well-known/') === false) {
                if (!empty($strFatherURL) && str_contains($strFatherURL, 'well-known/') === false) {
                    $title = views_getLinkTitle($strFatherURL);
                    $row["Father"] = '<a target="_blank" title="' . $title . '" href="' . $strFatherURL . '">' . $strFatherLabel . '</a>';
                }
            } else {
                $row["Father"] = '';
            }

            if (!empty($strMotherLabel) && str_contains($strMotherLabel, 'well-known/') === false) {

                if (!empty($strMotherURL) && str_contains($strMotherURL, 'well-known/') === false) {
                    $title = views_getLinkTitle($strMotherURL);
                    $row["Mother"] = '<a target="_blank" title="' . $title . '" href="' . $strMotherURL . '">' . $strMotherLabel . '</a>';
                }
            } else {
                $row["Mother"] = '';
            }

            $rows[] = $row;
        }
        break;
    case 'views__events_by_alphabet':
        $sql = "SELECT Books, Events, Links, TagsAndComments 
            FROM views__events_by_alphabet";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strEvents = $row["Events"];
            $strLinks = $row["Links"];
            $jsonTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Events"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strEvents . '</a>';
            }

            if (!empty($jsonTagsAndComments)) {
                $row["TagsAndComments"]  = processSingleTagsAndComments($jsonTagsAndComments, $strLinks);
            }

            $rows[] = $row;
        }
        break;
    case 'views__events_by_section':
        $sql = "SELECT Books, Events, Links, TagsAndComments 
            FROM views__events_by_section";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strEvents = $row["Events"];
            $strLinks = $row["Links"];
            $jsonTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Events"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strEvents . '</a>';
            }

            if (!empty($jsonTagsAndComments)) {
                $row["TagsAndComments"]  = processSingleTagsAndComments($jsonTagsAndComments, $strLinks);
            }

            $rows[] = $row;
        }
        break;
    case 'views__events_within_grouped_sections':
        $sql = "SELECT Events, Links, Books, TagsAndComments 
            FROM views__events_within_grouped_sections";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $strEvents = $row["Events"];
            $strLinks = $row["Links"];
            $jsonTagsAndComments = $row["TagsAndComments"];
            $jsonBooks = $row["Books"];

            unset($row['Links']);
            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Events"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strEvents . '</a>';
            }

            if (!empty($jsonTagsAndComments)) {
                $row["TagsAndComments"] = processTagsAndComments($jsonTagsAndComments, $strLinks);
            }

            if (!empty($jsonBooks)) {
                $row["Books"] = add_classToBookSections($jsonBooks);
            }

            $rows[] = $row;
        }
        break;

    case 'views__events_grouped_within_sections':
        $sql = "SELECT Books, Events 
                    FROM views__events_grouped_within_sections";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strEvents = $row["Events"];
            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            $list = convertQuoteToLinks($strEvents);
            if (!empty($list)) {
                $row["Events"] = $list;
            }

            $rows[] = $row;
        }
        break;

    case 'views__persons_by_alphabet':
        $sql = "SELECT Books, Persons, Links, TagsAndComments 
            FROM views__persons_by_alphabet";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strPersons = $row["Persons"];
            $strLinks = $row["Links"];
            $jsonTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Persons"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strPersons . '</a>';
            }

            if (!empty($jsonTagsAndComments)) {
                $row["TagsAndComments"]  = processSingleTagsAndComments($jsonTagsAndComments, $strLinks);
            }

            $rows[] = $row;
        }
        break;
    case 'views__persons_by_section':
        $sql = "SELECT Books, Persons, Links, TagsAndComments 
            FROM views__persons_by_section";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strPersons = $row["Persons"];
            $strLinks = $row["Links"];
            $jsonTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Persons"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strPersons . '</a>';
            }

            if (!empty($jsonTagsAndComments)) {
                $row["TagsAndComments"]  = processSingleTagsAndComments($jsonTagsAndComments, $strLinks);
            }


            $rows[] = $row;
        }
        break;
    case 'views__persons_within_grouped_sections':
        $sql = "SELECT Persons, Links, Books, TagsAndComments 
            FROM views__persons_within_grouped_sections";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strPersons = $row["Persons"];
            $strLinks = $row["Links"];
            $jsonTagsAndComments = $row["TagsAndComments"];
            $jsonBooks = $row["Books"];

            unset($row['Links']);

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Persons"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strPersons . '</a>';
            }

            if (!empty($jsonTagsAndComments)) {
                $row["TagsAndComments"] = processTagsAndComments($jsonTagsAndComments, $strLinks);
            }


            if (!empty($jsonBooks)) {
                $row["Books"] = add_classToBookSections($jsonBooks);
            }

            $rows[] = $row;
        }
        break;
    case 'views__persons_grouped_within_sections':
        $sql = "SELECT Books, Persons 
            FROM views__persons_grouped_within_sections
            ORDER BY 
                CAST(SUBSTRING_INDEX(Books, '.', 1) AS UNSIGNED) ,
                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(Books, '.', 2), '.', - 1) AS UNSIGNED) , 
                CAST(SUBSTRING_INDEX(Books, '.', - 1) AS UNSIGNED)";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strPersons = $row["Persons"];
            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            $list = convertQuoteToLinks($strPersons);
            if (!empty($list)) {
                $row["Persons"] = $list;
            }

            $rows[] = $row;
        }
        break;

    case 'views__places_by_alphabet':
        $sql = "SELECT Books, Places, Links, LatLng, TagsAndComments 
            FROM views__places_by_alphabet";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strPlaces = $row["Places"];
            $strLinks = $row["Links"];
            $jsonLatLng = $row["LatLng"];
            $jsonTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);
            unset($row['LatLng']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }
            if (!empty($strLinks)) {
                $strTitle = views_getLinkTitle($strLinks);
                $row["Places"] = '<a data-sort="' . $strPlaces . '" title="' . $strTitle . '" target="_blank" href="' . $strLinks . '">' . $strPlaces . '</a>';
            }

            $strMap = '';
            $arrLatLng = json_decode($jsonLatLng, true);
            if (is_array($arrLatLng) && !empty($arrLatLng[0])) {
                $strMap = '<span class="jq_LoadMap" data-place="' . $strPlaces . '"  data-lat="' . $arrLatLng[0] . '" data-lng="' . $arrLatLng[1] . '" title="Lat: ' . $arrLatLng[0] . ', Lng: ' . $arrLatLng[1] . '">Map</span>';
            }
            $row['Maps'] = $strMap;

            if (!empty($jsonTagsAndComments)) {
                $row["TagsAndComments"]  = processSingleTagsAndComments($jsonTagsAndComments, $strLinks);
            }
            $rows[] = $row;
        }
        break;
        
    case 'views__places_by_section':
        $sql = "SELECT Books, Places, Links, LatLng, TagsAndComments
            FROM views__places_by_section";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strPlaces = $row["Places"];
            $strLinks = $row["Links"];
            $jsonLatLng = $row["LatLng"];
            $jsonTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);
            unset($row['LatLng']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $strTitle = views_getLinkTitle($strLinks);
                $row["Places"] = '<a data-sort="' . $strPlaces . '" title="' . $strTitle . '" target="_blank" href="' . $strLinks . '">' . $strPlaces . '</a>';
            }

            $strMap = '';
            $arrLatLng = json_decode($jsonLatLng, true);
            if (is_array($arrLatLng) && !empty($arrLatLng[0])) {
                $strMap = '<span class="jq_LoadMap" data-place="' . $strPlaces . '"  data-lat="' . $arrLatLng[0] . '" data-lng="' . $arrLatLng[1] . '" title="Lat: ' . $arrLatLng[0] . ', Lng: ' . $arrLatLng[1] . '">Map</span>';
            }
            $row['Maps'] = $strMap;

            if (!empty($jsonTagsAndComments)) {
                $row["TagsAndComments"]  = processSingleTagsAndComments($jsonTagsAndComments, $strLinks);
            }

            $rows[] = $row;
        }
        break;

    case 'views__places_within_grouped_sections':
        $sql = "SELECT Places, Books , Links, LatLng, TagsAndComments
            FROM views__places_within_grouped_sections";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strPlaces = $row["Places"];
            $strLinks = $row["Links"];
            $jsonLatLng = $row["LatLng"];
            $jsonTagsAndComments = $row["TagsAndComments"];
            $jsonBooks = $row["Books"];

            unset($row['Links']);
            unset($row['LatLng']);

            if (!empty($strLinks)) {
                $strTitle = views_getLinkTitle($strLinks);
                $row["Places"] = '<a data-sort="' . $strPlaces . '" title="' . $strTitle . '" target="_blank" href="' . $strLinks . '">' . $strPlaces . '</a>';
            }

            if (!empty($jsonBooks)) {
                $row["Books"] = add_classToBookSections($jsonBooks);
            }

            if (!empty($jsonTagsAndComments)) {
                $row["TagsAndComments"] = processTagsAndComments($jsonTagsAndComments, $strLinks);
            }

            $strMap = '';
            $arrLatLng = json_decode($jsonLatLng, true);
            if (is_array($arrLatLng) && !empty($arrLatLng[0])) {
                $strMap = '<span class="jq_LoadMap" data-place="' . $strPlaces . '"  data-lat="' . $arrLatLng[0] . '" data-lng="' . $arrLatLng[1] . '" title="Lat: ' . $arrLatLng[0] . ', Lng: ' . $arrLatLng[1] . '">Map</span>';
            }
            $row['Maps'] = $strMap;

            $rows[] = $row;
        }
        break;
    case 'views__places_grouped_within_sections':
        $sql = "SELECT Books, Places, Maps
                FROM views__places_grouped_within_sections
                ORDER BY 
                CAST(SUBSTRING_INDEX(Books, '.', 1) AS UNSIGNED) ,
                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(Books, '.', 2), '.', - 1) AS UNSIGNED) , 
                CAST(SUBSTRING_INDEX(Books, '.', - 1) AS UNSIGNED)";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $jsonPlaces = $row["Places"];
            $jsonMaps = $row["Maps"];
            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            $listPlaces = convertQuoteToLinks($jsonPlaces);
            if (!empty($listPlaces)) {
                $row["Places"] = $listPlaces;
            }
            $listMaps = convertMapsToLinks($jsonMaps);
            if (!empty($listMaps)) {
                $row["Maps"] = $listMaps;
            }

            $rows[] = $row;
        }
        break;
}
/*
echo '<pre>';
print_r($rows);
echo '</pre>';
*/

/**
 * The variable $json_Data is passed to a jQuery function
 */

$json_Data = null;
if (isset($rows) && !empty($rows)) {
    $veiwRows = [];
    $i = 1;
    foreach ($rows as $row) {
        $arrLoop = array();
        $arrLoop['RowID'] = $i++;
        foreach ($row as $key => $value) {
            $arrLoop[$key] = $value;
        }
        $veiwRows[] = $arrLoop;
    }
    $rows = null;

    $json_Data = json_encode($veiwRows);
    $veiwRows = null;
}

/*
$arr_RequestableViews = [
    'view__events_by_alphabet',
    'view__events_by_section',
    'view__events_grouped_by_sections',
    'view__persons_by_alphabet',
    'view__persons_by_section',
    'view__persons_grouped_by_sections',
    'view__persons_grouped_within_sections',
    'view__places_by_alphabet',
    'view__places_by_section',
    'view__places_grouped_by_sections'
    'view_animals',
    'view_artworks',
    'view_attributes',
    'view_epithets',
    'view_focalisations',
    'view_interventions',
    'view_materials',
    'view_measures',
    'view_movements',
    'view_objects',
    'view_quotes',
    'view_transformations',
    'view_txs',
    'view_works',
    'views_books_grouped_by_chapter_sections',
    'views_rows_by_type',
    'views_wiki_persons'
];
*/
