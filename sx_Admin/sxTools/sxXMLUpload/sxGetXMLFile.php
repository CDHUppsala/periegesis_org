<?php

$radioLocalFile = false;
// $strUploadXMLFileName is only used for information
$strUploadXMLFileName = '';
$strUploadXMLFile = "";
$request_Table = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['LocalFile']) &&  $_FILES['LocalFile']['error'] === UPLOAD_ERR_OK) {
        // Use the temporal name which includes the path to windows temporal files
        $strUploadXMLFileName = $_FILES["LocalFile"]["name"];;
        $_SESSION["UploadFileName"] = $strUploadXMLFileName;
        $strUploadXMLFile = $_FILES["LocalFile"]["tmp_name"];;
        $_SESSION["UploadFile"] = $strUploadXMLFile;
        $radioLocalFile = true;
        $_SESSION["LocalFile"] = true;
    } elseif (isset($_POST["ServerFile"]) && !empty($_POST["ServerFile"])) {
        $strUploadXMLFile = PATH_ToImportFolder . $_POST["ServerFile"];
        $_SESSION["UploadFile"] = $strUploadXMLFile;
        $strUploadXMLFileName = $strUploadXMLFile;
        $_SESSION["UploadFileName"] = $strUploadXMLFileName;
    }
    if (!empty($_POST["TableName"])) {
        $request_Table = $_POST["TableName"];
        $_SESSION["TableName"] = $request_Table;
    }
}
if (isset($_SESSION["UploadFile"])) {
    $strUploadXMLFileName = $_SESSION["UploadFileName"];
    $strUploadXMLFile = $_SESSION["UploadFile"];
    if (!empty($_SESSION["LocalFile"])) {
        $radioLocalFile = true;
    }
}
if (isset($_SESSION["TableName"])) {
    $request_Table = $_SESSION["TableName"];
}

if (empty($request_Table) || empty($strUploadXMLFile) || !empty($_GET["clean"]) || !empty($_POST["clean"])) {
    $strUploadXMLFile = "";
    $request_Table = "";
    unset($_SESSION["TableName"]);
    unset($_SESSION["UploadFile"]);
    unset($_SESSION["LocalFile"]);
    unset($_SESSION["UploadFileName"]);
}

// Define constants for use in functions
define("CON_Table", $request_Table);
define("CON_XML_File_Path", $strUploadXMLFile);

?>

