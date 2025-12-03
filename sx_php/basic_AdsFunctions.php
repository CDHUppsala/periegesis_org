<?php

/**
 * ============================================================
 * Convention for naming functions:
 * - Functions that return a value start with return_*
 * - Functions that deisplay a content start with get_*
 * ============================================================
 */
$mainTableVersion = 'articles';
if (defined('sx_TextTableVersion')) {
    $mainTableVersion = sx_TextTableVersion;
}

define('MAIN_TableVersion', $mainTableVersion);



/**
 * Define the current page to determine
 * which advertises will be shown
 * DEVELOP: For additional applications
 */

$s_Page = "CP";
if (strpos(sx_PATH, "/index.php", 1) > 0) {
    $s_Page = "FP";
} else if (strpos(sx_PATH, "/articles.php", 1) > 0) {
    $s_Page = "AP";
} else if (strpos(sx_PATH, "/texts.php", 1) > 0) {
    $s_Page = "TP";
} else if (strpos(sx_PATH, "/items.php", 1) > 0) {
    $s_Page = "IP";
} else if (strpos(sx_PATH, "/contact.php", 1) > 0) {
    $s_Page = "CP";
} else if (strpos(sx_PATH, "/about.php", 1) > 0) {
    $s_Page = "AP";
} else if (strpos(sx_PATH, "/products.php", 1) > 0) {
    $s_Page = "PP";
}

define("STR_page", $s_Page);

/**
 * For advertisements: Header, Main and Footer
 * Build WHERE clauses and an array with BINDING PARAMETERS, depending on STR_page
 * @return array{where: string, params: array}
 */

function build_Advertisement_Filter(): array
{
    // Current page (file name) and query string, which is the first parameter, if many
    $queryString = sx_getCheckedQueryString(sx_QUERY);
    $fileName = basename(sx_PATH);
    $fileName = sx_checkFileName($fileName) ? $fileName : '';

    $where = "";
    $params = [date('Y-m-d'), date('Y-m-d')];

    // Determine WHERE clause and PARAMETERS based on page type
    switch (STR_page) {
        case "FP":
            $where = " AND PublishInFirstPage = 1 ";
            break;
        case "TP":
        case "AP":
        case "IP":
        case "PP":
            $where = " AND ((PublishInMainPages = 1 OR PublishInAllPages = 1)";
            if (!empty($fileName)) {
                if (!empty($queryString)) {
                    $where .= " OR (PublishInPageQuery = ? OR PublishInPageQuery = ?) ";
                    $params[] = $fileName;
                    $params[] = $queryString;
                } else {
                    $where .= " OR PublishInPageQuery = ? ";
                    $params[] = $fileName;
                }
            }
            $where .= ')';
            break;
        default:
            if (!empty($fileName)) {
                if (!empty($queryString)) {
                    $where = " AND (PublishInPageQuery = ? OR PublishInPageQuery = ?) ";
                    $params[] = $fileName;
                    $params[] = $queryString;
                } else {
                    $where = " AND PublishInPageQuery = ? ";
                    $params[] = $fileName;
                }
            }
            break;
    }

    return ['where' => $where, 'params' => $params];
}


/** 
 * Advertises on the right (left) of the site logo
 * - Either an image or a Header with text, never both
 */
function get_Logo_Advertisements()
{
    $conn = dbconn();
    $strWhere = " AND PublishInAllPages = True ";
    $languageAnd = str_LanguageAnd;

    if (STR_page == "FP") {
        $strWhere = " AND PublishInFirstPage = True ";
    } else {
        $strWhere = " AND PublishInAllPages = True ";
    }

    $sql = "SELECT Title, ImageURL, LinkURL, Notes
        FROM ads_logo
        WHERE Publish = True AND PublishPlace = 'Right'
    	AND (StartDate <= ? OR StartDate IS NULL)
   		AND (EndDate >= ? OR EndDate IS NULL)
        $strWhere $languageAnd LIMIT 1";

    //echo $sql;

    $stmt = $conn->prepare($sql);
    $stmt->execute([date("Y-m-d"), date("Y-m-d")]);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rs) {
        return;
    }

    $strTitle = $rs["Title"];
    $strImageURL = $rs["ImageURL"];
    $stLinkURL = $rs["LinkURL"];
    $memoNotes = $rs["Notes"];
    $rs = null;
