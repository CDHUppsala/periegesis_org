<section>
	<?php
	$arrTracks = "";
	if (intval($int_RequestAlbumID) == 0) { ?>
		<h1 class="head"><span><?= lngAlbum . ": " . $int_RequestAlbumID ?></span></h1>
		<p><b><?= lngRecordsNotFound ?></b></p>

	<?php
	} else {
		$sql = "SELECT 
		    TrackID,
        	TrackNumber,
	        TrackDate,
	        TrackTitle" . str_LangNr . " AS TrackTitle, 
    	    TrackURL,
        	FreeDownload,
	        TrackImage,
    	    TrackNotes" . str_LangNr . " AS TrackNotes
		FROM music_tracks 
		WHERE AlbumID = ?
			AND Hidden = False
		ORDER BY TrackNumber ASC";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$int_RequestAlbumID]);
		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if ($rs) {
			$arrTracks = $rs;
		}
		$stmt = null;
		$rs = null;
	}

	if (is_array($arrTracks) > 0) { ?>

		<h1 class="head"><span><?= lngAlbum . ": " . $str_AlbumTitle ?></span></h1>
		<div class="print">
			<?php
			getLocalEmailSender("", $str_AlbumTitle, return_Left_Part_FromText($memo_AlbumNote, 100), "");
			if (intval($int_GalleryID) > 0) {
				getPhotoGallery($int_GalleryID);
			} ?>
		</div>

		<table class="no_bg">
			<?php
			$iRows = count($arrTracks);
			$x = 0;
			$arrMusic = "";
			for ($r = 0; $r < $iRows; $r++) {
				$intTrackID = $arrTracks[$r]["TrackID"];
				$intTrackNumber = $arrTracks[$r]["TrackNumber"];
				$strTrackTitle = $arrTracks[$r]["TrackTitle"];
				$strTrackURL = $arrTracks[$r]["TrackURL"];
				$radioFreeDownload = $arrTracks[$r]["FreeDownload"];
				$strTrackImage = $arrTracks[$r]["TrackImage"];
				$memoTrackNotes = $arrTracks[$r]["TrackNotes"];
				if ($arrMusic != "") {
					$arrMusic = $arrMusic . "|";
				}
				$strType = "audio/mpeg";
				if (strpos($strTrackURL, ".ogg")) {
					$strType = "audio/ogg";
				}

				$arrMusic = $arrMusic . "../music/" . $strTrackURL; ?>
				<tr>
					<td><?= $intTrackNumber ?>.</td>
					<td>
						<?php if (!empty($memoTrackNotes)) { ?>
							<a href="music.php?trackID=<?= $intTrackID ?>"><?= $strTrackTitle ?></a>
						<?php
						} else { ?>
							<?= $strTrackTitle ?>
						<?php
						}
						if ($radioFreeDownload) { ?>
							<br><a href="../music/<?= $strTrackURL ?>"><?= lngDownload ?></a>
						<?php
						} ?>
					</td>
					<td style="width: 50%;">
						<audio src="../music/<?= $strTrackURL ?>" controls>
							<source src="../music/<?= $strTrackURL ?>" type="<?= $strType ?>">
						</audio>
					</td>
				</tr>
			<?php
			} ?>
		</table>

		<p class="align_right">
			<button title="Click on Stop and Play to jump to the next audio" id="jg_start_playing" class="button">Play All Audios</button>
			<button title="Click on Stop and Play to jump to the next audio" id="jg_stop_playing" class="button">Stop All Audios</button>
		</p>

		<?php
		if (!empty($str_SellingSiteURL) && !empty($str_SellingSiteTitle)) { ?>
			<p class="align_right">
				<a target=_blank href="<?= $str_SellingSiteURL ?>"><?= $str_SellingSiteTitle ?></a>
			</p>
		<?php
		} ?>
		<h3><?= lngDescription ?></h3>
		<?php

		if (!empty($str_AlbumImage)) {
			if (strpos($str_AlbumImage, ";") > 0) {
				get_Manual_Image_Cycler($str_AlbumImage, "", $memo_AlbumImageNotes);
			} else {
				get_Any_Media($str_AlbumImage, $str_AlbumImagePlace, $memo_AlbumImageNotes);
			}
		}
		if (!empty($memo_AlbumNote)) { ?>
			<div class="text"><div class="text_max_width"><?= $memo_AlbumNote ?></div></div>
	<?php
		}
		if (!empty($str_FilesForDownload)) {
			echo '<div class="align_right">';
			sx_getDownloadableFiles($str_FilesForDownload);
			echo '</div>';
		}
	}
	$arrTracks = null;
	?>
</section>

<script>
	$sx(function() {
		index = 0;
		var audios = $sx("td audio");
		$sx('#jg_start_playing').on('click', function() {
			$sx('#jg_stop_playing').click();
			var audio = audios[index];
			audio.play();
			index++
			audio.onended = function() {
				audio.currentTime = 0;
				if (index < audios.length) {
					$sx('#jg_start_playing').click();
				}
			}
		});

		$sx('#jg_stop_playing').on('click', function() {
			for(x=0; x < audios.length; x++) {
				audios[x].pause();
				audios[x].currentTime = 0;
			}
		});
	});
</script>