<form method="POST" name="SelectFileAndTable" action="default.php" enctype="multipart/form-data">
    <fieldset class="row">
        <div>
        <label><?= lngSelectTable ?>:</label><br>
            <select size="1" name="TableName">
                <option value="">Select Table</option>
                <?php
                $rs = sx_getTableList();
                foreach ($rs as $table) {
                    $loopTable = $table[0];
                    $strSelected = "";
                    if ($loopTable == $request_Table) {
                        $strSelected = "selected ";
                    } ?>
                    <option <?= $strSelected ?>value="<?= $loopTable ?>"><?= $loopTable ?></option>
                <?php
                    $rs = null;
                }
                ?>
            </select>
            <?php
            $rs = null;
            ?>
        </div>
        <?php
        if (!empty(PATH_ToImportFolder)) {
            $strXMLFiles = sx_getFolderFilesByExtention(PATH_ToImportFolder, "xml");
        ?>
            <div>
                <label>Select Remote File:</label><br>
                <select Name="ServerFile">
                    <option VALUE="">Select File</option>
                    <?php
                    if (is_array($strXMLFiles)) {
                        $iCount = count($strXMLFiles);
                        for ($i = 0; $i < $iCount; $i++) {
                            $loopFile = $strXMLFiles[$i];
                            $strSelected = "";
                            if ($loopFile == $strUploadXMLFileName) {
                                $strSelected = "selected";
                            } ?>
                            <option VALUE="<?= $loopFile ?>" <?= $strSelected ?>><?= $loopFile ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
        <?php
        }
        $importFileName = '';
        if ($radioLocalFile && !empty($strUploadXMLFileName)) {
            $importFileName = $strUploadXMLFileName;
        } ?>
        <div>
            <label>Select Local File
                <?php
                if (!empty($importFileName)) {
                    echo "<span>(Last: $importFileName)</span>";
                } ?>
            </label><br>
            <input type="file" name="LocalFile" id="LocalFile" />
        </div>
    </fieldset>

    <fieldset>
        <input type="submit" value="<?= lngReset ?>" name="clean">
        <input type="submit" value="<?= lngOpenArchive ?>" name="SelectFileTable">
    </fieldset>
</form>
<?php
if (!empty($strUploadXMLFile)) { ?>

    <h2>XML-File: Parent and Children Elements</h2>
    <div class="textBG">
        <?php
        libxml_use_internal_errors(true);
        $xmlDoc = new DOMDocument('1.0', 'utf-8');
        $xmlDoc->preserveWhiteSpace = false;
        $xmlDoc->formatOutput = true;
        $xmlDoc->validateOnParse = false;

        if ($xmlDoc->load($strUploadXMLFile) === false) {
            echo "<h2>Error in XML-File</h2>";
            foreach (libxml_get_errors() as $error) {
                echo $error->message;
            }
            echo "<p>Check if there is any space on the top of the XML-document";
            exit();
        }

        /*
        foreach ($xmlDoc->documentElement->childNodes as $node) {
            // Check if the node is an element node
            if ($node->nodeType == XML_ELEMENT_NODE) {
                foreach ($node->childNodes as $subnode) {
                    echo "Node Name: " . $subnode->nodeName . ", Node Value: " . $subnode->nodeValue . "<br>";
                }
            }
        }

        $dom = new DOMDocument();
        $dom->load('file.xml');
        $simplexml = simplexml_import_dom($dom);
        echo $simplexml->user->name;
        */

        $xml = $xmlDoc->documentElement;
        $iXMLTables = count($xml->childNodes);
        $sXMLFirstChild = $xml->childNodes->item(0);
        //$sXMLFirstChild = $xml->firstChild;
        $iXMLFields = $sXMLFirstChild->childNodes->length;
        $sXMLTableName = $sXMLFirstChild->nodeName;

        $arrXMLNodeNames = array();
        $arrXMLNodeValues = array();
        foreach ($sXMLFirstChild->childNodes as $subItem) {
            $sNodeName = $subItem->nodeName;
            $sNodeValue = $subItem->nodeValue;

            $arrXMLNodeNames[] = $sNodeName;
            $arrXMLNodeValues[] = $sNodeValue;
        }

        echo "<b>The name of Parent Element (Row):</b> " . $sXMLTableName . " ";
        echo "<br><b>Number of Parent Elements:</b> " . $iXMLTables . "<br>";
        echo "<b>Number of Child Elements (Fields):</b> " . $iXMLFields . "<br>";
        echo "<b>The Name of Childe Elements:</b> " . implode(", ", $arrXMLNodeNames);
        $xmlDoc = null;
        ?>
    </div>
<?php
}
$radioUseThis = true;
if (!empty($request_Table) && $radioUseThis) {
    $strFieldNames = implode(", ", $arrXMLNodeNames);

    $arrFieldNamesAndTypes = sx_getFieldNamesAndTypes($request_Table);
    $arrTableFieldNames = $arrFieldNamesAndTypes[0];
    $arrTableFieldTypes = $arrFieldNamesAndTypes[1];
    $arrFieldNamesAndTypes = null;
    /*
	The arrays are used by the next inpu file
	*/
}

if (empty($request_Table)) { ?>
    <div class="text">
        <p>Οι πληροφορίες σε ένα XML-αρχείο είναι οργανωμένες σε γονικά (Parent) και παιδικά (Child) στοιχεία (Element).</p>
        <p>Το όνομα του γονικού στοιχείου θα πρέπει να είναι ίδιο με το όνομα του πίνακα,
            ενώ το όνομα των παιδικών στοιχείων θα πρέπει να είναι ίδια με το όνομα των πεδίων του πίνακα.</p>
        <p>Η Access και άλλες Βάσεις Δεδομένων καθώς και τα προγράμματα επεξεργασίας XML-αρχείων (π.χ. Microsoft Excel)
            αποθηκεύουν έναν πίνακα σε XML-αρχείο σύμφωνα με την παραπάνω οργάνωση.</p>
        <p><b>Προσοχή!</b> Aποθήκευσε, από τα παραπάνω προγράμματα, πάντα ένα <b>καθαρό</b> XML-αρχείο (XML Data).</p>
    </div>
<?php } ?>