?>
    <div class="ads_logo">
        <?php
        $aOpen = "";
        $aClose = "";
        if (!empty($stLinkURL)) {
            $aOpen = return_Left_Link_Tag($stLinkURL);
            $aClose = "</a>";
        }
        if (!empty($strImageURL)) {
            get_Any_Media($strImageURL, '', $strTitle, $stLinkURL);
        } else {
            if (!empty($strTitle)) {
                echo '<h3>' . $aOpen . $strTitle . $aClose . '</h3>';
            }
            if ($memoNotes != "") {
                echo '<div>' . $memoNotes . '</div>';
            }
        }
        ?>
    </div>
<?php
}

/** 
 * 	Advertises on the top/header of the First Page, above the content
 * - basicaly, one or two big images
 */
function get_Header_Advertisements(): void
{
    $conn = dbconn();
    $languageAnd = str_LanguageAnd;

    // map return values from shared function
    ['where' => $strWhere, 'params' => $params] = build_Advertisement_Filter();

    if ($strWhere === "") {
        return;
    }

    $sql = "
        SELECT Title, ImageURL, LinkURL, Notes
        FROM ads_header
        WHERE publish = TRUE
          AND (StartDate <= ? OR StartDate IS NULL)
          AND (EndDate >= ? OR EndDate IS NULL)
          {$strWhere} {$languageAnd}
        ORDER BY Sorting DESC, StartDate DESC
    ";
    //echo $sql;

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$ads) {
        return;
    }

    // Decide layout classes
    $classGridCard = count($ads) === 1 ? '' : ' class="grid_cards"';
    $classHeaderWide = count($ads) === 1 ? ' class="ads_header_wide"' : '';

?>
    <section class="ads_header">
        <div<?= $classGridCard ?>>
            <?php foreach ($ads as $ad) {
                $title   = $ad['Title'];
                $image   = $ad['ImageURL'];
                $link    = $ad['LinkURL'];
                $notes   = $ad['Notes'];

                $safeTitle = sx_Remove_Quotes($title);

                $aOpen = $aClose = "";
                if (!empty($link)) {
                    $aOpen  = return_Left_Link_Tag($link);
                    $aClose = "</a>";
                } ?>
                <figure<?= $classHeaderWide ?>>
                    <?php if (!empty($image)) {
                        $mediaType = return_Media_Type_URL($image);
                        if ($mediaType) {
                            get_Media_Type_Player($image, $mediaType);
                        } else { ?>
                            <?= $aOpen ?><img alt="<?= sx_html_sc($safeTitle) ?>" src="../images/<?= sx_html_sc($image) ?>"><?= $aClose ?>
                        <?php }
                    }
                    if (!empty($title) || !empty($notes)) { ?>
                        <figcaption>
                            <?php if (!empty($title)) { ?>
                                <h4><?= $aOpen . sx_html_sc($title) . $aClose ?></h4>
                                <?php $aOpen = $aClose = ""; ?>
                            <?php } ?>
                            <?php if (!empty($notes)) { ?>
                                <div><?= $aOpen . $notes . $aClose ?></div>
                            <?php } ?>
                        </figcaption>
                    <?php
                    } ?>
                    </figure>
                <?php
            } ?>
                </div>
    </section>
<?php
}

/** main_Advertisements
 * Main Advertises within the main content of pages
 * - Might content Images, Titles and Texts, with links
 * Places: 
 *  - Top and Bottom of the aside column
 *  - Within Text (for text version)
 */

function get_Main_Advertisements(string $place): void
{
    global $str_AdvertisesTitle;

    $conn = dbconn();
    $languageAnd = str_LanguageAnd;
    $place = sx_GetSanitizedLatinLetters($place);

    // map return values from shared function
    ['where' => $strWhere, 'params' => $params] = build_Advertisement_Filter();
    // Add $place as the first value of the array
    array_unshift($params, $place);

    $sql = "
        SELECT Title, ImageURL, LinkURL, Notes, CommonTitle
        FROM ads_main
        WHERE publish = 1
          AND PublishPlace = ?
          AND (StartDate <= ? OR StartDate IS NULL)
          AND (EndDate >= ? OR EndDate IS NULL)
          {$strWhere} {$languageAnd}
        ORDER BY Sorting DESC ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$ads) {
        return;
    }
    //echo '<pre>';
    //print_r($ads);
    //echo '</pre>';

