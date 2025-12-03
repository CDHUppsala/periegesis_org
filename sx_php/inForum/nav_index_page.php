<?php
/**
 * For the first, index page of the site
 */
if ($radio_UseForum == True) {
	$sql = "SELECT a.InsertID, a.ForumID AS intForumID, 
			a.ResponseID, a.Title, a.FirstName, a.LastName,
			f.ForumID
		FROM forum_articles AS a
			INNER JOIN forum AS f
			ON a.ForumID = f.ForumID
		WHERE f.Publish = True {$str_LanguageAnd}
	ORDER BY a.InsertDate DESC, a.InsertID DESC LIMIT 6";
	$query = $conn->prepare($sql);
	$query->execute();
	$rs = $query->fetchAll(PDO::FETCH_ASSOC);
	if ($rs) { ?>
		<section class="jqNavMainToBeCloned">
			<h2 class="head <?= $strSlide ?> jqToggleNextRight"><span><?= lngArticlesInForum ?></span></h2>
			<nav class="nav_aside" style="display: <?= $strDisplay ?>">
				<ul class="listContent">
					<?php
					foreach ($rs as $row) {
						if ($row["ResponseID"] > 0) {
							$strInsertID = $row["ResponseID"] . "#" . $row["InsertID"];
						} else {
							$strInsertID = $row["InsertID"];
						} ?>
						<li>
							<a href="forum.php?forumID=<?php echo $row["intForumID"] ?>&articleID=<?php echo $strInsertID ?>">
								<?php echo $row["Title"] ?>
								<span><?php echo $row["FirstName"] .' '. $row["LastName"] ?></span></a>
						</li>
					<?php
					}
					?>
				</ul>
			</nav>
		</section>
<?php
	}
	$rs = null;
}
?>