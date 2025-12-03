<h1><span><?= $strMenuTitle ?></span></h1>

<?php
if ($strPDFMenuForm == "YearByCategory") {
    include "sxNav_PDFbyGroupYear.php";
} elseif ($strPDFMenuForm == "Year") {
    include "sxNav_PDFbyYear.php";
} else {
    include "sxNav_PDFByGroup.php";
}

if ($strPDFMenuForm == "Both") {
    include "sxNav_PDFbyYear.php";
}
?>