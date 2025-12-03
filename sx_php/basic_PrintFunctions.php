<?php

//Recall Multimedia and Social Media links separately from any page
//===================================== 

//Next function is not used
function getLinkToFlipBookURL($urlFlip)
{ ?>
    <a target="_blank" target="_blank" title="<?= lngOpenurlFlipile ?>" href="../imgFlipBok/<?= $urlFlip ?>"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_book_open"></use>
        </svg></a>
<?php
}
// Next function is Not Used
function getLinkToFileInArchives($urlArch)
{ ?>
    <a target="_blank" title="<?= lngUploadFile ?>" href="../archives/<?= $urlArch ?>" download><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_save"></use>
        </svg></a>
<?php
}

function get_LinkToDownload($url, $title)
{ ?>
    <a target="_blank" title="<?= lngDownloadFile . ": " . $title ?>" href="../imgPDF/<?= $url ?>"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_download"></use>
        </svg></a>
<?php
}
function get_LinkToPDFFile($urlPDF, $title)
{ ?>
    <a target="_blank" title="<?= lngOpenPDFFile . ": " . $title ?>" href="../imgPDF/<?= $urlPDF ?>"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_PDF"></use>
        </svg></a>
<?php
}
function get_LinkToCSVFile($url, $title)
{ ?>
    <a target="_blank" title="<?= lngDownloadFile . ": " . $title ?>" href="../imgPDF/<?= $url ?>" download><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_CSV"></use>
        </svg></a>
<?php
}
function get_LinkToJSONFile($url, $title)
{ ?>
    <a target="_blank" title="<?= lngDownloadFile . ": " . $title ?>" href="../imgPDF/<?= $url ?>" download><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_JSON"></use>
        </svg></a>
<?php
}

function get_LinkToXMLFile($url, $title)
{ ?>
    <a target="_blank" title="<?= lngDownloadFile . ": " . $title ?>" href="../imgPDF/<?= $url ?>" download><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_XML"></use>
        </svg></a>
<?php
}
function get_LinkToHTMLFile($url, $title)
{ ?>
    <a target="_blank" title="<?= lngDownloadFile . ": " . $title ?>" href="../imgPDF/<?= $url ?>" download><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_HTML"></use>
        </svg></a>
<?php
}

function get_LinkToDOCFile($url, $title)
{ ?>
    <a target="_blank" title="<?= lngDownloadFile . ": " . $title ?>" href="../imgPDF/<?= $url ?>" download><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_DOC"></use>
        </svg></a>
<?php
}

function get_LinkToXLSFile($url, $title)
{ ?>
    <a target="_blank" title="<?= lngDownloadFile . ": " . $title ?>" href="../imgPDF/<?= $url ?>" download><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_XLS"></use>
        </svg></a>
<?php
}

function getLinkToPDFGallery($intPDF)
{ ?>
    <a target="_blank" title="<?= lngOpenInPDFArchives ?>" href="ps_PDF.php?archID=<?= $intPDF ?>"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_PDF_archives"></use>
        </svg></a>
<?php
}
function getPhotoGallery($intID)
{ ?>
    <a target="_blank" title="<?= lngViewGallery ?>" href="ps_gallery.php?int1=<?= $intID ?>"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_image_gallery"></use>
        </svg></a>
<?php
}
function getMediaGallery($intID)
{ ?>
    <a target="_blank" title="<?= lngViewVideoGallery ?>" href="ps_media.php?archID=<?= $intID ?>"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_video_gallery"></use>
        </svg></a>
<?php
}
function getTextPrinter($url, $id)
{ ?>
    <a href="<?= $url ?>" title="<?= lngOpenForPrinting ?>" onclick="openCenteredWindow(this.href,'text<?= $id ?>','800','');return false;"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_print"></use>
        </svg></a>
<?php
}
// Email sender from server is not used
function getEmailSender($url, $id)
{ ?>
    <a title="<?= lngSuggestToAFriend ?>" href="<?= $url ?>" onclick="openCenteredWindow(this.href,'email<?= $id ?>','580','500');return false;"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_mail_open"></use>
        </svg></a>
<?php
}
function getBackArrow()
{ ?>
    <a title="<?= lngClickHereToReturn ?>" href="javascript:history.back()"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_left_xl"></use>
        </svg></a>
<?php
}
function getLocalEmailSender($url, $title, $subtitle, $author)
{
    if (empty($url)) {
        $strTipPath = (sx_LOCATION);
    } else {
        $strTipPath = (sx_LANGUAGE_PATH . $url);
    }
    if (!empty($strTipPath)) {
        $strTipPath = rawurlencode($strTipPath);
    }

    $sTextTitle = $title;
    if (!empty($subtitle)) {
        $sTextTitle .= " - " . $subtitle;
    }
    if (!empty($author)) {
        $sTextTitle .= ", " . $author;
    }
    if (!empty($sTextTitle)) {
        $sTextTitle = rawurlencode(strip_tags($sTextTitle));
    }

    if (str_contains($url, 'events.php')) {
        $strMailSubject = lngTipEventSubject . " " . str_SiteTitle;
        $strTipBodyTitle = lngTipEventBodyTitle;
    } else {
        $strMailSubject = lngTipSubject . " " . str_SiteTitle;
        $strTipBodyTitle = lngTipBodyTitle;
    }
    $strMailSubject = rawurlencode($strMailSubject);

    $strMailBody = $strTipBodyTitle . " " . str_SiteTitle . ": ";
    $strMailBody = rawurlencode($strMailBody) . "%0D%0A%0D%0A" . $sTextTitle . ".";
    $strMailBody = $strMailBody . "%0D%0A" . rawurlencode(lngTipURL) . " " . $strTipPath;

    $strURL = htmlspecialchars("mailto:?subject=" . $strMailSubject . "&body=" . $strMailBody);
?>
    <a title="<?= lngSuggestToAFriend ?>" href="<?= $strURL ?>"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_mail_open"></use>
        </svg></a>
<?php
}
function getExternalLink($url, $txt = '')
{ ?>
    <a title="<?= lngExternalLink . " " . $txt ?>" href="<?= $url ?>" target="_blank"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_new_window_arrow_bold"></use>
        </svg></a>
<?php
}

