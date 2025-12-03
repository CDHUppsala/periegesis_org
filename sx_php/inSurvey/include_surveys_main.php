<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>

<?php
$intSurveyID = 0;
if (isset($_GET["surveyID"])) {
    $intSurveyID = (int) $_GET["surveyID"];
}

$intQuizThemeID = 0;
if (isset($_GET["quizThemeID"])) {
    $intQuizThemeID = (int) $_GET["quizThemeID"];
}


$strP = "";
if (isset($_GET["p"])) {
    $strP = $_GET["p"];
}
if ($strP == "p") {
    include __DIR__ . "/poll_results.php";
} elseif ($strP == "pa") {
    include __DIR__ . "/poll_archives.php";
} elseif ($strP == "s" and intval($intSurveyID) > 0) {
    include __DIR__ . "/survey_questions.php";
} elseif ($strP == "sr" and intval($intSurveyID) > 0) {
    include __DIR__ . "/survey_results.php";
} elseif ($strP == "sa") {
    include __DIR__ . "/survey_archives.php";
} elseif ($strP == "q" and intval($intQuizThemeID) > 0) {
    include __DIR__ . "/quiz_questions.php";
} elseif ($strP == "qr" and intval($intQuizThemeID) > 0) {
    include __DIR__ . "/quiz_results.php";
}else{
    include __DIR__ . "/graphics.php";
}
