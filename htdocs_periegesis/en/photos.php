<?php
require "siteLang/sxLang.php";
include PROJECT_PHP ."/sx_config.php";
include PROJECT_PHP ."/inPhotos/config_photos.php";
include PROJECT_PHP ."/defaultHeader.php";
?>
</head>

<body id="body_photos">
	<?php require PROJECT_PHP ."/sx_Header.php"; ?>
	<div class="page">
		<div class="content">
			<main class="main">
			<?php require PROJECT_PHP ."/inPhotos/default.php"; ?>
			</main>
			<aside class="aside">
			<?php 
				require PROJECT_PHP ."/inPhotos/aside.php"; 
			?>
			</aside>
		</div>
	</div>
	<?php
	include PROJECT_PHP ."/sx_Footer.php";
	$conn = null;
	?>
</body>


</html>