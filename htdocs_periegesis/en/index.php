<?php
/*
    Business Sphere - Articles and Posts  
 */

include __DIR__ . "/siteLang/sxLang.php";
include PROJECT_PHP . "/sx_config.php";
include PROJECT_PHP . "/defaultHeader.php";
?>
</head>

<body id="body_index" class="body_one_column">
	<?php
	require PROJECT_PHP . "/sx_Header.php";
	?>
	<div class="page">
		<?php
		include PROJECT_PHP . "/inArticles/includes/index_first_page.php";
		?>
		<div class="content">
			<main class="main">
				<?php
				include PROJECT_PHP . "/inArticles/includes/index_main.php";
				?>
			</main>
			<aside class="aside">
				<?php
				include PROJECT_PHP . "/inArticles/includes/index_aside.php";
				?>
			</aside>
		</div>
		<?php
		include PROJECT_PHP . "/inArticles/includes/index_footer.php";
		?>
	</div>
	<?php
	include PROJECT_PHP . "/sx_Footer.php";
	$conn = null;
	?>
</body>

</html>