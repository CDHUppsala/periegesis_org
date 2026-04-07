<?php
include __DIR__ . "/siteLang/sxLang.php";
include PROJECT_PHP . "/sx_config.php";
include PROJECT_PHP . "/inArticles/config_articles.php";
include PROJECT_PHP . "/defaultHeader.php";
?>
</head>

<body id="body_search" class="body_one_column">
	<?php include PROJECT_PHP . "/sx_Header.php"; ?>
	<div class="page">
		<div class="content">
			<main class="main" id="jqLoadPageNav">
				<?php
				include PROJECT_PHP . "/app_wikidata/default.php";
				?>
			</main>
			<aside class="aside">

			</aside>
		</div>
	</div>
	<?php
	include PROJECT_PHP . "/sx_Footer.php";
	$conn = null;
	?>
</body>

</html>