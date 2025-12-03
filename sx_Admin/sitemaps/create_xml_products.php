<?php

if (sx_radioUseProductMaps == False) {
	echo "<h2>Products Map is not active</h1>";
	echo "<p>Please check the file admin_design.php</p>";
	exit();
}

$xmlProductFileURL = "/products.php?pid=";
$iRows = 0;

/**
 * ===============================
 * SITE MAP PRODUCTS
 * ===============================
 */
$radioUpToDate = False;
if (!empty(@$_POST["Create"])) {
	$radioAddToXMLFile = False;
	$strProductWhere = "";
	if (@$_POST["XMLFileExists"] == "Yes" && @$_POST["CreateNew"] != "Yes") {
		$int_LastID = @$_POST["LastID".$strXMLSufix];
		if (intval($int_LastID) == 0) {$int_LastID = 0;}
		if (intval($int_LastID) > 0) {
			$radioAddToXMLFile = True;
			$strProductWhere = " AND ProductID > ".$int_LastID;
		}
	}

	$sql = "SELECT ProductID 
		FROM Products 
		WHERE Discontinued = False "
		.$strProductWhere ." 
		ORDER BY ProductID ASC ";
	//echo $sql
	$rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
	if ($rs) {
		$arrListXML= $rs;
	}
	$rs = null;

	if (is_array($arrListXML)) {
		$iRows = count($arrListXML);

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        /**
         * Create or Recreate the XML - or Add last Records
         */
        if ($radioAddToXMLFile) {
            $doc->load($strSitemapFolder . "products" . $strXMLSufix . ".xml");
            $url_set = $doc->documentElement;
        } else {
            $url_set = $doc->createElement('urlset');
            $url_set->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $doc->appendChild($url_set);
        }
        for ($r = 0; $r < $iRows; $r++) {
            $root = $doc->createElement('url');
            $url_set->insertBefore($root, $url_set->firstChild);
            $root->appendChild($doc->createElement('loc', $xmlSiteURL . $xmlProductFileURL . $arrListXML[$r][0]));
            $root->appendChild($doc->createElement('lastmod', date('Y-m-d')));
            $root->appendChild($doc->createElement('changefreq', 'monthly'));
        }
        $arrListXML = null;
        $doc->save($strSitemapFolder . "products" . $strXMLSufix . ".xml");
        $urlViewXMLFile = $levelsBack . "products" . $strXMLSufix . ".xml";

        $doc = null;
    } else {
        $radioUpToDate = true;
    }
    $arrListXML = null;
}
?>
<h2>Sitemaps for Products</h2>
<form action="create_xml.php?type=Products" name="createProductXML" method="post" class="maxWidth">
    <h3>Create a New Sitemap or add New Products to the Existed Sitemap</h3>
	<fieldset>
		<p><input type="radio" name="Type" value="Products" checked> Sitemap for Products</p>
	</fieldset>
<?php if (is_array($languageXML) && $radioUseLanguages) {?>
    <h3>Select the Language Sitemap to Create or Update</h3>
	<fieldset>
    	<?php 
		$rows = count($languageXML,2);
    	for ($r=0; $r < $rows; $r++) {
    		$strChecked = "";
    		if (!empty($strLangXML)) {
    			if ($languageXML[$r][1] == $sLang) {$strChecked = "checked ";}
    		} else {
    			if ($languageXML[$r][1] == sx_DefaultSiteLang) {$strChecked = "checked ";}
    		} ?>
    		<input <?=$strChecked?>type="radio" value="<?=$languageXML[$r][0]."_".$languageXML[$r][1]?>" name="langXML"><?=$languageXML[$r][0]." ".$languageXML[$r][2]?>
        <?php }?>
	</fieldset>
<?php ;}
 
/**
 * Get Last Entries of existed XML-Files, if any
 */

 $radioRecreatProductSitemap = False;
 
 $doc = new DOMDocument();
 if (is_array($languageXML) && $radioUseLanguages) {
    $rows = count($languageXML);
    for ($r=0; $r < $rows; $r++) {
		$strXMLSufix = "_".$languageXML[$r][1];
        $strLoc = ($strSitemapFolder . "products" . $strXMLSufix . ".xml");
        if (file_exists($strLoc)) {

			echo "<h3>Last entry in Sitemap: products".$strXMLSufix.".xml</h3>";


            $doc->load($strLoc);
            $LastEntry = $doc->getElementsByTagName("url")[0];
            $strLastLoc = $LastEntry->getElementsByTagName("loc")->item(0)->nodeValue;
            echo "<ul>" . "\n";
            echo "<li><b>Last Text ID:</b> " . $strLastLoc . "</li>" . "\n";
            echo "<li><b>Last Modified:</b> " . $LastEntry->getElementsByTagName("lastmod")->item(0)->nodeValue . "</li>" . "\n";
            echo "</ul>" . "\n";

            $radioRecreatTextSitemap = true;
            $arrLastID = explode("=",$strLastLoc);
            echo '<input type="hidden" name="LastID' . $strXMLSufix . '" value="' . trim($arrLastID[1]) . '">';


		}
	}
} else {
    $strLoc = ($strSitemapFolder . "products.xml");
    if (file_exists($strLoc)) {
		echo "<h3>Last entry in Sitemap: products.xml</h3>";

		$doc->load($strLoc);
        $LastEntry = $doc->getElementsByTagName("url")[0];
        $strLastLoc = $LastEntry->getElementsByTagName("loc")->item(0)->nodeValue;
        echo "<ul>" . "\n";
        echo "<li><b>Last Products ID:</b> " . $strLastLoc . "</li>" . "\n";
        echo "<li><b>Last Modified:</b> " . $LastEntry->getElementsByTagName("lastmod")->item(0)->nodeValue . "</li>" . "\n";
        echo "</ul>" . "\n";
        $radioRecreatTextSitemap = true;
        $arrLastID = explode("=",$strLastLoc);
        echo '<input type="hidden" name="LastID" value="' . trim($arrLastID(1)) . '">';
	}
}
$doc = null;
?>
	<fieldset style="display: flex">
		<p style="padding-right: 40px;">
		<?php if ($radioRecreatProductSitemap) {?>
			<input type="hidden" name="XMLFileExists" value="Yes">
			<input type="checkbox" name="CreateNew" value="Yes"> Create a New Sitemap
			<br><br>
			If sitemaps already <b>Exist</b>, only <b>New Products</b> will be added.<br>
			If you want to <b>Delete</b> existed sitemaps and <b>Recreate</b> them, check the above box.
		<?php }?>
		</p>
		<p class="alignRight">
			<input type="submit" value="Create or Update Sitemap" name="Create">
		</p>
	</fieldset>
	<p style="font-weight: bold; text-align: right">
		<?php if (intval($iRows) > 0) {?>
			Added itemes: <?=$iRows + 1?><br>
		<?php ;}
		if ($urlViewXMLFile != "") {?>
	    	Sitemap: <a href="<?=$urlViewXMLFile?>" target="_blank"><?=$urlViewXMLFile?></a>
		<?php ;}
		if ($radioUpToDate) {?>
			The existed Sitemap is Up to Date!
		<?php }?>
	</p>
</form>
