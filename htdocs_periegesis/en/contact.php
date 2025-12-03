<?php
include __DIR__ . "/siteLang/sxLang.php";
include PROJECT_PHP . "/sx_config.php";
include PROJECT_PHP . "/defaultHeader.php";
?>
</head>

<body id="body_contact">
	<?php require PROJECT_PHP . "/sx_Header.php"; ?>
	<div class="page">
		<div class="content">
			<main class="main">
				<?php
				if (isset($_GET["nl"])) {
					include PROJECT_PHP . "/sx_NewsLetter/sx_NewsLetterResponse.php";
				}
				include PROJECT_PHP . "/inContact/default.php";
				?>
			</main>
			<aside class="aside">
				<?php
				require PROJECT_PHP . "/inContact/aside.php";
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