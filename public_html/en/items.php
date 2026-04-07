<?php
include __DIR__ . "/siteLang/sxLang.php";
include PROJECT_PHP . "/sx_config.php";
include PROJECT_PHP . "/inItems/config_items.php";
include PROJECT_PHP . "/defaultHeader.php";
?>
</head>

<body id="body_items">
	<?php require PROJECT_PHP . "/sx_Header.php"; ?>
	<div class="page">
		<div class="content_wide">
			<main class="main">
				<?php
				include PROJECT_PHP . "/inItems/default.php";
				?>
			</main>
			<aside class="aside">
				<?php
				include PROJECT_PHP . "/inItems/aside.php";
				?>
			</aside>
		</div>
	</div>
	<?php
	include PROJECT_PHP . "/sx_Footer.php";
	$conn = null;
	?>
</body>

</html>