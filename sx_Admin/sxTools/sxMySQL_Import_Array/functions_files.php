<?php

function convertSimpleXmlToArray($xmlObject) {
    $array = [];
    foreach ($xmlObject as $key => $value) {
        // Check if the value is a SimpleXMLElement instance
        if ($value instanceof SimpleXMLElement) {
            // If it contains CDATA, text or number, convert it to string
            $array[$key] = (string)$value;
        } else {
            // Otherwise, process as normal
            $array[$key] = $value;
        }
    }
    return $array;
}

function normalizeArrayStructure($arrData)
{
    $referenceKeys = array_keys($arrData[0]);

    // Normalize all rows to match the reference keys
    return array_map(function ($row) use ($referenceKeys) {
        return array_merge(array_fill_keys($referenceKeys, null), $row);
    }, $arrData);
}

// If the value is an empty array, set it to null (or "")
function cleanEmptyFields($data)
{
    foreach ($data as $key => &$value) {
        if (is_array($value)) {
            if (empty($value)) {
                $value = '';
            } else {
                // Recursively clean arrays
                $value = cleanEmptyFields($value);
            }
        }
    }
    return $data;
}

// Remove BOM from XML string (if exists)
function removeBOM($string)
{
    // Check if the string starts with the UTF-8 BOM
    if (substr($string, 0, 3) == "\xEF\xBB\xBF") {
        $string = substr($string, 3);
    }
    return $string;
}


/**
 * @abstract : estimate the number of rows of a csv file by deviding 
 *  the size of the file with the average length in bytes of the first 20 rows
 */
function sx_estimate_csv_rows($fopen_csv, $fsize)
{
    $loop = 1;
    $sumBytes = 0;
    while (!feof($fopen_csv)) {
        if ($loop > 1) {
            $array = fgetcsv($fopen_csv, 0, ',', '"', '\\');
            $string = implode(',', $array);
            $sumBytes += strlen($string);
        }
        if ($loop >= 21) {
            break;
        }
        $loop++;
    }
    $iEvrage = $sumBytes / ($loop - 1);
    return round($fsize / $iEvrage);
}
