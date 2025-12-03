<body>
<?php
if (intval($intTextID) == 0) {
    echo "<h2>No Records Found</h2>";
    die;
}
if (empty($strExport)) {?>
	<div style="margin: 20px;">
	<div style="font-size: 0.9em;">
		<a href="index.php"><?=lngHomePage?></a> |
		<a target="_top" href="sx_PrintPage.php?tid=<?= $intTextID ?>&export=print"><?=lngSavePrintInPDF?></a> |
		<a target="_top" href="sx_PrintPage.php?tid=<?= $intTextID ?>&export=word"><?=lngSaveInWord?></a> |
		<a target="_top" href="sx_PrintPage.php?tid=<?= $intTextID ?>&export=html"><?=lngSaveInHTML?></a>
	</div>
	<hr>
<?php
}

$strSourceMedia = "";
if (sx_TextTableVersion != "texts_blog") {
    $strSourceMedia = " t.Source, t.PublishedMedia, ";
}
$sql = "SELECT t.TextID, t.Title, t.SubTitle, t.AllowTextComments, 
    t.Coauthors, ". $strSourceMedia ." t.PublishedDate, 
    t.FirstPageMediaURL, t.FirstPageMediaNotes, 
    t.TopMediaURL, t.TopMediaNotes, 
    t.RightMediaURL, t.RightMediaNotes, t.MainText, 
    a.FirstName, a.LastName, a.Photo, a.Notes 
    FROM ". sx_TextTableVersion ." AS t 
    LEFT JOIN text_authors AS a ON t.AuthorID = a.AuthorID 
    WHERE t.TextID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$intTextID]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if (!is_array($rs)) { ?>
	<h2><?=lngTextDoesNotExist?></h2><?=lngCloseWindowReturnToSite?>
<?php } else {
    echo '<h1>'. $rs["Title"] .'</h1>';
    if ($rs["SubTitle"] != "") {
        echo '<h2>'. $rs["SubTitle"] .'</h2>';
    }
 
    $radioAllowTextComments = $rs["AllowTextComments"];
    $strMainText = $rs["MainText"];
 
    $authorsName = "";
    if ($rs["LastName"] != "") {
        $authorsName = $rs["FirstName"].", ".$rs["LastName"];
    }
    if (!empty($authorsName)) {
        if ($rs["Coauthors"] != "") {
            $authorsName = $authorsName.", ".$rs["Coauthors"];
        }
    }
 
    $strTextInfo = "";
    if (sx_TextTableVersion != "texts_blog") {
        if ($rs["Source"] != "") {
            $strTextInfo = $rs["Source"].", ";
        }
        if ($rs["PublishedMedia"] != "") {
            $strTextInfo = $strTextInfo.$rs["PublishedMedia"].", ";
        }
    }
 
    $strTextInfo = $strTextInfo.$rs["PublishedDate"];
    $strPhoto = $rs["Photo"]; ?>
	<h3><?= $authorsName  ?></h3>
	<h4><?= $strTextInfo  ?></h4>
	<hr>
	<?php
    if ($strPhoto != "") {
        echo '<img src="'.sx_ROOT_HOST.'/images/'.$strPhoto.'" style="width: 40%; float: right">';
    }
    echo $strMainText;
 
    $memoNotes = $rs["Notes"];
    if (!empty($memoNotes)) {
        echo "<hr>".$memoNotes."<hr>";
    }
 
    $strMedia = $rs["FirstPageMediaURL"];
    if (!empty($strMedia)) {
        $strMediaNotes = $rs["FirstPageMediaNotes"];
        get_Images_To_Print($strMedia, $strMediaNotes);
    }
    
    $strMedia = $rs["TopMediaURL"];
    if (!empty($strMedia)) {
        $strMediaNotes = $rs["TopMediaNotes"];
        get_Images_To_Print($strMedia, $strMediaNotes);
    }
    
    $strMedia = $rs["RightMediaURL"];
    if (!empty($strMedia)) {
        $strMediaNotes = $rs["RightMediaNotes"];
        get_Images_To_Print($strMedia, $strMediaNotes);
    }
    $rs = null;

    if ($radio_UseTextComments && $radioAllowTextComments) {
        $sql = "SELECT * FROM text_comments 
		WHERE TextID = ? AND Visible = True
		ORDER BY InsertDate ASC ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$intTextID]);
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (is_array($rs)) {
            echo "<hr>";
			$x = 1;
            $iRows = count($rs);
            for ($r = 0; $r < $iRows; $r++) { ?>
		<h3><?=lngForumResponse." ".$x.": ".$rs[$r]["Title"]?></h3>
		<h4><?=$rs[$r]["FirstName"]." ".$rs[$r]["LastName"].", ".$rs[$r]["InsertDate"]?></h4>
				<?php
		        echo $rs[$r]["MainText"];
		        $x=$x+1;
    		}
            $rs = null;
        }
    } ?>
	<hr>
	<p style="text-align: center;">
		<?=lngPrintedDate?>: <?=Date("Y-m-d")?><br>
		<?=lngFromWebPage?>: <a href="<?=sx_HOST?>"><?= str_SiteTitle?></a><br>
		<?=sx_LOCATION?>
	</p>

	<?php
    if ($strExport == "") {?>
	</div>
	<?php } ?>
</body>
</html>
	<?php
    if ($strExport == "print") {?>
<script>
	window.print();
</script>
	<?php
    }
}?>
