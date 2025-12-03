<?php

// Link to text page;
$xmlTextFileURL = "/texts.php?tid=";

/**
 * ===============================
 * SITE MAPS TEXTS
 * ===============================
 */

$radioTextsUpToDate = false;
if (!empty($_POST["Create"]) && !empty(STR_TextTableVersion)) {
    $intYear = @$_POST["Year"];
    if (intval($intYear) == 0) {
        $intYear = 0;
    }
    if (intval($intYear) > 0) {
        $strWhere .= " AND YEAR(PublishedDate) = " . $intYear;
        $intLinkYear = "_" . $intYear;
    }

    $radioThisYear = true;
    if (intval($intYear) > 0) {
        if ($intYear != Date('Y')) {
            $radioThisYear = false;
        }
    }
    echo $radioThisYear;

    $radioAddToXMLFile = false;
	$strTextsWhere = "";
	$iRows = 0;
    if (@$_POST["XMLFileExists"] == "Yes" && @$_POST["CreateNew"] != "Yes" && $radioThisYear) {
        $int_LastID = @$_POST["LastID" . $strXMLSufix];
        if (intval($int_LastID) == 0) {
            $int_LastID = 0;
        }
        if (intval($int_LastID) > 0) {
            $radioAddToXMLFile = true;
            $strTextsWhere = " AND t.TextID > " . $int_LastID;
        }
    }

    $arrListXML = null;
    $sql = "SELECT t.TextID, t.Title, t.PublishedDate
    		FROM " . STR_TextTableVersion . " AS t
		    INNER JOIN text_groups AS g ON t.GroupID = g.GroupID
		    WHERE t.Publish = True AND g.Hidden = False AND g.LoginToRead = False
		    " . $strWhere . $strTextsWhere . "
		    ORDER BY t.TextID ";
    //echo $sql;
    $rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
    if ($rs) {
        $arrListXML = $rs;
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
            $doc->load($strSitemapFolder . "articles" . $strXMLSufix . $intLinkYear . ".xml");
            $url_set = $doc->documentElement;
        } else {
            $url_set = $doc->createElement('urlset');
            $url_set->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $doc->appendChild($url_set);
        }
        for ($r = 0; $r < $iRows; $r++) {
            $root = $doc->createElement('url');
            $url_set->insertBefore($root, $url_set->firstChild);
            $root->appendChild($doc->createElement('loc', $xmlSiteURL . $xmlTextFileURL . $arrListXML[$r][0]));
            $root->appendChild($doc->createElement('title', sx_getEntityReference($arrListXML[$r][1])));
            $root->appendChild($doc->createElement('lastmod', $arrListXML[$r][2]));
            $root->appendChild($doc->createElement('changefreq', 'never'));
        }
        $arrListXML = null;
        $doc->save($strSitemapFolder . "articles" . $strXMLSufix . $intLinkYear . ".xml");
        $urlViewXMLFile = $levelsBack . "articles" . $strXMLSufix . $intLinkYear . ".xml";

        $doc = null;
    } else {
        $radioTextsUpToDate = true;
    }
    $arrListXML = null;
}
?>

<h2>Sitemaps for Texts</h2>
<form action="create_xml.php?type=Texts" name="createTextXML" method="post" class="maxWidth">
    <h3>Create a New Sitemap or add New Texts to the Existed Sitemap</h3>

	<fieldset>
    <p><input type="radio" name="Type" value="Texts" Checked> Sitemap for Texts</p>
    <?php
