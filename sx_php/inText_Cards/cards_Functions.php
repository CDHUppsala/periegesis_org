<?php

/**
 * COOMON Functions for text selected by relation, classification level or theme
 * Selected Texts are shown within Multiple Cards:
 *      - either in multiple rows ($cycler = false)
 *      - or by cycling in one row ($cycler = true)
 * All parameters, except the first one can be empty
 * @param array $aResults : an array from database request with the relevant content
 * @param string $cycler : true/false (Cycler for true, Cards for false)
 * @param string $place : cycler_nav_middle, cycler_nav_bottom (The place of navigation);
 * @param string $mode : move_left_right, move_right_left (The direction of cycler movement)
 * @param string $more : true, false (crates a button link with Read More);
 * @param string $requestName : the name of a request parameter to by used in the button link, e.g. themeID
 * @param int $requestID : the ID value of the request parameter, to be used by the button link
 * @return mixed : HTML of multiple cards, with cyckling or not
 */
function sx_getTextInCards($aResults, $cycler = false, $place = 'cycler_nav_middle', $mode = 'move_left_right', $more = false, $requestName = '', $requestID = 0)
{
    if (is_array($aResults)) {
        if ($cycler) { ?>
            <div class="cycler_flex jq_CyclerFlexCards" data-place="<?= $place ?>" data-mode="<?= $mode ?>">
            <?php
        } ?>
            <div class="grid_cards">
                <?php
                $iRows = count($aResults);
                for ($r = 0; $r < $iRows; $r++) {

                    $iTextID = $aResults[$r][0];
                    $strTitle = $aResults[$r][1];
                    $strAuthorsName = "";
                    $strAltImage = "";
                    if (!empty($aResults[$r][5])) {
                        $strAuthorsName = $aResults[$r][5] . " " . $aResults[$r][6];
                        $strAltImage = $strAuthorsName;
                    }
                    $strCoauthors = $aResults[$r][4];
                    if ($strCoauthors != "") {
                        if (!empty($strAuthorsName)) $strAuthorsName .= ", ";
                        $strAuthorsName .= $strCoauthors;
                    }
                    if ($aResults[$r][3] == false) {
                        if (!empty($strAuthorsName)) $strAuthorsName .= ", ";
                        $strAuthorsName .=  $aResults[$r][2];
                    }

                    $strImageURL = "";
                    if (!empty($aResults[$r][8])) {
                        $strImageURL = $aResults[$r][8];
                    } elseif (!empty($aResults[$r][9])) {
                        $strImageURL = $aResults[$r][9];
                    } elseif (!empty($aResults[$r][7])) {
                        $strImageURL = $aResults[$r][7];
                    }
                    $memoIngress = null;
                    if (SX_radioShowIngressInTextCards) {
                        $memShortText = $aResults[$r][10];
                        $memoIngress = return_Left_Part_FromText($memShortText, SX_TextIngressLength);
                    }

                    $strRequest = "?";
                    if (!empty($requestName) && intval($requestID) > 0) {
                        $strRequest = "?" . $requestName . "=" . $requestID . "&";
                    }

                    if (empty($strAltImage)) {
                        $strAltImage = $strTitle;
                    }

                    $aTagOpen = '<a href="texts.php' . $strRequest . 'tid=' . $iTextID . '">';
                    $aTagClose = "</a>" ?>
                    <figure>
                        <?php
                        if (!empty($strImageURL)) {
                            if (strpos($strImageURL, ";") > 0) {
                                $strImageURL = substr($strImageURL, 0, strpos($strImageURL, ";"));
                            }
                            $strObjectValue = return_Media_Type_URL($strImageURL);
                            if (!empty($strObjectValue)) {
                                get_Media_Type_Player($strImageURL, $strObjectValue);
                            } else {
                                // Do Not include the class .img_wrapper here, if most imagas are protrates
                                if (SX_radioAbsolutCardImages) {
                                    echo '<div class="img_wrapper">';
                                } ?>
                                <?= $aTagOpen ?><img alt="<?= $strAltImage ?>" src="../images/<?= $strImageURL ?>"><?= $aTagClose ?>
                        <?php
                                if (SX_radioAbsolutCardImages) {
                                    echo '</div>';
                                }
                            }
                        } ?>
                        <figcaption>
                            <?php
                            if (!empty($strTitle)) {
                                echo "<h4>" . $aTagOpen . $strTitle . $aTagClose . "</h4>";
                            }
                            if (!empty($strAuthorsName)) {
                                echo "<p>" . $strAuthorsName . "</p>";
                            }
                            if (!empty($memoIngress)) {
                                echo '<p>' . $memoIngress . '...</p>';
                            } ?>
                        </figcaption>
                        <?php
                        if ($more) { ?>
                            <a class="read_more" href="texts.php<?= $strRequest ?>tid=<?= $iTextID ?>">
                                <?= lngMore ?></a>
                        <?php
                        } ?>
                    </figure>
                <?php
                } ?>
            </div>
    <?php if ($cycler) {
            echo '</div>';
        }
    }
    $aResults = Null;
} ?>