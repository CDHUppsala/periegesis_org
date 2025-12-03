<?php
$arrs = null;
if (!empty($iBookID) && (int) $iBookID > 0) {
	$sql = "SELECT CommentID, Title, insertDate, FirstName, LastName, MainText 
		FROM book_comments 
	WHERE BookID = ?
		AND Visible = True 
	ORDER BY InsertDate DESC ";
	$smtp = $conn->prepare($sql);
	$smtp->execute([$iBookID]);
	$rs = $smtp->fetchAll(PDO::FETCH_ASSOC);
	if (is_array($rs)) {
		$arrs = $rs;
	}
	$smtp = null;
	$rs = null;
}

if (is_array($arrs)) {
	$rows = count($arrs);
?>
	<section class="comments">
		<div class="bar">
			<div class="flex_between">
				<h3><?= lngNumberOfComments . ': ' . $rows ?></h3>
				<?php
				if ($rows >= 2) { ?>
					<button class="button-grey button-gradient" id="jqSortComments"><?= lngChangeOrder ?></button>
				<?php
				} ?>
				<button class="button-grey button-gradient" id="jqAddComments"><?= LNG_Comments_Add ?></button>
			</div>
		</div>
		<ul id="jqSortComments_Target">
			<?php
			$intAnchore = 0;
			if (isset($_GET["anchor"]) && (int) ($_GET["anchor"]) > 0) {
				$intAnchore = (int) $_GET["anchor"];
			}
			$x = intval($rows);
			for ($r = 0; $r < $rows; $r++) {

				$bg = "comment_odd";
				if (($r % 2) == 0) {
					$bg = "comment_even";
				}

				$strDisplay = "none";
				$iCommentID = $arrs[$r]["CommentID"];
				$strCommentClass = "comment_show";
				if ($intAnchore == intval($iCommentID)) {
					$strDisplay = "block";
					$strCommentClass = "comment_hide";
				} ?>
				<li>
					<div class="<?= $bg ?> comment_header" id="<?= $iCommentID ?>">
						<span title="<?= lngViewHideResponses ?>" class="jqCommentsToggle <?= $strCommentClass ?>"></span>
						<h3><?= lngForumResponse ?>: <?= $x ?> - <?= $arrs[$r]["Title"] ?></h3>
						<div class=float_right>
							<?= $arrs[$r]["insertDate"] ?>
						</div>
						<h4><?= ($arrs[$r]["FirstName"] . " " . $arrs[$r]["LastName"]) ?></h4>
					</div>
					<div class="comment_content <?= $bg ?>" style="display: <?= $strDisplay ?>">
						<div class="text">
							<div class="text_max_width">
								<?= $arrs[$r]["MainText"] ?>
							</div>
						</div>
					</div>
				</li>
			<?php
				$x--;
			} ?>
		</ul>
	</section>
<?php }
$arrs = null;
?>