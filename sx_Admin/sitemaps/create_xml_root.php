<?php

/**
 * Create Root Sitemap in the root directory
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["Create"])) {
    $dateCurrent = date('Y-m-d');
    /**
     * Create XML-file as string - Not as Constraction
     */
    $strXML = ('<?xml version="1.0" encoding="utf-8"?>');
    $strXML = $strXML . ('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

    /**
     * ============
     * FOR TEXTS
     * ============
     */
    if (sx_radioUseTextMaps) {
        if (!empty($_POST["RootMapByYear"]) && is_array($arrPublishYears)) { //By Year
            $iYear = date('Y');
            $iCount = count($arrPublishYears);
            for ($i = 0; $i < $iCount; $i++) {
                $y = $arrPublishYears[$i][0];
                /*
                if ($y == $iYear) {
                    $dateLoop = $dateCurrent;
                } else {
                    $dateLoop = $y . "-12-31";
                }
                */
                if (is_array($languageXML) && $radioUseLanguages) { //By language
                    $rows = count($languageXML);
                    for ($r = 0; $r < $rows; $r++) {
                        $strTemp = $languageXML[$r][1];
                        $strXML = $strXML . "<sitemap>";
                        $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "articles_" . $strTemp . "_" . $y . ".xml</loc>";
                        $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
                        $strXML = $strXML . "</sitemap>";
                    }
                } else { //Not by language
                    $strXML = $strXML . "<sitemap>";
                    $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "articles_" . $y . ".xml</loc>";
                    $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
                    $strXML = $strXML . "</sitemap>";
                }
            }
        } else { //Not by Year
            if (is_array($languageXML) && $radioUseLanguages) { //By language
                $rows = count($languageXML);
                for ($r = 0; $r < $rows; $r++) {
                    $strTemp = $languageXML[$r][1];
                    $strXML = $strXML . "<sitemap>";
                    $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "articles_" . $strTemp . ".xml</loc>";
                    $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
                    $strXML = $strXML . "</sitemap>";
                }
            } else { //Not by language
                $strXML = $strXML . "<sitemap>";
                $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "articles.xml</loc>";
                $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
                $strXML = $strXML . "</sitemap>";
            }
        }
        exit;
    }

    /**
     * ============
     * FOR ARTICLES
     * ============
     */
    if (sx_radioUseArticleMaps && !empty($_POST["RootArticles"])) {
        if (is_array($languageXML) && $radioUseLanguages) { //By language
            $rows = count($languageXML);
            for ($r = 0; $r < $rows; $r++) {
                $strTemp = "_" . $languageXML[$r][1];
                $strXML = $strXML . "<sitemap>";
                $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "articles" . $strTemp . ".xml</loc>";
                $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
                $strXML = $strXML . "</sitemap>";
            }
        } else { //Not by language
            $strXML = $strXML . "<sitemap>";
            $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "articles.xml</loc>";
            $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
            $strXML = $strXML . "</sitemap>";
        }
    }
    /**
     * ============
     * FOR ABOUT
     * ============
     */
    if (sx_radioUseAboutMaps && !empty($_POST["RootAbout"])) {
        if (is_array($languageXML) && $radioUseLanguages) { //By language
            $rows = count($languageXML);
            for ($r = 0; $r < $rows; $r++) {
                $strTemp = "_" . $languageXML[$r][1];
                $strXML = $strXML . "<sitemap>";
                $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "about" . $strTemp . ".xml</loc>";
                $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
                $strXML = $strXML . "</sitemap>";
            }
        } else { //Not by language
            $strXML = $strXML . "<sitemap>";
            $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "about.xml</loc>";
            $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
            $strXML = $strXML . "</sitemap>";
        }
    }

    /**
     * ============
     * FOR AUTHORS
     * ============
     */
    if ((STR_TextTableVersion == "texts" || STR_TextTableVersion == "posts") && sx_radioUseAuthorMaps && !empty($_POST["RootAuthors"])) {
        $strXML = $strXML . "<sitemap>";
        $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "authors.xml</loc>";
        $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
        $strXML = $strXML . "</sitemap>";
    }

    /**
     * ============
     * FOR THEMES
     * ============
     */
    if ((STR_TextTableVersion == "texts" || STR_TextTableVersion == "posts") && sx_radioUseThemesMaps && !empty($_POST["RootThemes"])) {
        $strXML = $strXML . "<sitemap>";
        $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "themes.xml</loc>";
        $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
        $strXML = $strXML . "</sitemap>";
    }

    /**
     * ============
     * FOR EVENTS
     * ============
     */
    if (sx_radioUseEventMaps && !empty($_POST["RootEvents"])) {
        $strXML = $strXML . "<sitemap>";
        $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "events.xml</loc>";
        $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
        $strXML = $strXML . "</sitemap>";
    }


    /**
     * ============
     * FOR CONFERENCES
     * ============
     */
    if (sx_radioUseConferneceMaps && !empty($_POST["RootConferences"])) {
        $strXML = $strXML . "<sitemap>";
        $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "conferences.xml</loc>";
        $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
        $strXML = $strXML . "</sitemap>";

        $strXML = $strXML . "<sitemap>";
        $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "sessions.xml</loc>";
        $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
        $strXML = $strXML . "</sitemap>";

        $strXML = $strXML . "<sitemap>";
        $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "papers.xml</loc>";
        $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
        $strXML = $strXML . "</sitemap>";
    }

    /**
     * ============
     * FOR PRODUCTS
     * ============
     */
    if (sx_radioUseProductMaps && !empty($_POST["RootProducts"])) {
        if (is_array($languageXML) && $radioUseLanguages) { //By language
            $rows = count($languageXML);
            for ($r = 0; $r <= $rows; $r++) {
                $strTemp = "_" . $languageXML[$r][1];
                $strXML = $strXML . "<sitemap>";
                $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "products" . $strTemp . ".xml</loc>";
                $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
                $strXML = $strXML . "</sitemap>";
            }
        } else { //Not by language
            $strXML = $strXML . "<sitemap>";
            $strXML = $strXML . "<loc>" . $strXMLTrueSiteURL . $xmlFolderPath . "products.xml</loc>";
            $strXML = $strXML . "<lastmod>" . $dateCurrent . "</lastmod>";
            $strXML = $strXML . "</sitemap>";
        }
    }

    $strXML = $strXML . "</sitemapindex>";
    $doc = new DOMDocument('1.0', 'utf-8');
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;
    $doc->loadXML($strXML, LIBXML_NOBLANKS);
    $doc->save($_SERVER['DOCUMENT_ROOT'] . "/sitemap.xml");
    $doc = null;
    $urlViewXMLFile = "../../" . "sitemap.xml";
}
?>

