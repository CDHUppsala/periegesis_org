<?php

$arr_FieldNames = array();
$arr_FieldTypes = array();
$strAutoField = sx_GetPrimaryKey(CON_Table);
$strSQL = "SELECT * FROM " . CON_Table;
$stmt = $conn->query($strSQL);
$iCountCol = $stmt->columnCount();
for ($c = 0; $c < $iCountCol; $c++) {
	$meta = $stmt->getColumnMeta($c);
	$arr_FieldNames[] = $meta['name'];
	$arr_FieldTypes[] = $meta['native_type'];
}
$stmt = null;

/**
 * Define the table fields/types that will be added or updated
 */
$arrUpdateableFields = array();
$arrUpdateableFieldsTypes = array();
$radioSelectAllFields = false;
/**
 * When all table fields are selected for update or add
 */
if (@$_POST["selectAllFields"] == "Yes") {
	$arrUpdateableFields = $arr_FieldNames;
	$arrUpdateableFieldsTypes = $arr_FieldTypes;
	$radioSelectAllFields = true;
}

?>
<h2>Επίλεξε πεδία του πίνακα αντίστοιχα του XML-αρχείου</h2>
<form method="POST" name="ChooseFields" action="default.php" enctype="multipart/form-data">
	<fieldset>
		<table style="width: 100%">
			<tr>
				<th>Table Fields</th>
				<th>Select</th>
				<th>Data Type</th>
				<th>Upload Key <span style="cursor: pointer" title="Must include unique values both in XML-File and in Table">[i]</span></th>
				<th>XML-File: Compatible Filds</th>
			</tr>
			<?php
			$intUpdateableFields = 0;
			$radioCheckFirstNodeCompatibility = False;
			$strFirstNodeErrors = "";
			$radioFirstNodeError = False;

			$paramPrimaryKeyType = "";

			for ($i = 0; $i < $iCountCol; $i++) {
				$xName = $arr_FieldNames[$i];
				$xType = $arr_FieldTypes[$i];
				$radioCheckThisField = False;
				//== Get the fields that will be updated or added in the database
				if ($radioSelectAllFields) {
					$radioCheckThisField = True;
					$intUpdateableFields = $iCountCol;
				} elseif (@$_POST[$xName] == "Yes") {
					$arrUpdateableFields[] = $xName;
					$arrUpdateableFieldsTypes[] = $xType;
					$radioCheckThisField = True;
					$intUpdateableFields++;
				}
				?>
				<tr>
					<td valign="top"><?= $xName ?> </td>
					<td valign="top">
						<?php
							if ($radioCheckThisField) {
								$checkBox = "checked";
							} else {
								$checkBox = "";
							} ?>
						<input type="checkbox" name="<?= $xName ?>" value="Yes" <?= $checkBox ?>><?= @$_POST[$xName] ?>
					</td>
					<td valign="top" nowrap><?= $xType ?>
						<?php if ($xName == $strAutoField) {
								echo '| <b style="color: #d60; cursor: pointer" title="Primary Key">PK</b>';
							} ?>
					</td>
					<td>
						<?php
							if (@$_POST["PrimeKey"] != "") {
								if (@$_POST["PrimeKey"] == $xName) {
									$paramPrimaryKeyType = $xType;
									$checkRadio = "checked";
								} else {
									$checkRadio = "";
								}
							} else {
								if ($xName == $strAutoField) {
									$checkRadio = "checked";
								} else {
									$checkRadio = "";
								}
							}
							?>
						<input type="radio" value="<?= $xName ?>" name="PrimeKey" <?= $checkRadio ?>>
					</td>
					<td>
						<?php
							//== Check the First Node value compatibility of the seleceted fields
							if (is_array($arrXMLNodeNames) && $radioCheckThisField) {
								$radioEnableCheckAction = true;
								$radioCheckFirstNodeCompatibility = True;
								$loopName = "";
								$loopValue = "";
								$checkInput = false;
								$elNumber = Null;
								//The arrays are defined in sxGetXMLFile.php
								$radioFieledFound = false; 
								for ($z = 0; $z < count($arrXMLNodeNames); $z++) {
									if ($xName == trim($arrXMLNodeNames[$z])) {
										$loopName = trim($arrXMLNodeNames[$z]);
										$loopValue = trim($arrXMLNodeValues[$z]);
										$elNumber = $z;
										$radioFieledFound = true; 
										break;
									}
								}
								if($radioFieledFound == false) {
									$strFirstNodeErrors .= " Field in Database Not fount in XML-File: <b>". $xName ."</b> |Type: <b>" . $xType ."</b><br>";
									$radioFirstNodeError = true;
									$checkInput = false;
                                }elseif (!empty($loopName) && !empty($loopValue)) {
									$checkInput = sx_checkTypeCompatibility($xType, $loopValue);
									if ($checkInput === False) {
										$radioFirstNodeError = true;
										$strFirstNodeErrors .= "Error in Data Type for the field: <b>". $xName ."</b> |Type: <b>". $xType ."</b> |Value: " . substr($loopValue, 0, 200) . "<br>";
									}
								} else {
									$checkInput = true;
								}
								$errWarn = "";
								if ($radioCheckThisField) {
									if ($checkInput == false) {
										$errWarn = 'style="border:1px solid #943; background: #e98"';
									} else {
										$errWarn = 'style="border:1px solid #596; background: #adb"';
									}
								} ?>
							<input <?= $errWarn ?> type="text" name="FieldName<?= $xName ?>" value="<?= $loopName ?>" size="26"></td>
				<?php
					} else { ?>
					<input type="text" name="FieldName<?= $xName ?>" value="" size="26"></td>
				<?php
					} ?>
				</tr>
			<?php
			}
			/**
			 * Free memory from XML-File array variables
			 */
			$arrXMLNodeNames = null;
			$arrXMLNodeValues = null;
			?>
		</table>
	</fieldset>
	<?php


	/**
	 * Disable all subsequent action if errors in the First Node of the XML-File 
	 */
	if($radioFirstNodeError) {
		$radioEnableCheckAction = false;
		$radioEnableUploadAction = false;
	}

	/**
	 * Check the compatibility of fields and values for the entire XML-File
	 * Disable all subsequent action if the XML-File is incompatible 
	 */
	$radioCheckXMLFileCompatibility = false;
	if (!empty($arrUpdateableFields) && !empty(@$_POST["CheckAllLines"])) {
		$radioCheckXMLFileCompatibility = true;
		$arrCheckXMLReturn = sx_checkEntireXMLFileCompatibility();
		$radioXMLTypeErrors = $arrCheckXMLReturn[0];
		$strXMLTypeErrors = $arrCheckXMLReturn[1];
		if ($radioXMLTypeErrors) {
			$radioEnableUploadAction = false;
		} else {
			$radioEnableUploadAction = true;
		}
	}

	/**
	 * Check the uniqness of values for the Upload Key:
	 * 		- Allways in the entire XML-File
	 * 		- Also in Table, if Upload Key ia different from Primary Key
	 * Disable all subsequent actions if values are Not unique 
	 */
	if (!empty(@$_POST["AutoField"]) || !empty(@$_POST["PrimeKey"])) {
		$arrCheckXMLUniquePK = sx_checkXMLForUniquePrimaryKeys(@$_POST["PrimeKey"]);
		$radioXMLUniquePKErrors = $arrCheckXMLUniquePK[1];
		$strXMLUniquePKErrors = $arrCheckXMLUniquePK[1];
		if ($radioXMLUniquePKErrors) {
			$radioEnableUploadAction = false;
			$radioEnableCheckAction = false;
		}
	}
	if ((@$_POST["AutoField"]) != (@$_POST["PrimeKey"])) {
		$arrCheckTableUniquePK = sx_checkTableForUniquePrimaryKeys(@$_POST["AutoField"], @$_POST["PrimeKey"]);
		$radioTableUniquePKErrors = $arrCheckTableUniquePK[1];
		$strTableUniquePKErrors = $arrCheckTableUniquePK[1];
		if ($radioTableUniquePKErrors) {
			$radioEnableUploadAction = false;
			$radioEnableCheckAction = false;
		}
	}

	?>
	<fieldset>
		<?php if (isset($_POST["selectAllFields"]) && $_POST["selectAllFields"] == "Yes") {
			$strChecked = "checked";
		} else {
			$strChecked = "";
		} ?>
		<p>Επίλεξε όλα τα στοιχεία του XML-αρχείου: <input <?= $strChecked ?> type="checkbox" name="selectAllFields" value="Yes"></p>
		<?php
        $tempFields = '';
        $tempTypes = '';
		if (!empty($arrUpdateableFields)) {
			$tempFields = implode(",", $arrUpdateableFields);
			$tempTypes = implode(",", $arrUpdateableFieldsTypes);
		}
		?>
		<input type="hidden" value="<?= $strAutoField ?>" name="AutoField">
		<input type="hidden" value="<?= $tempFields ?>" name="UpdateableFields">
		<input type="hidden" value="<?= $tempTypes ?>" name="UpdateableFieldsTypes">
		<input type="submit" value="Επίλεξε πεδία" name="Fields">

		<?php

		if ($radioEnableCheckAction) {
			$strDisable = "";
		} else {
			$strDisable = " disabled";
		} ?>
		<input <?= $strDisable ?> type="submit" value="Έλεγξε όλο το αρχείο" name="CheckAllLines">

		<?php
		if ($radioEnableUploadAction) {
			$strDisable = "";
		} else {
			$strDisable = "disabled";
		} ?>
		<input <?= $strDisable ?> type="submit" value="Update" name="Update">
		<input <?= $strDisable ?> type="submit" value="Insert" name="AddToDB">
	</fieldset>
