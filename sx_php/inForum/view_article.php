<?php
if (intval($intForumID) == 0 || intval($intArticleID) == 0) {
	header("Location: forum.php");
	exit();
}

$strDisplay = "block";
$strCommentClass = "comment_hide";
if (intval($intAnchor) > 0) {
	$strDisplay = "none";
	$strCommentClass = "comment_show";
}

$radioTemp = False;
$sql = "SELECT FirstName, LastName, InsertDate, Title, TextBody 
	FROM forum_articles 
	WHERE InsertID = ?
		AND Hidden = False ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intArticleID]);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
	$radioTemp = true;
	$strFirstName = $rs["FirstName"];
	$strLastName = $rs["LastName"];
	$dataInsertDate = $rs["InsertDate"];
	$s_Title = $rs["Title"];
	$memoTextBody = $rs["TextBody"];
}
$rs = null;

if ($radioTemp === false) {
	header("Location: forum.php");
	exit;
} else { ?>
	<section>
		<h1 class="head"><span><?= LNG_Forum_Theme . " " . $intForumID ?>:
				<a href="forum.php?forumID=<?= $intForumID ?>"><?= $strForumTheme ?></a></span></h1>

		<div class="print align_right">
			<?php
			getTextResizer();
			getTextPrinter("sx_PrintPage.php?articleID=" . $intArticleID, "articleID" . $intArticleID);
			?>
		</div>

		<div class="comment_header comment_even">
			<span title="<?= lngViewHideResponses ?>" class="jqCommentsToggle <?= $strCommentClass ?>"></span>
			<h5><?= lngInitialArticle ?>:</h5>
			<h2><?= $s_Title ?></h2>
			<h5><?= $strFirstName . " " . $strLastName . ", " . $dataInsertDate ?></h5>
		</div>
		<div class="comment_content" style="display: <?= $strDisplay ?>">
			<div class="text_resizeable">
				<div class="text_max_width">
					<?= $memoTextBody ?>
				</div>
			</div>
		</div>

		<?php
		$sql = "SELECT InsertID, FirstName, LastName, InsertDate, Title, TextBody 
		FROM forum_articles 
		WHERE ResponseID = ?
			AND Hidden = False 
		ORDER BY InsertID DESC ";
		$query = $conn->prepare($sql);
		$query->execute([$intArticleID]);
		$rs = $query->fetchAll(PDO::FETCH_NUM);
		if ($rs) {
			$iRows = count($rs);
			$x = $iRows; ?>
			<div class="comments">
				<div class="bar">
					<div class="flex_between">
						<h4><?= lngForumResponses ?>: <?= $iRows ?></h4>
						<button class="button-grey button-gradient" id="jqSortComments"><?= lngChangeOrder ?></button>
						<?php
						if ($radioLoginToParticipate == false || $radio___ForumMemberIsActive) {
							if ($radioShowAsActual) { ?>
								<button class="button-grey button-gradient" id="jqAddComments"><?= LNG_Comments_Add ?></button>
						<?php
							}
						} ?>
					</div>
				</div>
				<ul id="jqSortComments_Target">
					<?php
					for ($r = 0; $r < $iRows; $r++) {
						$intInsertID = $rs[$r][0];
						$strFirstName = $rs[$r][1];
						$strLastName = $rs[$r][2];
						$dateInsertDate = $rs[$r][3];
						$strTitle = $rs[$r][4];
						$memoTextBody  = $rs[$r][5];

						if (($x % 2) == 0) {
							$bg = "comment_even";
						} else {
							$bg = "comment_odd";
						}
						$strDisplay = "none";
						$strCommentClass = "comment_show";
						if (intval($intAnchor) == intval($intInsertID)) {
							$strDisplay = "block";
							$strCommentClass = "comment_hide";
						} ?>
						<li>
							<div id="<?= $intInsertID ?>" class="<?= $bg ?> comment_header">
								<span title="<?= lngViewHideResponses ?>" class="jqCommentsToggle <?= $strCommentClass ?>"></span>
								<h5><?= lngForumResponse ?> <?= $x ?>:</h5>
								<h2><?= $strTitle ?></h2>
								<h5><?= $strFirstName . " " . $strLastName . ", " . $dateInsertDate ?></h5>
							</div>
							<div class="comment_content <?= $bg ?>" style="display: <?= $strDisplay ?>">
								<div class="text_resizeable">
									<div class="text_max_width">
										<?= $memoTextBody ?>
									</div>
								</div>
							</div>
						</li>
					<?php
						$x--;
					}
					$query = null;
					$rs = null;
					?>
				</ul>
			</div>
		<?php
		}

		if ($radioLoginToParticipate && $radio___ForumMemberIsActive === false) { ?>
			<div class="bg_info align_center">
				<p><?= lngForumSubscribeToParticipate ?></p>
			</div>
			<?php
		} else {
			if ($radioShowAsActual) {
				include __DIR__ . "/add_response.php";
			} else { ?>
				<div class="bg_info align_center">
					<p><?= lngCanNotParicipateOldTheme ?></p>
				</div>
		<?php
			}
		} ?>
	</section>
<?php
} ?>