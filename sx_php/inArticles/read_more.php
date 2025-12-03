<?php

/**
 * Returns the total number of records for a request
 * @param string $sql : the sql statement
 * @return int : Total number of records
 */
function return_TotalArticles($sql)
{
	$conn = dbconn();
	$arrBy = explode("ORDER BY", $sql);
	$arrFrom = explode("FROM ", $arrBy[0]);
	$strSql = "SELECT count(*) FROM " . $arrFrom[1];
	$stmt = $conn->query($strSql);
	$iCount = $stmt->fetchColumn();
	if ($iCount) {
		return $iCount;
	} else {
		return 0;
	}
}

/**
 * Highlight first 2 articles
 * Check if the first row of article grids will contain max 2 columns
 * Meaningfull only in index page, where X articles are shown withought pagination
 */
$radioFirstRowTwoColumns = false;
 if (defined('SX_FirstRowTwoColumns') && SX_FirstRowTwoColumns && str_contains(sx_ROOT_HOST_PATH, '/index.php')) {
	$radioFirstRowTwoColumns = true;
}

/**
 * The variable "page" can be requested 
 * both as _GET and _POST variable
 */
$i_RequestPage = 0;
if (!empty(return_Get_or_Post_Request("page"))) {
	$i_RequestPage = return_Get_or_Post_Request("page");
}
if (return_Filter_Integer($i_RequestPage) == 0) {
	$i_RequestPage = 0;
}

/**
 * Following variables are originally defined in config_articles.php
 * This part shows all articles:
 * 	1.	directly for a requested Category ID or Group ID
 * 	2.	inderectly, for a Category ID or Group ID that includes a requested Article ID
 * So, when both Category and Group ID are equal to 0, the requested Article ID does not exist
 * 	-	In that case, redirect (if you don't want to show all your articles!)
 */

if (!isset($int_ArticleGroupID) || intval($int_ArticleGroupID) == 0) {
	$int_ArticleGroupID = 0;
}
if (!isset($int_ArticleCategoryID) || intval($int_ArticleCategoryID) == 0) {
	$int_ArticleCategoryID = 0;
}

if (!isset($int_ArticleID) || intval($int_ArticleID) == 0) {
	$int_ArticleID = 0;
}

$sArticleClasses = "";
$strPaginationPath = "articles.php?";

if (intval($int_ArticleGroupID) > 0) {
	$sArticleClasses = '<a href="articles.php?agid=' . $int_ArticleGroupID . '">' . $str_ArticleGroupName . '</a>';
	$strPaginationPath .= 'agid=' . $int_ArticleGroupID . '&';
	if (intval($int_ArticleCategoryID) > 0) {
		$sArticleClasses .= ' / <a href="articles.php?acid=' . $int_ArticleCategoryID . '">' . $str_ArticleCategoryName . '</a>';
		$strPaginationPath .= 'acid=' . $int_ArticleCategoryID . '&';
	}
}
/**
 * The variable $str_ArticlesTitle is defined in apps configuration and comes from sx_config.php
 * Should/will never be used, as the page is redirected when $sArticleClasses is empty
 * ... if you don't want to show all yoyr articles
 */
if (empty($sArticleClasses) && !empty($str_ArticlesTitle)) {
	$sArticleClasses = $str_RecentTextsTitle;
}

$prefixArchives = '';
$prefixRecent = '';
if (defined('SX_SetPrefixInReadMoreArtticles') && SX_SetPrefixInReadMoreArtticles) {
	$prefixArchives = '<i>' . lngArchives . ':</i> ';
	$prefixRecent = '<i>' . lngRecent . ':</i> ';
}

if (intval($int_ArticleID) > 0) {
	$sArticleClasses = '<i>' . lngViewMoreFrom . ':</i> ' . $sArticleClasses;
} elseif (intval($int_ArticleGroupID) > 0 || intval($int_ArticleCategoryID) > 0) {
	if (intval($i_RequestPage) > 1) {
		$sArticleClasses = $prefixArchives . $sArticleClasses;
	} else {
		$sArticleClasses = $prefixRecent . $sArticleClasses;
	}
}

$iPageSize = 0;
if (intval($i_MaxFirstPageArticles) > 0 and str_contains(sx_HOST_PATH, '/index.php')) {
	$iPageSize = (int) $i_MaxFirstPageArticles;
} elseif (intval($i_MaxArticlesPerPage) > 0 and str_contains(sx_HOST_PATH, '/index.php') == false) {
	$iPageSize = (int) $i_MaxArticlesPerPage;
}
if ($iPageSize == 0) {
	$iPageSize = SX_pageSizeForArticles;
}


