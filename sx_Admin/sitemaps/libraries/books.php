<?php
$xmlBookFileURL = "/books.php?bid=";
$iRows = 0;

if (sx_radioBookMaps == false) {
    echo "<h2>Products Map is not active</h1>";
    echo "<p>Please check the file admin_design.php</p>";
    exit();
}
/**
 * ===============================
 * SITE Books
 * ===============================
 */
if (!empty(@$_POST["Create"])) {
    $arrListXML = null;
    $sql = "SELECT BookID, Title
        FROM books
        WHERE Hidden = False " . $strWhere . "
        ORDER BY BookID DESC ";
    $rs = $conn->query($sql);
    if ($rs) {
        $arrListXML = $rs;
    }
    $rs = null;

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
            $root->appendChild($doc->createElement('loc', $xmlSiteURL . $xmlBookFileURL . $arrListXML[$r][0]));
            $root->appendChild($doc->createElement('title', sx_getEntityReference($arrListXML[$r][1])));
            $root->appendChild($doc->createElement('lastmod', date('Y-m-d')));
            $root->appendChild($doc->createElement('changefreq', 'never'));
        }
        $doc->save($strSitemapFolder . "books" . $strXMLSufix . ".xml");
        $urlViewXMLFile = $levelsBack . "books" . $strXMLSufix . ".xml";
        $doc = null;
    }
    $arrListXML = null;
}