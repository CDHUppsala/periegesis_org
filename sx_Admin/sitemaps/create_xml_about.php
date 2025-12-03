<?php

$strAboutTableInUsed = "about";
$xmlAboutFileURL = "/about.php?aboutid=";
$iRows = 0;

/**
 * ===============================
 * SITE MAPS
 * ===============================
 */
if (!empty(@$_POST["Create"])) {
	$arrListXML = null;
	if (!empty($strAboutTableInUsed)) {
		$sql = "SELECT AboutID, Title, InsertDate 
			FROM ".$strAboutTableInUsed." 
			WHERE Hidden = False ". $strWhere ." ORDER BY InsertDate DESC";
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
			$root->appendChild($doc->createElement('loc', $xmlSiteURL . $xmlAboutFileURL . $arrListXML[$r][0]));
			$root->appendChild($doc->createElement('title', sx_getEntityReference($arrListXML[$r][1])));
			$root->appendChild($doc->createElement('lastmod', $arrListXML[$r][2]));
			$root->appendChild($doc->createElement('changefreq', 'never'));
		}
		$doc->save($strSitemapFolder."about".$strXMLSufix.".xml");
		$urlViewXMLFile = $levelsBack."about".$strXMLSufix.".xml";
		$doc = null;
	}
	$arrListXML = null;
}
?>

<h2>Sitemap for Texts About</h2>
<form action="create_xml.php?type=About" name="createAboutXML" method="post" class="maxWidth">
	<h3>Recreate the Sitemaps every time you add a New Text</h3>
	<fieldset>
	    <p><input type="radio" name="Type" value="About" Checked> Sitemap for About Texts</p>
	</fieldset>
<?php 
/**
 * SITEMAPS FOR EVERY LANGUAGE
 */
if (is_array($languageXML) && $radioUseLanguages) {?>
    <h3>Select the Language Sitemap to be Created or Recreated</h3>
	<fieldset>
	<?php 
    	$rows = count($languageXML);
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
<?php 
}
/**
 * GET LAST ENTRY FOR EVERY LANGUAGE'
 */
$doc = new DOMDocument();
if (is_array($languageXML) && $radioUseLanguages) {
    $rows = count($languageXML);
    for ($r=0; $r < $rows; $r++) {
		$strXMLSufix = "_".$languageXML[$r][1];
		$strLoc = ($strSitemapFolder."about".$strXMLSufix.".xml");
		if (file_exists($strLoc)) {
			echo "<h3>Last entry in Sitemap: about".$strXMLSufix.".xml</h3>";
			$doc->load($strLoc);
			$LastEntry = $doc->getElementsByTagName("url")[0];
			echo "<ul>"."\n";
			echo "<li><b>Last Text ID:</b> ". $LastEntry->getElementsByTagName("loc")->item(0)->nodeValue ."</li>"."\n";
			echo "<li><b>Last Modified:</b> ". $LastEntry->getElementsByTagName("lastmod")->item(0)->nodeValue ."</li>"."\n";
			echo "</ul>"."\n";
		}
	}
} else {
	$strLoc = ($strSitemapFolder."about.xml");
	if (file_exists($strLoc)) {
		echo "<h3>Last entry in Sitemap: about.xml</h3>";
		$doc->load($strLoc);
		$LastEntry = $doc->getElementsByTagName("url")[0];
		echo "<ul>"."\n";
		echo "<li><b>Last Text ID:</b> ". $LastEntry->getElementsByTagName("loc")->item(0)->nodeValue ."</li>"."\n";
		echo "<li><b>Last Modified:</b> ". $LastEntry->getElementsByTagName("lastmod")->item(0)->nodeValue ."</li>"."\n";
		echo "</ul>"."\n";
	}
}
$doc = null;

?>
	<fieldset>
        <div class="floatRight">
        <?php if (intval(@$iRows) > 0) {?>
        	Created itemes: <?=$iRows + 1?>
        <?php ;}
        if (!empty($urlViewXMLFile)) {?>
            <h3>Sitemap: <a href="<?=$urlViewXMLFile?>" target="_blank"><?=$urlViewXMLFile?></a></h3>
        <?php }?>
        </div>
		<p><input type="submit" value="Create XML" name="Create"></p>
	</fieldset>
</form>
