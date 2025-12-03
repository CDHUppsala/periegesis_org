<?php
require __DIR__ . "/functions.php";
?>
<section>
	<?php
	if (intval($i_FolderGalleryID) == 0) { ?>
		<h1><?= $strGallerySetupTitle ?></h1>
		<figure class="image_center"><img alt="<?php echo sx_Remove_Quotes($strGallerySetupTitle) ?>" src="../images/<?= $strGallerySetupImage ?>" /></figure>
		<?php
		if ($memoGallerySetupNote != "") { ?>
			<div class="text"><div class="text_max_width"><?= $memoGallerySetupNote ?></div></div>
		<?php
		}
	} else {
		if (intval($i_FolderGroupID) > 0) { ?>
			<h1><?= $strGroupName ?></h1>
			<?php
			if (!empty($memoGroupNote)) { ?>
				<div class="text"><div class="text_max_width"><?= $memoGroupNote ?></div></div>
		<?php
			}
		} ?>
		<h2><?= $strGalleryName ?></h2>
		<?php
		if (!empty($memoGalleryNote)) { ?>
			<div class="text"><div class="text_max_width"><?= $memoGalleryNote ?></div></div>
		<?php
		}
	}
	if (intval($i_FolderGalleryID) > 0 && !empty($strSubFolderName)) { ?>
		<div class="ps_inline_gallery jqps_inline_gallery">
			<?php
			return_Folder_ImagesAsInlineGallery($strSubFolderName);
			?>
		</div>
	<?php
	} ?>
</section>