?>
    <section class="ads">
        <?php if (!empty($str_AdvertisesTitle) && !in_array(strtolower($place), ['top', 'left'])): ?>
            <h5><?= sx_html_sc($str_AdvertisesTitle) ?></h5>
        <?php endif; ?>

        <?php foreach ($ads as $row):
            $title   = $row['Title'];
            $image   = $row['ImageURL'];
            $link    = $row['LinkURL'];
            $notes   = $row['Notes'];
            $isCommon = $row['CommonTitle'];
            echo $notes . '<hr>';

            $class = $isCommon ? 'common' : 'item';

            $aOpen = $aClose = "";
            if (!empty($link)) {
                $aOpen  = return_Left_Link_Tag($link);
                $aClose = "</a>";
            }
        ?>
            <div class="<?= $class ?>">
                <?php if (!empty($title)): ?>
                    <h3><?= $aOpen . sx_html_sc($title) . $aClose ?></h3>
                    <?php $aOpen = $aClose = ""; ?>
                <?php endif; ?>

                <?php if (!empty($image)): ?>
                    <?php get_Any_Media($image, 'Center', $notes, $link); ?>
                <?php endif; ?>

                <?php if (!empty($notes)): ?>
                    <div class="text_normal text_small" lang="<?= sx_CurrentLanguage ?>">
                        <?= $aOpen . $notes . $aClose ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
<?php
}


/**
 * main_Advertisements_cycler
 * @abstract : Cycler for automatic and manual scrolling of single cards
 * @param string $place : TopSlider or BottomSlider at the Aside Column 
 * @param string $effect_mode : move_left_right (default), fade_both, fade_active, move_right_left, move_top_bottom, start_top_left
 * @param string $nav_place : The place of Thumbs Navigation in relation to images - bottom, or bottom_margin (default)
 * @return mixed : Cycler in HTML.
 */
function get_Main_Advertisements_Cycler(string $place, string $effectMode): void
{
    $languageAnd = str_LanguageAnd;
    $place        = sx_GetSanitizedLatinLetters($place);
    $conn         = dbconn();

    // map return values from shared function
    ['where' => $strWhere, 'params' => $params] = build_Advertisement_Filter();

    // Add $place as the first value of the array
    array_unshift($params, $place);

    $sql = "
        SELECT Title, ImageURL, LinkURL, Notes
        FROM ads_main
        WHERE publish = TRUE
          AND PublishPlace = ?
          AND (StartDate <= ? OR StartDate IS NULL)
          AND (EndDate >= ? OR EndDate IS NULL)
          {$strWhere} {$languageAnd}
        ORDER BY Sorting DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    if (empty($ads)) {
        return;
    }
?>
    <section>
        <div class="sx_cycler_ads jqCyclerAds" data-mode="<?= htmlspecialchars($effectMode, ENT_QUOTES) ?>">
            <figure>
                <?php foreach ($ads as $ad):
                    $title   = htmlspecialchars($ad['Title'] ?? '', ENT_QUOTES);
                    $image   = htmlspecialchars($ad['ImageURL'] ?? '', ENT_QUOTES);
                    $linkURL = $ad['LinkURL'] ?? '';
                    $notes   = htmlspecialchars($ad['Notes'] ?? '', ENT_QUOTES);

                    if (!empty($linkURL)) {
                        $linkURL = return_URL_For_Links($linkURL);
                    }
                ?>
                    <img src="../images/<?= $image ?>"
                        data-href="<?= htmlspecialchars($linkURL, ENT_QUOTES) ?>"
                        data-title="<?= $title ?>"
                        data-notes="<?= $notes ?>"
                        alt="<?= $title ?>">
                <?php endforeach; ?>
            </figure>
        </div>
    </section>
<?php
}


/**
 * @abstract : Multiple Flex Card Advertises on the Footer
 * @param string $place : Footer (default), FooterMore
 */

