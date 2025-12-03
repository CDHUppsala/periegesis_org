<?php
include realpath(dirname(dirname(__DIR__)) . "/functionsLanguage.php");
include PROJECT_ADMIN . "/login/lockPage.php";
include PROJECT_ADMIN . "/login/adminLevelPages.php";
include PROJECT_ADMIN . "/functionsDBConn.php";

include "functions.php";

$radioCreateXML = False;
$xmlTableName = trim(@$_POST["xmlTable"]);
$strTypeXML = @$_POST["TypeXML"];
if ($strTypeXML == "XML") {
	$radioCreateXML = True;
}


$iColCount = 0;
$aResults = null;
if (!empty($xmlTableName)) {
	$strWhere = "";
	$strOrderBy = "";
	if (!empty(@$_POST["WhereStatement"])) {
		$strWhere = " WHERE " . str_replace("where", "", strtolower(@$_POST["WhereStatement"]));
	}
	if (!empty(@$_POST["OrderByStatement"])) {
		$strOrderBy = " ORDER BY " . str_replace("order by", "", strtolower(@$_POST["OrderByStatement"]));
	}

	$fieldNames = array();
	$fieldTypes = array();

	$sql = "SELECT * FROM " . $xmlTableName . $strWhere . $strOrderBy . " LIMIT 1";
	$stmt = $conn->query($sql);
	$iColCount = $stmt->columnCount();
	for ($c = 0; $c < $iColCount; $c++) {
		$meta = $stmt->getColumnMeta($c);
		$fieldNames[] = $meta['name'];
		$fieldTypes[] = $meta['native_type'];
	}
	$stmt = null;
}

if (floor($iColCount) > 0) {
	if ($radioCreateXML) {
		/**
		 * The start content of the XML file
		 */
		$doc = new DOMDocument('1.0', 'utf-8');
		$doc->preserveWhiteSpace = true;
		$doc->formatOutput = true;

		$ce_Table = $doc->createElement('table');
		$ce_Table->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$ce_Table->setAttribute('xsi:noNamespaceSchemaLocation', 'sx_' . $xmlTableName . '.xsd');
		$doc->appendChild($ce_Table);

		$sql = "SELECT * FROM " . $xmlTableName . $strWhere . $strOrderBy;
		$stmt = $conn->query($sql);

		while ($row = $stmt->fetch()) {

			$ce_TableName = $doc->createElement($xmlTableName);
			$ce_Table->appendChild($ce_TableName);
			for ($c = 0; $c < $iColCount; $c++) {
				$xName = $fieldNames[$c];
				$xType = $fieldTypes[$c];
				$xValue = $row[$xName];
				if ($xType == "VAR_STRING" || $xType == "STRING" || $xType == "BLOB") {
					//$xValue = sx_getEntityReference($xValue);
				} elseif (is_numeric($xValue)) {
					if (strpos($xValue, ",") > 0) {
						$xValue = str_replace(",", ".", $xValue);
					}
				}
				$ce_Column = $doc->createElement($xName, $xValue);
				$ce_TableName->appendChild($ce_Column);
			}
		}
		header('Content-Disposition: attachment;filename=sx_' . $xmlTableName . "_" . date("Y-m-d") . '.xml');
		header('Content-Type: text/xml');
		echo $doc->saveXML();
		$stmt = null;
	} else {
		$stmt = null;
		/**
		 * Open the content of the XSD file
		 */
		$strXSD = '<?xml version="1.0" encoding="utf-8"?>';
		$strXSD = '<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">';
		$strXSD .= '<xsd:element name="table">';
		$strXSD .= "<xsd:complexType>";
		$strXSD .= "<xsd:sequence>";
		$strXSD .= '<xsd:element ref="' . $xmlTableName . '" minOccurs="0" maxOccurs="unbounded"/>';
		$strXSD .= "</xsd:sequence>";
		$strXSD .= "</xsd:complexType>";
		$strXSD .= "</xsd:element>";
		$strXSD .= '<xsd:element name="' . $xmlTableName . '">';
		$strXSD .= "<xsd:complexType>";
		$strXSD .= "<xsd:sequence>";

		//== Get the content of the XSD file as string
		for ($i = 0; $i < $iColCount; $i++) {
			$xName = $fieldNames[$i];
			$xType = $fieldTypes[$i];
			$strXSD = $strXSD . '<xsd:element name="' . $xName . '" type="' . sx_getXSD_DataTypes($xType) . '"/>';
		}
		//== Close the content of the XSD file
		$strXSD .= "</xsd:sequence>";
		$strXSD .= "</xsd:complexType>";
		$strXSD .= "</xsd:element>";
		$strXSD .= "</xsd:schema>";

		$doc = new DOMDocument('1.0', 'utf-8');
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->loadXML($strXSD);

		header('Content-Disposition: attachment;filename=sx_' . $xmlTableName . '.xsd');
		header('Content-Type: text/xml');
		echo $doc->saveXML();
	}
} else {
	$stmt = null;
?>
	<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>

	<body>
		<div style="margin: 40px 300px; font-family: verdana; font-size: 11px">
			<h4>Λάθος στις επιλογές όρων</h4>
			<p>Κλείσε την σελίδα και προσπάθησε ξανά!</p>
		</div>
	</body>

	</html>
<?php
} ?>