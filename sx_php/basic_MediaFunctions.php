<?php

/*
    ============================================================
    Convention for naming functions:
    - Functions that return a value start with return_* or check_*
    - Functions that deisplay a content start with get_*
    ============================================================
*/

/**
 * Used for links to IDs of the main (currently used) table.
 * Returns the pure URL to be contain in HREF.
 * Is only called by functions in this page
 * @param int $linkID: integer as ID to Main Table
 */
function get_Link_to_Main_Table(int $linkID): string
{
    $linkID = is_numeric($linkID) ? (int) $linkID : 0;
    if ($linkID == 0) {
        return '';
    }
    $baseURLs = [
        'texts' => 'texts.php?tid=',
        'articles' => 'articles.php?aid=',
        'items' => 'items.php?itemid=',
        'products' => 'products.php?pid=',
        'conferences' => 'conferences.php?paperid=',
    ];

    $mainTableVersion = 'articles';
    if (defined('sx_TextTableVersion')) {
        $mainTableVersion = sx_TextTableVersion;
    }

    $target = $baseURLs[$mainTableVersion] ?? '';
    if (empty($target)) {
        return '';
    }
    return $target . $linkID;
}

/**
 * Returns ONLY the URL to be included in HREF
 * Is called from basic_AdsFunctions 
 * @param mixed $link: the initial link, number, as ID to main table, 
 *  - or string to internal or external sources
 * */
function return_URL_For_Links($link)
{
    if (stripos($link, "http://") !== false || stripos($link, "https://") !== false) {
        $url = $link;
    } elseif (stripos($link, "www.") !== false) {
        $url = 'http://' . $link;
    } elseif (stripos($link, "../") !== false) {
        $url = $link;
    } elseif (return_Filter_Integer($link) > 0) {
        $url = get_Link_to_Main_Table($link);
    } else {
        if (stripos($link, "imgPDF/") !== false) {
            $url = '../' . $link;
        } elseif (stripos($link, "imgMedia/") !== false) {
            $url = '../' . $link;
        } else {
            $url = $link;
        }
    }
    return $url;
}


/**
 * 
 * Generates the opening <a> tag with the correct HREF for internal or external links.
 * @param mixed $link An integer (ID of a source) or part of a URL.
 * @param string $class: Used for dynamic styling of First Page
 * @return string The left part of the link with the complete HREF.
 */
function return_Left_Link_Tag($link, $class = '')
{
    // Default <a> tag structure
    $aTagOpen = '<a ';

    // Handle external URLs
    if (filter_var($link, FILTER_VALIDATE_URL)) {
        $aTagOpen .= 'target="_blank" title="Link to External URL" href="' . $link . '"';
    } elseif (strpos($link, "www.") === 0) {
        $aTagOpen .= 'target="_blank" title="Link to External URL" href="http://' . $link . '"';
    } elseif (strpos($link, "../") === 0) {
        $aTagOpen .= 'target="_blank" title="Link to Internal Resource" href="' . $link . '"';
    } elseif (is_numeric($link) && intval($link) > 0) {

        $target = get_Link_to_Main_Table($link);
        $aTagOpen .= 'href="' . $target . '"';
    } else {
        // Extract file extension for further processing
        $extension = mb_strtolower(pathinfo($link, PATHINFO_EXTENSION), 'UTF-8');

        // Handle internal resources based on the file type
        switch ($extension) {
            case 'pdf':
                $aTagOpen .= 'target="_blank" title="Link to Internal PDF File" href="../imgPDF/' . $link . '"';
                break;
            case 'mp4':
            case 'ogg':
            case 'webm':
            case 'mp3':
            case 'odp':
            case 'ppt':
            case 'pptx':
            case 'ppsx':
                $aTagOpen .= 'target="_blank" title="Link to Internal Media File" href="../imgMedia/' . $link . '"';
                break;
            default:
                $aTagOpen .= 'href="' . $link . '"';
                break;
        }
    }

    if (!empty($class)) {
        $aTagOpen .= ' class="' . $class . '"';
    }

    // Close the <a> tag opening
    $aTagOpen .= '>';

    return $aTagOpen;
}

