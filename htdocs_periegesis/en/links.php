<?php
include __DIR__ . "/siteLang/sxLang.php";
include PROJECT_PHP ."/sx_config.php";
include PROJECT_PHP ."/defaultHeader.php";
?>
</head>

<body id="body_links">
	<?php require PROJECT_PHP ."/sx_Header.php"; ?>
	<div class="page">
		<div class="content">
			<main class="main">
				<?php require PROJECT_PHP ."/inLinks/default.php"; ?>
			</main>
			<aside class="aside">
				<?php
				require PROJECT_PHP ."/inLinks/nav_link_categories.php";
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