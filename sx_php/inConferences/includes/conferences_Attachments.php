<?php

/**
 * ATTACHMENTS can be shown under the MAIN column, 
 * or over FOOTER, att the BOTTOM of the class .page.
 * It is used to Show Media and PDF attachments for Conference, Sessions and Papers
 *    following the Grandparent, Parents, Children structure
 *  - if confernce is open, all attachments to this conference, 
 *      its sessions and its papers are shown
 *  - if Session is open, all its attachments, as well as the attachments 
 *      of its papers and its parent conference are shown
 *  - if Paper is open, all its attachments, as well as the attachments 
 *      of its parent session and its grand-parent conference are shown
 */

include_once dirname(__DIR__) . "/attachments.php";

/**
 * First parameter: Footer, FooterMore
 */

get_Footer_Advertisements('Footer');
get_Footer_Advertisements('FooterMore');