/**
 * 1. Check the type of media from its extension and return the correct local or external URL.
 * 
 * - Prepares HTML for video, audio, or iFrame (for presentation files).
 * - Handles media files of type: image, video, audio, presentation, or PDF.
 * - Returns:
 *   - The unchanged URL for external sources.
 *   - A relative URL for local video/audio files.
 *   - An absolute URL for local presentation files.
 *   - An empty string for images.
 *
 * @param string $url URL to external or internal sources with file name.
 * @return string Correct URL for video/audio/presentation/PDF or an empty string for images.
 */
function return_Media_Type_URL($url)
{
    // Get the file extension
    $extension = mb_strtolower(pathinfo($url, PATHINFO_EXTENSION), 'UTF-8');

    // Determine the URL type
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return $url; // Return as is for full external URLs
    }

    if (strpos($url, "https://") !== false || strpos($url, "http://") !== false || strpos($url, "www.") !== false) {
        return $url;
    }

    // Handle based on file extension
    switch ($extension) {
        case 'mp4':
        case 'ogg':
        case 'webm':
        case 'mp3':
            return "../imgMedia/" . $url; // Relative URL for local media files

        case 'odp':
        case 'ppt':
        case 'pptx':
        case 'ppsx':
            return sx_ROOT_HOST . "/imgMedia/" . $url; // Absolute URL for presentation files

        case 'pdf':
            return "../imgPDF/" . $url; // Relative URL for PDFs

        case 'doc':
        case 'docx':
            return sx_ROOT_HOST . "/imgPDF/" . $url; // Relative URL for PDFs

        case 'html':
            return $url; // Return as is for HTML files

        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'svg':
        case 'bmp':
            return ""; // Empty string for image files

        default:
            return ""; // Empty string for unsupported types
    }
}

/**
 * 2. Renders an HTML media player or viewer depending on the media type.
 *
 * @param string $url The partial URL containing the file name and extension.
 * @param string $strObjectValue The full URL from the `return_Media_Type_URL()` function.
 * @return void Outputs the HTML for the requested media.
 */
function get_Media_Type_Player($url, $strObjectValue, $notes = '')
{
    // Extract file extension
    $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));

    // Video types
    $videoTypes = ['mp4' => 'video/mp4', 'ogv' => 'video/ogg', 'webm' => 'video/webm'];
    if (array_key_exists($extension, $videoTypes)) {
        ps_renderVideoPlayer($strObjectValue, $videoTypes[$extension], $notes);
        return;
    }

    // Audio types
    $audioTypes = ['mp3' => 'audio/mp3', 'wav' => 'audio/wav', 'ogg' => 'audio/ogg'];
    if (array_key_exists($extension, $audioTypes)) {
        ps_renderAudioPlayer($strObjectValue, $audioTypes[$extension], $notes);
        return;
    }

    // PDF files
    if ($extension === 'pdf') {
        ps_renderPDFViewer($strObjectValue, $notes);
        return;
    }

    // Check for YouTube URLs
    if (strpos($strObjectValue, "youtube.com") !== false) {
        if (str_contains($strObjectValue, 'watch?v=')) {
            $strObjectValue = str_replace('watch?v=', 'embed/', $strObjectValue);
        }
        if (str_contains($strObjectValue, 'https://') === false) {
            $strObjectValue = "https://{$strObjectValue}";
        }
        ps_renderYoutubeIframe($strObjectValue, $notes);
        return;
    }

    // Check for YouTube URLs
    // Replace youtu.be/ with www.youtube.com/embed/
    if (str_contains($strObjectValue, "youtu.be/")) {
        $strObj = str_replace('youtu.be/', 'www.youtube.com/embed/', $strObjectValue);
        ps_renderYoutubeIframe($strObj, $notes);
        return;
    }

    // Check for Facebook URLs
    if (str_contains($strObjectValue, "facebook.com/")) {
        ps_renderFacebookContainer($strObjectValue, $notes);
        return;
    }


    // Handle Office Apps presentations
    $presentationTypes = ['odp', 'ppt', 'pptx', 'ppsx', 'doc', 'docx'];
    if (in_array($extension, $presentationTypes)) {
        ps_renderOfficeViewer($strObjectValue, $notes);
        return;
    }


    // General URLs or HTML files
    if (filter_var($strObjectValue, FILTER_VALIDATE_URL) || $extension === 'html') {
        ps_renderIframeWithLoader($strObjectValue, $notes);
        return;
    }

    // Default: Unsupported or unknown file type
    echo '<p>Unsupported media type: ' . htmlspecialchars($url) . '</p>';
}

