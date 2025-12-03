<section>
	<?php
	/*
    $radio_UseLinks = $rs["UseLinks"];
    $str_LinksTitle = $rs["LinksTitle"];
    $radio_ShowFavorites = $rs["ShowFavorites"];
    $str_FavoritesTitles = $rs["FavoritesTitles"];

	*/

	// Ensure favorites title is set
	if (empty($str_FavoritesTitles) && !empty($radio_ShowFavorites)) {
		$str_FavoritesTitles = lngFavoriteLinks;
	}

	// Fetch categories
	$radio_UseCategories = false;
	$sql = "SELECT CategoryID, CategoryName{$str_LangNr} AS CategoryName
        FROM link_categories
        WHERE Hidden = FALSE
        ORDER BY Sorting DESC, CategoryName{$str_LangNr} ASC";
	$rs_Cats = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

	if (!empty($rs_Cats)) {
		$radio_UseCategories = true;
	}

	// Get linkID from query string
	$intLinkID = 0;
	if ($radio_UseCategories && isset($_GET['linkID'])) {
		$intLinkID = filter_var($_GET['linkID'], FILTER_VALIDATE_INT) ?: 0;
	}

	// Render output
	if ($radio_UseCategories) {
		if ($intLinkID === 0) {
			echo '<h1 class="head"><span>' . lngLinksPerCategory . '</span></h1>';
			echo '<p class="bg_grey">' . lngLinksInform . '</p>';

			if (!empty($radio_ShowFavorites)) {
				include __DIR__ . '/link_favorites.php';
			}
		} else {
			$sql = "SELECT links.WebAddress, links.LinkName, links.FavoritesImg,
                       link_categories.CategoryName{$str_LangNr} AS CategoryName
                FROM links
                INNER JOIN link_categories ON links.CategoryID = link_categories.CategoryID
                WHERE links.CategoryID = ? $str_LanguageAnd
                ORDER BY links.Sorting DESC, links.LinkName ASC";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$intLinkID]);
			$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if (!empty($rs)) {
				echo '<h2 class="head"><span>' . htmlspecialchars($rs[0]['CategoryName']) . '</span></h2>';
				echo '<div class="nav_aside"><ul>';

				foreach ($rs as $row) {
					$strURL = $row['WebAddress'] ?? '';
					if (!empty($strURL)) {
						$strLinkName = $row['LinkName'] ?? '';
						$strFavoritesImg = $row['FavoritesImg'] ?? '';

						if (!preg_match('/^https?:\/\//i', $strURL)) {
							$strURL = 'http://' . $strURL;
						}

						echo '<li>';
						if (!empty($strFavoritesImg)) {
							echo '<a title="' . htmlspecialchars($strLinkName) . '" target="_blank" href="' . htmlspecialchars($strURL) . '">';
							echo '<img alt="' . htmlspecialchars($strLinkName) . '" src="../images/' . htmlspecialchars($strFavoritesImg) . '">';
							echo '</a>';
						} else {
							echo '<a target="_blank" href="' . htmlspecialchars($strURL) . '">' . htmlspecialchars($strLinkName) . '</a>';
						}
						echo '</li>';
					}
				}

				echo '</ul></div>';
			}
		}
	} else {
		echo '<h1 class="head"><span>' . htmlspecialchars($str_LinksTitle) . '</span></h1>';

		$sql = "SELECT WebAddress, LinkName, FavoritesImg
            FROM links
            WHERE Hidden = FALSE
            ORDER BY Sorting DESC, LinkName ASC";
		$rs = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

		if (!empty($rs)) {
			echo '<div class="nav_aside"><ul>';

			foreach ($rs as $row) {
				$strURL = $row['WebAddress'] ?? '';
				$strLinkName = $row['LinkName'] ?? '';
				$strFavoritesImg = $row['FavoritesImg'] ?? '';

				if (!preg_match('/^https?:\/\//i', $strURL)) {
					$strURL = 'http://' . $strURL;
				}

				echo '<li>';
				if (!empty($strFavoritesImg)) {
					echo '<a title="' . htmlspecialchars($strLinkName) . '" target="_blank" href="' . htmlspecialchars($strURL) . '">';
					echo '<img border="0" alt="' . htmlspecialchars($strLinkName) . '" src="../images/' . htmlspecialchars($strFavoritesImg) . '">';
					echo '</a>';
				} else {
					echo '<a target="_blank" href="' . htmlspecialchars($strURL) . '">' . htmlspecialchars($strLinkName) . '</a>';
				}
				echo '</li>';
			}

			echo '</ul></div>';
		}
	} ?>
</section>