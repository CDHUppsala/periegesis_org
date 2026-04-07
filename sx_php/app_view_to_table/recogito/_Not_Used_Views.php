<?php
// To convert annotations to table
switch ($str_requestedRecogitoView) {

    case 'view_animals':
        $sql = "SELECT Books, Animals, Links, TagsAndComments 
            FROM view_animals";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strAnimals = $row["Animals"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Animals"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strAnimals . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_artworks':
        $sql = "SELECT Books, Artworks, Links, TagsAndComments 
            FROM view_artworks";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strArtworks = $row["Artworks"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Artworks"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strArtworks . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_attributes':
        $sql = "SELECT Books, Attributes, Links, TagsAndComments 
            FROM view_attributes";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strAttributes = $row["Attributes"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Attributes"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strAttributes . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_epithets':
        $sql = "SELECT Books, Epithets, Links, TagsAndComments 
            FROM view_epithets";
        $stmt = $conn->query($sql);

        //$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strEpithets = $row["Epithets"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Epithets"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strEpithets . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_focalisations':
        $sql = "SELECT Books, Focalisations, Links, TagsAndComments 
            FROM view_focalisations";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strFocalisations = $row["Focalisations"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Focalisations"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strFocalisations . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_interventions':
        $sql = "SELECT Books, Interventions, Links, TagsAndComments 
            FROM view_interventions";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strInterventions = $row["Interventions"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Interventions"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strInterventions . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_materials':
        $sql = "SELECT Books, Materials, Links, TagsAndComments 
            FROM view_materials";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strMaterials = $row["Materials"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Materials"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strMaterials . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_measures':
        $sql = "SELECT Books, Measures, Links, TagsAndComments 
            FROM view_measures";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strMeasures = $row["Measures"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Measures"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strMeasures . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_movements':
        $sql = "SELECT Books, Movements, Links, TagsAndComments 
            FROM view_movements";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strMovements = $row["Movements"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Movements"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strMovements . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_objects':
        $sql = "SELECT Books, Objects, Links, TagsAndComments 
            FROM view_objects";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strObjects = $row["Objects"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Objects"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strObjects . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_quotes':
        $sql = "SELECT Books, Quotes, Links, TagsAndComments 
            FROM view_quotes";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strQuotes = $row["Quotes"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["$strQuotes"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strQuotes . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_transformations':
        $sql = "SELECT Books, Transformations, Links, TagsAndComments 
            FROM view_transformations";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strTransformations = $row["Transformations"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Transformations"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strTransformations . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_txs':
        $sql = "SELECT Books, Txs, Links, TagsAndComments 
            FROM view_txs";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strTxs = $row["Txs"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Txs"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strTxs . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
    case 'view_works':
        $sql = "SELECT Books, Works, Links, TagsAndComments 
            FROM view_works";
        $stmt = $conn->query($sql);

        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $strBooks = $row["Books"];
            $strWorks = $row["Works"];
            $strLinks = $row["Links"];
            $strTagsAndComments = $row["TagsAndComments"];

            unset($row['Links']);

            if (!empty($strBooks)) {
                $row["Books"] = '<span class="jq_book_section">' . $strBooks . '</span>';
            }

            if (!empty($strLinks)) {
                $title = views_getLinkTitle($strLinks);
                $row["Works"] = '<a target="_blank" title="' . $title . '" href="' . $strLinks . '">' . $strWorks . '</a>';
            }

            if (!empty($strTagsAndComments)) {
                $row["TagsAndComments"] = get_cleaned_TagsAndComments($strLinks, $strTagsAndComments);
            }

            $rows[] = $row;
        }
        break;
}


// To export annotations
switch ($str_requestedRecogitoView) {

    case 'view_animals':
        $sql = "SELECT Books, Animals, Links, TagsAndComments FROM view_animals";
        break;

    case 'view_artworks':
        $sql = "SELECT Books, Artworks, Links, TagsAndComments FROM view_artworks";
        break;

    case 'view_attributes':
        $sql = "SELECT Books, Attributes, Links, TagsAndComments FROM view_attributes";
        break;

    case 'view_epithets':
        $sql = "SELECT Books, Epithets, Links, TagsAndComments FROM view_epithets";
        break;

    case 'view_focalisations':
        $sql = "SELECT Books, Focalisations, Links, TagsAndComments FROM view_focalisations";
        break;

    case 'view_interventions':
        $sql = "SELECT Books, Interventions, Links, TagsAndComments FROM view_interventions";
        break;

    case 'view_materials':
        $sql = "SELECT Books, Materials, Links, TagsAndComments FROM view_materials";
        break;

    case 'view_measures':
        $sql = "SELECT Books, Measures, Links, TagsAndComments FROM view_measures";
        break;

    case 'view_movements':
        $sql = "SELECT Books, Movements, Links, TagsAndComments FROM view_movements";
        break;

    case 'view_objects':
        $sql = "SELECT Books, Objects, Links, TagsAndComments FROM view_objects";
        break;

    case 'view_quotes':
        $sql = "SELECT Books, Quotes, Links, TagsAndComments FROM view_quotes";
        break;

    case 'view_transformations':
        $sql = "SELECT Books, Transformations, Links, TagsAndComments FROM view_transformations";
        break;

    case 'view_txs':
        $sql = "SELECT Books, Txs, Links, TagsAndComments FROM view_txs";
        break;

    case 'view_works':
        $sql = "SELECT Books, Works, Links, TagsAndComments FROM view_works";
        break;
}