function get_Footer_Advertisements(string $place = 'Footer'): void
{
    $conn = dbconn();
    $languageAnd = str_LanguageAnd;

    // map return values from shared function
    ['where' => $strWhere, 'params' => $params] = build_Advertisement_Filter();

    // Add $place as the first value of the array
    array_unshift($params, $place);

    $sql = "
        SELECT Title, CommonTitle, ImageURL, LinkURL, Notes
        FROM ads_footer
        WHERE Publish = 1
          AND PublishPlace = ?
          AND (StartDate <= ? OR StartDate IS NULL)
          AND (EndDate >= ? OR EndDate IS NULL)
          {$strWhere} {$languageAnd}
        ORDER BY CommonTitle DESC, Sorting DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        return; // nothing to show
    }

    echo '<section class="grid_cards_wrapper">';

    // Common title, if any, comes from the *first* row
    foreach ($rows as $row) {
        if (!empty($row['CommonTitle'])) {
            echo '<h1>' . htmlspecialchars($row['Title']) . '</h1>';
            if (!empty($row['Notes'])) {
                echo '<div class="head_notes">' . $row['Notes'] . '</div>';
            }
            break;
        }
    }

    echo '<div class="grid_cards">';

    foreach ($rows as $row) {
        if (!empty($row['CommonTitle'])) {
            continue; // skip header record
        }
        $title     = $row['Title'];
        $imageURL  = $row['ImageURL'];
        $linkURL   = $row['LinkURL'];
        $notes     = $row['Notes'];

        $aOpen = '';
        $aClose = '';
        if (!empty($linkURL)) {
            $aOpen  = return_Left_Link_Tag($linkURL);
            $aClose = "</a>";
        }

        echo '<figure>';

        if (!empty($imageURL)) {
            $strObjectValue = return_Media_Type_URL($imageURL);
            if (!empty($strObjectValue)) {
                get_Media_Type_Player($imageURL, $strObjectValue);
            } else {
                echo '<div class="img_wrapper">';
                echo $aOpen . '<img alt="' . htmlspecialchars($title) . '" src="../images/' . htmlspecialchars($imageURL) . '">' . $aClose;
                echo '</div>';
            }
        }

        echo '<figcaption>';
        if (!empty($title)) {
            echo '<h4>' . $aOpen . htmlspecialchars($title) . $aClose . '</h4>';
        }
        if (!empty($notes)) {
            echo $notes;
        }
        echo '</figcaption>';

        echo '</figure>';
    }

    echo '</div>'; // close cards

    echo '</section>';
}



/**
 * @abstract : Automatic and manual Cycler for multiple (4 to 1) Flex Card Advertises on Footer
 * @param string $nav_place : The place av manual navigation: cycler_nav_middle (default), cycler_nav_bottom
 * @param string $move_mode : The moving mode: move_left_right (default), move_right_left
 */

function get_Footer_Advertisements_Slider(
    string $nav_place = 'cycler_nav_middle',
    string $move_mode = 'move_left_right'
): void {
    $conn = dbconn();
    $languageAnd = str_LanguageAnd;
    $place = 'FooterSlider';

    // map return values from shared function
    ['where' => $strWhere, 'params' => $params] = build_Advertisement_Filter();

    // Add $place as the first value of the array
    array_unshift($params, $place);

    $sql = "
        SELECT Title, CommonTitle, ImageURL, LinkURL, Notes
        FROM ads_footer
        WHERE Publish = 1
          AND PublishPlace = ?
          AND (StartDate <= ? OR StartDate IS NULL)
          AND (EndDate >= ? OR EndDate IS NULL)
          {$strWhere} {$languageAnd}
        ORDER BY CommonTitle DESC, Sorting DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        return; // nothing to show
    }

    echo '<section class="grid_cards_wrapper">';

    // header block (only from the *first* row)
    foreach ($rows as $row) {
        if ($row['CommonTitle']) {
            echo '<h1>' . htmlspecialchars($row['Title']) . '</h1>';
            if (!empty($row['Notes'])) {
                echo '<div class="head_notes">' . $row['Notes'] . '</div>';
            }
            break;
        }
    }

    echo '<div class="cycler_flex jq_CyclerFlexCards" 
        data-place="' . htmlspecialchars($nav_place) . '" 
        data-mode="' . htmlspecialchars($move_mode) . '">';

    echo '<div class="flex_cards">';

    foreach ($rows as $row) {
        if ($row['CommonTitle']) {
            continue; // skip header record
        }
        $title     = $row['Title'];
        $imageURL  = $row['ImageURL'];
        $linkURL   = $row['LinkURL'];
        $notes     = $row['Notes'];

        $aOpen = '';
        $aClose = '';
        if (!empty($linkURL)) {
            $aOpen  = return_Left_Link_Tag($linkURL);
            $aClose = "</a>";
        }

        echo '<figure>';

        if (!empty($imageURL)) {
            $strObjectValue = return_Media_Type_URL($imageURL);
            if (!empty($strObjectValue)) {
                get_Media_Type_Player($imageURL, $strObjectValue);
            } else {
                echo '<div class="img_wrapper">';
                echo $aOpen . '<img alt="' . htmlspecialchars($title) . '" src="../images/' . htmlspecialchars($imageURL) . '">' . $aClose;
                echo '</div>';
            }
        }

        echo '<figcaption>';
        if (!empty($title)) {
            echo '<h4>' . $aOpen . htmlspecialchars($title) . $aClose . '</h4>';
        }
        if (!empty($notes)) {
            echo $notes;
        }
        echo '</figcaption>';

        echo '</figure>';
    }

    echo '</div>'; // close cards

    echo '</div>'; // close cycler

    echo '</section>';
}


