<?php
if (empty($str_PollTitle)) {
    $str_PollTitle = lngGallup;
}

$arrBBColor = [
    'rgb(255, 99, 132)',
    'rgb(75, 192, 192)',
    'rgb(54, 162, 235)',
    'rgb(255, 159, 64)',
    'rgb(255, 205, 86)',
    'rgb(153, 102, 255)',
    'rgb(201, 203, 207)',
    'rgb(51, 204, 204)',
    'rgb(51, 204, 132)',
    'rgb(51, 204, 232)'
];

$iTotal = 0;
$sql = "SELECT * FROM poll_questions 
		WHERE ShowInSite = True 
        ORDER BY QuestionID DESC LIMIT 1";
$stm = $conn->query($sql);
$rs = $stm->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $intQuestionID = $rs["QuestionID"];
    $strInsertDate = $rs["InsertDate"];
    $strTitle = trim(strip_tags($rs["VoteQuestion"]));
    $arrTitle = get_Lines_From_Title($strTitle,40);
    $title = "'". implode("','",$arrTitle) ."'";
    $iTotal = $rs["Total"];
    if (intval($iTotal) > 0) {
        $iColumns = $rs["NumberOfChoices"];
        $labels = array();
        $data = array();
        $percent = array();
        for ($i = 1; $i < $iColumns + 1; $i++) {
            $labels[] = $rs["Choice" . $i];
            $data[] = $rs["Vote" . $i];
            $percent[] = round(($rs["Vote" . $i] / $iTotal) * 100, 2);
        }
        $arrBGColor = array_slice($arrBBColor, 0, $iColumns);
    }
    $stm = null;
    $rs = null;
}
$strBGColor = implode("','", $arrBGColor);
$strLabels = implode("','", $labels);
$strData = implode(",", $data);
$strPercent = implode(",", $percent);
?>

<script>
    const data_poll = {
        labels: ['<?= $strLabels ?>'],
        datasets: [{
            label: ' % ',
            backgroundColor: ['<?= $strBGColor ?>'],
            data: [<?= $strPercent ?>]
        }, ]
    };
    window.onload = function() {
        new Chart('poll_bar', {
            type: 'bar',
            data: data_poll,
            options: {
                aspectRatio: 1,
                //maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: [<?= $title ?>],
                        font: {
                            size: 15,
                            weight: 'bold',
                        }
                    },
                    subtitle: {
                        display: true,
                        text: 'Percent of Total Votes: <?= $iTotal ?>',
                        padding: {
                            bottom: 10
                        }
                    }
                }
            }
        });
        new Chart('poll_doughnut', {
            type: 'doughnut',
            data: data_poll,
            options: {
                aspectRatio: 1,
                //maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: [<?= $title ?>],
                        font: {
                            size: 15,
                            weight: 'bold',
                        }
                    },
                    subtitle: {
                        display: true,
                        text: 'Percent of Total Votes: <?= $iTotal ?>'
                    }
                }
            }
        });
    }
</script>

<section>
    <h1 class="head"><span><?= $str_PollTitle . " " . $intQuestionID . ", " . lngDate . ": " . $strInsertDate ?></span></h1>
    <h3><?=$strTitle?></h3>
    <h4>Percent of Total Votes: <?= $iTotal ?></h4>
    <div class="chart_flex">
        <div class="chart_container">
            <canvas id="poll_bar"></canvas>
        </div>
        <div class="chart_container">
            <canvas id="poll_doughnut"></canvas>
        </div>
    </div>
</section>

<?php

$intSurveyID = 0;
$sql = "SELECT SurveyID, 
        InsertDate,
        SurveyTheme" . str_LangNr . " AS SurveyTheme,
        SurveyNote" . str_LangNr . " AS SurveyNotes
    FROM surveys 
    ORDER BY SurveyID DESC LIMIT 1 ";
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rs) {
    $intSurveyID = $rs["SurveyID"];
    $strInsertDate = $rs["InsertDate"];
    $strSurveyTheme = $rs["SurveyTheme"];
    $strSurveyNotes = $rs["SurveyNotes"];
}
$stmt = null;
$rs = null;


