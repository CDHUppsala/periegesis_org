<?php
include __DIR__ . "/functions.php";

$lang_And = str_LanguageAnd;
$lang_Mark = str_LangNr;
$aResults = null;
if (sx_IncludeFirstPageSections) {
    $sql = "SELECT
    pe.RowNumber,
    pe.Title,
    pe.TitleOnBottom,
    pe.TitleAtCenter,
    pe.LinkToTable,
    pe.FirstLinkPathID,
    pe.FirstLinkTitle,
    pe.SecondLinkPathID,
    pe.SecondLinkTitle,
    pe.MediaURL,
    pe.MediaPlace,
    pe.ToggleNotes,
    pe.ElementNotes,
    s.SectionID,
    s.TemplateID,
    s.SectionTitle{$lang_Mark} AS SectionTitle,
    s.SectionHeaderPlace,
    s.NotesAtCenter,
    s.SectionNotes{$lang_Mark} AS SectionNotes,
    t.SectionBackground,
    t.SectionGradientPath,
    t.HeaderBackground,
    t.HeaderTitleColor,
    t.HeaderNotesColor,
    t.ContentBackground,
    t.ContenGradientPath,
    t.RowBackground,
    t.ElementBackground,
    t.ElementTitleColor, 
    t.ElementNotesBackground,
    t.ElementNotesColor,
    t.ElementBorderWidth,
    t.ElementBorderColor,
    t.ElementShadow,
    t.ElementHoverShadow,
    t.ElementHoverColor,
    t.ElementImageShadow,
    t.ImageSmallHeight,
    t.ElementImageRadius
    FROM first_page_sections AS s
        INNER JOIN first_page_elements AS pe
            ON pe.SectionID = s.SectionID
        INNER JOIN templates AS t
            ON s.TemplateID = t.TemplateID
    WHERE s.Hidden = False AND pe.Publish = True {$lang_And}
    ORDER BY s.Sorting DESC, s.SectionID ASC, pe.RowNumber ASC, pe.Sorting DESC, pe.ElementID ASC ";
    $stmt = $conn->query($sql);
    $aResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;
}