/**
 * Helper functions for rendering media players or viewers.
 */

function ps_renderVideoPlayer($url, $type, $notes)
{
    echo '<video controls><source src="' . htmlspecialchars($url) . '" type="' . htmlspecialchars($type) . '">Your browser does not support the video tag.</video>';
    if (!empty($notes)) {
        echo "<figcaption>$notes</figcaption>";
    }
}

function ps_renderAudioPlayer($url, $type, $notes)
{
    echo '<audio title="' . htmlspecialchars($notes) . '" controls><source src="' . htmlspecialchars($url) . '" type="' . htmlspecialchars($type) . '"></audio>';
    if (!empty($notes)) {
        echo "<figcaption>$notes</figcaption>";
    }
}

function ps_renderPDFViewer($url, $notes)
{
    echo '<div class="media_wrapper">';
    echo '<object data="' . htmlspecialchars($url) . '" type="application/pdf" aria-label="' . htmlspecialchars($notes) . '"></object>';
    echo '</div>';
    echo '<figcaption>';
    if (!empty($notes)) {
        echo "<span>$notes</span>: ";
    }
    echo '<a href="' . htmlspecialchars($url) . '" target="_blank">Open in New Tab</a> | 
          <a href="' . htmlspecialchars($url) . '" download>Download</a>';
    echo '</figcaption>';
}

function ps_renderYoutubeIframe($url, $notes)
{
    echo '<div class="media_wrapper">';
    echo '<iframe class="youtubeFrame" src="' . htmlspecialchars($url) . '" frameborder="0" allowfullscreen wmode="transparent"></iframe>';
    echo '</div>';
    if (!empty($notes)) {
        echo "<figcaption>$notes</figcaption>";
    }
}

function ps_renderFacebookContainer($url, $notes)
{
    static $load_FacebookSDK = true;

    if ($load_FacebookSDK) {
        echo '<div id="fb-root"></div>';
        echo '<script>window.fbAsyncInit = function() {FB.init({xfbml: true, version: "v3.2"});};</script>';
        echo '<script async defer src="https://connect.facebook.net/en_US/sdk.js"></script>';

        $load_FacebookSDK = false;
    }
    $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $safeNotes = htmlspecialchars($notes, ENT_QUOTES, 'UTF-8');

    echo '<div class="media_facebook">';
    echo '<div class="fb-video" data-href="' . $safeUrl . '" data-width="auto" data-allowfullscreen="true" data-autoplay="false" data-show-captions="false"></div>';
    echo '</div>';
    if (!empty($safeNotes)) {
        echo "<figcaption>$safeNotes</figcaption>";
    }
}