$sql = "SELECT * FROM survey_questions 
	WHERE SurveyID = ?
    $strLanguageAnd
	ORDER BY SurveyQuestionID ASC ";
$stmt = $conn->prepare($sql);
$stmt->execute([$intSurveyID]);
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (is_array($rs)) {
    $iTotal = $rs[0]["Total"];
    $iRows = count($rs);
    if ($iTotal > 0) {

        if (empty($str_SurveyTittle)) {
            $str_SurveyTittle = lngSurvey;
        } ?>
        <section>
            <h1 class="head"><span><?= $str_SurveyTittle . " " . $intSurveyID . ", " . lngDate . ": " . $strInsertDate ?></span></h1>
            <h2 class="head"><span><?= lngTheme ?>: <?= $strSurveyTheme ?></span></h2>
            <?php
            if ($strSurveyNotes != "") { ?>
                <div><?= $strSurveyNotes ?></div>
            <?php
            }
            for ($r = 0; $r < $iRows; $r++) { ?>
            <h4><?=$rs[$r]["SurveyQuestion"] ?></h4>
                <div class="chart_flex">
                    <div class="chart_container">
                        <canvas id="survey_bar_<?= $r ?>"></canvas>
                    </div>
                    <div class="chart_container">
                        <canvas id="survey_doughnut_<?= $r ?>"></canvas>
                    </div>
                </div>
            <?php
            } ?>
        </section>

        <script>
            <?php
            for ($r = 0; $r < $iRows; $r++) {
                $title = trim(strip_tags($rs[$r]["SurveyQuestion"]));
                $arrTitle = get_Lines_From_Title($title,50);
                $title = "'". implode("','",$arrTitle) ."'";
                $labels = array();
                $percent = array();
                $iColumns = $rs[$r]["NumberOfChoices"] + 1;
                for ($i = 1; $i < $iColumns; $i++) {
                    $labels[] = $rs[$r]["Choice" . $i];
                    $percent[] = round(($rs[$r]["Vote" . $i] / $iTotal) * 100, 2);
                }
                $arrBGColor = array_slice($arrBBColor, 0, $iColumns);
                $strBGColor = implode("','", $arrBGColor);
                $strLabels = implode("','", $labels);
                $strPercent = implode(",", $percent);
            ?>
                const data_<?= $r ?> = {
                    labels: ['<?= $strLabels ?>'],
                    datasets: [{
                        label: 'Percent of Total Votes: <?= $iTotal ?>',
                        backgroundColor: ['<?= $strBGColor ?>'],
                        data: [<?= $strPercent ?>]
                    }]
                };
                new Chart('survey_bar_<?= $r ?>', {
                    type: 'bar',
                    data: data_<?= $r ?>,
                    options: {
                        aspectRatio: 1,
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                                display: false
                            },
                            title: {
                                display: false,
                                text: [<?= $title ?>],
                                font: {
                                    size: 14,
                                    weight: 'bold',
                                    family: 'arial'
                                }
                            },
                            subtitle: {
                                display: true,
                                text: 'Percent of Total Votes: <?= $iTotal ?>',
                                padding: {
                                    bottom: 10
                                }
                            }
                        }
                    }
                });
                new Chart('survey_doughnut_<?= $r ?>', {
                    type: 'doughnut',
                    data: data_<?= $r ?>,
                    options: {
                        aspectRatio: 1,
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false,
                                text: [<?= $title ?>],
                                font: {
                                    size: 15,
                                    weight: 'bold',
                                }
                            },
                            subtitle: {
                                display: true,
                                text: 'Percent of Total Votes: <?= $iTotal ?>'
                            }
                        }
                    }
                });
            <?php
            }
        } ?>
        </script>
    <?php
}
$rs = null;
    ?>