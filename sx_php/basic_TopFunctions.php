<?php

/**
 * MULTI-LANGUAGE
 * Language variables are defined in sxSiteConfig/sx_languages.php
 */

function sx_getFlags()
{
    if (sx_RadioMultiLang && is_array(sx_LangArr)) {
        $iCount = count(sx_LangArr);
        if (!empty(sx_ShowLangByFlag)) {
            for ($z = 0; $z < $iCount; $z++) {
                if (!in_array(sx_LangArr[$z][0], ARR_HiddenLanguages)) {
                    if (strpos(sx_PATH, sx_LangArr[$z][0] . "/", 0) != 1 || sx_ShowCurrentLangFlag) { ?>
                        <a title="<?= sx_LangArr[$z][1] ?>" href="../<?= sx_LangArr[$z][0] ?>/index.php?">
                            <img alt="<?= sx_LangArr[$z][0] ?>" src="../imgPG/flags/flag_<?= sx_LangArr[$z][0] ?>.svg"></a>
                    <?php
                    }
                }
            }
        } else {
            for ($z = 0; $z < $iCount; $z++) {
                if (!in_array(sx_LangArr[$z][0], ARR_HiddenLanguages)) {
                    if (strpos(sx_PATH, sx_LangArr[$z][0] . "/", 0) != 1 || sx_ShowCurrentLangFlag) { ?>
                        <a title="<?= sx_LangArr[$z][1] ?>" href="../<?= sx_LangArr[$z][0] ?>/index.php?"><?= mb_strtoupper(mb_substr(sx_LangArr[$z][1], 0, 2)) ?></a>
    <?php
                        if ($z < $iCount - 1) {
                            echo ' | ';
                        }
                    }
                }
            }
        }
    }
}

/**
 * SEARCH
 * When search is on Top, defined by design with the variable sx_IncludeTopSearch
 * Else, a link to search page appears in the main navigation
 */
function sx_getTopSearch()
{ ?>
    <form class="search_top" action="search.php" method="POST" name="search_top">
        <input type="text" placeholder="<?= lngSearch ?>" name="SearchTextTop" maxlength="40"><button type="submit" title="Search from Top of the Page" name="TopSearch"></button>
    </form>
<?php
}

/**
 * STATISTICS
 */

if ($radio_UseStatistics) {
    include PROJECT_CONFIG . "/sx_counter.php";
} ?>