function ps_renderOfficeViewer($url, $notes)
{
    $intRandom = random_int(10, 100000);
    // Supported File Types: .doc, .docx, .xls, .xlsx, .ppt, .pptx, .rtf, .csv, and .txt
    // $viewerUrl = 'https://docs.google.com/gview?url=' . htmlspecialchars($url) . '&embedded=true';
    $viewerUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' . htmlspecialchars($url);
    echo '<div class="media_wrapper" id="iframe_container_' . $intRandom . '">';
    echo '<iframe class="modal_frame" src="' . $viewerUrl . '" width="100%" frameborder="0" allowfullscreen></iframe>';
    echo '</div>';

    echo '<figcaption>';
    if (!empty($notes)) {
        echo "<span>$notes</span>: ";
    }
    echo '<a href="' . htmlspecialchars($url) . '" target="_blank">Open in New Tab</a>';
    echo '</figcaption>';
}

function ps_renderIframeWithLoader($url, $notes)
{
    $intRandom = random_int(10, 100000);
    echo '<div class="media_wrapper" id="iframe_container_' . $intRandom . '">';
    echo '<div class="loading_message"><img src="../imgPG/LoaderIcon.gif" alt="iFrame is loading..."></div>';
    echo '<iframe class="modal_frame" src="' . htmlspecialchars($url) . '" frameborder="0" allowfullscreen wmode="transparent"></iframe>';
    echo '</div>';

    echo '<figcaption>';
    if (!empty($notes)) {
        echo "<span>$notes</span>: ";
    }
    echo '<a href="' . htmlspecialchars($url) . '" target="_blank">Open external link in New Tab</a> | ';
    echo "<a id=\"iframe_modal_button_{$intRandom}\" href=\"javascript:void(0)\">Open in Fullscreen Modal Window</a>";
    echo "</figcaption>\n";

    echo "<script>\n";
    echo "jQuery(function ($) {\n";
    echo "sx_iFrameToModalWindow($, $intRandom); \n";
    echo "});\n";
    echo "</script>\n";
}

/**
 * All kinds of multimedia in the site are calling this function:
 *  - If multimedia is video, audio, presentation, PDF or External Link
 *      - the function (1), return_Media_Type_URL(), gets the type of media and
 *        the function (2), get_Media_Type_Player(), renders the relevant media application.
 *  - If multimedia is an image, it is desplayed within a figure tag.
 * 
 * @param string $mediaURL The partial URL containing the file name and extension.
 * @param string $place OPTIONAL The position of the image: Left, Right, Center
 * @param string $notes OPTIONAL Description to be shown in figcaption
 * @param string $href OPTIONAL: Internal/external HREF or "lightBox" to open the image in a lightbox gallery.
 * @param string $lightGroup OPTIONAL or RECOMENTED when $href = "lightBox": Identifier for lightbox gallery images, if many.
 * @return void Outputs the HTML for the requested media.
 */

function get_Any_Media($mediaURL, $place, $notes, $href = '', $lightGroup = '')
{
    $strPlace = "image_center";
    if ($place == "Right") {
        $strPlace = "image_right";
    } elseif ($place == "Left") {
        $strPlace = "image_left";
    }
    // Sanitize and determine notes.
    $strNotes = '';
    if ($notes !== 'none') {
        $strNotes = !empty($notes)
            ? sx_Replace_Quotes(strip_tags($notes))
            : get_Link_Title_From_File_Name($mediaURL);
    }
    // Exclude caption titles created my the media file name
    if (!SX_radioCreateCaptionByMediaName && empty($notes)) {
        $strNotes = '';
    }

    // Get the full media URL, if any.
    $strObjectValue = return_Media_Type_URL($mediaURL);
    if (!empty($strObjectValue)) {
        echo '<figure class="' . $strPlace . '">';
        get_Media_Type_Player($mediaURL, $strObjectValue, $strNotes);
        echo '</figure>';
        return;
    }

    $leftLT = "";
    $rightLT = "";
    if (!empty($href)) {
        if ($href == 'lightBox') {
            $leftLT = '<figure data-lightbox="imgTop' . htmlspecialchars($lightGroup, ENT_QUOTES, 'UTF-8') . '">';
            $rightLT = '</figure>';
        } else {
            $leftLT = return_Left_Link_Tag($href);
            $rightLT = "</a>";
        }
    }

    echo '<figure class="' . $strPlace . '">';
    echo $leftLT . '<img alt="' . htmlspecialchars($strNotes, ENT_QUOTES, 'UTF-8') . '" src="' . htmlspecialchars(sx_DefaultImgFolder . $mediaURL, ENT_QUOTES, 'UTF-8') . '">' . $rightLT;
    echo '</figure>';
}

