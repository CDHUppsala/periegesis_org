<?php
/**
 * Is place within the class .page, on its top.
 * Can be included in any site page (index, articles, contacts, abouts, etc)
 * Mainly used for 1-2 advertices or a widescreen image on the top of the page
 */
if (sx_includeHeaderAds && $radio_UseAdvertises) {
    get_Header_Advertisements();
}