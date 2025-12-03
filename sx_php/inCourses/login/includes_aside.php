<?php
if (isset($_SESSION["Students_" . sx_DefaultSiteLang])) { ?>
	<section>
		<h2 class="head"><?= lngWelcome ?>
			<span><?= mb_substr(@$_SESSION["Students_FirstName"], 0, 1) . ". " . mb_substr(@$_SESSION["Students_LastName"], 0, 1) ."."?></span>
		</h2>
	</section>
<?php
}
include __DIR__ ."/nav_login.php";
include dirname(__DIR__) . "/nav_courses.php";

?>