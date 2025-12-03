<?php
include __DIR__ . "/functions.php";

$aResults = "";
if (!empty($int_ItemID) && intval($int_ItemID) > 0) {
    $aResults = sx_get_itemElementsByItemID($int_ItemID);
}

if (!empty($aResults) && is_array($aResults)) { ?>
    <section class="section_block">
        <?php
        $iRows = count($aResults);

        $radioAnchore = false;
        $radioContentAndRow = false;
        $radioCreateNav = true;

        $iLoopSectionID = 0;
        $iLoopRowNumber = 0;
        for ($r = 0; $r < $iRows; $r++) {
            $intElementID = $aResults[$r]['ElementID'];
            $intSectionID = $aResults[$r]['SectionID'];
            $strTitle = $aResults[$r]['Title'];
            $strTitlePlace = $aResults[$r]['TitlePlace'];
            $intRowNumber = (int) $aResults[$r]['RowNumber'];
            $strLinkToTable = $aResults[$r]['LinkToTable'];
            $mixFirstLinkPathID = $aResults[$r]['FirstLinkPathID'];
            $strFirstLinkTitle = $aResults[$r]['FirstLinkTitle'];
            $mixSecondLinkPathID = $aResults[$r]['SecondLinkPathID'];
            $strSecondLinkTitle = $aResults[$r]['SecondLinkTitle'];
            $strMediaURL = $aResults[$r]['MediaURL'];
            $strShowSliderOrGallery = $aResults[$r]["ShowSliderOrGallery"];
            $memoElementNotes = $aResults[$r]['ElementNotes'];

            $strSectionTitle = $aResults[$r]['SectionTitle'];
            $strSectionHeaderPlace = $aResults[$r]['SectionHeaderPlace'];
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

            if ($intSectionID != $iLoopSectionID) {

                $radioAnchore = true;
                // Close the last Row and Content of last Section
                if ($radioContentAndRow) {
                    echo "</div></div>";
                }
                $radioContentAndRow = false;
                // Close the last Section
                if ($iLoopSectionID > 0) {
                    echo '</div></div>';
                }


                /**
                 * 1. Get background for for current Section
                 */
                $strStyle_Section = "";
                if (!empty($strSectionBackground)) {
                    if (sx_check_image_suffix($strSectionBackground)) {
                        $strStyle_Section = 'background-image: url(\'../images/' . $strSectionBackground . '\');"';
                    } elseif (sx_check_color_prefix($strSectionBackground)) {
                        $strStyle_Section = 'background-color:' . $strSectionBackground . ';';
                    } elseif (str_contains($strSectionBackground, "--")) {
                        $strStyle_Section = 'background-color:var(' . $strSectionBackground . ');';
                    }
                }

                /**
                 * 2. To allow for gradients with their own colors, wtithout background color
                 */
                if (!empty($strSectionGradientPath)) {
                    $strStyle_Section .= 'background-image:' . $strSectionGradientPath . ';';
                }

                if (!empty($strStyle_Section)) {
                    $strStyle_Section = ' style="' . $strStyle_Section . '"';
                }

                /**
                 * 3. Get backgrond for Header
                 */
                $strStyle_HeaderBackground = "";
                if (!empty($strSectionTitle)) {
                    $strSectionTitle = htmlspecialchars($strSectionTitle);
                }
                if (!empty($strHeaderBackground)) {
                    if (sx_check_image_suffix($strHeaderBackground)) {
                        $strStyle_HeaderBackground = 'background-image: url(\'../images/' . $strHeaderBackground . '\');';
                    } elseif (sx_check_color_prefix($strHeaderBackground)) {
                        $strStyle_HeaderBackground = 'background-color:' . $strHeaderBackground . ';';
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
                if (!empty($strContentBackground)) {
                    if (sx_check_image_suffix($strContentBackground)) {
                        $strStyle_ContentBackground = 'background-image: url(\'../images/' . $strContentBackground . '\');';
                    } elseif (sx_check_color_prefix($strContentBackground)) {
                        $strStyle_ContentBackground = 'background-color:' . $strContentBackground . ';';
                    } elseif (str_contains($strContentBackground, "--")) {
                        $strStyle_ContentBackground = 'background-color:var(' . $strContentBackground . ');';
                    }
                }
                if (!empty($strContenGradientPath)) {
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
                        $strStyle_Row = 'style="background-color:' . $strRowBackground . ';"';
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
                        $strStyle_Elements = 'background-color:' . $strElementBackground . ';';
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
                    $strClass_Elements .= ' Element_shadow';
                }
                if ($radioElementHoverShadow) {
                    $strClass_Elements .= ' Element_shadow_hover';
                }
                if ($radioElementHoverColor) {
                    $strClass_Elements .= ' Element_hover';
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
                 * 9. Get classes and styles for Images - placed in Figure within Elements 
                 * .image_radius_100/50/25/10 %
                 * .image_max_height_100/150/200
                 */
                $strClass_images = '';
                if ($radioElementImageShadow) {
                    $strClass_images = 'image_shadow ';
                }
                if (!empty($strElementImageRadius) && $strElementImageRadius != 'Default') {
                    $strClass_images .= 'image_radius_' . $strElementImageRadius . ' ';
                }
                if (!empty($intImageSmallHeight) && $intImageSmallHeight > 0) {
                    $strClass_images .= 'image_max_height_' . $intImageSmallHeight;
                    $style_ElementTitleColor .= 'text-align: center;';
                }

                if (!empty($strClass_images)) {
                    $strClass_images = ' class="' . trim($strClass_images) . '"';
                }

                if (!empty($style_ElementTitleColor)) {
                    $style_ElementTitleColor =  ' style="' . $style_ElementTitleColor . '"';
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
        ?>

                <div class="section_wrapper" <?= $strStyle_Section ?>>
                    <div <?= $strClass_SectionFlex ?>>
                        <div class="section_header jq_anchor_mark" <?= $strStyle_Header ?> id="row-<?= $r ?>" data-id="<?= $strSectionTitle ?>">
                            <header>
                                <?php
                                echo '<h1' . $style_HeaderTitleColor . '>' . $strSectionTitle . '</h1>';
                                if (!empty($memoSectionNotes)) { ?>
                                    <div class="text_normal" <?= $style_HeaderNotesColor ?>><?= $memoSectionNotes ?></div>
                                <?php
                                } ?>
                            </header>
                        </div>
                    <?php
                    /**
                     * Get the first (lowest) Row Number of the current Section
                     * Elements with the same Row Number are placed within the same Row
                     * Used to close the last Row bellow
                     */
                    $iLoopRowNumber = $intRowNumber;
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
                        if (!empty($strTitle) && $strTitlePlace == 'Top') {
                            echo '<h2' . $style_ElementTitleColor . '>' . $strTitle . '</h2>';
                        } ?>
                        <figure<?php echo $strClass_images ?>>
                            <?php
                            if (!empty($strMediaURL)) {
                                // 0. Check if folder
                                if (!strpos($strMediaURL, ".") && is_dir(sx_DefaultImgFolder . $strMediaURL)) {
                                    $strMediaURL = return_Folder_Images($strMediaURL);
                                }

                                // 1. check for multiple media
                                if (strpos($strMediaURL, ";") > 0) {
                                    // 2. check for multiple videos or sounds
                                    $arrMediaURL = explode(";", $strMediaURL);
                                    $strObjectValue = return_Media_Type_URL(trim($arrMediaURL[0]));
                                    if (!empty($strObjectValue)) {
                                        echo '<div class="grid_cards">';
                                        $lenth = count($arrMediaURL);
                                        for ($m = 0; $m < $lenth; $m++) {
                                            $sMediaPath = trim($arrMediaURL[$m]);
                                            get_Any_Media($sMediaPath, 'Center', '');
                                        }
                                        echo '</div>';
                                    } else {
                                        // 3. if images, check the mode of display
                                        if ($strShowSliderOrGallery == "Slider") {
                                            get_Manual_Image_Cycler($strMediaURL, "", "");
                                        } else {
                                            get_Inline_Gallery_Images($strMediaURL, '../images/');
                                        }
                                    }
                                } else {
                                    // 4. for single media of any type
                                    get_Any_Media($strMediaURL, "", "", '');
                                }
                            }
                            if (!empty($strTitle) || !empty($memoElementNotes)) { ?>
                                <figcaption>
                                    <?php
                                    if (!empty($strTitle) && $strTitlePlace != 'Top') {
                                        echo '<h2' . $style_ElementTitleColor . '>' . $strTitle . '</h2>';
                                    }
                                    if (!empty($memoElementNotes)) { ?>
                                        <div class="text_normal" <?= $style_ElementText ?>><?= $memoElementNotes ?></div>
                                    <?php
                                    } ?>
                                </figcaption>
                            <?php
                            }
                            ?>
                        </figure>
                        <?php
                        if (!empty($mixFirstLinkPathID) || !empty($mixSecondLinkPathID)) {
                            sx_getButtons($strLinkToTable, $mixFirstLinkPathID, $strFirstLinkTitle, $mixSecondLinkPathID, $strSecondLinkTitle);
                        } ?>
                    </div>
                <?php
                $iLoopSectionID = $intSectionID;
            }
            $aResults = null;
            // Close last Row and the Content, if any
            if ($radioContentAndRow) {
                echo '</div></div>';
            } ?>
                    </div>
                </div>
                <?php
                //include __DIR__ . "/nav_jquery.php";
                ?>
    </section>
<?php
    include __DIR__ . "/nav_jquery.php";
}
?>