if (!empty($aResults)) { ?>
    <section class="section_block section_block_first_page">
        <?php
        $iRows = count($aResults);
        $radioAnchore = false;
        $radioContentAndRow = false;

        $radioCreateNav = false;
        if (sx_checkMultiSections($aResults, $iRows, 'SectionID')) {
            $radioCreateNav = true;
        }

        $iLoopSectionID = 0;
        $iLoopRowNumber = 0;
        for ($r = 0; $r < $iRows; $r++) {

            $intRowNumber = $aResults[$r]['RowNumber'];
            $strTitle = $aResults[$r]['Title'];
            $radioTitleOnBottom = $aResults[$r]["TitleOnBottom"];
            $radioTitleAtCenter = $aResults[$r]["TitleAtCenter"];
            $strLinkToTable = $aResults[$r]['LinkToTable'];
            $mixFirstLinkPathID = $aResults[$r]['FirstLinkPathID'];
            $strFirstLinkTitle = $aResults[$r]['FirstLinkTitle'];
            $mixSecondLinkPathID = $aResults[$r]['SecondLinkPathID'];
            $strSecondLinkTitle = $aResults[$r]['SecondLinkTitle'];
            $strMediaURL = $aResults[$r]['MediaURL'];
            $strMediaPlace = $aResults[$r]['MediaPlace'];
            $radioToggleNotes = $aResults[$r]['ToggleNotes'];
            $memoElementNotes = $aResults[$r]['ElementNotes'];

            $iSectionID = $aResults[$r]['SectionID'];
            $strTemplateID = $aResults[$r]['TemplateID'];
            $strSectionTitle = $aResults[$r]['SectionTitle'];
            $strSectionHeaderPlace = $aResults[$r]['SectionHeaderPlace'];
            $radioNotesAtCenter = $aResults[$r]["NotesAtCenter"];
            $memoSectionNotes = $aResults[$r]['SectionNotes'];

            $strSectionBackground = $aResults[$r]['SectionBackground'];
            $strSectionGradientPath = $aResults[$r]['SectionGradientPath'];
            $strHeaderBackground = $aResults[$r]['HeaderBackground'];
            $strHeaderTitleColor = $aResults[$r]['HeaderTitleColor'];
            $strHeaderNotesColor = $aResults[$r]['HeaderNotesColor'];
            $strContentBackground = $aResults[$r]['ContentBackground'];
            $strContenGradientPath = $aResults[$r]['ContenGradientPath'];
            $strRowBackground = $aResults[$r]['RowBackground'];

            $strElementBackground = $aResults[$r]['ElementBackground'];
            $strElementTitleColor = $aResults[$r]['ElementTitleColor'];
            $strElementNotesBackground = $aResults[$r]['ElementNotesBackground'];
            $strElementNotesColor = $aResults[$r]['ElementNotesColor'];

            $intElementBorderWidth = (int) $aResults[$r]['ElementBorderWidth'];
            $strElementBorderColor = $aResults[$r]['ElementBorderColor'];

            $radioElementShadow = $aResults[$r]['ElementShadow'];
            $radioElementHoverShadow = $aResults[$r]['ElementHoverShadow'];
            $radioElementHoverColor = $aResults[$r]['ElementHoverColor'];

            $radioElementImageShadow = $aResults[$r]['ElementImageShadow'];
            $intImageSmallHeight = (int) $aResults[$r]['ImageSmallHeight'];
            $strElementImageRadius = $aResults[$r]['ElementImageRadius'];

            /**
             * Loop through the Sections
             */

            if ($iSectionID != $iLoopSectionID) {
                $radioAnchore = true;

                // Close the last Row and Content of a Section
                if ($radioContentAndRow) {
                    echo "</div></div></div>";
                }
                $radioContentAndRow = false;
                // Close the Section
                if ($iLoopSectionID > 0) {
                    echo '</div>';
                }

                if (!empty($strSectionTitle)) {
                    $strSectionTitle = htmlspecialchars($strSectionTitle);
                }

                /**
                 * 1. Get background for for current Section
                 */
                $strStyle_Section = "";
                $radioUseGradientPath = true;
                if (!empty($strSectionBackground)) {
                    if (sx_check_image_suffix($strSectionBackground)) {
                        $strStyle_Section = 'background-image: url(\'../images/' . $strSectionBackground . '\');"';
                    } elseif (sx_check_color_prefix($strSectionBackground)) {
                        if (str_contains($strSectionBackground, ';')) {
                            $radioUseGradientPath = false;
                            $strSectionBackground = str_replace(';', ',', $strSectionBackground);
                            $strStyle_Section = "background: linear-gradient($strSectionBackground);";
                        } else {

                            $strStyle_Section = 'background-color:' . $strSectionBackground . ';';
                        }
                    } elseif (str_contains($strSectionBackground, "--")) {
                            $strStyle_Section = 'background-color:var(' . $strSectionBackground . ');';
                    }
                }

                /**
                 * 2. To allow for gradients with their own colors, wtithout background color
                 */
                if (!empty($strSectionGradientPath) && $radioUseGradientPath) {
                    $strStyle_Section .= 'background-image:' . $strSectionGradientPath . ';';
                }

                if (!empty($strStyle_Section)) {
                    $strStyle_Section = ' style="' . $strStyle_Section . '"';
                }

                /**
                 * 3. Get backgrond for Header
                 */
                $strStyle_HeaderBackground = "";
                if (!empty($strHeaderBackground)) {
                    if (sx_check_image_suffix($strHeaderBackground)) {
                        $strStyle_HeaderBackground = 'background-image: url(\'../images/' . $strHeaderBackground . '\');';
                    } elseif (sx_check_color_prefix($strHeaderBackground)) {
                        if (str_contains($strHeaderBackground, ';')) {
                            $strHeaderBackground = str_replace(';', ',', $strHeaderBackground);
                            $strStyle_HeaderBackground = "background: linear-gradient($strHeaderBackground);";
                        } else {
                            $strStyle_HeaderBackground = 'background-color:' . $strHeaderBackground . ';';
                        }
                    } elseif (str_contains($strHeaderBackground, "--")) {
                        $strStyle_HeaderBackground = 'background-color:var(' . $strHeaderBackground . ');';
                    }
                }

                /**
                 * 4. Get class for Header Title color
                 */
                $style_HeaderTitleColor = "";
                if (!empty($strHeaderTitleColor)) {
                    if (sx_check_color_prefix($strHeaderTitleColor)) {
                        $style_HeaderTitleColor = ' style="color:' . $strHeaderTitleColor . ';"';
                    } elseif (str_contains($strHeaderTitleColor, "--")) {
                        $style_HeaderTitleColor = ' style="color:var(' . $strHeaderTitleColor . ');"';
                    }
                }

                /**
                 * 5. Get class for Header Notes color
                 */
                $style_HeaderNotesColor = "";
                if (!empty($strHeaderNotesColor)) {
                    if (sx_check_color_prefix($strHeaderNotesColor)) {
                        $style_HeaderNotesColor = 'style="color:' . $strHeaderNotesColor . ';"';
                    } elseif (str_contains($strHeaderNotesColor, "--")) {
                        $style_HeaderNotesColor = 'style="color:var(' . $strHeaderNotesColor . ');"';
                    }
                }

                /**
                 * 6. Get Styles and Classes for Content Background & Gradient Path
                 */
                $strStyle_ContentBackground = "";
                $radioUseGradientPath = true;
                if (!empty($strContentBackground)) {
                    if (sx_check_image_suffix($strContentBackground)) {
                        $strStyle_ContentBackground = 'background-image: url(\'../images/' . $strContentBackground . '\');';
                    } elseif (sx_check_color_prefix($strContentBackground)) {
                        if (str_contains($strContentBackground, ';')) {
                            $radioUseGradientPath = false;
                            $strContentBackground = str_replace(';', ',', $strContentBackground);
                            $strStyle_ContentBackground = "background: linear-gradient($strContentBackground);";
                        } else {
                            $strStyle_ContentBackground = 'background-color:' . $strContentBackground . ';';
                        }
                    } elseif (str_contains($strContentBackground, "--")) {
                        $strStyle_ContentBackground = 'background-color:var(' . $strContentBackground . ');';
                    }
                }
                if (!empty($strContenGradientPath) && $radioUseGradientPath) {
                    $strStyle_ContentBackground .= 'background-image:' . $strContenGradientPath . ';';
                }

                /**
                 * 7. Get Styles for Rows backgroun
                 */
                $strStyle_Row = "";
                if (!empty($strRowBackground)) {
                    if (sx_check_image_suffix($strRowBackground)) {
                        $strStyle_Row = 'style="background-image: url(\'../images/' . $strRowBackground . '\');"';
                    } elseif (sx_check_color_prefix($strRowBackground)) {
                        if (str_contains($strRowBackground, ';')) {
                            $strRowBackground = str_replace(';', ',', $strRowBackground);
                            $strStyle_Row = 'style="background: linear-gradient(' . $strRowBackground . ');"';
                        } else {
                            $strStyle_Row = 'style="background-color:' . $strRowBackground . ';"';
                        }
                    } elseif (str_contains($strRowBackground, "--")) {
                        $strStyle_Row = 'style="background-color:var(' . $strRowBackground . ');"';
                    }
                }

                /**
                 * 8. Get Classes and Styles for Elements
                 */

                $strStyle_Elements = "";
                if (!empty($strElementBackground)) {
                    if (sx_check_image_suffix($strElementBackground)) {
                        $strStyle_Elements = 'background-image: url(\'../images/' . $strElementBackground . '\');';
                    } elseif (sx_check_color_prefix($strElementBackground)) {
                        if (str_contains($strElementBackground, ';')) {
                            $strElementBackground = str_replace(';', ',', $strElementBackground);
                            $strStyle_Elements = "background: linear-gradient($strElementBackground);";
                        } else {
                            $strStyle_Elements = 'background-color:' . $strElementBackground . ';';
                        }
                    } elseif (str_contains($strElementBackground, "--")) {
                        $strStyle_Elements = 'background-color:var(' . $strElementBackground . ');';
                    }
                }

                $border_color = "";
                if ($intElementBorderWidth > 0) {
                    if (!empty($strElementBorderColor)) {
                        if (sx_check_color_prefix($strElementBorderColor)) {
                            $border_color = $strElementBorderColor;
                        } elseif (str_contains($strElementBorderColor, "--")) {
                            $border_color = 'var(' . $strElementBorderColor . ')';
                        }
                    }
                    if (!empty($border_color)) {
                        $strStyle_Elements .= 'border: ' . $intElementBorderWidth . 'px solid ' . $border_color . ';';
                    }
                }

                if (!empty($strStyle_Elements)) {
                    $strStyle_Elements = 'style="' . $strStyle_Elements . '"';
                }

                $strClass_Elements = "";
                if ($radioElementShadow) {
                    $strClass_Elements .= ' element_shadow';
                }
                if ($radioElementHoverShadow) {
                    $strClass_Elements .= ' element_shadow_hover';
                }
                if ($radioElementHoverColor) {
                    $strClass_Elements .= ' element_hover';
                }

                /**
                 * 9. Get Classes and Styles for Element Contents
                 */

                $style_ElementTitleColor = "";
                if (!empty($strElementTitleColor)) {
                    if (sx_check_color_prefix($strElementTitleColor)) {
                        $style_ElementTitleColor = 'color: ' . $strElementTitleColor . ';';
                    } elseif (str_contains($strElementTitleColor, "--")) {
                        $style_ElementTitleColor = 'color: var(' . $strElementTitleColor . ');';
                    }
                }

                if (!empty($style_ElementTitleColor)) {
                    $style_ElementTitleColor =  ' style="' . $style_ElementTitleColor . '"';
                }

                $style_ElementText = "";
                if (!empty($strElementNotesColor)) {
                    if (sx_check_color_prefix($strElementNotesColor)) {
                        $style_ElementText = 'color: ' . $strElementNotesColor . ';';
                    } elseif (str_contains($strElementNotesColor, "--")) {
                        $style_ElementText = 'color: var(' . $strElementNotesColor . ');';
                    }
                }
                if(!empty($strElementNotesBackground)) {
                    $style_ElementText .= "background-color: {$strElementNotesBackground};";
                }
                if(!empty($style_ElementText)) {
                    $style_ElementText = ' style="' . $style_ElementText . '"';
                } 

                /**
                 * Flex order between Header and Content
                 * image_radius_ image_small
                 */
                $strHeaderOrder = "";
                $strContentOrder = "";
                $strClass_SectionFlex = "";
                if (!empty($strSectionHeaderPlace) && $strSectionHeaderPlace != 'Top') {
                    $strClass_SectionFlex = 'class="section_flex"';
                    if ($strSectionHeaderPlace == 'Right') {
                        $strHeaderOrder = " order: 2;";
                        $strContentOrder = " order: 1;";
                    }
                }

                /**
                 * Final Header Styling (Background and Flex order)
                 */

                $strStyle_Header = "";
                if (!empty($strStyle_HeaderBackground)) {
                    if (!empty($strHeaderOrder)) {
                        $strStyle_Header = 'style="' . $strStyle_HeaderBackground . $strHeaderOrder . '"';
                    } else {
                        $strStyle_Header = 'style="' . $strStyle_HeaderBackground . '"';
                    }
                } elseif (!empty($strHeaderOrder)) {
                    $strStyle_Header = 'style="' . $strHeaderOrder . '"';
                }

                $strStyle_Content = "";
                if (!empty($strStyle_ContentBackground)) {
                    if (!empty($strContentOrder)) {
                        $strStyle_Content = 'style="' . $strStyle_ContentBackground . $strContentOrder . '"';
                    } else {
                        $strStyle_Content = 'style="' . $strStyle_ContentBackground . '"';
                    }
                } elseif (!empty($strContentOrder)) {
                    $strStyle_Content = 'style="' . $strContentOrder . '"';
                }
                $addClassNotes = "";
                if ($radioNotesAtCenter) {
                    $addClassNotes = ' align_center';
                }

                echo "\r\n<!-- New Section -->\r\n";

                echo '<div class="section_wrapper" ' . $strStyle_Section . '>';
                echo '<div ' . $strClass_SectionFlex . '>';
                if (!empty($strSectionTitle) || !empty($memoSectionNotes)) { ?>
                    <div class="section_header jq_anchor_mark" <?= $strStyle_Header ?> id="row-<?= $r ?>" data-id="<?= $strSectionTitle ?>">
                        <?php
                        if (!empty($strSectionTitle)) {
                            echo '<h1' . $style_HeaderTitleColor . '>' . $strSectionTitle . '</h1>';
                        }
                        if (!empty($memoSectionNotes)) { ?>
                            <div class="text_normal<?= $addClassNotes ?>" <?= $style_HeaderNotesColor ?>><?= $memoSectionNotes ?></div>
                        <?php
                        } ?>
                    </div>
            <?php
                }
                /**
                 * Get the first (lowest) Row Number of the current Section
                 * Elements with the same Row Number are placed within the same Row
                 * Used to close the last Row bellow
                 */
                $iLoopRowNumber = $intRowNumber;
            }

            /**
             * END OF PROMOTION SECTION - LOPS WHEN  PROMOTION ID IS CHANGED
             * START PROMOTION CONTENT - ROWS AND ELEMENTS
             */

            /**
             * 10. Get classes and styles for Images
             */
            $strClass_images = '';
            if ($radioElementImageShadow) {
                $strClass_images .= 'image_shadow ';
            }
            if (!empty($strElementImageRadius) && (int) $strElementImageRadius > 0) {
                if (str_contains($strElementImageRadius, 'px')) {
                    $strElementImageRadius = (int) $strElementImageRadius;
                    $strClass_images .= 'image_px_radius_' . $strElementImageRadius . ' ';
                } else {
                    $strElementImageRadius = (int) $strElementImageRadius;
                    $strClass_images .= 'image_radius_' . $strElementImageRadius . ' ';
                }
            }

            if (!empty($intImageSmallHeight) && $intImageSmallHeight > 0) {
                $strClass_images .= 'image_max_height_' . $intImageSmallHeight . ' ';
            }

            $strClass_images_place = "";
            if ($strMediaPlace == "Left") {
                $strClass_images_place = ' class="image_float_left"';
            } elseif ($strMediaPlace == "Right") {
                $strClass_images_place = ' class="image_float_right"';
            }

            if ($radioToggleNotes && empty($strClass_images_place)) {
                $strClass_images .= 'text_fixed';
            }

            if (!empty($strClass_images)) {
                $strClass_images = 'class="' . trim($strClass_images) . '"';
            }

            $strClass_ElementTitle = "";
            if ($radioTitleAtCenter) {
                $strClass_ElementTitle = ' class="align_center" ';
            }

            /**
             * If $radioAnchore is True, we have a New Section
             * Add the Content and the First Row
             */
            if ($radioAnchore) {
                $radioAnchore = false;
                $radioContentAndRow = true;
                echo '<div class="section_content" ' . $strStyle_Content . '>';
                echo '<div class="section_row" ' . $strStyle_Row . '>';
            }

            /**
             * End of the loop of Section (including Header and Content)
             * Loop through the Rows of the content - Close last Row, if any
             */

            if ($intRowNumber != $iLoopRowNumber) {
                echo '</div>';
                echo '<div class="section_row" ' . $strStyle_Row . '>';
                $iLoopRowNumber = $intRowNumber;
            }

            /**
             * Loop through the Elements of each Row
             */
            ?>
            <div class="section_element<?= $strClass_Elements ?>" <?= $strStyle_Elements ?>>
                <?php
                if (!empty($strTitle) && $radioTitleOnBottom == false) {
                    echo '<h2' . $strClass_ElementTitle . $style_ElementTitleColor . '>' . $strTitle . '</h2>';
                } ?>
                <figure <?= $strClass_images ?>>
                    <?php
                    if (!empty($strMediaURL)) {
                        echo "<div{$strClass_images_place}>";
                        // Check for multiple images, else, any single media
                        if (strpos($strMediaURL, ";") > 0) {
                            get_Manual_Image_Cycler($strMediaURL, "", "");
                        } else {
                            get_Any_Media($strMediaURL, "", "", "");
                        }
                        echo '</div>';
                    }

                    if (!empty($strTitle) || !empty($memoElementNotes)) { ?>
                        <figcaption>
                            <?php
                            if (!empty($strTitle) && $radioTitleOnBottom) {
                                echo '<h2' . $strClass_ElementTitle . $style_ElementTitleColor . '>' . $strTitle . '</h2>';
                            }
                            if (!empty($memoElementNotes)) { ?>
                                <div class="text_normal" <?= $style_ElementText ?>><?= $memoElementNotes ?></div>
                            <?php
                            } ?>
                        </figcaption>
                    <?php
                    } ?>
                </figure>
                <?php
                if (!empty($mixFirstLinkPathID) || !empty($mixSecondLinkPathID)) {
                    sx_getButtons($strLinkToTable, $mixFirstLinkPathID, $strFirstLinkTitle, $mixSecondLinkPathID, $strSecondLinkTitle);
                } ?>
            </div>
        <?php
            $iLoopSectionID = $iSectionID;
        }
        $aResults = null;
        // Close last Row and the Content, if any
        if ($radioContentAndRow) {
            echo '</div></div>';
        }
        echo '</div></div>';
        echo "\r\n<!-- End of Last Section -->\r\n";

        if (sx_includeAnchoreInFirstPageSections) {
            include __DIR__ . "/nav_jquery.php";
        } ?>
    </section>
<?php
}
?>