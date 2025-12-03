<?php
$sql = "SELECT WebAddress, linkName, FavoritesImg 
	FROM links 
	WHERE Favorites = True {$str_LanguageAnd}
	ORDER BY sorting DESC";
$stmt = $conn->query($sql);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rs) {
	if (empty($str_FavoritesTitles)) {
		$str_FavoritesTitles = lngFavoriteLinks;
	} ?>
	<h2 class="head"><span><?= $str_FavoritesTitles ?></span></h2>
	<div class="nav_aside">
		<ul>
			<?php
			$iRows = count($rs);
			for ($r = 0; $r < $iRows; $r++) {
				$strlinkName = $rs[$r]["linkName"];
				$strFavoritesImg = $rs[$r]["FavoritesImg"];
				$strURL = $rs[$r]["WebAddress"];
				if (!empty($strURL)) {
					if (strpos(strtolower($strURL), "http://") === false && strpos(strtolower($strURL), "https://") === false) {
						$strURL = "https://$strURL";
					} ?>
					<li>
						<a class="option" target="_blank" href="<?= $strURL ?>"><?= $strlinkName ?></a>
					</li>
			<?php
				}
			} ?>
		</ul>
	</div>
<?php
}
$rs = null;
?>