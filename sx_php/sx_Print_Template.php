<?php

include __DIR__ . "/sx_config.php";
include __DIR__ . "/basic_MediaFunctions.php";
include __DIR__ . "/basic_PrintFunctions.php";

if (isset($_GET["export"])) {
    $strExport = $_GET["export"];
    $strSiteURL = $_SERVER["HTTP_HOST"];
    $sQuery = $_SERVER["QUERY_STRING"];
    $pos = strpos($sQuery, "&");
    if ($pos > 0) {
        $sQuery = substr($sQuery, 0, $pos);
        $sQuery = str_replace("=", "_", $sQuery);
        $strSiteURL .= "_" . $sQuery;
    }
} else {
    $strExport = "";
}

if (!empty($strExport)) {
    if ($strExport == "word") {
        header('Content-Description: File Transfer');
        header("Content-type: application/msword; charset=utf-8");
        header("Content-Disposition: attachment;Filename=" . $strSiteURL . ".doc");
    }
    if ($strExport == "html") {
        header("Content-Type: text/html");
        header("Content-Disposition: attachment; filename=" . $strSiteURL . ".html;");
        header("Content-Transfer-Encoding: binary");
    }
} ?>

<!DOCTYPE html>
<html lang="<?= sx_CurrentLanguage ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= str_SiteTitle ?></title>
    <style>
        html,
        body,
        table {
            font-family: "Trebuchet MS", Tahoma, Helvetica, sans-serif;
            font-size: 14pt;
            line-height: 140%;
        }

        h1,
        h2,
        h3,
        h4 {
            line-height: 120%;
            padding: 0;
            margin: 10pt 0
        }

        h1 {
            font-size: 24pt;
        }

        h2 {
            font-size: 20pt;
        }

        h3 {
            font-size: 16pt;
        }

        h4 {
            font-size: 12pt;
        }

        th {
            background: #ddd;
            color: #000;
            vertical-align: top;
        }

        td {
            vertical-align: top;
            padding: 2pt;
            border-bottom: 1px solid #ddd;
            ;
        }

        hr {
            clear: both;
        }

        a {
            text-decoration: none;
        }

        li {
            padding: 6pt;
        }

        img {
            width: 100%;
            height: auto;
        }

        .maxWidth {
            width: 40%;
            float: left;
        }

        .clear,
        .clearSpace,
        .line {
            clear: both;
            width: 100%;
        }
    </style>
</head>