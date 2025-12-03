<?php

/**
 * Following variables are originally defined in config_rticles.php
 */

if (intval($int_ArticleID) == 0) {
    $int_ArticleID = 0;
}
if (intval($int_ArticleGroupID) == 0) {
    $int_ArticleGroupID = 0;
}
if (intval($int_ArticleCategoryID) == 0) {
    $int_ArticleCategoryID = 0;
}


$strWhere = "";
$strLimitRecords = "";
$strOrderBy = " ORDER BY Sorting DESC, InsertDate DESC ";

if (intval($int_ArticleID) > 0) {
    $strWhere = " AND ArticleID = " . $int_ArticleID;
    $strOrderBy = "";
} elseif (intval($int_ArticleCategoryID) > 0) {
    $strWhere = " AND ArticleCategoryID = " . $int_ArticleCategoryID;
} elseif (intval($int_ArticleGroupID) > 0) {
    $strWhere = " AND ArticleGroupID = " . $int_ArticleGroupID;
} else {
    $strLimitRecords = " LIMIT 1";
}

$radioTemp = false;

$sql = "SELECT ArticleID, Title, SubTitle, ExternalLink,
	AuthorName, InsertDate, ShowDate,
    TopSubTitle, TopDataGroupID, TopMediaPaths, TopMediaSource, TopDisplayForm, TopMediaNotes,  
	MiddleSubTitle, MiddleDataGroupID, MiddleMediaPaths, MiddleMediaSource, MiddleDisplayForm, MiddleMediaNotes,
    PDFArchiveID, FilesForDownload, WideScreen, PrintableToPDF, NotesSubTitle, ArticleNotes 
    FROM articles 
	WHERE Hidden = False " . str_LanguageAnd
    . $strWhere . $strOrderBy . $strLimitRecords;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = null;
if (is_array($rs)) {
    $radioTemp = true;
    $iArticleID = $rs["ArticleID"];
    $strTitle = $rs["Title"];
    $strSubTitle = $rs["SubTitle"];
    $strExternalLink = $rs["ExternalLink"];
    $strAuthorName = $rs["AuthorName"];
    $dateInsertDate = $rs["InsertDate"];
    $radioShowDate = $rs["ShowDate"];

    $strTopSubTitle = $rs["TopSubTitle"];
    $intTopDataGroupID = $rs["TopDataGroupID"];
    $strTopMediaPaths = $rs["TopMediaPaths"];
    $strTopMediaSource = $rs["TopMediaSource"];
    $strTopDisplayForm = $rs["TopDisplayForm"];
    $strTopMediaNotes = $rs["TopMediaNotes"];

    $strMiddleSubTitle = $rs["MiddleSubTitle"];
    $intMiddleDataGroupID = $rs["MiddleDataGroupID"];
    $strMiddleMediaPaths = $rs["MiddleMediaPaths"];
    $strMiddleMediaSource = $rs["MiddleMediaSource"];
    $strMiddleDisplayForm = $rs["MiddleDisplayForm"];
    $strMiddleMediaNotes = $rs["MiddleMediaNotes"];

    $intPDFArchiveID = $rs["PDFArchiveID"];
    if (intval($intPDFArchiveID) == 0) {
        $intPDFArchiveID = 0;
    }
    $strFilesForDownload = $rs["FilesForDownload"];
    $radioWideScreen = $rs["WideScreen"];
    $radioPrintableToPDF = $rs["PrintableToPDF"];
    $strNotesSubTitle = $rs["NotesSubTitle"];
    $memoArticleNotes = $rs["ArticleNotes"];
}
$rs = null;

$str_ArticleClasses = "";

