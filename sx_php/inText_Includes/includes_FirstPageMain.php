<?php
include_once dirname(__DIR__) . "/inText_Archives/archives_TextsPagingQuery.php";

if ($radio_UseRelatedTexts) {
    /**
     * Functions are called from default.php and read.php 
     * - if $radio_UseRelatedTexts == True
     */
    require dirname(__DIR__) . "/inTexts/functions_texts_related.php";
}

include PROJECT_PHP . "/sx_Slider/includes_slider.php";

/**
 * If the text version uses aside texts in first page
 */

if ($radio_UseAsideTexts && sx_includeAsideTexts) { ?>
    <div class="grid_varied">
        <section class="grid_varied_left" id="jqLoadPageNav" aria-label="Introduction of Articles">
            <?php
            require dirname(__DIR__) . "/inTexts/default.php";
            ?>
        </section>
        <section class="grid_varied_right" aria-label="Introduction of Selected Articles">
            <?php
            require dirname(__DIR__) . "/inTexts/texts_Aside.php";
            ?>
        </section>
    </div>
<?php
} else { ?>
    <section id="jqLoadPageNav" aria-label="Introduction of Articles">
        <?php
        require dirname(__DIR__) . "/inTexts/default.php"; 
        ?>
    </section>
<?php
}

if (sx_includeSpotLights) {
    require dirname(__DIR__) . "/sx_Spots.php";
}

/**
 * Footer Slider
 */
if (sx_includeFooterSlider) {
    get_Footer_Advertisements_Slider('cycler_nav_bottom', 'move_right_left');
}