</form>

<?php
if ($strAutoField != "") {
	$infoAutoField = $strAutoField;
	$infoPrimeKey = $strAutoField;
}
if (isset($_POST["PrimeKey"]) && !empty($_POST["PrimeKey"])) {
	$infoAutoField = strval($_POST["AutoField"]);
	$infoPrimeKey = strval($_POST["PrimeKey"]);
}
?>
<div class="alignRight">
	<b>Auto Field</b> = <?= $infoAutoField ?>
	<b>Primary Key</b> = <?= $infoPrimeKey ?>
</div>

<div id="tabsBG">
	<div id="tabs">
		<a data-id="layer1" class="selected" href="javascript:void(0)">Σύντομες οδηγίες</a>
		<a data-id="layer2" href="javascript:void(0)">Λεπτομερείς οδηγίες</a>
	</div>
	<div id="layer1" class="text" style="display: block">
		<ol>
			<li>Επίλεξε πρώτα το πεδίο που αποτελεί το <b>Πρωταρχικό Κλειδί</b> (Primary Key) του πίνακα. Κατά κανόνα <b>δεν χρειάζεται ποτέ</b>
				να κάνεις αυτήν την επιλογή, καθώς το <b>Πρωταρχικό Κλειδί</b> τίθεται αυτόματα ίσο με τον <b>Αύξοντα Αριθμό</b> (ID) του πίνακα
				(Τύπος πεδίου: 3 Auto).
				<ul>
					<li>Μπορείς να ανανεώσεις ή να προσθέσεις καταγραφές <b>μόνον</b> όταν το Πρωταρχικό Κλειδί έχει
						<b>μοναδικές</b> τιμές.<ul>
							<li><b>Ανανεώνεις</b> πεδία του πίνακα που έχουν <b>Πρωταρχικό Κλειδί</b> ίσο με το αντίστοιχο του <b>XML-αρχείου</b>.</li>
							<li><b>Προσθέτεις</b> πεδία στον πίνακα όταν το <b>Πρωταρχικό Κλειδί</b> του <b>XML-αρχείου</b> δεν υπάρχει στον πίνακα.
								Αν <b>κατά λάθος</b> πατήσεις <b>Πρόσθεσε</b> και όλα τα Πρωταρχικά Κλειδιά του XML-αρχείου υπάρχουν στον πίνακα, δεν θα προστεθεί τίποτα.</li>
						</ul>
					</li>
					<li>Αν καθορίσεις ως Πρωταρχικό Κλειδί πεδίο άλλο του Αύξοντα Αριθμού θα γίνει αυτόματα έλεγχος της
						μοναδικότητας των τιμών του πεδίου αυτού σε ολόκληρο το XML-αρχείο και τον πίνακα της βάσης δεδομένων.</li>
					<li>Χρησιμοποίησε πρωταρχικό κλειδί διαφορετικό του Αυτόματου αριθμού, μόνον όταν προσδιορίζεις τις καταγραφές
						του πίνακα με άλλο κριτήριο μοναδικών τιμών - π.χ. Κωδικός προϊόντων.</li>
				</ul>
			</li>
			<li>Επίλεξε μετά τα <b>πεδία του πίνακα</b> που θέλεις να ανανεώσεις/προσθέσεις σημειώνοντας το
				αντίστοιχο κουτάκι - ή σημείωσε το κουτάκι &quot;<b>Πρόσθεσε όλα τα στοιχεία του XML-αρχείου</b>&quot;.</li>
			<li>Κάνε κλικ στο &quot;<b>Επίλεξε πεδία</b>&quot;. Το πρόγραμμα θα ελέγξει αυτόματα
				την συμβατότητα των τιμών του πρώτου κόμβου του XML-αρχείου.</li>
			<li>Κάνε έπειτα κλικ στο &quot;<b>Έλεγξε όλα το αρχείο</b>&quot;, για τον έλεγχο συμβατότητας τιμών όλων των κόμβων του XML-αρχείου.
				<ul>
					<li>Μπορείς να ανανεώσεις/προσθέσεις καταγραφές <b>μόνον</b> όταν οι τιμές όλου
						του XML-αρχείου είναι συμβατές με τον Πίνακα της βάσης δεδομένων.</li>
				</ul>
			</li>
		</ol>
	</div>

	<div id="layer2" class="text" style="display: none;">
		<p>Το όνομα του <b>γονικού στοιχείου</b> πρέπει να είναι ίδιο με το όνομα του πίνακα,
			ενώ τα ονόματα των <b>παιδικών στοιχείων</b> θα πρέπει να είναι ίδια με τα ονόματα των πεδίων του πίνακα.</p>
		<ol>
			<li>Από την στήλη <b>Επίλεξε</b>, σημείωσε το πλαίσιο ελέγχου όλων των πεδίων του πίνακα που θέλεις να ανανεώσεις/προσθέσεις. Αν θέλεις να ανανεώσεις/προσθέσεις όλα τα πεδία, σημείωσε το πλαίσιο ελέγχου <b>Πρόσθεσε όλα τα στοιχεία του XML-αρχείου</b>.
				<ul type="disc">
					<li>Ή θα κάνεις ξεχωριστές επιλογές πεδίων του πίνακα ή θα προσθέσεις όλα τα στοιχεία του XML-αρχείου - δεν μπορείς δηλαδή να κάνεις ταυτόχρονα και τις δύο επιλογές.</li>
					<li>Τα πεδία του πίνακα που επιλέγεις θα πρέπει να υπάρχουν στο XML-αρχείο - να έχουν δηλαδή το ίδιο όνομα με τα παιδικά στοιχεία του XML-αρχείου. Αν επιλέξεις πεδία του πίνακα που δεν υπάρχουν στο XML-αρχείο, το πρόγραμμα θα σημειώσει τα ασύμβατα ονόματα σε
						<font color="#FF0000"><b>κόκκινο πλαίσιο</b></font>. Τα πεδία αυτά δεν μπορούν να ανανεωθούν και πρέπει επομένως να τα καταργήσεις από την επιλογή σου.</li>
					<li>Για απλή πληροφόρηση, όταν επιλέξεις <b>Πρόσθεσε όλα τα στοιχεία του XML-αρχείου</b>, τα πεδία του πίνακα που δεν υπάρχουν στο XML-αρχείο σημειώνονται σε <font color="#0000FF"><b>μπλε πλαίσιο</b></font>.</li>
				</ul>
			</li>
			<li>Από την στήλη <b>Κλειδί</b>, επίλεξε μετά το πεδίο του πίνακα που θα λειτουργεί ως <b>πρωτεύον κλειδί</b> (συνήθως ο αυτόματα αυξανόμενος αριθμός, σημειωμένος στον πίνακα ως AUTO). <ul type="disc">
					<li>
						<font color="#FF0000"><b>Προσοχή:</b></font> Η τιμή του πρωτεύοντος κλειδιού πρέπει πάντα να είναι μοναδική για κάθε καταγραφή, και στον πίνακα και στο XML-αρχείο. Το πρόγραμμα ελέγχει την μοναδικότητα της τιμής του πρωτεύοντος κλειδιού, αν είναι άλλο από τον
						αυτόματα αυξανόμενος αριθμό.
					</li>
					<li><b>Ανανέωση καταγραφών:</b> Μόνον οι καταγραφές
						του XML-αρχείου που είναι ίσες με το πρωτεύον κλειδί του πίνακα θα ανανεωθούν.
						Ο αυτόματος αριθμός και το πρωτεύον κλειδί (αν είναι άλλο) δεν ανανεώνεται.
						Πρέπει όμως να τα σημειώσεις ως ανανεώσιμα.</li>
					<li><b>Πρόσθεση καταγραφών:</b> Στον πίνακα θα προστεθούν μόνον οι καταγραφές του XML-αρχείου που έχουν μοναδικό πρωτεύον κλειδί. Αν στον πίνακα υπάρχει καταγραφή με τιμή πεδίου ίση του πρωτεύοντος κλειδιού, η καταγραφή του XML-αρχείου δεν θα προστεθεί.</li>
					<li><b>Ανανέωση και πρόσθεση καταγραφών:</b> Κάνε πρώτα κλικ στο <b>Ανανέωσε</b>, για να ανανεώσεις υπάρχουσες καταγραφές και έπειτα στο <b>Πρόσθεσε</b>, για να προσθέσεις νέες καταγραφές του XML-αρχείου.</li>
				</ul>
			</li>
			<li>Κάνε κλικ στο <b>Επίλεξε πεδία</b> για να ολοκληρώσεις τις επιλογές σου.</li>
			<li>Αν οι επιλογές είναι συμβατές με τα πεδία και τους μεταβλητές του πρώτου κύκλου του XML-αρχείου, κάνε κλικ στο <b>Έλεγξε όλο το αρχείο</b> για την επιβεβαίωση της συμβατότητας όλου του XML-αρχείου. Ο έλεγχος κρατά κάποιο χρόνο,
				ανάλογα με τον αριθμό των καταγραφών.</li>
			<li>Κάνε τέλος κλικ στο <b>Ανανέωσε</b> ή <b>Πρόσθεσε</b> για να μεταφέρεις τις πληροφορίες του XML-αρχείου στην Βάση Δεδομένων. </li>
		</ol>
	</div>
</div>