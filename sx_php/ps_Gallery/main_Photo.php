<?php
if (intval($int0) == 0) {
	if (!empty($strGalleryTitle)) { ?>
		<h1><?= $strGalleryTitle ?></h1>
	<?php
	} ?>
	<p><img alt="<?= $strGalleryTitle ?>" src="../images/<?= $strGallerySetupImage ?>"></p>
	<?php
	if (!empty($memoGallerySetupNote)) { ?>
		<div class="text"><?= $memoGallerySetupNote ?></div>
	<?php
	}
} else { ?>
	<div id="photoBG"></div>
	<ul class="pagination" id="jqArrowPhotoNav" style="display:none">
		<li title="Press also the Left Key of the Keyboard" class="jqNavArrows" id="jqLeft"><span>&#10094;&#10094;</span></li>
		<li title="Press also the Up or Down Key of the Keyboard" id="jqBigPhoto"><span>&#10094;&#10095;</span></li>
		<li title="Press also the Right Key of the Keyboard" class="jqNavArrows" id="jqRight"><span>&#10095;&#10095;</span></li>
	</ul>
<?php
} ?>