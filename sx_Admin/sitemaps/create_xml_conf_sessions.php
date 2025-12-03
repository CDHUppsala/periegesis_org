<?php

if (sx_radioUseConferneceMaps == False) {
	echo "<h2>Conference Map is not active</h1>";
	echo "<p>Please check the file admin_design.php</p>";
	exit();
}
 
$xmlConferenceFileURL = "/conferences.php?sesid=";
$iRows = 0;

/**
 * ===============================
 * SITE MAP Conference Sessions
 * ===============================
 */
if (!empty(@$_POST["Create"])) {
	$arrListXML = null;
	$sql = "SELECT SessionID, SessionTitle, SessionDate 
		FROM conf_sessions 
		WHERE Hidden = False 
		ORDER BY ConferenceID DESC, SessionDate DESC, SessionID DESC ";
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
			$root->appendChild($doc->createElement('loc', $xmlSiteURL . $xmlConferenceFileURL . $arrListXML[$r][0]));
			$root->appendChild($doc->createElement('title', sx_getEntityReference($arrListXML[$r][1])));
			$root->appendChild($doc->createElement('lastmod', $arrListXML[$r][2]));
			$root->appendChild($doc->createElement('changefreq', 'monthly'));
		}
		$doc->save($strSitemapFolder."sessions.xml");
		$urlViewXMLFile = $levelsBack."sessions.xml";
		$doc = null;
	}
	$arrListXML = null;
}
?>

<h2>Sitemaps for Conference Sessions</h2>
<form action="create_xml.php?type=Sessions" name="createSessionsXML" method="post" class="maxWidth">
    <h3>Recreate the Sitemap every time you add a New Conference</h3>
	<fieldset>
		<p><input type="radio" name="Type" value="Sessions" checked> Sitemap for Sessions</p>
	</fieldset>

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
$strLoc = ($strSitemapFolder."sessions.xml");
if (file_exists($strLoc)) {
	echo "<h3>Last entry in Sitemap: sessions.xml</h3>";
	$doc->load($strLoc);
	$LastEntry = $doc->getElementsByTagName("url")[0];
	echo "<ul>"."\n";
	echo "<li><b>Last Conference Session ID:</b> ". $LastEntry->getElementsByTagName("loc")->item(0)->nodeValue ."</li>"."\n";
	echo "<li><b>Session Title:</b> ". $LastEntry->getElementsByTagName("title")->item(0)->nodeValue ."</li>"."\n";
	echo "<li><b>Last Modified:</b> ". $LastEntry->getElementsByTagName("lastmod")->item(0)->nodeValue ."</li>"."\n";
	echo "</ul>"."\n";
}
$doc = null;
?>