function get_multiple_PDF_Files($arrPDF_Files)
{
    foreach ($arrPDF_Files as $file) {
        get_Any_Media($file, 'Center', '');
    }
}


/**
 * @abstract Multiple images on the top of texts shown as manual cycler/slider
 * @param mixed $arrURL : Array, if you want to sort it or string, 
 *      separating elements by semicolon (;)
 * @param string $folder : Optional, the main folder that contains the media
 * @param string $notes : Optional, the descritpion on the bottom of Cicler
 * @return mixed : flex-cards in HTML.
 */
function get_Manual_Image_Cycler($arrURL, $folder, $notes, $nav_mode = 'number')
{
    if (!empty($arrURL)) {
        if (!is_array($arrURL)) {
            $arrURL = explode(';', $arrURL);
        }
        if (!empty($notes)) {
            $notes = sx_Replace_Quotes($notes);
        }

        $countURL = count($arrURL);
        $strImgFolder = sx_DefaultImgFolder;
        if (!empty($folder)) {
            $strImgFolder = $folder;
        } ?>
        <div class="img_cycler_manual jqImgCyclerManual">
            <figure data-lightbox="manual_cycler_<?php echo random_int(10, 10000) ?>">
                <?php
                $strNotesByImageName = "";
                $listNavImgs = '';
                for ($z = 0; $z < $countURL; $z++) {
                    $strClass = '';
                    if ($z == 0) {
                        $strClass = ' class="selected"';
                    }
                    $loopURL = trim($arrURL[$z]);
                    if (!empty($loopURL)) {
                        $strAlt = get_Link_Title_From_File_Name($loopURL);
                        $listNavImgs .= "<li$strClass><img src=\"$strImgFolder$loopURL\" /></li>";
                ?>
                        <img src="<?= $strImgFolder . $loopURL ?>" data-title="<?php echo $strAlt ?>" alt="<?php echo $strAlt . ' - ' . SX_imageAltName ?>" />
                <?php
                    }
                    if (!empty($strNotesByImageName)) {
                        $strNotesByImageName .= ', ';
                    }
                    $strNotesByImageName .= '<b>' . ($z + 1) . '</b> ' . $strAlt;
                } ?>
            </figure>
            <?php
            // Check if thumbnails are numbers or images (to decrease padding for images)
            $radioNumberThumbs = false;
            $sThumbClass = ' class="image_padding"';
            if (defined('SX_thumpsImageOrNumber') && !empty(SX_thumpsImageOrNumber)) {
                $nav_mode = SX_thumpsImageOrNumber;
            }
            if ($nav_mode == 'number' || empty($nav_mode)) {
                $radioNumberThumbs = true;
                $sThumbClass = '';
            } ?>
            <ul>
                <li class="more-prev"></li>
                <li>
                    <ul<?php echo $sThumbClass ?>>
                        <?php
                        /*
                        if (defined('SX_thumpsImageOrNumber') && !empty(SX_thumpsImageOrNumber)) {
                            $nav_mode = SX_thumpsImageOrNumber;
                        }
                        if ($nav_mode == 'number' || empty($nav_mode)) {
                        */
                        if ($radioNumberThumbs) {
                            for ($z = 0; $z < $countURL; $z++) {
                                if (!empty(trim($arrURL[$z]))) {
                                    $strClass = "";
                                    if ($z == 0) {
                                        $strClass = 'class="selected"';
                                    } ?>
                <li <?= $strClass ?>><span><?= ($z + 1) ?></span></li>
    <?php
                                }
                            }
                        } else {
                            echo $listNavImgs;
                        } ?>
            </ul>
            </li>
            <li class="more-next"></li>
            </ul>
            <?php
            if (!empty($notes)) {
                echo "<div>$notes</div>";
            } elseif (!empty($strNotesByImageName) && SX_showCaptionInGallery) {
                echo "<div>$strNotesByImageName</div>";
            } ?>
        </div>
    <?php
    }
}

