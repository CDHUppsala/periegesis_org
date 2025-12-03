<?php
if (intval($int_TextID) == 0) {
    header("Location: index.php");
    exit();
}

$radioTemp = false;
$sql = "SELECT
	t.ThemeID,
    t.IncludeInTextID,
    t.Title,
    t.SubTitle,
    t.AllowTextComments,
    t.AuthorID,
    a.FirstName AS AuthorFirstName,
    a.LastName AS AuthorLastName,
    a.Photo,
	a.Notes,
    t.Coauthors,
    t.Source,
    t.PublishedMedia,
    t.PublishedMediaLink,
    t.PublishedDate,
    t.HideDate,
    t.UseAuthorPhoto,
    t.FirstPageMediaURL,
    t.FirstPageMediaNotes,
    t.FirstPageMediaPlace,
    t.TopImagesFromFolder,
    t.TopMediaURL,
    t.TopMediaNotes,
    t.TopMediaExternalLink,
    t.RightMediaURL,
    t.RightMediaNotes,
    t.FilesForDownload,
    t.FilesForDownloadHidden,
    t.PDFArchiveID,
    t.PhotoGalleryID,
    t.MediaArchiveID,
    t.MainText
    FROM texts AS t
    LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID
    WHERE t.TextID = " . $int_TextID . " ";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($rs)) {
    $radioTemp = true;
    $iThemeID = $rs["ThemeID"];
    $iIncludeInTextID = $rs["IncludeInTextID"];
    $strTitle = $rs["Title"];
    $strSubTitle = $rs["SubTitle"];
    $radio__AllowTextComments = $rs["AllowTextComments"];
    $iAuthorID = $rs["AuthorID"];
    $strAuthorFirstName = $rs["AuthorFirstName"];
    $strAuthorLastName = $rs["AuthorLastName"];
    $strPhoto = $rs["Photo"];
    $memoAuthorNotes = $rs["Notes"];
    $strCoauthors = $rs["Coauthors"];
    $strSource = $rs["Source"];
    $strPublishedMedia = $rs["PublishedMedia"];
    $strPublishedMediaLink = $rs["PublishedMediaLink"];
    $datePublishedDate = $rs["PublishedDate"];
    $radioHideDate = $rs["HideDate"];
    $radioUseAuthorPhoto = $rs["UseAuthorPhoto"];

    $strFirstPageMediaURL = $rs["FirstPageMediaURL"];
    $strFirstPageMediaNotes = $rs["FirstPageMediaNotes"];
    $strFirstPageMediaPlace = $rs["FirstPageMediaPlace"];

    $strTopImagesFromFolder = $rs["TopImagesFromFolder"];
    $strTopMediaURL = $rs["TopMediaURL"];
    $strTopMediaNotes = $rs["TopMediaNotes"];
    $strTopMediaExternalLink = $rs["TopMediaExternalLink"];
    $strRightMediaURL = $rs["RightMediaURL"];
    $strRightMediaNotes = $rs["RightMediaNotes"];
    $strFilesForDownload = $rs["FilesForDownload"];
    $strFilesForDownloadHidden = $rs["FilesForDownloadHidden"];
    $intPDFArchiveID = $rs["PDFArchiveID"];
    $intPhotoGalleryID = $rs["PhotoGalleryID"];
    $intMediaArchiveID = $rs["MediaArchiveID"];
    $memoText = $rs["MainText"];
}
$stmt = null;
//$rs = null;
unset($rs);

