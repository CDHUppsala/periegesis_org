<body>
	<?php

	/**
	 * Following variables are originally defined in sx_Prints.php
	 */

	if (intval($intArticleID) == 0) {
		echo '<h3>' . lngTextDoesNotExist . '</h3>';
		echo lngCloseWindowReturnToSite;
		exit();
	}

	if (empty($strExport)) { ?>
		<div style="margin: 12px 20px;">
			<a href="default.php"><?= lngHomePage ?></a> |
			<a target="_top" href="sx_PrintPage.php?aid=<?= $intArticleID ?>&export=print"><?= lngSavePrintInPDF ?></a> |
			<a target="_top" href="sx_PrintPage.php?aid=<?= $intArticleID ?>&export=word"><?= lngSaveInWord ?></a> |
			<a target="_top" href="sx_PrintPage.php?aid=<?= $intArticleID ?>&export=html"><?= lngSaveInHTML ?></a>
			<hr>
		<?php
	}

	$radioTemp = false;

	$sql = "SELECT Title, SubTitle, ExternalLink,
	AuthorName, InsertDate, ShowDate,
    TopMediaPaths, TopMediaSource, TopDisplayForm, TopMediaNotes,  
	MiddleMediaPaths, MiddleMediaNotes,
    ArticleNotes 
    FROM articles 
	WHERE ArticleID = " . $intArticleID . " AND
		Hidden = False " . str_LanguageAnd;
	$stmt = $conn->query($sql);
	$rs = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt = null;
	if (is_array($rs)) {
		$radioTemp = true;
		$strTitle = $rs["Title"];
		$strSubTitle = $rs["SubTitle"];
		$strExternalLink = $rs["ExternalLink"];
		$strAuthorName = $rs["AuthorName"];
		$dateInsertDate = $rs["InsertDate"];
		$radioShowDate = $rs["ShowDate"];
		$strTopMediaPaths = $rs["TopMediaPaths"];
		$strTopMediaSource = $rs["TopMediaSource"];
		$strTopDisplayForm = $rs["TopDisplayForm"];
		$strTopMediaNotes = $rs["TopMediaNotes"];
		$strMiddleMediaPaths = $rs["MiddleMediaPaths"];
		$strMiddleMediaNotes = $rs["MiddleMediaNotes"];

		$memoArticleNotes = $rs["ArticleNotes"];
	}
	$rs = null;

	$str_ArticleClasses = "";

	if ($radioTemp) {
		$str_Left = '';
		$str_Right = '';
		$strLeft = '';
		$strRight = '';
		if (!empty($strExternalLink)) {
			$strLeft = '<a href="' . $strExternalLink . '" target="_blank">';
			$strRight = '</a>';
		}
		if (empty($strSubTitle)) {
			$str_Left = $strLeft;
			$str_Right = $strRight;
		} ?>

			<h1><?php echo $str_Left . $strTitle . $str_Right ?></h1>
			<?php
			if (!empty($strSubTitle)) { ?>
				<h2><?= $strLeft . $strSubTitle . $strRight ?></h2>
			<?php
			}

			$strTemp = "";
			if (!empty($strAuthorName)) {
				$strTemp = $strAuthorName;
			}
			$sx_radioShowArticleDate = true;
			if (!empty($dateInsertDate) && $radioShowDate && SX_radioShowArticleDate) {
				if (!empty($strTemp)) {
					$strTemp .= ", ";
				}
				$strTemp .= $dateInsertDate;
			}

			if (!empty($strTemp)) { ?>
				<h4><?= $strTemp ?></h4>
			<?php
			}

			$strPhotos = "";
			if (!empty($strTopMediaSource)) {
				$strPhotos = return_Folder_Images($strTopMediaSource);
			} else {
				$strPhotos = $strTopMediaPaths;
			}

			if (!empty($strPhotos)) {
				get_Images_To_Print($strPhotos, $strTopMediaNotes);
			} elseif (!empty($strTopMediaNotes)) {
				echo $strTopMediaNotes;
			}

			if (!empty($strMiddleMediaPaths)) {
				get_Images_To_Print($strMiddleMediaPaths, $strMiddleMediaNotes);
			} elseif (!empty($strMiddleMediaNotes)) {
				echo $strMiddleMediaNotes;
			}

			if (!empty($memoArticleNotes)) {
				echo $memoArticleNotes;
			}

			if (empty($strExport)) { ?>
		</div>
	<?php
			} ?>
</body>

</html>
<?php
	}
	if ($strExport == "print") { ?>
	<script>
		window.print();
	</script>
<?php
	} ?>