/**
 * Multiple cards in circular Scrolling
 * - as alternative to get_Main_Advertisements_Slider()
 */

function get_Footer_Advertisements_Cycler(): void
{
    $languageAnd = str_LanguageAnd;
    $place        = 'FooterCycler';
    $conn         = dbconn();

    // map return values from shared function
    ['where' => $strWhere, 'params' => $params] = build_Advertisement_Filter();

    // Add $place as the first value of the array
    array_unshift($params, $place);


    $sql = "SELECT Title, ImageURL, LinkURL, Notes, CommonTitle
            FROM ads_footer
            WHERE Publish = true
              AND PublishPlace = ?
              AND (StartDate <= ? OR StartDate IS NULL)
              AND (EndDate >= ? OR EndDate IS NULL)
              {$strWhere} {$languageAnd}
            ORDER BY Sorting DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;

    if (empty($ads)) return;

    echo '<section class="grid_cards_wrapper">';

    // Common title, if any, comes from the *first* row
    foreach ($ads as $ad) {
        if ($ad['CommonTitle']) {
            echo '<h1>' . htmlspecialchars($ad['Title']) . '</h1>';
            if (!empty($ad['Notes'])) {
                echo '<div class="head_notes">' . $ad['Notes'] . '</div>';
            }
            break;
        }
    }
    echo '<div class="cycler-viewport">';
    echo '<div class="cycler-track">';

    foreach ($ads as $ad) {
        if ($ad['CommonTitle']) {
            continue; // skip header record
        }

        $title   = htmlspecialchars($ad['Title'] ?? '', ENT_QUOTES);
        $image   = htmlspecialchars($ad['ImageURL'] ?? '', ENT_QUOTES);
        $linkURL = $ad['LinkURL'] ?? '';
        $notes   = $ad['Notes'] ?? '';

        $aOpen = '';
        $aClose = '';
        if (!empty($linkURL)) {
            $aOpen  = return_Left_Link_Tag($linkURL);
            $aClose = "</a>";
        }

        if (!empty($linkURL)) {
            $linkURL = return_URL_For_Links($linkURL);
        }

        echo '<div class="cycler-card">';
        echo '<figure>';

        if (!empty($image)) echo '<img src="../images/' . $image . '" alt="' . $title . '">';

        echo '<figcaption>';

        if (!empty($title)) echo  '<h4>' . $aOpen . $title . $aClose . '</h4>';

        if (!empty($notes)) echo $notes;

        echo '</figcaption>';
        echo '</figure>';
        echo '</div>';
    }
    echo '</div></div></section>';

?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const track = document.querySelector(".cycler-track");
            const viewport = document.querySelector(".cycler-viewport");
            let pos = 0;
            const speed = 1; // pixels per frame
            let paused = false;

            function getSpeed() {
                // fallback: 1px per frame
                const speedVar = getComputedStyle(track).getPropertyValue("--scroll-speed");
                return parseFloat(speedVar) || 1;
            }

            function animate() {
                if (!paused) {
                    pos -= getSpeed();
                    track.style.transform = `translateX(${pos}px)`;

                    const firstCard = track.firstElementChild;
                    const style = getComputedStyle(firstCard);
                    const cardWidth = firstCard.offsetWidth +
                        parseFloat(style.marginLeft) +
                        parseFloat(style.marginRight);

                    if (-pos >= cardWidth) {
                        // move first card to the end
                        track.appendChild(firstCard);

                        // reset position without flicker
                        pos += cardWidth;

                        track.style.transition = "none"; // disable transition
                        track.style.transform = `translateX(${pos}px)`;

                        requestAnimationFrame(() => {
                            track.style.transition = ""; // restore CSS default
                        });
                    }
                }

                requestAnimationFrame(animate);
            }

            // Pause on hover
            viewport.addEventListener("mouseenter", () => paused = true);
            viewport.addEventListener("mouseleave", () => paused = false);

            animate();
        });
    </script>

    <?php
}


