<?php
/**
 * First Page Sider
 * The CONSTANT sx_includeSlider is defined by design
 * The variables $radio_DefaultPage and $radio_UseSlider is defined in sx_Config.php
 */

$radio_DefaultSliderPage = false;
$radioUseSlider = false;
$str_SliderSource = null;
$int_MaxSliders = 0;
if (sx_includeSlider && $radio_UseSlider && $radio_DefaultPage ) {
    $sql = "SELECT UseSlider, SliderImageFolder,
        SliderSource, MaxSliders, SliderBGImageSize, SliderImageRatio, 
        SliderEfffectMode, SliderThumpsType, SliderThumpsPlace, SliderDescPlace
        FROM slider_setup WHERE FirstPageSlider = 1 ";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radioUseSlider = $rs["UseSlider"];
        $str_SliderImageFolder = $rs["SliderImageFolder"];
        $str_SliderSource = $rs["SliderSource"];
        $int_MaxSliders = (int) $rs["MaxSliders"];
        $strSliderBGImageSize = $rs["SliderBGImageSize"];
        $str_SliderImageRatio = $rs["SliderImageRatio"];
        $str_SliderEfffectMode = $rs["SliderEfffectMode"];
        $str_SliderThumpsType = $rs["SliderThumpsType"];
        $str_SliderThumpsPlace = $rs["SliderThumpsPlace"];
        $str_SliderDescPlace = $rs["SliderDescPlace"];
    }
    $rs = null;
    $stmt = null;
    if($int_MaxSliders == 0) {
        $int_MaxSliders = 7;
    }
}
if (!empty($str_SliderSource) && $radioUseSlider) {
    $radio_DefaultSliderPage = true;
}
