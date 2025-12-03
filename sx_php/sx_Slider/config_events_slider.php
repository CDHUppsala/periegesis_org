<?php
/**
 * First Event Page Sider
 * The variable $radioFirstEventPage comes from inEvents/functions_calendar.php
 * The CONSTANT sx_includeSlider is defined by design
  * The variables $radio_UseEventsSlider is defined in sx_Config.php
*/

$radioEventsSlider = false;
$radioUseSlider = false;
if (sx_includeSlider && $radio_UseEventsSlider && $radioFirstEventPage) {
    $sql = "SELECT UseSlider, SliderImageFolder,
        SliderSource, MaxSliders, SliderBGImageSize, SliderImageRatio, 
        SliderEfffectMode, SliderThumpsPlace, SliderThumpsType, SliderDescPlace
    FROM slider_setup 
    WHERE FirstPageSlider = 0 
    AND SliderSource = 'Events' ";
    $stmt = $conn->query($sql);
    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rs) {
        $radioUseSlider = $rs["UseSlider"];
        $str_SliderImageFolder = $rs["SliderImageFolder"];
        $str_SliderSource = $rs["SliderSource"];
        $int_MaxSliders = $rs["MaxSliders"];
        $str_SliderBGImageSize = $rs["SliderBGImageSize"];
        $str_SliderImageRatio = $rs["SliderImageRatio"];
        $str_SliderEfffectMode = $rs["SliderEfffectMode"];
        $str_SliderThumpsPlace = $rs["SliderThumpsPlace"];
        $str_SliderThumpsType = $rs["SliderThumpsType"];
        $str_SliderDescPlace = $rs["SliderDescPlace"];
    }
    $rs = null;
    $stmt = null;
}
if($radioUseSlider) {
    $radioEventsSlider = true;
}
