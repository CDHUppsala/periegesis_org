<?php

/**
 * Show Media and PDF attachments for Conference and Sessions in cards
 */
if (
    $radio_ShowAttachmentsAlsoInCards
    && ($radio_LoginToViewConferenceAttachments == false
        || $radio_LoggedParticipant)
) {
    /**
     * Check if paper attachments should be alos shown
     * The same check is repeated in read_papers.php
     */
    $radioShowPaperAttachments = false;
    if ($radio_RegisterToViewPaperAttachments == false) {
        if ($radio_LoginToViewPaperAttachments == false || $radio_LoggedParticipant) {
            $radioShowPaperAttachments = true;
        }
    } elseif ($radio_LoggedParticipant) {
        $radio_IsRegistered = sx_RegisteredForThisConference($int_ConferenceID, $int_ParticipantID);
        if ($radio_IsRegistered) {
            $radioShowPaperAttachments = true;
        }
    }

    if (intval($int_ConferenceID) > 0) {
        $strParameter = true;
        if ($int_SessionID == 0) {
            $strParameter = false;
        }
        $arC = sx_getConferenceAttachments($int_ConferenceID);
        $arS = sx_getSessionAttachments($int_ConferenceID, $int_SessionID);

        $arP = "";
        if ($radioShowPaperAttachments) {
            $arP = sx_getPaperAttachments($int_ConferenceID, $int_SessionID);
        }

        echo sx_showAttachments($arC, $arS, $arP, 'Media', $strParameter);

        $arC = sx_getConferenceAttachments($int_ConferenceID, 'PDF');
        $arS = sx_getSessionAttachments($int_ConferenceID, $int_SessionID, 'PDF');

        $arP = "";
        if ($radioShowPaperAttachments) {
            $arP = sx_getPaperAttachments($int_ConferenceID, $int_SessionID, 'PDF');
        }

        echo sx_showAttachments($arC, $arS, $arP, 'PDF', $strParameter);
    }
}