if ($radioTemp) {
    /**
     * If Top Media is empty, replace it with First Page Meida, if any 
     */
    /*
    if (empty($strTopMediaURL) && !empty($strFirstPageMediaURL)) {
        $strTopMediaURL = $strFirstPageMediaURL;
        $strTopMediaNotes = $strFirstPageMediaNotes;
    }
    */

    if (!empty($strTopMediaURL)) {
        $strFirstPageMediaURL = "";
    }

    if (return_Filter_Integer($intPDFArchiveID) == 0) {
        $intPDFArchiveID = 0;
    }
    if (return_Filter_Integer($intPhotoGalleryID) == 0) {
        $intPhotoGalleryID = 0;
    }
    if (return_Filter_Integer($intMediaArchiveID) == 0) {
        $intMediaArchiveID = 0;
    }
    if (return_Filter_Integer($iAuthorID) == 0) {
        $iAuthorID = 0;
    }

    $strAuthor = "";
    if (intval($iAuthorID) > 0) {
        $strAuthor = '<a class="opacity_link" title="' . lngAuthorAllTexts . '" href="texts.php?authorID=' . $iAuthorID . '">' . $strAuthorFirstName . " " . $strAuthorLastName . "</a>";
    }
    if (!empty($strCoauthors)) {
        if (!empty($strAuthor)) {
            $strAuthor = $strAuthor . ", ";
        }
        $strAuthor .= $strCoauthors;
    }
    if (!empty($strSource)) {
        if (!empty($strAuthor)) {
            $strAuthor = $strAuthor . ", ";
        }
        $strAuthor .= $strSource;
    }
    if (!empty($strPublishedMedia)) {
        $leftTag = "";
        $rightTag = "";
        if (!empty($strPublishedMediaLink)) {
            $leftTag = return_Left_Link_Tag($strPublishedMediaLink);
            $rightTag = "</a>";
        }
        if (!empty($strAuthor)) {
            $strAuthor .= ", ";
        }
        $strAuthor .= $leftTag . $strPublishedMedia . $rightTag;
    }
    if (return_Is_Date($datePublishedDate) && $radioHideDate == false) {
        if (!empty($strAuthor)) {
            $strAuthor .= ", ";
        }
        $strAuthor .= '<span>' . lngPublished . ":</span> " . $datePublishedDate;
    } ?>
    <article>
        <header>
            <h1><?= $strTitle ?></h1>
            <?php if (!empty($strSubTitle)) { ?>
                <h2><?= $strSubTitle ?></h2>
            <?php }
            if (!empty($strAuthor)) { ?>
                <h4><?= $strAuthor ?></h4>
            <?php } ?>
        </header>

        <?php
        $radioMediaLinks = false;
        if ($radio_ShowSocialMediaInText && $radioLoginToReadGroup == false) {
            $radioMediaLinks = true;
        }
        include PROJECT_PHP . "/basic_PrintIncludes.php";

        $strFolderPhotos = "";
        if (!empty($strTopImagesFromFolder)) {
            $strFolderPhotos = return_Folder_Images($strTopImagesFromFolder);
        }
        if (strpos($strFolderPhotos, ";") > 0) {
            // The folder is included in the string of paths
            get_Manual_Image_Cycler($strFolderPhotos, "", $strTopMediaNotes);
        } elseif (!empty($strTopMediaURL)) {
            if (strpos($strTopMediaURL, ";") > 0) {
                get_Manual_Image_Cycler($strTopMediaURL, "", $strTopMediaNotes);
            } else {
                get_Any_Media($strTopMediaURL, "Center", $strTopMediaNotes, $strTopMediaExternalLink, $int_TextID);
            }
        }

        if (!empty($strRightMediaURL)) {
            if (strpos($strRightMediaURL, ";") > 0) {
                get_Right_Images($strRightMediaURL, $strRightMediaNotes);
            } else {
                get_Any_Media($strRightMediaURL, "Right", $strRightMediaNotes, "", $int_TextID);
            }
        } elseif (!empty($strPhoto) && $radioUseAuthorPhoto) {
            get_Any_Media($strPhoto, "Left", $strAuthorFirstName . " " . $strAuthorLastName, "", $int_TextID);
        } elseif (!empty($strFirstPageMediaURL)) {
            if (strpos($strFirstPageMediaURL, ";") > 0) {
                get_Manual_Image_Cycler($strFirstPageMediaURL, "", $strFirstPageMediaNotes);
            } else {
                echo $strTopMediaExternalLink;
                get_Any_Media($strFirstPageMediaURL, $strFirstPageMediaPlace, $strFirstPageMediaNotes, $strTopMediaExternalLink, $int_TextID);
            }
        } ?>

        <div class="text text_resizeable" lang="<?= sx_CurrentLanguage ?>">
            <div class="text_max_width">
            <?php
            echo $memoText;

            if (!empty($strFilesForDownload)) {
                sx_getDownloadableFiles($strFilesForDownload);
            }
            if (!empty($strFilesForDownloadHidden)) {
                echo '<h4>' . lngLoginToDownloadFile . '</h4>';
                if ($radio__UserSessionIsActive) {
                    echo sx_getDownloadableHiddenFiles($strFilesForDownloadHidden);
                }
            }
            if (!empty($memoAuthorNotes)) {
                echo '<footer>' . $memoAuthorNotes . '</footer>';
            } ?>
            </div>
        </div>
    <?php
} ?>
    </article>