if ($radioTemp) {
    $str_Left = '';
    $str_Right = '';
    $strLeft = '';
    $strRight = '';
    if (!empty($strExternalLink)) {
        $strLeft = '<a href="' . $strExternalLink . '" target="_blank">';
        $strRight = '</a>';
    }
    if (empty($strSubTitle)) {
        $str_Left = $strLeft;
        $str_Right = $strRight;
    }

    if (intval($int_ArticleGroupID) > 0 && $str_ArticleGroupName != $strTitle) {
        $str_ArticleClasses = '<h4 class="head_paging"><a href="articles.php?agid=' . $int_ArticleGroupID . '">' . $str_ArticleGroupName . '</a> /';
        if (intval($int_ArticleCategoryID) > 0) {
            $str_ArticleClasses .= ' <a href="articles.php?acid=' . $int_ArticleCategoryID . '">' . $str_ArticleCategoryName . '</a>';
        }
        $str_ArticleClasses .= "</h4>";
    } ?>

    <section id="immersive_reading" class="immersive-reader-button">
        <article>
            <header>
                <?php echo $str_ArticleClasses ?>
                <h1 class="head"><span><?php echo $str_Left . $strTitle . $str_Right ?></span></h1>
                <?php
                if (!empty($strSubTitle)) { ?>
                    <h2 class="head"><span><?= $strLeft . $strSubTitle . $strRight ?></span></h2>
                <?php
                }

                $strTemp = "";
                if (!empty($strAuthorName)) {
                    $strTemp = $strAuthorName;
                }
                $sx_radioShowArticleDate = true;
                if (!empty($dateInsertDate) && $radioShowDate && SX_radioShowArticleDate) {
                    if (!empty($strTemp)) {
                        $strTemp .= ", ";
                    }
                    $strTemp .= $dateInsertDate;
                }
                if (!empty($strTemp)) {
                    echo "<h4>$strTemp</h4>";
                } ?>
            </header>

            <?php
            $radioMediaLinks = false;
            if ($radio_ShowSocialMediaInText) {
                $radioMediaLinks = true;
            }
            if (SX_includeTextPrintFunctions) {
                include PROJECT_PHP . "/basic_PrintIncludes.php";
            }

            /**
             * ===================================
             * Top media section
             * ===================================
             */

            if (
                !empty($strTopSubTitle)
                || (!empty($intTopDataGroupID) && (int) $intTopDataGroupID > 0)
                || !empty($strTopMediaSource)
                || !empty($strTopMediaPaths)
                || !empty($strTopMediaNotes)
            ) {
                $strClass = 'article_section';
                if ($strTopDisplayForm == "Right Margin") {
                    $strClass = 'align_right';
                } ?>

                <section class="<?php echo $strClass ?>">
                    <?php

                    if (!empty($strTopSubTitle)) {
                        echo "<h2>$strTopSubTitle</h2>";
                    }

                    /**
                     * Ignor media paths if:
                     * - 1. there is a valid Data Group ID
                     * - 2. the media source name has the prefix 'view_' or 'views_, 
                     *   which referes to predefined database views
                     *  Both cases use external applications called by functions
                     */
                    $strMediaPath = "";
                    $radioFirst_Database_Views = false;

                    if (!empty($intTopDataGroupID) && (int) $intTopDataGroupID > 0) {
                        // Get data from the table multi_data
                        get_apps_multi_data($intTopDataGroupID);
                    } elseif (
                        // Get data from prepared database views - can be used only once 
                        // database view cannot include '/' or '.', while a folder or file can
                        !empty($strTopMediaSource)
                        && str_contains($strTopMediaSource, '/') === false
                        && str_contains($strTopMediaSource, '.') === false
                        && (str_contains($strTopMediaSource, 'view_')
                            || str_contains($strTopMediaSource, 'views_'))
                    ) {
                        get_apps_database_views($strTopMediaSource);
                        $radioFirst_Database_Views = true;
                    } else {
                        if (!empty($strTopMediaSource)) {
                            $strMediaPath = return_Folder_Images($strTopMediaSource);
                        } else {
                            $strMediaPath = $strTopMediaPaths;
                        }
                    }

                    if (!empty($strMediaPath)) {
                        // Check for multiple file paths and get the file extension of the first file
                        $radioMultiplePaths = false;
                        $strFirstFilePath = $strMediaPath;
                        $arrMediaPath = [];
                        if (strpos($strMediaPath, ";") > 0) {
                            $radioMultiplePaths = true;
                            $arrMediaPath = explode(';', $strMediaPath);
                            // Trim spaces from each array value 
                            $arrMediaPath = array_map('trim', $arrMediaPath);
                            // Remove empty values 
                            $arrMediaPath = array_filter($arrMediaPath);
                            $strFirstFilePath = $arrMediaPath[0];
                        }
                        $strFileExtension = return_file_extension($strFirstFilePath);

                        if ($strFileExtension === 'csv' || $strFileExtension === 'xml' || $strFileExtension === 'json') {
                            // Expected one or more CSV, XML or JSON files, to be transformed to tables
                            get_apps_files_to_table($strMediaPath);
                        } elseif ($radioMultiplePaths && !empty($arrMediaPath)) {
                            // Now, use the array variable: $arrMediaPath
                            if ($strFileExtension === 'pdf') {
                                // Expected multiple PDF files
                                get_multiple_PDF_Files($arrMediaPath);
                            } elseif (sx_is_extension_an_image($strFileExtension)) {
                                if ($strTopDisplayForm == "Gallery") {
                                    get_Inline_Gallery_Images($arrMediaPath, '../images/');
                                } elseif ($strTopDisplayForm == "Slider") {
                                    get_Manual_Image_Cycler($arrMediaPath, "", "");
                                } elseif ($strTopDisplayForm == "Right Margin") {
                                    get_Right_Images($arrMediaPath, '');
                                } else {
                                    /**
                                     * If images have different dimentions, order them with a common first letter (H,V,W)
                                     *  - different first letters are displayed in different flex-cards
                                     *  - Relevant only for images - the function checks if the array contains images
                                     */
                                    $radioNewFlexCard = false;
                                    if (defined('SX_newFlexCardByNewFirstLetter') && SX_newFlexCardByNewFirstLetter) {
                                        $radioNewFlexCard = true;
                                    }
                                    get_media_in_grid_cards($arrMediaPath, $radioNewFlexCard);
                                }
                            }
                        } else {
                            if ($strTopDisplayForm == "Right Margin") {
                                get_Any_Media($strMediaPath, "Right", "", "", $iArticleID);
                            } else {
                                get_Any_Media($strMediaPath, "Center", 'none', $strMediaPath);
                            }
                        }
                    }

                    if (!empty($strTopMediaNotes)) {
                        echo '<div class="text text_resizeable text_bg"><div class="text_max_width">' . $strTopMediaNotes . '</div></div>';
                    } ?>

                </section>
            <?php
            }
            /**
             * Middle media section
             */

            if (
                !empty($strMiddleSubTitle)
                || (!empty($intMiddleDataGroupID) && (int) $intMiddleDataGroupID > 0)
                || !empty($strMiddleMediaSource)
                || !empty($strMiddleMediaPaths)
                || !empty($strMiddleMediaNotes)
            ) { ?>
                <section class="article_section" id="article_section">
                    <?php
                    if (!empty($strMiddleSubTitle)) {
                        echo "<h2 id='$strMiddleSubTitle'>$strMiddleSubTitle</h2>";
                    }

                    /**
                     * Ignor media paths if:
                     * - there is a valid Multi-Data Group ID
                     * - the media source name has the prefic 'veiw_', 
                     *   which referes to predefined database views
                     */
                    $strMediaPath = "";
                    if (!empty($intMiddleDataGroupID) && (int) $intMiddleDataGroupID > 0) {
                        // Get data from the table multi_data
                        get_apps_multi_data($intMiddleDataGroupID);
                    } elseif (
                        // Get data from prepared database views - can be used only once
                        // database view cannot include '/' or '.', while a folder or file can
                        !empty($strMiddleMediaSource)
                        && str_contains($strMiddleMediaSource, '/') === false
                        && str_contains($strMiddleMediaSource, '.') === false
                        && (str_contains($strMiddleMediaSource, 'view_')
                            || str_contains($strMiddleMediaSource, 'views_'))
                    ) {
                        get_apps_database_views($strMiddleMediaSource, $radioFirst_Database_Views);
                    } else {
                        if (!empty($strMiddleMediaSource)) {
                            $strMediaPath = return_Folder_Images($strMiddleMediaSource);
                        } else {
                            $strMediaPath = $strMiddleMediaPaths;
                        }
                    }

                    if (!empty($strMediaPath)) {

                        // Check for multiple file paths and get the file extension of the first file
                        $radioMultiplePaths = false;
                        $strFirstFilePath = $strMediaPath;
                        $arrMediaPath = [];
                        if (strpos($strMediaPath, ";") > 0) {
                            $radioMultiplePaths = true;
                            $arrMediaPath = explode(';', $strMediaPath);
                            // Trim spaces from each array value 
                            $arrMediaPath = array_map('trim', $arrMediaPath);
                            // Remove empty values 
                            $arrMediaPath = array_filter($arrMediaPath);
                            $strFirstFilePath = $arrMediaPath[0];
                        }
                        $strFileExtension = return_file_extension($strFirstFilePath);

                        if ($strFileExtension === 'csv' || $strFileExtension === 'xml' || $strFileExtension === 'json') {
                            // Expected CSV, XML or JSON file, to be transformed to table
                            get_apps_files_to_table($strMediaPath);
                        } elseif ($radioMultiplePaths && !empty($arrMediaPath)) {
                            // Now, use the array variable: $arrMediaPath
                            if ($strFileExtension === 'pdf') {
                                // Expected multiple PDF files
                                get_multiple_PDF_Files($arrMediaPath);
                            } elseif (sx_is_extension_an_image($strFileExtension)) {
                                if ($strMiddleDisplayForm == "Gallery") {
                                    // Expected multiple image files
                                    get_Inline_Gallery_Images($arrMediaPath, '../images/');
                                } elseif ($strMiddleDisplayForm == "Slider") {
                                    // Expected multiple image files
                                    get_Manual_Image_Cycler($arrMediaPath, "", "");
                                } else {
                                    /**
                                     * Display Cards: Expected multiple video or/and image files
                                     * If images have different dimentions, order them with a common first letter (H,V,W)
                                     *  - different first letters are displayed in different flex-cards
                                     *  - Relevant only for images - the function checks if the array contains images
                                     */
                                    $radioNewFlexCard = false;
                                    if (defined('SX_newFlexCardByNewFirstLetter') && SX_newFlexCardByNewFirstLetter) {
                                        $radioNewFlexCard = true;
                                    }
                                    get_media_in_grid_cards($arrMediaPath, $radioNewFlexCard);
                                }
                            }
                        } else {
                            get_Any_Media($strMediaPath, 'Center', 'none', $strMediaPath);
                        }
                    }

                    if (!empty($strMiddleMediaNotes)) {
                        echo '<div class="text text_resizeable text_bg"><div class="text_max_width">' . $strMiddleMediaNotes . '</div></div>';
                    } ?>
                </section>
            <?php
            }

            if (!empty($strNotesSubTitle) || !empty($memoArticleNotes)) { ?>

                <section class="article_section">
                    <?php
                    if (!empty($strNotesSubTitle)) {
                        echo "<h2>$strNotesSubTitle</h2>";
                    }

                    if (!empty($memoArticleNotes)) {
                        $strTextClass = '';
                        if ($radioWideScreen) {
                            $strTextClass = 'text text_resizeable';
                        }
                        if ($radioPrintableToPDF || str_contains($memoArticleNotes, '<table') || str_contains($memoArticleNotes, '<TABLE')) {
                            $strTextClass = 'text_normal';
                        }

                        if (!empty($strTextClass)) { ?>
                            <div id="PrintDataToPDF" class="<?php echo $strTextClass ?>"><?php echo $memoArticleNotes; ?></div>
                        <?php
                        } else { ?>
                            <div id="PrintDataToPDF" class="text text_resizeable">
                                <div class="text_max_width"><?php echo $memoArticleNotes; ?></div>
                            </div>
                    <?php
                        }
                    }
                    if (!empty($strFilesForDownload)) {
                        sx_getDownloadableFiles($strFilesForDownload);
                    } ?>
                </section>
            <?php
            } ?>
        </article>
        <?php
        if ($radioPrintableToPDF && !empty($memoArticleNotes)) {
            $strPrintClass = 'button-complement button-gradient';
            if (defined('SX_ButtonPrintClass')) {
                $strPrintClass = SX_ButtonPrintClass;
            } ?>
            <p class="align_center">
                <a href="javascript:void(0)" class="<?php echo $strPrintClass ?> jq_PrintDataToPDF" data-id="PrintDataToPDF"><?php echo lngSavePrintInPDF ?></a>
            </p>
        <?php
        } ?>

    </section>
<?php
} ?>