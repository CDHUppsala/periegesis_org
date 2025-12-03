<?php

if ($radio__UserSessionIsActive) { ?>
	<section>
		<h2 class="head"><?= lngWelcome ?>
			<br><span class="text_xxsmall"><?= mb_substr(@$_SESSION["Users_FirstName"], 0, 1) . ". " . @$_SESSION["Users_LastName"] ?></span>
		</h2>
	</section>
<?php

}if ($radio__UserSessionIsActive && sx_IncludeLoginGroupsIn_SeparateMenu) {
	include PROJECT_PHP ."/sxNav_Main/sxNavMain_Acc_PublishedBySubCategories_Login.php";
} ?>

<section class="jqNavMainToBeCloned">
	<h2 class="head"><span><?= lngNavigation ?></span></h2>
	<div class="nav_aside">
		<ul>
			<?php if ($radio__UserSessionIsActive) { ?>
				<li><a href="login.php?pg=logout"><?= lngLogout ?></a></li>
				<li><a href="login.php?pg=leave"><?= lngLeave ?></a></li>
				<li><a href="login.php?pg=edit"><?= lngChangeProfile ?></a></li>
			<?php } else { ?>
				<li><a href="login.php?pg=login"><?= lngLogin ?></a></li>
				<?php if ($radioAllowOnlineRegistration) { ?>
					<li><a href="login.php?pg=join"><?= lngJoin ?></a></li>
				<?php } ?>
				<li><a href="login.php?pg=forgot"><?= lngForgotPassword ?></a></li>
			<?php } ?>
		</ul>
	</div>
</section>

<?php
if (!$radio__UserSessionIsActive) { ?>
	<section>
		<h2 class="head"><?= $strUsersConditionsTitle ?></h2>
		<div class="text text_small">
			<?= $memoUsersConditions ?>
		</div>
	</section>
<?php
} ?>