<section class="jqNavMainToBeCloned">
	<?php
	$strSlide = "slide_up";
	$strDisplay = "block";
	if ($radio___ForumMemberIsActive || intval($intForumID) > 0) {
		$strSlide = "slide_down";
		$strDisplay = "none";
	}

	if ($radio___ForumMemberIsActive) {	?>
		<h2><?= lngWelcome ?><br>
			<span class="text_xxsmall"><?= mb_substr($s__FirstName, 0, 1) . ". " . $s__LastName ?></span>
		</h2>
	<?php
	}
	if ($radioUseForumRegistration) { ?>
		<h2 class="head <?= $strSlide ?> jqToggleNextRight"><span><?= lngNavigation ?></span></h2>
		<nav class="nav_aside" style="display: <?= $strDisplay ?>">
			<ul>
				<?php
				if ($radio___ForumMemberIsActive) { ?>
					<li><a href="forum_login.php?pg=logout"><?= lngLogout ?></a></li>
					<li><a href="forum_login.php?pg=leave"><?= lngLeave ?></a></li>
					<li><a href="forum_login.php?pg=edit"><?= lngChangeProfile ?></a></li>
				<?php
				} else { ?>
					<li><a href="forum_login.php?pg=login"><?= lngLogin ?></a></li>
					<?php if ($radioUseForumRegistration) { ?>
						<li><a href="forum_login.php?pg=join"><?= lngJoin ?></a></li>
					<?php } ?>
					<li><a href="forum_login.php?pg=forgot"><?= lngForgotPassword ?></a></li>
				<?php
				} ?>
				<li><a href="forum.php?pg=conditions"><?= lngParticipationTerms ?></a></li>
				<li><a href="forum.php"><?= lngForumPage ?></a></li>
			</ul>
		</nav>
	<?php
	} ?>
</section>

<?php
$strSlide = "slide_up";
$strDisplay = "block";
if (intval($intForumID) > 0) {
	$strSlide = "slide_down";
	$strDisplay = "none";
}
$aResults = forum_get_active_themes();
if (is_array($aResults)) {
	$iRows = count($aResults);
?>
	<section class="jqNavMainToBeCloned">
		<h2 class="head <?= $strSlide ?> jqToggleNextRight"><span><?= LNG_Forum_Actual_Themes ?></span></h2>
		<nav class="nav_aside" style="display: <?= $strDisplay ?>">
			<ul class="no_styles max_height">
				<?php
				if (is_array($aResults)) {
					for ($r = 0; $r < $iRows; $r++) { ?>
						<li><a href="forum.php?forumID=<?= $aResults[$r][0] ?>"><?= $aResults[$r][0] . ". " . $aResults[$r][1] ?></a></li>
				<?php }
				} ?>
			</ul>
		</nav>
	</section>

<?php
}

$aResults = forum_get_previous_themes();
if (is_array($aResults)) {
	$iRows = count($aResults);
?>
	<section class="jqNavMainToBeCloned">
		<h2 class="head <?= $strSlide ?> jqToggleNextRight"><span><?= LNG_Forum_Previous_Themes ?></span></h2>
		<nav class="nav_aside" style="display: <?= $strDisplay ?>">
			<ul class="no_styles max_height">
				<?php
				for ($r = 0; $r < $iRows; $r++) { ?>
					<li><a href="forum.php?forumID=<?= $aResults[$r][0] ?>"><?= $aResults[$r][0] . ". " . $aResults[$r][1] ?></a></li>
				<?php
				} ?>
			</ul>
		</nav>
	</section>
<?php
}
$aResults = null;

?>