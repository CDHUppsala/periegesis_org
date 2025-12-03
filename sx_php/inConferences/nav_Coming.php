<?php

$aResults = sx_getPastComingConferences(0);
if (is_array($aResults)) { ?>
	<section class="jqNavMainToBeCloned">
		<h2 class="head_nav"><span><?= $str_ComingConferencesTitle ?></span></h2>
		<nav class="sxAccordionNav">
			<ul>
				<?php
				if (is_array($aResults)) {
					foreach ($aResults as $row) { ?>
						<li>
							<a href="conferences.php?confid=<?= $row['ConferenceID'] ?>"><?= $row['Title'] . " <span>(" . $row['Conference_Date'] ?>)</span></a>
						</li>
				<?php
					}
				} ?>
			</ul>
		</nav>
	</section>
<?php
}
$aResults = null;
?>