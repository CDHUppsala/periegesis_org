<?php

if ($radio_UseCategories && !empty($rs_Cats)) { ?>
	<section class="jqNavMainToBeCloned">
		<h2 class="head"><span><?= lngLinkCategoris ?></span></h2>
		<div class="nav_aside">
			<ul>
				<?php
				$irows = count($rs_Cats);
				for ($r = 0; $r < $irows; $r++) { ?>
					<li><a href="links.php?linkID=<?= $rs_Cats[$r]["CategoryID"] ?>"><?= $rs_Cats[$r]["CategoryName"] ?></a></li>
				<?php
				} ?>
			</ul>
		</div>
	</section>
<?php }
$rs_Cats = null;

if ($radio_UseAdvertises) {
	//== Place: Top,  Bottom
	get_Main_Advertisements("Bottom");
}
?>