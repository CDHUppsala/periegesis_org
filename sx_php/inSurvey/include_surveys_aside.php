<?php

if (sx_IncludeQuiz && $radio_UseQuiz) :
    require __DIR__ . "/quiz_lists.php";
    require __DIR__ . "/quiz_lists_archives.php";
endif;

if (sx_IncludeSurveys) {
    if ($radio_UsePoll) {
        require __DIR__ . "/poll_questions.php";
    };
    if ($radio_UseSurvey) :
        require __DIR__ . "/survey_lists.php";
    endif;
}
