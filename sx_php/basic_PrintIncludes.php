<?php

/**
 * Calls ALL functions from basic_PrintFunctions.php
 * The class .print is used to print any resourse
 * - The class .print_fixed is anly for text resources, 
 *   to move the position of the print container.
 */

$sx_GetAllLinks = true;
if ($sx_GetAllLinks) { ?>
	<aside class="print print_fixed">
		<?php
		getTextResizer();
		if (intval($int_TextID) > 0) {
			/**
			 * To print text of any language that replaces Event information
			 * See sx_config.php
			 */
			if (isset($int__EventID) && $int__EventID > 0) {
				getTextPrinter("sx_PrintPage.php?tid=" . $int_TextID . "&eid=" . $int__EventID, $int_TextID);
			} else {
				getTextPrinter("sx_PrintPage.php?tid=" . $int_TextID, $int_TextID);
			}
			getLocalEmailSender("texts.php?tid=" . $int_TextID, $strTitle, $strSubTitle, $strAuthor);
		}
		if (isset($iAboutID) && intval($iAboutID) > 0) {
			getTextPrinter("sx_PrintPage.php?aboutid=" . $iAboutID, $iAboutID);
			getLocalEmailSender("about.php?aboutid=" . $iAboutID, $strTitle, $strSubTitle, "");
		}
		if (isset($int_ArticleID) && intval($int_ArticleID) > 0) {
			getTextPrinter("sx_PrintPage.php?aid=" . $int_ArticleID, $int_ArticleID);
			getLocalEmailSender("articles.php?aid=" . $int_ArticleID, $strTitle, $strSubTitle, "");
		}
		if (isset($iCourseID) && intval($iCourseID) > 0) {
			getTextPrinter("sx_PrintPage.php?courseid=" . $iCourseID, $iCourseID);
			getLocalEmailSender("courses.php?courseid=" . $iCourseID, $strTitle, $strSubTitle, "");
		}

		if (isset($int_ReportID) && intval($int_ReportID) > 0) {
			getTextPrinter("sx_PrintPage.php?reportid=" . $int_ReportID, $int_ReportID);
			getLocalEmailSender("reports.php?reportid=" . $int_ReportID, $strTitle, $strSubTitle, "");
		}

		if (isset($int_SubjectID) && intval($int_SubjectID) > 0) {
			getTextPrinter("sx_PrintPage.php?subjectid=" . $int_SubjectID, $int_SubjectID);
			getLocalEmailSender("faq.php?subjectid=" . $int_SubjectID, $strQuestion, $strSubQuestion, "");
		}
		// Email sender from server is not used
		if (isset($intProductID) > 0 && intval($intProductID) > 0) {
			getTextPrinter("sx_PrintPage.php?pid=" . $intProductID, $intProductID);
			getEmailSender("sx_EmailFriend.php?pid=" . $intProductID, $intProductID);
		}

		/**
		 * The variable $str_LinksToFiles can be echoed at the end of text pages
		 */
		$str_LinksToFiles = "";
		$strTemp = "";
		if (!empty($strFilesForDownload)) {
			$strTemp = $strFilesForDownload;
		}
		if (!empty($strTemp)) {
			if (strpos($strTemp, ";") == 0) {
				$strTemp .= ";";
			}
			$arrTemp = explode(";", $strTemp);
			for ($f = 0; $f < count($arrTemp); $f++) {
				$sLink = trim($arrTemp[$f]);
				$sTitle = "";
				if (!empty($sLink)) {
					if (check_External_Link_Tag($sLink)) {
						getExternalLink($sLink, '');
					} else {
						$iPos = strpos($sLink, "/");
						$sTitle = substr($sLink, $iPos, strrpos($sLink, ".") - $iPos);
						$sTitle = str_replace("_", " ", str_replace("-", " ", $sTitle));
						$extention = sx_getFileType($sLink);
						if ($extention == "pdf") {
							get_LinkToPDFFile($sLink, $sTitle);
						} elseif ($extention == "csv") {
							get_LinkToCSVFile($sLink, $sTitle);
						} elseif ($extention == "json") {
							get_LinkToJSONFile($sLink, $sTitle);
						} elseif ($extention == "xml") {
							get_LinkToXMLFile($sLink, $sTitle);
						} elseif (str_contains($extention,"htm")) {
							get_LinkToHTMLFile($sLink, $sTitle);
						} elseif (str_contains($extention,"doc")) {
							get_LinkToDOCFile($sLink, $sTitle);
						} elseif (str_contains($extention,"xls")) {
							get_LinkToXLSFile($sLink, $sTitle);
						} else {
							get_LinkToDownload($sLink, $sTitle);
						}
						$str_LinksToFiles .= '<p><b>' . lngDownloadFile . ':</b> <a href="/imgPDF/' . $sLink . '" target="_blank">' . $sTitle . "</a></p>";
					}
				}
			}
		}

		if (isset($intPDFArchiveID) && intval($intPDFArchiveID)  > 0) {
			getLinkToPDFGallery($intPDFArchiveID);
		}
		/* Next variable and function is not used */
		if (!empty($strFlipBookURL)) {
			getLinkToFlipBookURL($strFlipBookURL);
		}
		if (isset($intPhotoGalleryID) && intval($intPhotoGalleryID) > 0) {
			getPhotoGallery($intPhotoGalleryID);
		}
		if (isset($intMediaArchiveID) && intval($intMediaArchiveID) > 0) {
			getMediaGallery($intMediaArchiveID);
		}
		if (!empty($radioMediaLinks)) {
			get_Social_MediaLinks();
		} ?>
	</aside>
<?php
} ?>