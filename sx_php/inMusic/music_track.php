<?php
if (intval($int_TrackID) > 0) {
	$strType = "audio/mpeg";
	if (strpos($str_TrackURL, ".ogg")) {
		$strType = "audio/ogg";
	} ?>
	<section>
		<h1 class="head"><span><?= lngAlbum . " " . $str_AlbumTitle ?></span></h1>
		<div class="print float_right">
			<?php
			getBackArrow();
			getLocalEmailSender("", $str_TrackTitle, return_Left_Part_FromText($memo_TrackNotes, 100), "");
			?>
		</div>
		<h2 class="head"><span><?= $str_TrackTitle ?></span></h2>
		<audio src="../music/<?= $str_TrackURL ?>" controls>
			<source src="../music/<?= $str_TrackURL ?>" type="<?= $strType ?>">
		</audio>
		<?php
		if (!empty($str_SellingSiteURL) && !empty($str_SellingSiteTitle)) { ?>
			<p class="align_right">
				<a target=_blank href="<?= $str_SellingSiteURL ?>"><?= $str_SellingSiteTitle ?></a>
			</p>
		<?php
		} elseif ($radio_FreeDownload) { ?>
			<p class="align_right">
				<a href="../music/<?= $str_TrackURL ?>"><?= lngDownload ?></a>
			</p>
		<?php
		} ?>
		<h3><?= lngDescription ?></h3>
		<?php
		if (!empty($str_TrackImage)) {
			get_Any_Media($str_TrackImage, "Left", "");
		} ?>
		<div class="text">
			<div class="text_max_width">
				<?= $memo_TrackNotes ?>
			</div>
		</div>

	</section>
<?php
} ?>