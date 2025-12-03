<?php

$intNumberComments = 0;
if (isset($int_TextID) && intval($int_TextID) > 0) {
	$sql = "SELECT count(InsertID) AS CountNumber
		FROM text_comments 
		WHERE TextID = :id 
			AND Visible = True ";
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':id', $int_TextID, PDO::PARAM_INT);
	$stmt->execute();
	$intNumberComments = $stmt->fetchColumn();
	$stmt = null;
}

$radioAdd = true;
if (intval($i_CommentableDays) > 0) {
	$radioAdd = false;
	if (!return_Is_Date($datePublishedDate)) {
		$datePublishedDate = date('Y-m-d');
	}
	if ($datePublishedDate >= return_Add_To_Date(date('Y-m-d'), -$i_CommentableDays)) {
		$radioAdd = true;
	}
}
/**
 * Do not show anything if add comments is false and number of comments is 0
 */

$radio_Show_Add_Comment = false;
if ($radioAdd && $show_Comment_Add_Form) {
	$radio_Show_Add_Comment = true;
}
if ($radio_Show_Add_Comment || (int) $intNumberComments > 0) { ?>
	<section>
		<article aria-label="Comments">
			<div class="text_max_width">
				<div class="comments" id="comment">
					<div class="bar">
						<div class="flex_between">
							<h3><?= lngNumberOfComments . ": " . $intNumberComments ?></h3>
							<?php if (intval($intNumberComments) >= 2) { ?>
								<button class="button-grey button-gradient" id="jqSortComments"><?= str_replace(" ", " ", lngChangeOrder . "") ?></button>
							<?php }
							if ($radioAdd) { ?>
								<button class="button-grey button-gradient" id="jqAddComments"><?= str_replace(" ", " ", LNG_Comments_Add . "") ?></button>
							<?php } ?>
						</div>
					</div>
					<?php

					if ($radio_Show_Add_Comment) {
						require __DIR__ . "/add_comment_process.php";
					}
					if (intval($intNumberComments) > 0) {
						require __DIR__ . "/read_comment.php";
					}
					if ($radio_Show_Add_Comment) {
						require __DIR__ . "/add_comment_form.php";
					} ?>
				</div>
			</div>
		</article>
	</section>
<?php
} ?>