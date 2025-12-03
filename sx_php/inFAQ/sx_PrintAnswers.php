<body>
	<?php

	function sx_getReport($id)
	{
		$return = null;
		$conn = dbconn();
		$sql = "SELECT
    a.AnswerID,
	s.SubjectName" . str_LangNr . " AS SubjectName,
	s.SubjectNotes" . str_LangNr . " AS SubjectNotes,
    a.SubjectID,
    a.Question,
    a.SubQuestion,
    a.InsertDate,
    a.MediaURL,
    a.MediaPlace,
    a.MediaNotes,
    a.PDFArchiveID,
    a.FilesForDownload,
    a.AnswerText
    FROM faq_subjects AS s 
		INNER JOIN faq_answers as a
		ON s.SubjectID = a.SubjectID
	WHERE a.SubjectID = ? AND a.Hidden = False " . str_LanguageAnd;
		$stmt = $conn->prepare($sql);
		$stmt->execute([$id]);
		$return = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = null;
		return $return;
	}

	$rsAnswers = sx_getReport($int_SubjectID);

	if (empty($strExport)) { ?>
		<div style="margin: 12px 20px;">
				<a href="faq.php"><?= lngHomePage ?></a> |
				<a target="_top" href="sx_PrintPage.php?subjectid=<?= $int_SubjectID ?>&export=print"><?= lngSavePrintInPDF ?></a> |
				<a target="_top" href="sx_PrintPage.php?subjectid=<?= $int_SubjectID ?>&export=word"><?= lngSaveInWord ?></a> |
				<a target="_top" href="sx_PrintPage.php?subjectid=<?= $int_SubjectID ?>&export=html"><?= lngSaveInHTML ?></a>
			<hr>
		<?php
	}
	if (is_array($rsAnswers)) { ?>
			<h1><?= $rsAnswers[0]["SubjectName"] ?></h1>
			<?php
			if (!empty($rsAnswers[0]["SubjectName"])) { ?>
				<h4><?= $rsAnswers[0]["SubjectName"] ?></h4>
			<?php
			}
			echo "<hr>";
			$rows = count($rsAnswers);
			for ($row = 0; $row < $rows; $row++) {
				$int_SubjectID = $rsAnswers[$row]["SubjectID"];
				$strQuestion = $rsAnswers[$row]["Question"];
				$strSubQuestion = $rsAnswers[$row]["SubQuestion"];
				$dateInsertDate = $rsAnswers[$row]["InsertDate"];
				$strMediaURL = $rsAnswers[$row]["MediaURL"];
				$strMediaPlace = $rsAnswers[$row]["MediaPlace"];
				$strMediaNotes = $rsAnswers[$row]["MediaNotes"];
				$strFilesForDownload = $rsAnswers[$row]["FilesForDownload"];
				$memoAnswerText = $rsAnswers[$row]["AnswerText"];
			?>
				<h2><?= $strQuestion ?></h2>
				<?php
				if (!empty($strSubQuestion)) { ?>
					<h3><?= $strSubQuestion ?></h3>
			<?php
				}

				echo $memoAnswerText;
				if (!empty($strFilesForDownload)) {
					sx_getDownloadableFiles($strFilesForDownload);
				}
				if (!empty($strMediaURL)) {
					get_Images_To_Print($strMediaURL, $strMediaNotes);
				}
				echo "<hr>";
			}
			$rsAnswers = null;
			?>
			<p style="text-align: center;">
				<?= lngPrintedDate ?>&nbsp;<?= Date("Y-m-d") ?><br>
				<?= lngFromWebPage ?>&nbsp;<b><?= str_SiteTitle ?></b><br>
				<?= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ?>
			</p>

		<?php
	} else { ?>
			<h3><?= lngTextDoesNotExist ?></h3>
			<?= lngCloseWindowReturnToSite ?>
		<?php
	}

	if (empty($strExport)) { ?>
		</div>
	<?php
	} ?>
</body>

</html>
<?php

if ($strExport == "print") { ?>
	<script>
		window.print();
	</script>
<?php
} ?>