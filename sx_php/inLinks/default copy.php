<section>
	<?php
	/*
    $radio_UseLinks = $rs["UseLinks"];
    $str_LinksTitle = $rs["LinksTitle"];
    $radio_ShowFavorites = $rs["ShowFavorites"];
    $str_FavoritesTitles = $rs["FavoritesTitles"];

	*/

	if (empty($str_FavoritesTitles && $radio_ShowFavorites)) {
		$str_FavoritesTitles = lngFavoriteLinks;
	}

	$radio_UseCategories = false;

	$sql = "SELECT CategoryID, CategoryName{$str_LangNr} AS CategoryName 
	FROM link_categories
	WHERE Hidden = False
	ORDER BY Sorting DESC, CategoryName{$str_LangNr} ASC ";
	$stmt = $conn->query($sql);
	$rs_Cats = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($rs_Cats) {
		$radio_UseCategories = true;
	} else {
		$rs_Cats = null;
	}

	$intLinkID = 0;
	if ($radio_UseCategories) {
		if (isset($_GET["linkID"])) {
			$intLinkID = $_GET["linkID"];
			if (return_Filter_Integer($intLinkID) == 0) {
				$intLinkID = 0;
			}
		}
	}

	if ($radio_UseCategories) {
		if (intval($intLinkID) === 0) { ?>
			<h1 class="head"><span><?= lngLinksPerCategory ?></span></h1>
			<p class="bg_grey"><?= lngLinksInform ?></p>
			<?php
			if ($radio_ShowFavorites) {
				include __DIR__ . "/link_favorites.php";
			}
		} else {
			$sql = "SELECT links.WebAddress, 
			links.LinkName, links.FavoritesImg, 
			link_categories.CategoryName$str_LangNr AS CategoryName 
			FROM links 
				INNER JOIN link_categories 
				ON links.CategoryID = link_categories.CategoryID 
			WHERE links.CategoryID = ? $str_LanguageAnd
			ORDER BY links.Sorting DESC, links.LinkName ASC";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$intLinkID]);
			$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($rs) { ?>
				<h2 class="head"><span><?= $rs[0]["CategoryName"] ?></span></h2>
				<div class="nav_aside">
					<ul>
						<?php
						$iRows = count($rs);
						for ($r = 0; $r < $iRows; $r++) {
							$strURL = $rs[$r]["WebAddress"] ?? '';
							if (!empty($strURL)) {
								$strLinkName = $rs[$r]["LinkName"] ?? '';
								$strFavoritesImg = $rs[$r]["FavoritesImg"];
								if (strpos(strtolower($strURL), "http://") === false && strpos(strtolower($strURL), "https://") === false) {
									$strURL = "http://" . $strURL;
								}
								if (!empty($strFavoritesImg)) { ?>
									<li><a title="<?= $strLinkName ?>" target="_blank" href="<?= $strURL ?>"><img alt="<?= $strLinkName ?>" src="../images/<?= $strFavoritesImg ?>"></a></li>
								<?php } else { ?>
									<li><a target="_blank" href="<?= $strURL ?>"><?= $strLinkName ?></a></li>
						<?php
								}
							}
						} ?>
					</ul>
				</div>
		<?php }
			$rs = null;
		}
	} else {  ?>
		<h1 class="head"><span><?= $str_LinksTitle ?></span></h1>
		<?php
		$sql = "SELECT WebAddress, LinkName, FavoritesImg 
	    	FROM links 
	    	WHERE Hidden = False 
			ORDER BY Sorting DESC, LinkName ASC";
		$stmt = $conn->query($sql);
		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if ($rs) { ?>
			<div class="nav_aside">
				<ul>
					<?php
					$iRows = count($rs);
					for ($r = 0; $r < $iRows; $r++) {
						$strURL = $rs[$r]["WebAddress"] ?? '';
						$strLinkName = $rs[$r]["LinkName"] ?? '';
						$strFavoritesImg = $rs[$r]["FavoritesImg"];
						if (strpos(strtolower($strURL), "http://") === false && strpos(strtolower($strURL), "https://") === false) {
							$strURL = "http://$strURL";
						}
						if (!empty($strFavoritesImg)) { ?>
							<li>
								<a title="<?= $strLinkName ?>" target="_blank" href="<?= $strURL ?>">
									<img border="0" alt="<?= $strLinkName ?>" src="../images/<?= $strFavoritesImg ?>">
								</a>
							</li>
						<?php
						} else { ?>
							<li><a target="_blank" href="<?= $strURL ?>"><?= $strLinkName ?></a></li>
					<?php }
					} ?>
				</ul>
			</div>
	<?php }
		$rs = null;
	} ?>
</section>