<h2>Create a Root Sitemap</h2>
<form action="create_xml.php" name="createRootXML" method="post" class="maxWidth">
    <fieldset>
        <?php if (sx_radioUseTextMaps) { ?>
            <p><input type="checkbox" value="Yes" name="RootMap"> Create a Root Sitemap for <b>Texts</b>.</p>
            <?php if (is_array($arrPublishYears)) { ?>
                <p><input type="checkbox" value="Yes" name="RootMapByYear"> Create links to Text Sitemaps for <b>Every Year</b> (You must check both boxes).</p>
            <?php }
        }
        if (sx_radioUseArticleMaps) { ?>
            <input type="checkbox" value="Yes" name="RootArticles"> <span>Create a Root Sitemap for <b>Articles</b>.</span><br>
        <?php }
        if (sx_radioUseConferneceMaps) { ?>
            <input type="checkbox" value="Yes" name="RootConferences"> <span>Create a Root Sitemap for <b>Conferences, Sessions and Papers</b>.</span><br>
        <?php }
        if (sx_radioUseProductMaps) { ?>
            <p><input type="checkbox" value="Yes" name="RootProducts"> Create a Root Sitemap for <b>Products</b>.</p>
        <?php } ?>
    </fieldset>
    <fieldset>
        <h3>Eventually more links to include in the Root Sitemap</h3>
        <div class="paddingLeft" style="line-height: 180%">
            <?php if (sx_radioUseAboutMaps) { ?>
                <input type="checkbox" value="Yes" name="RootAbout"> <span>Sitemap for <b>About</b>.</span><br>
            <?php }
            if (sx_radioUseAuthorMaps) { ?>
                <input type="checkbox" value="Yes" name="RootAuthors"> <span>Sitemap for <b>Authors</b>.</span><br>
            <?php }
            if (sx_radioUseThemesMaps) { ?>
                <input type="checkbox" value="Yes" name="RootThemes"> <span>Sitemap for <b>Themes</b>.</span><br>
            <?php }
            if (sx_radioUseEventMaps) { ?>
                <input type="checkbox" value="Yes" name="RootEvents"> <span>Sitemap for <b>Events</b>.</span><br>
            <?php }
            ?>
        </div>
    </fieldset>
    <fieldset>
        <div class="floatRight">
            <?php if (intval(@$iRows) > 0) { ?>
                Created itemes: <?= $iRows + 1 ?>
            <?php }
            if ($urlViewXMLFile != "") { ?>
                <h4>Root Sitemap: <a href="<?= $urlViewXMLFile ?>" target="_blank"><?= $urlViewXMLFile ?></a></h4>
            <?php } ?>
        </div>
        <p><input type="submit" value="Create XML" name="Create"></p>
    </fieldset>
</form>