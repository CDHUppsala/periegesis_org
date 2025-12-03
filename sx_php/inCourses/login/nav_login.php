<section class="jqNavMainToBeCloned">
	<h2 class="head"><span><?= $str_CoursesLinkTitle ?></span></h2>
	<div class="nav_aside">
		<ul>
			<?php
			if (isset($_SESSION["Students_" . sx_DefaultSiteLang])) { ?>
				<li><a href="courses_login.php?pg=course"><?= lngRegisterForCourse ?></a></li>
				<li><a href="courses_login.php?pg=edit"><?= lngChangeProfile ?></a></li>
				<li><a href="courses_login.php?pg=leave"><?= lngLeave ?></a></li>
				<li><a href="courses_login.php?pg=logout"><?= lngLogout ?></a></li>
			<?php
			} else { ?>
				<li><a href="courses_login.php?pg=login"><?= lngLogin ?></a></li>
				<?php if ($radio_AllowOnlineRegistration) { ?>
					<li><a href="courses_login.php?pg=join"><?= lngJoinStudentsArea ?></a></li>
				<?php } ?>
				<li><a href="courses_login.php?pg=forgot"><?= lngForgotPassword ?></a></li>
			<?php
			} ?>
		</ul>
	</div>
</section>

<?php

if (!isset($_SESSION["Students_" . sx_DefaultSiteLang])) { ?>
	<section>
		<h2 class="head slide_down jqToggleNextRight"><span><?= $str_GeneralConditionsTitle ?></span></h2>
		<div class="text text_small overflow_hidden" style="display: none;">
			<?= $memo_GeneralConditions ?>
		</div>
	</section>
<?php
} ?>