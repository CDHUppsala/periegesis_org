<section>
	<h2>Form for Sending Newsletters</h2>
	<form name="SendEmailLetter" method="POST" target="_blank" action="sx_NewsletterSender.php">
		<input type="Hidden" name="EmailList" id="jqEmailList" value="">
		<input type="Hidden" name="EmailListSource" id="jqEmailListSource" value="">
		<div>
			<textarea style="width: 98%; height: 640px;" name="TextBody"><?= $str_TextArea ?></textarea>
		</div>
		<p class="floatRight" style="margin-right: 20px;">
			<span id="count">Youc can Load and Post your First Email List!</span>
			<input id="startClock" data-sec="<?= $iSecondsDelay ?>" type="Submit" value="<?= lngSubmitForm ?>">
		</p>
	</form>

	<h3>Email List</h3>
	<p id="jqEmailListView">Please, use the Form on the right to load and select a List of Emails.</p>
	<p>
		<b>Obs!</b> Your <b>Session</b> as administrator must be active in order to send emails. If the session is timed out,
		go back to the administration page, <b>without closing this one</b>, and login again.
		<b>Return then back to this page</b> and continue the above procedures
		(in that way you can avoid the mistake to send emails to the same addresses).
	</p>
</section>