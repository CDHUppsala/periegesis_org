<?php
$sql = "SELECT AlbumID, AlbumTitle 
	FROM music_albums 
	ORDER BY Sorting DESC, AlbumID ";
$rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
if ($rs) { ?>
	<section class="jqNavMainToBeCloned">
		<h2 class="head"><span><?= $str_MusicNavTitle ?></span></h2>
		<div class="sxAccordionNav">
			<ul>
				<?php
				$iRows = count($rs);
				for ($r = 0; $r < $iRows; $r++) {
				?>
					<li><a href="music.php?albumID=<?= $rs[$r][0] ?>"><?= $rs[$r][1] ?></a></li>
				<?php
				} ?>
			</ul>
		</div>
	</section>
<?php
}
$rs = null;

if ($radio_UseAdvertises) {
	//== Place: Top,  Bottom
	get_Main_Advertisements("Bottom");
}
?>