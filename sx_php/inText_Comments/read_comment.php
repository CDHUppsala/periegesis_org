<?php
$sql = "SELECT InsertID, Title, insertDate, FirstName, LastName, MainText 
        FROM text_comments 
    WHERE TextID = ?
        AND Visible = True 
    ORDER BY InsertDate DESC ";
$stmt = $conn->prepare($sql);
$stmt->execute([$int_TextID]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rows) { ?>
	<ul id="jqSortComments_Target">
		<?php
		$x = count($rows);
		$a = 0;
		$intAnchor = return_Filter_Integer(@$_GET["anchor"]);
		foreach ($rows as $rs) {
			$a++;
			if ($a % 2 == 0) {
				$bg = "comment_even";
			} else {
				$bg = "comment_odd";
			}
			$strDisplay = "none";
			$iInsertID = $rs["InsertID"];
			$strCommentClass = "comment_show";
			if (intval($intAnchor) == intval($iInsertID)) {
				$strDisplay = "block";
				$strCommentClass = "comment_hide";
			} ?>
			<li id="<?= $iInsertID ?>">
				<div class="comment_header <?= $bg ?>">
					<span title="<?= lngViewHideResponses ?>" class="jqCommentsToggle <?= $strCommentClass ?>"></span>
					<h3><?= lngForumResponse ?>: <?= $x ?> - <?= $rs["Title"] ?></h3>
					<div class=float_right><?= $rs["insertDate"] ?></div>
					<h4><?= ($rs["FirstName"] . " " . $rs["LastName"]) ?></h4>
				</div>
				<div class="comment_content <?= $bg ?>" style="display: <?= $strDisplay ?>">
					<div class="text text_resizeable"><?= $rs["MainText"] ?></div>
				</div>
			</li>
		<?php
			$x++;
		} ?>
	</ul>
<?php }
$stmt = null;
$rows = null;
?>