<div class="dirLeft">
	<?php if (intval($int1) == 0) {
		if (!empty($strGalleryTitle)) { ?>
			<h1><?= $strGalleryTitle ?></h1>
		<?php
		}
	} else {
		if (intval($int0) > 0) { ?>
			<h1><?= $strGroupName ?></h1>
			<?php
			if (!empty($strGroupNote)) { ?>
				<div class="text"><?= $strGroupNote ?></div>
			<?php
			}
		}
		if (intval($int1) > 0) { ?>
			<h2><?= $strGalleryName ?></h2>
			<?php if (!empty($strGalleryNote)) { ?>
				<div class="text"><?= $strGalleryNote ?></div>
		<?php
			}
		} ?>
		<div id="photoInfo"></div>
	<?php
	} ?>
</div>