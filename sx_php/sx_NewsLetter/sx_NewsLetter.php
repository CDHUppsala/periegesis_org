<?php
$str_FooterFormToken = sx_generate_form_token('FooterNewsLettersForm', 64);
?>
<p><?php echo lngNewsletterSubscribe ?></p>
<form class="merge_inputs jq_load_modal_window" name="NewsLetter" method="post">
	<input type="hidden" name="FooterFormToken" value="<?= $str_FooterFormToken ?>">
	<input type="hidden" name="FormName" value="NewsLetter" />
	<input type="hidden" name="FooterNewsForm" value="yes" />
	<input type="email" name="FooterNewsEmail" minlength="6" placeholder="Email" required />
	<input type="submit" value="<?= lngSubscribe ?>" name="Submit" />
</form>