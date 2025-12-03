<?php

/**
 * 3. Send warning email to administration and stop the registration
 * Max 2 warning,
 * - A. for multiple registrations from same Email (adds value = 10) 
 * - B. for multiple registrations from IPs with the first 6 left characters
 *  	equal to the current IP (adds value = 20)
 * Check the value of the field AdminWarnings in the table Events:
 * - If the value is = 10, warning has been sent for case A, 
 * - If the value is = 20, warning has been sent for case B, 
 * - If the value is = 30 (10+20), both warnings have been sent
 *   don't send more warnings
 */
if ($radioSendWarningForEmail || $radioSendWarningForIP) {
    $radioContinue = false;
    $radio_DisplayWarning = true;

    /**
     * Since registration will not be continued, increase registration times
     * for this email and Event ID, if they already exist in event_participants.
     */
    if ($intParticipantID > 0) {
        $intRegistrationTimes++;
        $sql = "UPDATE event_participants SET
						ParticipationMode = ?,
						RegistrationTimes = ?
					WHERE ParticipantID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$strMode, $intRegistrationTimes, $intParticipantID]);
    }

    /**
     * Check if previous warnings have been sent
     */
    $sql = "SELECT AdminWarnings
				FROM events
				WHERE EventID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$iEventID]);
    $intAdminWarnings = (int) $stmt->fetchColumn();
    $stmt = null;

    if ($intAdminWarnings > 0) {
        // At lest one warning has been sent
        if ($intAdminWarnings == 30) {
            /**
             * Both warnings have been sent,
             * So, just warn the visitor and stop the registration
             */
            $radioSendWarningForEmail = false;
            $radioSendWarningForIP = false;
        } elseif ($intAdminWarnings == 10) {
            $radioSendWarningForEmail = false;
        } elseif ($intAdminWarnings == 20) {
            $radioSendWarningForIP = false;
        } else {
            // Just in case: Value is not valid, so set it to 0
            $intAdminWarnings = 0;
            $sql = "UPDATE events SET
							AdminWarnings = 0
						WHERE EventID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$iEventID]);
        }
    }
}

/**
 * So, just check wich warning is true, if any, and:
 * - add its value (10 or 20) to the field AdminWarnings, in Events table
 * - send warning mail to administration
 * If $intAdminWarnings = 0, both warnings might be true
 * - so, add their value and send warnings for both
 */
if ($radioSendWarningForEmail || $radioSendWarningForIP) {
    $sx_mail_subject = 'Information about Event Participation';
    $sx_send_to_email = str_SiteEmail;

    $str_ComonHeader = '<h4>Warning about possible Spam Attack</h4>';
    $str_ComonWarning = '<p>Please open the table <b>Event Blacklisted IPs</b> and check the activity.
			If too many registration are made, the registration to events might be under <b>Spam Attack</b>.</p>
		
			<p>Please, conseder to open the table <b>Events Setup</b> as soon as possible and set the
			value of the field <b>Stop Mail With Blacklisted IP</b> temporally to YES,
			just to stop registration and sending emails for all Blacklisted IPs
			that have not previously been marked as valid by you.</p>
			
			<p>Keep then checking the table <b>Event Blacklisted IPs</b>.
			If the attack slows down or stops, set the value of the field <b>Use Blacklisted IP Table</b> back to NO,
			to allow registration of new emails even from blacklisted IPs.</p>
			
			<p><b>Alternatively</b>, if you do not suspect a Spam Attack,
			check the last entries in the table <b>Event Blacklisted IPs</b>
			and if Names and Emails are valid, set the value of the field <b>Valid Mail Address</b> to YES.</p>
			
			<p><b>Please Notice</b> that you will get max two warnings:</p>
			<ol>
				<li>One for the first email with multiple registration for the same Event and</li>
				<li>One for the first set of IPs which have the same first 6 characters and have together
				multiple registrations for the same Event.</li>
			</ol>

			<p>In both these cases, the participation is not registered in the table <b>Event Participants</b>,
			no mail is sent to participant and a warning is shown to the visitor in the website.</p>';

    if ($radioSendWarningForEmail) {
        $str_ForThis = '</p>The email address (' . $strEmail . ') with IP (' . $iEventID . ')
					has been used for multiple registrations to the same Event ID (' . $iEventID . ').</p>';

        $sx_mail_content = $str_ComonHeader . $str_ForThis . $str_ComonWarning;

        require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";

        $intAdminWarnings += 10;
        $sql = "UPDATE events SET
					AdminWarnings = ?
				WHERE EventID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$intAdminWarnings, $iEventID]);
    }

    if ($radioSendWarningForIP) {
        $str_ForThis = '</p>IP addresses which have their 6 first characters equal to
					the current IP (' . sx_UserIP . '), with email address (' . $strEmail . '), have been used
					for multiple registrations to the same Event ID (' . $iEventID . ').</p>';

        $sx_mail_content = $str_ComonHeader . $str_ForThis . $str_ComonWarning;

        require dirname(dirname(__DIR__)) . "/sx_Mail/sx_mail_template.php";

        $intAdminWarnings += 20;
        $sql = "UPDATE events SET
					AdminWarnings = ?
				WHERE EventID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$intAdminWarnings, $iEventID]);
    }
}
