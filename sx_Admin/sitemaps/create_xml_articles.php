<?php
$strarticlesTableInUsed = "articles";
$xmlarticlesFileURL = "/articles.php?aid=";
$iRows = 0;

/**
 * ===============================
 * SITE MAPS
 * ===============================
 */
if (!empty($_POST["Create"])) {
    $arrListXML = null;
    if (!empty($strarticlesTableInUsed)) {
        $sql = "SELECT ArticleID, Title, InsertDate 
			FROM articles 
			WHERE Hidden = False " . $strWhere . " ORDER BY InsertDate DESC";
        $rs = $conn->query($sql)->fetchAll();
        if ($rs) {
            $arrListXML = $rs;
        }
        $rs = null;
    }

    if (is_array($arrListXML)) {
        $iRows = count($arrListXML);

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        $basic = $doc->createElement('urlset');
        $basic->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $doc->appendChild($basic);

        for ($r = 0; $r < $iRows; $r++) {
            $root = $doc->createElement('url');
            $basic->appendChild($root);
            $root->appendChild($doc->createElement('loc', $xmlSiteURL . $xmlarticlesFileURL . $arrListXML[$r][0]));
            $root->appendChild($doc->createElement('title', sx_getEntityReference($arrListXML[$r][1])));
            $root->appendChild($doc->createElement('lastmod', $arrListXML[$r][2]));
            $root->appendChild($doc->createElement('changefreq', 'never'));
        }
        $doc->save($strSitemapFolder . "articles" . $strXMLSufix . ".xml");
        $urlViewXMLFile = $levelsBack . "articles" . $strXMLSufix . ".xml";
        $doc = null;
    }
    $arrListXML = null;
}
?>

<h2>Sitemap for Articles</h2>
<form action="create_xml.php?type=Articles" name="createArticlesXML" method="post" class="maxWidth">
    <h3>Recreate the Sitemaps every time you add a New Text</h3>
    <fieldset>
        <p><input type="radio" name="Type" value="Articles" Checked> Sitemap for articles</p>
    </fieldset>
    <?php
    /**
     * SITEMAPS FOR EVERY LANGUAGE
     */
    if (is_array($languageXML) && $radioUseLanguages) { ?>
        <h3>Select the Language for the Sitemap to be Created or Recreated</h3>
        <fieldset>
            <?php
            $rows = count($languageXML);
            for ($r = 0; $r < $rows; $r++) {
                $strChecked = "";
                if (!empty($strLangXML)) {
                    if ($languageXML[$r][1] == $sLang) {
                        $strChecked = "checked ";
                    }
                } else {
                    if ($languageXML[$r][1] == sx_DefaultSiteLang) {
                        $strChecked = "checked ";
                    }
                } ?>
                <input <?= $strChecked ?>type="radio" value="<?= $languageXML[$r][0] . "_" . $languageXML[$r][1] ?>" name="langXML"><?= $languageXML[$r][0] . " " . $languageXML[$r][2] ?>
            <?php } ?>
        </fieldset>
    <?php
    } ?>

    <fieldset>
        <div class="floatRight">
            <?php
            if (intval($iRows) > 0) {
                echo "<p>Created itemes: $iRows </p>";
            }
            if (!empty($urlViewXMLFile)) { ?>
                <p>Sitemap: <a href="<?= $urlViewXMLFile ?>" target="_blank"><?= $urlViewXMLFile ?></a></p>
            <?php } ?>
        </div>
        <p><input type="submit" value="Create XML" name="Create"></p>
    </fieldset>
</form>
<?php
/**
 * GET LAST ENTRY FOR EVERY LANGUAGE'
 */
if (is_array($languageXML) && $radioUseLanguages) {
    $rows = count($languageXML);
    for ($r = 0; $r < $rows; $r++) {
        $strXMLSufix = "_" . $languageXML[$r][1];
        $strLoc = ($strSitemapFolder . "articles" . $strXMLSufix . ".xml");
        if (file_exists($strLoc)) {
            echo '<h3>Last entry in Sitemap: <a target="_blank" href="../../sitemap/articles' . $strXMLSufix . '.xml">articles' . $strXMLSufix . '.xml</a></h3>';
            $xml = simplexml_load_file($strLoc);
            if ($xml !== false) {
                $firstURL = $xml->url[0];
                if ($firstURL !== null) {
                    echo "<ul>" . "\n";
                    echo "<li><b>Last ID:</b> " . (string) $firstURL->loc . "</li>" . "\n";
                    echo "<li><b>Last Modified:</b> " . (string) $firstURL->lastmod . "</li>" . "\n";
                    echo "</ul>" . "\n";
                } else {
                    echo "URL element not found.";
                }
            } else {
                echo "Failed to load XML file.";
            }
            $xml = null;
        }
    }
} else {
    $strLoc = ($strSitemapFolder . "articles.xml");
    if (file_exists($strLoc)) {
        echo '<h3>Last entry in Sitemap: <a target="_blank" href="../../sitemap/articles.xml">articles.xml</a></h3>';
        $xml = simplexml_load_file($strLoc);
        if ($xml !== false) {
            $firstURL = $xml->url[0];
            if ($firstURL !== null) {
                echo "<ul>" . "\n";
                echo "<li><b>Last ID:</b> " . (string) $firstURL->loc . "</li>" . "\n";
                echo "<li><b>Last Modified:</b> " . (string) $firstURL->lastmod . "</li>" . "\n";
                echo "</ul>" . "\n";
            } else {
                echo "URL element not found.";
            }
        } else {
            echo "Failed to load XML file.";
        }
        $xml = null;
    } else {
        echo '<p>The XML file has not been created yet.</p>';
    }
}

?>