if (sx_radioUseTextMapsByYear && is_array($arrPublishYears)) {?>
        <p>If the number of texts ar too big (more than 1000), you can create separate sitemaps for Every Year. They get the name: sitemap_YEAR.xml.</p>
        <p><b>Obs!</b> Create a Sitemap for passed years <b>Only Once</b>.
        <br><b>Obs!</b> Recreate the Sitemap for the <b>Current Year</b> every time you add New Texts.
        <br><b>Obs!</b> Μη δημιουργείς <b>ποτέ</b> Sitemap για <b>όλες τις ημερομηνίες</b> αν έχεις δημιουργήσει Sitemaps ανά έτος.</p>


		<p><select name="Year">
      <option value="0"><?=lngAllDates?></option>
      <?php
$iCount = count($arrPublishYears);
    for ($i = 0; $i < $iCount; $i++) {
        $y = $arrPublishYears[$i][0];
        $strSelected = "";
        if (intval($intYear) > 0) {
            if (intval($y) == intval($intYear)) {$strSelected = " selected";}
        } elseif ($i == 0) {
            $strSelected = " selected";
        }?>
			<option value="<?=$y?>"<?=$strSelected?>><?=$y?></option>
	<?php }?>
		</select></p>


<?php
}?>
	</fieldset>
<?php

/**
 * SITEMAPS FOR EVERY LANGUAGE
 */

 if (is_array($languageXML) && $radioUseLanguages) {?>
    <h3>Select the Language Sitemap to Create or Update</h3>

	<fieldset> <?php
$rows = count($languageXML);
    for ($r = 0; $r < $rows; $r++) {
        $strChecked = "";
        if (!empty($strLangXML)) {
            if ($languageXML[$r][1] == $sLang) {$strChecked = "checked ";}
        } else {
            if ($languageXML[$r][1] == sx_DefaultSiteLang) {$strChecked = "checked ";}
        }?>
    		<input <?=$strChecked?>type="radio" value="<?=$languageXML[$r][0] . "_" . $languageXML[$r][1]?>" name="langXML"><?=$languageXML[$r][0] . " " . $languageXML[$r][2]?>
        <?php }?>
	</fieldset> <?php
}


/**
 * GET LAST ENTRY FOR EVERY LANGUAGE
 */
if (sx_radioUseTextMapsByYear && is_array($arrPublishYears)) {
    $intLinkYear = "_" . date('Y');
}

$radioRecreatTextSitemap = false;

$doc = new DOMDocument();
if (is_array($languageXML) && $radioUseLanguages) {
    $rows = count($languageXML);
    for ($r = 0; $r < $rows; $r++) {
        $strXMLSufix = "_" . $languageXML[$r][1];
        $strLoc = ($strSitemapFolder . "articles" . $strXMLSufix . $intLinkYear . ".xml");
        if (file_exists($strLoc)) {
            echo "<h3>Last entry in Sitemap: articles" . $strXMLSufix . $intLinkYear . ".xml</h3>";
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
    $strLoc = ($strSitemapFolder . "articles" . $intLinkYear . ".xml");
    if (file_exists($strLoc)) {
        echo "<h3>Last entry in Sitemap: articles" . $intLinkYear . ".xml</h3>";
        $doc->load($strLoc);
        $LastEntry = $doc->getElementsByTagName("url")[0];
        $strLastLoc = $LastEntry->getElementsByTagName("loc")->item(0)->nodeValue;
        echo "<ul>" . "\n";
        echo "<li><b>Last Text ID:</b> " . $strLastLoc . "</li>" . "\n";
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
		<?php if ($radioRecreatTextSitemap) {?>
			<input type="hidden" name="XMLFileExists" value="Yes">
			<input type="checkbox" name="CreateNew" value="Yes"> Create a New Sitemap
		<br><br>
			If sitemaps already <b>Exist</b>, only <b>New Texts</b> will be added.<br>
			If you want to <b>Delete</b> existed sitemaps and <b>Recreate</b> them, check the above box.
		<?php }?>
		</p>
		<p class="alignRight">
			<input type="submit" value="Create or Update Sitemap" name="Create">
		</p>
	</fieldset>

	<p style="font-weight: bold; text-align: right">
		<?php if (intval(@$iRows) > 0) {?>
			Added itemes: <?=@$iRows + 1?><br>
		<?php ;}
if ($urlViewXMLFile != "") {?>
	    	Sitemap: <a href="<?=$urlViewXMLFile?>" target="_blank"><?=$urlViewXMLFile?></a>
		<?php ;}
if ($radioTextsUpToDate) {?>
			The existed Sitemap is Up to Date!
		<?php }?>
	</p>
</form>
