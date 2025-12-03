<?php

if (sx_radioUseAuthorMaps == False) {
	echo "<h2>Authors Map is not active</h1>";
	echo "<p>Please check the file admin_design.php</p>";
	exit();
}

$xmlAuthorFileURL = "/texts.php?authorID=";
$iRows = 0;

/**
 * ===============================
 * SITE MAP AUTHORS
 * ===============================
 */
if (!empty(@$_POST["Create"])) {
	$sql = "SELECT AuthorID, FirstName, LastName 
		FROM text_authors 
		WHERE Hidden = False 
		ORDER BY AuthorID ASC ";
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

		$basic = $doc->createElement('urlset');
		$basic->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$doc->appendChild($basic);
 
		for ($r = 0; $r < $iRows; $r++) {
			$root = $doc->createElement('url');
			$basic->appendChild($root);
			$root->appendChild($doc->createElement('loc', $xmlSiteURL . $xmlAuthorFileURL . $arrListXML[$r][0]));
			$root->appendChild($doc->createElement('author', sx_getEntityReference($arrListXML[$r][1])." ".sx_getEntityReference($arrListXML[$r][2])));
			$root->appendChild($doc->createElement('lastmod', date('Y-m-d')));
			$root->appendChild($doc->createElement('changefreq', 'never'));
		}
		$doc->save($strSitemapFolder."authors.xml");
		$urlViewXMLFile = $levelsBack."authors.xml";
		$doc = null;

	}
}
?>

<h2>Sitemaps for Authors</h2>
<form action="create_xml.php?type=Authors" name="createAuthorsXML" method="post" class="maxWidth">
    <h3>Recreate the Sitemap every time you add a New Author</h3>
	<fieldset>
		<p><input type="radio" name="Type" value="Authors" checked> Sitemap for Authors</p>
	</fieldset>
<?php 

/**
 * VIEW CREATED SITEMAP
 */
?>
	<fieldset>
	    <div class="floatRight">
		<?php if (intval($iRows) > 0) {?>
			Created itemes: <?=$iRows + 1?>
		<?php ;}
		if ($urlViewXMLFile != "") {?>
	    	<h4>Sitemap: <a href="<?=$urlViewXMLFile?>" target="_blank"><?=$urlViewXMLFile?></a></h4>
		<?php }?>
		</div>
		<p><input type="submit" value="Create XML" name="Create"></p>
	</fieldset>
</form>
<?php 
 
/**
 * Get Last Entry
 */
 
$doc = new DOMDocument();
$strLoc = ($strSitemapFolder."authors.xml");
if (file_exists($strLoc)) {
	echo "<h3>Last entry in Sitemap: authors.xml</h3>";
	$doc->load($strLoc);
	$LastEntry = $doc->getElementsByTagName("url")[0];
	echo "<ul>"."\n";
	echo "<li><b>Last Author ID:</b> ". $LastEntry->getElementsByTagName("loc")->item(0)->nodeValue ."</li>"."\n";
	echo "<li><b>Author Name:</b> ". $LastEntry->getElementsByTagName("author")->item(0)->nodeValue ."</li>"."\n";
	echo "<li><b>Last Modified:</b> ". $LastEntry->getElementsByTagName("lastmod")->item(0)->nodeValue ."</li>"."\n";
	echo "</ul>"."\n";
}
$doc = null;
?>