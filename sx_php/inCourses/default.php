<?php

if (empty($str_CourseSetupTitle)) {
	$str_CourseSetupTitle = $str_CoursesLinkTitle;
} ?>
<section>
	<h1 class="head"><span><?= $str_CourseSetupTitle ?></span></h1>
	<article class="text_wraper">
		<?php
		if (!empty($str_CourseSetupMedia)) {
			if (strpos($str_CourseSetupMedia, ";") > 0) {
				get_Manual_Image_Cycler($str_CourseSetupMedia, "", "");
			} else {
				get_Any_Media($str_CourseSetupMedia, "Center", "");
			}
		}  ?>
		<div class="text text_resizeable">
			<div class="text_max_width">
				<?php
				echo $memo_CourseSetupDescription;
				?>
			</div>
		</div>
	</article>
</section>
<section>
	<h1 class="head"><span><?= $str_GeneralConditionsTitle ?></span></h1>
	<article class="text_wraper">
		<div class="text text_resizeable">
			<div class="text_max_width">
				<?php
				echo $memo_GeneralConditions;
				?>
			</div>
		</div>
	</article>
</section>