/**
 * @abstract Places multiple images one under the other on the right of texts
 * @param mixed $arrURL : Array, if you want to sort it or string, 
 *      separating elements by semicolon (;)
 * @param string $notes : Optional, the descritpion on the bottom of all images
 * @return mixed : HTML
 */
function get_Right_Images($arrURL, $notes, $position = 'Right')
{
    if (!empty($arrURL)) {
        if (!is_array($arrURL)) {
            $arrURL = explode(';', $arrURL);
        }
        if (!empty($notes)) {
            $notes = sx_Replace_Quotes($notes);
        }

        if ($position === 'Left') {
            echo '<figure class="image_left">';
        } else {
            echo '<figure class="image_right">';
        }
        for ($z = 0; $z < count($arrURL); $z++) {
            $strImg = trim($arrURL[$z]);
            $strObjectValue = return_Media_Type_URL($strImg);
            if (!empty($strObjectValue)) {
                echo '<figure>';
                get_Media_Type_Player($strImg, $strObjectValue);
                echo '</figure>';
            } elseif (!empty($strImg)) {
                $linkTitle = get_Link_Title_From_File_Name($strImg);
                echo '<figure data-lightbox="imgMore"><img src="' . sx_DefaultImgFolder . $strImg . '" alt="' . $linkTitle . '" /></figure>';
                if (empty($notes)) {
                    echo "<figcaption> $linkTitle . </figcaption>";
                }
            }
        }
        if (!empty($notes)) {
            echo '<figcaption>' . $notes . '</figcaption>';
        }
        echo '</figure>';
    }
}

/**
 * @abstract Places multiple images one under the other on the right of texts
 * @param mixed $arrURL : Array, if you want to sort it or string, 
 *      separating elements by semicolon (;)
 * @param string $notes : Optional, the descritpion on the bottom of all images
 * @return mixed : HTML
 */
function get_Left_Images($arrURL, $notes)
{
    if (!empty($arrURL)) {
        if (!is_array($arrURL)) {
            $arrURL = explode(';', $arrURL);
        }
        if (!empty($notes)) {
            $notes = sx_Replace_Quotes($notes);
        }

        echo '<figure class="image_left">';
        for ($z = 0; $z < count($arrURL); $z++) {
            $strImg = trim($arrURL[$z]);
            $strObjectValue = return_Media_Type_URL($strImg);
            if (!empty($strObjectValue)) {
                echo '<figure>';
                get_Media_Type_Player($strImg, $strObjectValue);
                echo '</figure>';
            } elseif (!empty($strImg)) {
                $linkTitle = get_Link_Title_From_File_Name($strImg);
                echo '<figure data-lightbox="imgMore"><img src="' . sx_DefaultImgFolder . $strImg . '" alt="' . $linkTitle . '" /></figure>';
                if (empty($notes)) {
                    echo "<figcaption> $linkTitle . </figcaption>";
                }
            }
        }
        if (!empty($notes)) {
            echo '<figcaption>' . $notes . '</figcaption>';
        }
        echo '</figure>';
    }
}


/**
 * Portraits of one or more text authors
 * - used in conferences application
 */
