<?php
include dirname(dirname(__DIR__)) ."/functionsLanguage.php";
include dirname(dirname(__DIR__)) ."/login/lockPage.php";
include dirname(dirname(__DIR__)) ."/functionsTableName.php";
include dirname(dirname(__DIR__)) ."/functionsDBConn.php";


/**
 * Might be used in admin
 */
function return_template_colors()
{
    $conn = dbconn();
    $sql = "SELECT DISTINCT MainColor
    FROM templates";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function return_templates($color = "")
{
    $strWhere = "";
    if (!empty($color)) {
        $strWhere = " WHERE MainColor = ?";
    }
    $conn = dbconn();
    $sql = "SELECT
        TemplateID,
        MainColor,
        TemplateTitle,
        SectionBackground,
        SectionGradientPath,
        HeaderBackground,
        HeaderTitleColor,
        HeaderNotesColor,
        ContentBackground,
        ContenGradientPath,
        RowBackground,
        ElementBackground,
        ElementTitleColor,
        ElementNotesColor,
        ElementBorderWidth,
        ElementBorderColor,
        ElementShadow,
        ElementHoverShadow,
        ElementHoverColor,
        ElementImageShadow,
        ImageSmallHeight,
        ElementImageRadius
    FROM templates $strWhere
    ORDER BY MainColor, TemplateTitle";

    $stmt = $conn->prepare($sql);
    if (!empty($strWhere)) {
        $stmt->execute([$color]);
    } else {
        $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$postMainColor = "";
if (!empty($_POST['MainColor'])) {
    $postMainColor = $_POST['MainColor'];
}

$arrTemps = return_templates($postMainColor);
?>

<link rel="stylesheet" charset="utf-8" href="../sxCss/root_Colors.css?v=2023">
<link rel="stylesheet" charset="utf-8" href="../sxCss/sx_Images.css?v=2023">
<link rel="stylesheet" charset="utf-8" href="../sxCss/sx_Sections.css?v=2023">
<?php
$arrColors = return_template_colors();
if (!empty($arrColors) && is_array($arrColors)) { ?>
    <h2>Select a Main Template Color</h2>
    <form action="design/templates/index.php" method="post" name="GetTemplatesByColor" class="jqLoadSelectForm">
        <select name="MainColor">
            <option value="">All Colors</option>
            <?php
            foreach ($arrColors as $row) {
                $loopMainColor = $row["MainColor"];
                $strChecked = "";
                if ($loopMainColor == $postMainColor) {
                    $strChecked = ' selected';
                }
                echo '<option style="background-color: var(--' . strtolower($row["MainColor"]) . ')" value="' . $row["MainColor"] . '" ' . $strChecked . '>' . $row["MainColor"] . '</option>';
            }
            ?>
        </select>
        <input type="submit" value="Get Templates" name="GetTemlates">
    </form>
<?php
} ?>

<div class="section_block" id="jq_selectTemplateID">
    <?php

    if (!empty($arrTemps) && is_array($arrTemps)) {
        $iRows = count($arrTemps);

        for ($r = 0; $r < $iRows; $r++) {
            $intTemplateID = $arrTemps[$r]['TemplateID'];
            $strMainColor = $arrTemps[$r]['MainColor'];
            $strTemplateTitle = $arrTemps[$r]['TemplateTitle'];

            $mixTemplateID = $intTemplateID . ': ' . $strMainColor . ' - ' . $strTemplateTitle;

            $strSectionBackground = $arrTemps[$r]['SectionBackground'];
            $strSectionGradientPath = $arrTemps[$r]['SectionGradientPath'];
            $strHeaderBackground = $arrTemps[$r]['HeaderBackground'];
            $strHeaderTitleColor = $arrTemps[$r]['HeaderTitleColor'];
            $strHeaderNotesColor = $arrTemps[$r]['HeaderNotesColor'];
            $strContentBackground = $arrTemps[$r]['ContentBackground'];
            $strContenGradientPath = $arrTemps[$r]['ContenGradientPath'];
            $strRowBackground = $arrTemps[$r]['RowBackground'];
            $strElementBackground = $arrTemps[$r]['ElementBackground'];
            $strElementTitleColor = $arrTemps[$r]['ElementTitleColor'];

            $strElementNotesColor = $arrTemps[$r]['ElementNotesColor'];
            $intElementBorderWidth = (int) $arrTemps[$r]['ElementBorderWidth'];
            $strElementBorderColor = $arrTemps[$r]['ElementBorderColor'];

            $radioElementShadow = $arrTemps[$r]['ElementShadow'];
            $radioElementHoverShadow = $arrTemps[$r]['ElementHoverShadow'];
            $radioElementHoverColor = $arrTemps[$r]['ElementHoverColor'];

            $radioElementImageShadow = $arrTemps[$r]['ElementImageShadow'];
            $intImageSmallHeight = (int) $arrTemps[$r]['ImageSmallHeight'];
            $strElementImageRadius = $arrTemps[$r]['ElementImageRadius'];



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
                    $style_ElementText = ' style="color: ' . $strElementNotesColor . '";';
                } elseif (str_contains($strElementNotesColor, "--")) {
                    $style_ElementText = ' style="color: var(' . $strElementNotesColor . ')";';
                }
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
            $strStyle_images = '';
            if (!empty($intImageSmallHeight) && $intImageSmallHeight > 0) {
                $strClass_images .= 'image_max_height_' . $intImageSmallHeight;
                $style_ElementTitleColor .= 'text-align: center;';
            }

            if (!empty($strClass_images)) {
                $strClass_images = 'class="' . trim($strClass_images) . '"';
            }

            if (!empty($style_ElementTitleColor)) {
                $style_ElementTitleColor =  ' style="' . $style_ElementTitleColor . '"';
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
            } ?>
            <h3><input type="radio" name="SectionTemplateID" value="<?= $intTemplateID ?>" data-id="<?= $strTemplateTitle ?>">
                Select: <?= $strTemplateTitle ?></h3>
    <?php
            include __DIR__ . "/get_templates.php";
        }
    }
    $arrTemps = null;
    ?>
</div>
<script>
    sxAjaxLoadArchives();
</script>