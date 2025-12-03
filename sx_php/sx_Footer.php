<div class="scroll jqScrollup">&#9650;</div>

<?php
if (defined('SX_BusinessShop') && SX_BusinessShop) {
    include PROJECT_PHP_SHOP . "/sx_MsgFooter.php";
}

$useSpecialFooter = false;

if (defined('SX_showSpecialFooter') && SX_showSpecialFooter) {
    include __DIR__ . "/sx_Footer_Special.php";
    $useSpecialFooter = true;
} elseif (defined('SX_showSpecialArtFooter') && SX_showSpecialArtFooter) {
    include __DIR__ . "/sx_Footer_Art.php";
    $useSpecialFooter = true;
} else { ?>
    <footer class="footer" id="footer">
        <?php get_footer_Advertisements_In_Footer_Top() ?>
        <div class="footer_top">
            <div>
                <h4><?= lngInfo ?></h4>
                <ul class="svg____list">
                    <?php
                    if (!empty($str_SiteAdminEmail)) { ?>
                        <li>
                            <?php get_Email_In_Script($str_SiteAdminEmail, $str_SiteAdministrator); ?>
                        </li>
                    <?php
                    } ?>
                    <li>
                        <a href="contact.php">
                            <?= lngContact ?>
                        </a>
                    </li>
                    <?php
                    if ($radio_UseMembersList) { ?>
                        <li>
                             <a href="about.php?members=yes">
                               <?= $str_MembersListTitle ?>
                            </a>
                        </li>
                    <?php
                    }
                    if ($radio_ShowAcceptCookies) { ?>
                        <li>
                            <a target="_blank" href="sx_PrintPage.php?print=cookies" onclick="openCenteredWindow(this.href,'cookies','580','500');return false;">
                                <?= $str_CookiesTitle ?>
                            </a>
                        </li>
                    <?php
                    }
                    if ($radio_UsePrivacyStatement) { ?>
                        <li>
                            <a target="_blank" href="sx_PrintPage.php?print=privacy" onclick="openCenteredWindow(this.href,'privacy','580','500');return false;">
                                <?= $str_PrivacyStatementTitle ?>
                            </a>
                        </li>
                    <?php
                    }
                    if ($radio_UseConditions) { ?>
                        <li>
                            <a target="_blank" href="sx_PrintPage.php?print=conditions" onclick="openCenteredWindow(this.href,'conditions','580','500');return false;" )>
                                <?= $str_ConditionsTitle ?>
                            </a>
                        </li>
                    <?php
                    }
                    $radioShowLogotypes = true;
                    if (defined('SX_inludeLinkToLogtypsInFooter')) {
                        $radioShowLogotypes = SX_inludeLinkToLogtypsInFooter;
                    }
                    if ($radioShowLogotypes) { ?>
                        <li>
                            <a target="_blank" href="../images/logo/index.html">
                                <?= lngLogotypes ?>
                            </a>
                        </li>
                    <?php
                    } ?>
                </ul>
            </div>
            <div>
                <h4><?= str_SiteTitle ?></h4>
                <ul class="svg____list">
                    <?php
                    if (!empty($str_SiteAdminEmail)) { ?>
                        <li>
                            <?php get_Email_In_Script($str_SiteAdminEmail, "") ?>
                        </li>
                    <?php
                    }
                    if (!empty($str_Site_AllTelephones)) { ?>
                        <li>
                            <?= $str_Site_AllTelephones ?>
                        </li>
                    <?php
                    }
                    if (!empty($str_Site_FullAddress)) { ?>
                        <li>
                            <?= $str_Site_FullAddress ?>
                        </li>
                    <?php
                    }
                    if ($radio_UseMap) { ?>
                        <li>
                            <a href="contact.php">
                                <?= lngShowAddressInMap ?>
                            </a>
                        </li>
                    <?php
                    } ?>
                </ul>
            </div>
            <div>
                <?php
                if ($radio_ShowAboutTextsInFooter && SX_IncludeAboutTextsInFooter) {
                    sx_getAboutMenu_HeaderFooterTexts("footer", $str_TextsAboutTitle);
                }
                if (SX_IncludeArticlesInFooter && !empty($str_ArticlesTitle)) {
                    sx_getFooterArticles($str_ArticlesTitle);
                } ?>
            </div>
        </div>

        <div class="footer_bottom">
            <div class="social_media">
                <?php
                get_Social_Media('Footer');
                ?>
            </div>
            <div>
                <?php
                if ($radio_UseNewsLetters) {
                    include __DIR__ . "/sx_NewsLetter/sx_NewsLetter.php";
                } ?>
            </div>
            <div class="powered_by">
                By Public Sphere
            </div>
        </div>
    </footer>
<?php
}

include __DIR__ . "/sx_FooterIncludes.php";

?>