function get_Photo_Portrates($photos)
{ ?>
    <div class="grid_cards flex_cards_portrait">
        <?php
        if (strpos($photos, ";") == 0) {
            $photos .= ";";
        }
        $arrPorts = explode(";", $photos);
        $iCount = count($arrPorts);
        for ($f = 0; $f < $iCount; $f++) {
            $strURL = $arrPorts[$f];
            if (!empty($strURL)) {
                if (strpos($strURL, "/") > 0) {
                    $arrTemp = explode("/", $strURL);
                    $strName = $arrTemp[count($arrTemp) - 1];
                }
                if (strpos($strURL, "_") > 0) {
                    $strName = explode("_", $strName)[0];
                    $strName = str_replace("-", " ", $strName);
                } ?>
                <figure data-lightbox="portraits">
                    <img alt="<?= $strName ?>" src="<?= sx_DefaultImgFolder . $strURL ?>" />
                    <figcaption><?= $strName ?></figcaption>
                </figure>
        <?php
            }
        } ?>
    </div>
<?php
}

/**
 * Prepares text images for printing, usually on the end of page
 * - used in all print pages
 */
function get_Images_To_Print($strURL, $notes)
{
    if (strpos($strURL, ";") == 0) {
        $strURL = $strURL . ";";
    }
    $arrURL = explode(";", $strURL);
    for ($z = 0; $z < count($arrURL); $z++) {
        $strImg = trim($arrURL[$z]);
        if (!empty($strImg)) {
            echo '<p><img src="' . sx_ROOT_HOST . str_replace("../", "/", sx_DefaultImgFolder) . $strImg . '" alt=""></p>';
        }
    }
    if (!empty($notes)) {
        echo '<p>' . $notes . '</p>';
    }
}

/*
    Return a string with all images in a folder
    - which are than used to create a manual slider or an inline gallery
*/
function return_Folder_Images($strSubFolderName, $subFolder = "images")
{
    $_retval = "";
    $arrImageTypes = ["gif", "png", "jpg", "jpeg", "webp", "svg"];
    $strAbsolutePath = realpath($_SERVER["DOCUMENT_ROOT"] . "/" . $subFolder . "/" . $strSubFolderName);
    $strPhotoPath = $strSubFolderName . "/";

    if (is_dir($strAbsolutePath)) {
        $dir = dir($strAbsolutePath);
        while (($file = $dir->read()) !== false) {
            $ext  = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array(strtolower($ext), $arrImageTypes)) {
                if (!empty($_retval)) {
                    $_retval .= ";";
                }
                $_retval .= $strPhotoPath . $file;
            }
        }
        $dir->close();
    }
    return $_retval;
}

/**
 * Get the images of an inline gallery
 */
/**
 * @abstract Display multiple images in inline gallery
 * @param mixed $arrPhotos : Array or string, with elements separated by semicolon (;)
 * @param string $subFolder : The main folder that contains the images, 
 * @param bool $radioSort : If images should be sorted by name, 
 * @param array $arrNotes : Description of each image in corresponding array - Do NOT Sort images if not empty, 
 * @param bool $radioCaptionTitle : If the name of image will be transformed to a caption title 
 * @return mixed : HTML with figurs and images.
 */
function get_Inline_Gallery_Images($arrPhotos, $subFolder = '../images/', $radioSort = true, $arrNotes = '', $radioCaptionTitle = SX_showCaptionInGallery)
{
    if (!empty($arrPhotos)) {
        if (!is_array($arrPhotos)) {
            $arrPhotos = explode(";", $arrPhotos);
        }
        if ($radioSort && empty($arrNotes)) {
            sort($arrPhotos);
        }
        $lengthNotes = 0;
        $radioNotes = false;
        if (is_array($arrNotes)) {
            $lengthNotes = count($arrNotes);
        }
        echo '<div class="ps_inline_gallery jqps_inline_gallery">';
        $length = count($arrPhotos);
        if ($length === $lengthNotes) {
            $radioNotes = true;
        }
        for ($p = 0; $p < $length; $p++) {
            $file  = trim($arrPhotos[$p]);
            $strAlt = get_Link_Title_From_File_Name($file);
            $strNote = '';
            if ($radioNotes) {
                $strNote = sx_Replace_Quotes(trim($arrNotes[$p]));
            }
            echo '<figure><img src="' . $subFolder . $file . '" alt="' . $strAlt . ' - ' . SX_imageAltName . '" />';
            $styleDisplay = ' style="display: none"';
            if ($radioCaptionTitle) {
                $styleDisplay = '';
            }
            echo '<figcaption data-notes="' . $strNote . '"' . $styleDisplay . '>' . $strAlt . '</figcaption>';
            echo '</figure>';
        }
        echo '</div>';
    }
}