$strWhere = "";
$strOrderBy = " ORDER BY a.InsertDate DESC,a.ArticleID DESC ";
if (intval($int_ArticleCategoryID) > 0) {
	$strWhere = " AND a.ArticleCategoryID = " . $int_ArticleCategoryID;
	$strOrderBy = " ORDER BY a.Sorting DESC, a.InsertDate DESC, a.ArticleID DESC ";
} elseif (intval($int_ArticleGroupID) > 0) {
	$strWhere = " AND a.ArticleGroupID = " . $int_ArticleGroupID;
} else {
	//header("location: index.php");
	//exit;
}

$strIngressFields = "";
if (SX_IncludeIntroductionInArticlePaging) {
	$strIngressFields = ', a.TopMediaNotes, a.MiddleMediaNotes, a.ArticleNotes';
}

$sql = "SELECT a.ArticleID, a.ArticleCategoryID,
		c.CategoryName,
		a.Title, a.SubTitle, a.AuthorName, a.InsertDate, a.ShowDate,
		a.TopMediaPaths, a.TopMediaSource, a.MiddleMediaPaths,
		a.IngressFromField " . $strIngressFields . "
    FROM articles AS a
    LEFT JOIN article_categories AS c
        ON a.ArticleCategoryID = c.ArticleCategoryID
	WHERE a.Hidden = False 
	AND (c.Hidden = False OR c.Hidden IS NULL) 
	" . str_LanguageAnd . $strWhere . $strOrderBy;
$intArticlesCount = return_TotalArticles($sql);
$iPageCount = ceil($intArticlesCount / $iPageSize);

if ($iPageCount < $intArticlesCount / $iPageSize) {
	$iPageCount = $iPageCount + 1;
}

$iCurrentPage = 0;
if ($iPageCount > 1) {

	if (intval($i_RequestPage) > 0) {
		$iCurrentPage = intval($i_RequestPage);
	}
	if ($iCurrentPage < 1) {
		$iCurrentPage = 1;
	}

	if ($iCurrentPage > $iPageCount) {
		$iCurrentPage = $iPageCount;
	}

	$iStartRecord = ($iPageSize * $iCurrentPage) - $iPageSize;
	$sql .= " LIMIT " . $iStartRecord . "," . $iPageSize;
}

$stmt = $conn->query($sql);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null;

