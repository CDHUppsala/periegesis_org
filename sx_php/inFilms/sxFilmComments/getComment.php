<?php
function sx_getComents($id, $nr)
{
	$conn = dbconn();
	$arrs = null;
	$sql = "SELECT CommentID, Title, insertDate, FirstName, LastName, MainText 
		FROM film_comments 
	WHERE FilmID = " . $id . " AND Visible = True 
	ORDER BY InsertDate DESC ";
	//echo $sql;
	$smtp = $conn->prepare($sql);
	$smtp->execute();
	$rs = $smtp->fetchAll(PDO::FETCH_ASSOC);
	if (is_array($rs)) {
		$arrs = $rs;
	}
	$smtp = null;
	$rs = null;

	if (is_array($arrs)) { ?>
		<section class="commentsList">
			<div class="bar">
				<div class="flex_between">
					<h3><?= lngNumberOfComments . ": " . $nr ?></h3>
					<?php if (intval($nr) >= 2) { ?>
						<button class="button-grey button-gradient" id="jqSortComments"><?= lngChangeOrder ?></button>
					<?php } ?>
					<button class="button-grey button-gradient" id="jqAddComments"><?= LNG_Comments_Add ?></button>
				</div>
			</div>
			<ul id="jqSortComments_Target">
				<?php
				$x = intval($nr);
				$rows = count($arrs);
				$a = 0;
				for ($r = 0; $r < $rows; $r++) {
					$a = $a + 1;
					if (($a % 2) == 0) {
						$bg = "comment_even";
					} else {
						$bg = "comment_odd";
					}

					$strDisplay = "none";
					$iCommentID = $arrs[$r]["CommentID"];
					$strCommentClass = "comment_show";
					if (intval(@$_GET["anchor"]) == intval($iCommentID)) {
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
							<div class="text text_resizeable">
								<?= $arrs[$r]["MainText"] ?>
							</div>
						</div>
					</li>
				<?php
					$x = $x - 1;
				} ?>
			</ul>
		</section>
<?php }
	$arrs = null;
} ?>