/**
 * footer_Advertisements_footer_top
 * For a sole advertisement at the top of footer
 * Is displayed in all pages
 */

function get_footer_Advertisements_In_Footer_Top()
{
    $conn = dbconn();
    $place = 'InFooterTop';
    $languageAnd = str_LanguageAnd;
    $aResults = null;
    $sql = "SELECT Title, ImageURL, Notes 
        FROM ads_footer
        WHERE Publish = True 
        AND PublishPlace = ?
        $languageAnd
        ORDER BY Sorting DESC 
        LIMIT 1 ";
    //echo $sql;

    $stmt = $conn->prepare($sql);
    $stmt->execute([$place]);
    if ($stmt->rowCount() > 0) {
        $aResults = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if (!empty($aResults)) {
        $sTitle = $aResults['Title'];
        $sImageURL = $aResults['ImageURL'];
        $sNotes = $aResults['Notes'];
    ?>
        <div class="in_footer_ads">
            <?php
            if (!empty($sTitle)) {
                echo "<h4>$sTitle</h4>";
            }
            if (!empty($sImageURL)) {
                echo "<div><img src=\"../images/$sImageURL\" alt=\"$sTitle\"></div>";
            }
            if (!empty($sNotes)) {
                echo "<div class=\"text_notes\">>$sNotes</div>";
            }
            ?>
        </div>
<?php
    }
}


/**
 * get_Social_Media
 * @abstract Include links to social media from table social_media
 * @param string $place : On the Top Navigation menu or on the Footer (default)
 */
function get_Social_Media(string $place = 'footer'): void
{
    $conn = dbconn();

    $sql = "
        SELECT MediaName, MediaImg, MediaImgTop, MediaURL
        FROM social_media
        WHERE Publish = 1
        ORDER BY Sorting DESC
    ";
    $stmt = $conn->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        return; // nothing to show
    }

    foreach ($rows as $row) {
        $mediaName   = $row['MediaName'];
        $mediaImg    = $row['MediaImg'];
        $mediaImgTop = $row['MediaImgTop'];
        $mediaURL    = $row['MediaURL'];

        // override image for "Top" placement
        if (sx_ShowSocialMediaOnTop && $place === "Top" && !empty($mediaImgTop)) {
            $mediaImg = $mediaImgTop;
        }

        if (empty($mediaURL)) {
            continue; // skip broken links
        }

        echo '<a target="_blank" title="' . htmlspecialchars($mediaName) . '" href="' . htmlspecialchars($mediaURL) . '">';

        if (!empty($mediaImg)) {
            if (preg_match('/\.(svg|jpg|png)$/i', $mediaImg)) {
                // standard image file
                echo '<img class="sx_svg_image" src="' . htmlspecialchars(sx_DefaultImgFolder . $mediaImg) . '" alt="' . htmlspecialchars($mediaName) . '">';
            } else {
                // SVG symbol reference
                echo '<svg class="sx_svg" title="' . htmlspecialchars($mediaName) . '">';
                echo '<use xlink:href="../imgPG/sx_svg/sx_media_symbols.svg?v=2025-04#' . htmlspecialchars($mediaImg) . '"></use>';
                echo '</svg>';
            }
        } else {
            // fallback: just show name
            echo htmlspecialchars($mediaName);
        }

        echo '</a>';
    }
}

?>