/**
 * Check if a link is to an external source
 * @param mixed $link : the URL Address to be checked
 * @return bool
 */
function check_External_Link_Tag($link)
{
    if (
        stripos($link, "http://") !== false
        || stripos($link, "https://") !== false
        || stripos($link, "www.") !== false
        || stripos($link, ".html") !== false
    ) {
        return true;
    } else {
        return false;
    }
}


/**
 * @abstract Get any media (images, videos, audio, external files) in flec-cards
 * @param mixed $arrMedia : Array or string, where elements are separated by semicolon (;)
 * @param bool $radioNewFlexCard : Optional
 *      - If images with different H/W ratio will be displayed in different flex-cards
 *      - Related to the next parameter
 * @param bool $radioSort : Optional
 *      - If images will be alphabetically ordered
 *      - Is set automatically to true if $radioNewFlexCard = true
 *      - Prefix the image names with an alphabetic order, grouped by H/W ratio
 *      - If the prefix is followed by 2 underlines (__), it will not appear in titles
 * @return mixed : flex-cards in HTML.
 */
function get_media_in_grid_cards($arrMedia, $radioNewFlexCard = false, $radioSort = false)
{
    if (!empty($arrMedia)) {
        if (!is_array($arrMedia)) {
            $arrMedia = explode(';', $arrMedia);
        }

        $radioImageFiles = sx_is_file_an_image($arrMedia[0]);
        if ($radioImageFiles === false) {
            $radioNewFlexCard = false;
        }
        if ($radioNewFlexCard || $radioSort) {
            sort($arrMedia);
        }
        echo '<div class="grid_cards">';
        $strLoopABC = '';
        $lenth = count($arrMedia);
        for ($m = 0; $m < $lenth; $m++) {
            $linkTitle = "";
            $linkURL = "";
            $strMedia = trim($arrMedia[$m]);
            /**
             * If images have different dimentions, order them with a common first letter (H,V,W)
             *  - different first letters are displayed in different flex-cards
             */
            if ($radioNewFlexCard) {
                $arrFileName = explode('/', $strMedia);
                $strABC = mb_substr(end($arrFileName), 0, 1);
                if ($m > 0 && $strABC != $strLoopABC) {
                    echo '</div><div class="grid_cards">';
                }
                $strLoopABC = $strABC;
            }

            if (check_External_Link_Tag($strMedia)) {
                $linkURL = $strMedia;
                $linkTitle = lngOpenInNewWindow;
            }
            get_Any_Media($strMedia, 'Center', $linkTitle, $linkURL, '');
        }
        echo '</div>';
    }
}

/**
 * Checks if an extention is image or video
 */
function sx_is_extension_a_video($extension)
{
    $arrExtension = ["mp3", "mp4", "ogg", "webm", "wav"];
    return in_array($extension, $arrExtension);
}
function sx_is_extension_an_image($extension)
{
    $arrExtension = ["gif", "jpg", "jpeg", "png", "svg", "webp"];
    return in_array($extension, $arrExtension);
}

function sx_is_file_an_image($file)
{
    //$fileExtension = explode('.', $file);
    $fileInfo = pathinfo($file);
    if (isset($fileInfo['extension'])) {
        $fileExtension = $fileInfo['extension'];
        return sx_is_extension_an_image($fileExtension);
    } else {
        return false;
    }
}
function return_file_extension($file)
{
    $fileInfo = pathinfo($file);
    if (isset($fileInfo['extension'])) {
        return strtolower($fileInfo['extension']);
    } else {
        return '';
    }
}