if (is_array($rs)) {
	$str_jqNavClass = "";
	if ($int_ArticleID > 0) {
		$str_jqNavClass = " jqNavSideToBeCloned";
	} ?>
	<section class="archives_articles<?php echo $str_jqNavClass ?>">
		<h1 class="head"><span><?= $sArticleClasses ?></span></h1>
		<div class="grid_cards">
			<?php
			$iRows = count($rs);
			for ($iRow = 0; $iRow < $iRows; $iRow++) {
				$iArticleID = $rs[$iRow]["ArticleID"];
				$iArticleCategoryID = (int) $rs[$iRow]["ArticleCategoryID"];
				$sArticleCategoryName = $rs[$iRow]["CategoryName"];
				$strTitle = $rs[$iRow]["Title"];
				$strSubTitle = $rs[$iRow]["SubTitle"];
				$strAuthorName = $rs[$iRow]["AuthorName"];
				$dateInsertDate = $rs[$iRow]["InsertDate"];
				$radioShowDate = $rs[$iRow]["ShowDate"];
				$strTopMediaPaths = $rs[$iRow]["TopMediaPaths"];
				$strMiddleMediaPaths = $rs[$iRow]["MiddleMediaPaths"];
				$strTopMediaSource = $rs[$iRow]["TopMediaSource"];
				$strIngressFromField = $rs[$iRow]["IngressFromField"];


				$memoIngress = "";
				if (
					SX_IncludeIntroductionInArticlePaging && ($strIngressFromField != 'None')
				) {
					$radioDefault = false;
					if (empty($strIngressFromField) || $strIngressFromField == 'Default') {
						$radioDefault = true;
					}
					$strTopMediaNotes = $rs[$iRow]["TopMediaNotes"];
					$strMiddleMediaNotes = $rs[$iRow]["MiddleMediaNotes"];
					$memoArticleNotes = $rs[$iRow]["ArticleNotes"];
					if (!empty($memoArticleNotes) && ($strIngressFromField == 'Article Notes' || $radioDefault)) {
						$memoIngress = return_Left_Part_FromText($memoArticleNotes, 200);
					} elseif (!empty($strTopMediaNotes) && ($strIngressFromField == 'Top Notes' || $radioDefault)) {
						$memoIngress = return_Left_Part_FromText($strTopMediaNotes, 200);
					} elseif (!empty($strMiddleMediaNotes) && ($strIngressFromField == 'Middle Notes' || $radioDefault)) {
						$memoIngress = return_Left_Part_FromText($strMiddleMediaNotes, 200);
					}
				}
			?>
				<figure>
					<?php
					$strUsedMedia = "";
					if (SX_IncludeImagesInArticlePaging) {
						$strPhotos = "";
						if (!empty($strTopMediaSource)) {
							$strPhotos = return_Folder_Images($strTopMediaSource);
						} else {
							$strPhotos = $strTopMediaPaths;
						}
						$radioFirstMedia = false;
						if (!empty($strPhotos)) {
							if (strpos($strPhotos, ";") > 0) {
								$arrPhotos = explode(';', $strPhotos);
								sort($arrPhotos);
								$strPhotos = $arrPhotos[0];
								$arrPhotos = null;
							}
							// WE have now only one media
							$extension = substr($strPhotos, strrpos($strPhotos, '.') + 1);
							if (sx_is_extension_an_image($extension)) {
								$strUsedMedia = $strPhotos;
								$radioFirstMedia = true;
							} elseif (SX_IncludeMediaInArticlePaging && strtolower($extension) !== 'pdf') {
								$strObjectValue = return_Media_Type_URL($strPhotos);
								if (!empty($strObjectValue)) {
									get_Media_Type_Player($strPhotos, $strObjectValue);
									$radioFirstMedia = true;
								}
							}
						}
						if ($radioFirstMedia === false) {
							if (!empty($strMiddleMediaPaths)) {
								if (strpos($strMiddleMediaPaths, ";") > 0) {
									$arrMiddleMediaPaths = explode(';', $strMiddleMediaPaths);
									sort($arrMiddleMediaPaths);
									$strMiddleMediaPaths = trim($arrMiddleMediaPaths[0]);
									$arrMiddleMediaPaths = null;
								}
								// WE have now only one media
								$extension = substr($strMiddleMediaPaths, strrpos($strMiddleMediaPaths, '.') + 1);
								if (sx_is_extension_an_image($strMiddleMediaPaths)) {
									$strUsedMedia = $strMiddleMediaPaths;
								} elseif (SX_IncludeMediaInArticlePaging && strtolower($extension) !== 'pdf') {
									$strObjectValue = return_Media_Type_URL($strMiddleMediaPaths);
									if (!empty($strObjectValue)) {
										get_Media_Type_Player($strMiddleMediaPaths, $strObjectValue);
									} else {
										$strUsedMedia = STR_ReplaceListImage;
									}
								} else {
									$strUsedMedia = STR_ReplaceListImage;
								}
							} else {
								$strUsedMedia = STR_ReplaceListImage;
							}
						}
						if (!empty($strUsedMedia)) { ?>
							<div class="img_wrapper">
								<a href="articles.php?aid=<?= $iArticleID ?>&page=<?= $iCurrentPage ?>"><img alt=" <?= $strTitle ?>" src="../images/<?= $strUsedMedia ?>"></a>
							</div>
					<?php
						}
					} ?>
					<figcaption>
						<?php
						echo '<h4><a href="articles.php?aid=' . $iArticleID . '&page=' . $iCurrentPage . '">' . $strTitle . '</a></h4>';
						if (!empty($strSubTitle)) {
							echo '<h5>' . $strSubTitle . '</h5>';
						}
						$strTemp = "";
						if (!empty($strAuthorName)) {
							$strTemp = $strAuthorName;
						}
						if (!empty($dateInsertDate) && $radioShowDate && SX_radioShowArticleDate) {
							if (!empty($strTemp)) {
								$strTemp .= ", ";
							}
							$strTemp .= $dateInsertDate;
						}

						if (!empty($strTemp)) {
							echo '<p><em>' . $strTemp . '</em></p>';
						}
						if (!empty($memoIngress)) {
							echo '<p class="text_small">';
							echo $memoIngress . '...';
							echo '</p>';
						}
						if ($int_ArticleCategoryID == 0 && $iArticleCategoryID > 0) {
							echo '<p class="text_xsmall">';
							echo '<strong>' . lngCategory . '</strong>: <a href="articles.php?acid=' . $iArticleCategoryID . '">' . $sArticleCategoryName . '</a>';
							echo '</p>';
						} ?>
					</figcaption>
				</figure>
			<?php
				if ($radioFirstRowTwoColumns && $iRow === 1) {
					echo '</div>';
					echo '<div class="grid_cards">';
				}
			}
			?>
		</div>
	</section>
<?php
}
$rs = null;

/**
 * ==================================================================
 * PAGINATION: 
 * - Use ajax only for pagination of texts published in the first page
 * - Not with Archive Navigation 
 * ==================================================================
 */

$radioUsePagination = true;
if (SX_IncludeArticlePagingInFirstPage == false && str_contains(sx_ROOT_HOST_PATH, '/index.php')) {
	$radioUsePagination = false;
}

if ($radioUsePagination && intval($iPageCount) > 1) { ?>
	<section class="<?php echo $str_jqNavClass ?>">
		<div class="page_navigation">
			<?php
			sx_getPageNavigation_ByForm($strPaginationPath, $iCurrentPage, $iPageCount);
			sx_getPageNavigation_ByArrows($strPaginationPath, $iCurrentPage, $iPageCount);
			?>
		</div>
		<div class="text_xxsmall align_center"><?php echo LNG__TotalRecords . ': ' . $intArticlesCount ?></div>
	</section>
<?php
} ?>