<?php
if (SX_includeAppsNavigation) {
    include_once __DIR__ . "/sxNav_Apps/sxNav_TopApps.php";
}

/**
 * Optional: Separate header that contains the site logo and logo advertises
 *  - Can be place anywhere, here and under the nav_head menu
 *  - Alternatively, place the site logo in nav_head and don't use header
 */

 if (defined('SX_IncludeLogoInHeader') && SX_IncludeLogoInHeader) {
    // If header has a background image
    $strStyle_Header = "";
    $strStyle_Flex = "";
    if (!empty($str_LogoBGImage)) {
        $strStyle_Header = ' style="background-image:url(../images/' . $str_LogoBGImage . ')"';
        if (sx_setLogoBackgroundInFlex) {
            $strStyle_Flex = $strStyle_Header;
            $strStyle_Header = "";
        }
    } ?>
    <header id="header" <?= $strStyle_Header ?>>
        <div class="header_content">
            <div class="header_flex" <?= $strStyle_Flex ?>>
                <div id="logo">
                    <?php
                    if (!empty($str_LogoImage)) { ?>
                        <a href="index.php"><img src="../images/<?= $str_LogoImage ?>" alt="<?= $str_LogoTitle ?>" /></a>
                    <?php
                    } else { ?>
                        <h1><a href="index.php"><?= $str_LogoTitle ?></a></h1>
                        <?php if (!empty($str_LogoSubTitle)) { ?>
                            <h2><?= $str_LogoSubTitle ?></h2>
                        <?php } ?>
                    <?php
                    } ?>
                </div>
                <?php
                if (sx_includeLogoAds) {
                    get_Logo_Advertisements();
                } ?>
            </div>
        </div>
    </header>
<?php
}

/**
 * If Navigation will be fixed in the top, jQuery toggles the position of .nav_head_fixed between static and fixed
 * jQuery recognizes the class jq_NavHeadFixed
 */
$strClassFixed = "";
if ($radio_FixedTopMenu) {
    $strClassFixed = ' jq_NavHeadFixed';
} ?>

<nav id="nav_head" aria-label="Primary Navigation">
    <div class="nav_head_fixed<?= $strClassFixed ?>">
        <div class="nav_head_flex_between">
            <?php
            if (!defined('SX_IncludeLogoInHeader') || SX_IncludeLogoInHeader === false) { ?>
                <div id="logo">
                    <?php if (!empty($str_LogoImage)) { ?>
                        <a href="index.php"><img src="../images/<?= $str_LogoImage ?>" alt="<?= $str_LogoTitle ?>" /></a>
                    <?php
                    } else { ?>
                        <h1><a href="index.php"><?= $str_LogoTitle ?></a></h1>
                        <?php if (!empty($str_LogoSubTitle)) { ?>
                            <h2><?= $str_LogoSubTitle ?></h2>
                    <?php
                        }
                    } ?>
                </div>
            <?php
            }
            if (!empty($str_LogoImageSmall)) { ?>
                <div id="logo_small">
                    <a href="index.php"><img src="../images/<?= $str_LogoImageSmall ?>" alt="<?= $str_LogoTitle ?>" /></a>
                </div>
            <?php
            } ?>

            <div class="nav_marks_flex_between">
                <div class="nav_aside_menus_Marker" id="jqNavAsideMenusMarker">
                    <svg class="sx_svg">
                        <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_menu_cards"></use>
                    </svg>
                </div>
                <div class="nav_main_menu_Marker" id="jqNavMainMenuMarker">
                    <svg class="sx_svg">
                        <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_menu_squre_list"></use>
                    </svg>
                </div>
                <div class="nav_head_menu_Marker" id="jqNavHeadMenuMarker">
                    <svg class="sx_svg">
                        <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_menu_squre"></use>
                    </svg>
                </div>
            </div>

            <div class="nav_aside_menus_Cloner" id="jqNavAsideMenusCloner"></div>
            <div class="nav_main_menu_Cloner" id="jqNavMainCloner"></div>
            <?php
            require __DIR__ . "/sxNav_Articles/sxNavHead_By_Articles.php";
            ?>
        </div>
    </div>
</nav>

<?php
/*
		sxNavHead_ByGroups					| Shows links to the 1:st level of Text Classification
		sxNavHead_ByCategories				| Shows links to the 1:st level of Text Classification and Drop Down Menus to the 2:nd level
		sxNavHead_BySubCategories			| Shows links to the 1:st level of Text Classification and Drop Down Menus to the 2:nd and 3:d level
												- You can actually use only this one: if 3:d level is absent, it has the same effect as sxNavHead_ByCategories
		sxNavHead_WideToCategories			| Shows a Common Titel fo Texts and 2 classification levels (1:st 2:nd) as Widen Screen.
		sxNavHead_WideToSubCategories		| Shows a Common Titel fo Texts and all classification levels (1:st, 2:nd and 3:d) as Widen Screen.
												- You can actually use only this one: if 3:d level is absent, it has the same effect as sxNavHead_WideByCategories
		sxNavHead_WideGroupsToSubCategories	| Shows Text Groups in First Level Menu and all other classification levels (2:nd and 3:d) as Wide Screen
												- Use it only if you have few Text Groups and a big naumber of Text Categories and Subcategories
		sxNavHead_Business/ change to:
        sxNavHead_By_Articles_Items			| For business sphere: 
												- includes navigation for articles (products/services) and items (multiple service descriptions)
*/
?>