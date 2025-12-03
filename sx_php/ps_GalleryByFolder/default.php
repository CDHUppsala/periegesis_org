<?php
require __DIR__ . "/config_galleryFolder.php";
include dirname(__DIR__) . "/default_header_apps.php";
?>

</head>

<body id="bodyFolderGallery">
	<header>
		<div class="row">
			<div class="left">
				<?php
				if (!empty($strLogoReturn)) { ?>
					<a href="index.php" title="<?= $strSiteTitle ?>"><img src="../images/<?= $strLogoReturn ?>" /></a>
				<?php
				} else { ?>
					<a href="index.php"><?= $strSiteTitle ?></a>
				<?php
				} ?>
			</div>
			<div class="middle">
				<div class="sxNavMarker" id="jqNavMarker">
					<svg class="sx_svg">
						<use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_left_right"></use>
					</svg>
				</div>
			</div>

			<div class="right">
				<a title="Reload Default Page" href="ps_gallery_byfolder.php"></a>
			</div>
		</div>
	</header>
	<div class="content">
		<main>
			<?php include __DIR__ . "/photos_GalleryFolder.php"; ?>
		</main>
		<aside>
			<?php include __DIR__ . "/nav_GalleryFolder.php"; ?>
		</aside>
	</div>
</body>

</html>