function get_Social_MediaLinks()
{
    $sMetaTitle = str_MetaTitle ?? '';
?>
    <div class="sx_share">
        <div class="sx_share_image jqToggleNext" title="Share on Social Media">
            <svg class="sx_svg">
                <use xlink:href="../imgPG/sx_svg/sx_media_symbols.svg?v=2025_01-17#sx_share-variant-square"></use>
            </svg>
        </div>
        <div class="sx_share_media">
            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(sx_LOCATION) ?>" title="Share on Facebook">
                <svg class="sx_svg">
                    <use xlink:href="../imgPG/sx_svg/sx_media_symbols.svg?v=2025_01-17#sx_facebook_square"></use>
                </svg></a>
            <a target="_blank" href="https://twitter.com/share?url=<?= urlencode(sx_LOCATION) ?>&amp;text=<?= urlencode($sMetaTitle) ?>" title="Share on Twitter">
                <svg class="sx_svg">
                    <use xlink:href="../imgPG/sx_svg/sx_media_symbols.svg?v=2025_01-17#sx_x-twitter"></use>
                </svg></a>
            <a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(sx_LOCATION) ?>&amp;title=<?= urlencode($sMetaTitle) ?>&amp;source=<?= urlencode(sx_LANGUAGE_PATH) ?>" title="Share on Linkedin">
                <svg class="sx_svg">
                    <use xlink:href="../imgPG/sx_svg/sx_media_symbols.svg?v=2025_01-17#sx_linkedin"></use>
                </svg></a>
        </div>
    </div>
<?php
}

/**
 * Creates a list of downable fiels located at the server
 * For externally located files (e.g. https://docs.google.com)
 * 	- If it is only one source, it can be displayed in iFrame
 * 	- Muliple ones are despayed as links
 * @param mixed $strFiles : the URL of one or more files
 * @return string
 */
function sx_getDownloadableFiles($strFiles)
{
    $sReturn = '';
    $radioExternalSources = false;
    if (!empty($strFiles) && strpos($strFiles, ";") == 0) {
        $radioExternalSources = check_External_Link_Tag(trim($strFiles));
        $strFiles .= ";";
    }

    if (!empty($strFiles)) {
        $sReturn = '<h4>' . lngDownloadFiles . '</h4>';
        $arrTemp = explode(";", $strFiles);
        if ($radioExternalSources && SX_includeExternalDownloadsInFrame) {
            $sLink = trim($arrTemp[0]);
            echo $sReturn;
            get_Media_Type_Player($sLink, $sLink);
        } else {
            $sReturn .= '<ul>';
            for ($f = 0; $f < count($arrTemp); $f++) {
                $sLink = trim($arrTemp[$f]);
                if (!empty($sLink)) {
                    $radioExternalSources = check_External_Link_Tag($sLink);
                    if ($radioExternalSources) {
                        if (SX_downloadExternalDocumentIsCV) {
                            $sTitle = LNG_DownloadMyCV;
                        } else {
                            $sTitle = lngDownloadFile . ' ' . ($f + 1);
                        }
                    } else {
                        $sTitle = $sLink;
                        $iPos = 0;
                        if (strpos($sLink, "/") !== false) {
                            $iPos = strpos($sLink, "/") + 1;
                        }
                        $sTitle = substr($sLink, $iPos, strpos($sLink, ".") - $iPos);
                        $sTitle = str_replace("_", " ", str_replace("-", " ", $sTitle));
                        $sTitle = sx_separateWordsWithCamelCase($sTitle);
                    }
                    $leftLinkTag = return_Left_Link_Tag($sLink);
                    $sReturn .= '<li>' . $leftLinkTag . $sTitle . "</a></li>";
                }
            }
            $sReturn .= '</ul>';
        }
    }
    return $sReturn;
}

function sx_getDownloadableHiddenFiles($strFiles)
{
    if (strpos($strFiles, ";") == 0) {
        $strFiles .= ";";
    }
    $sReturn = '<ul>';
    $arrTemp = explode(";", $strFiles);
    for ($f = 0; $f < count($arrTemp); $f++) {
        $sLink = trim($arrTemp[$f]);
        $sTitle = "";

        if (!empty($sLink)) {
            $iPos = strpos($sLink, "/");
            $sTitle = substr($sLink, $iPos, strpos($sLink, ".") - $iPos);
            $sTitle = str_replace("_", " ", str_replace("-", " ", $sTitle));
            $sReturn .= '<li><a href="sx_PrintFile.php?fn=' . $sLink . '" target="_blank">' . $sTitle . '</a></li>';
        }
    }
    return $sReturn . '</ul>';
}

/**
 * Recall Text Resizer and Printer separately from any page 
 */
function getTextResizer()
{ ?>
    <a class="sxTextResizer" href="javascript:void(0)" id="incr"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_search_plus"></use>
        </svg></a>
    <a class="sxTextResizer" href="javascript:void(0)" id="decr"><svg class="sx_svg">
            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_search_minus"></use>
        </svg></a>
<?php
}
?>