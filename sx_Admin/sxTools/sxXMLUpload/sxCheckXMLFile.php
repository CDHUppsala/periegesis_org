<?php
if (!empty($request_Table)) {
	if (!empty(@$_POST["Update"]) && intval($intUpdateableFields) <= intval($iXMLFields)) {
		sx_updateArchiveToDatabase($paramPrimaryKeyType);
	}
	if (!empty(@$_POST["AddToDB"]) && intval($intUpdateableFields) <= intval($iXMLFields)) {
		sx_addArchiveToDatabase();
	}
	?>
	<div class="text padding paddingLeft">
		<?php
			/**
			 * Check the First Node of the XML-File
			 */

			if ($radioCheckFirstNodeCompatibility) { ?>
			<h2>Έλεγχος πρώτου κόμβου στοιχείων του XML-αρχείου.</h2>
			<?php
					if (@$radioFirstNodeError) { ?>
				<div class="errMsg">
					<?php if (!empty(@$strFirstNodeErrors)) {
									echo "<p><b>" . lngIncompatibleValuesFieldsType . "</b>: <br>" . $strFirstNodeErrors . "</p>";
									//echo "<p><b>Υπέρβαση πεδίων ή ασύμβατα ονόματα!</b><br>";
									echo "Αριθμός ανανεώσιμων πεδίων του πίνακα = " . @$intUpdateableFields . "<br>";
									echo "Αριθμός στοιχείων του XML-αρχείου = " . @$iXMLFields . "</p>";
								} ?>
				</div>
			<?php
					} else {
						?>
				<div class="text">
					<b><?= lngCompatibleNamesAndFieldsType ?></b><br>
					<?php echo "Αριθμός ανανεώσιμων πεδίων = " . @$intUpdateableFields . "<br>";
								echo "Αριθμός στοιχείων του XML-αρχείου = " . @$iXMLFields ?>
				</div>
			<?php
					} ?>
			<div class="text">
				<ol>
					<li>Ο αριθμός ανανεώσιμων πεδίων του πίνακα μπορεί να είναι μικρότερος αλλά όχι μεγαλύτερος από τον
						αριθμός στοιχείων του XML-αρχείου. Τα ονόματα των πεδίων του πίνακα και των στοιχείων του XML-αρχείου πρέπει να είναι ταυτόσημα.<br>
						<br>
						<b>Στην περίπτωση υπέρβασης πεδίων ή ασύμβατων ονομάτων:</b> Από την στήλη <b>Επίλεξε</b>, κατάργησε τις επιλογές ελέγχου όλων των πεδίων του πίνακα που είναι σημειωμένα με κόκκινο πλαίσιο. Αν επέλεξες το &quot;Πρόσθεσε όλα τα στοιχεία του XML-αρχείου&quot;, κατάργησε την επιλογή.</li>
					<li>Οι τιμές του κάθε στοιχείου του XML-αρχείου θα πρέπει να είναι του ίδιου τύπου
						(κείμενο, αριθμός, νόμισμα, ημερομηνία, κλπ.) με το αντίστοιχο πεδίο του πίνακα.<br>
						<br>
						<b>Στην περίπτωση ασύμβατης σχέσης τύπου:</b> Πρέπει να διορθώσεις τις τιμές των αντίστοιχων στοιχείων του XML-αρχείου.</li>
				</ol>
			</div>
		<?php }

			/**
			 * Check Unique values in XML-File and in Table
			 */


			if (strval(@$_POST["AutoField"]) != strval(@$_GET["PrimeKey"])) { ?>

			<h2>Έλεγχος τις μοναδικότητας τιμών του πρωταρχικού κλειδιού.</h2>

			<?php
					if (@$radioXMLUniquePKErrors || @$radioTableUniquePKErrors) {
						?>
				<div class="text">
					Αν οι τιμές του πρωταρχικού κλειδιού δεν είναι μοναδικές πρέπει να επιλέξεις ένα άλλο
					πρωταρχικό κλειδί ή να αλλάξεις τις τιμές στον Πίνακα της Βάσης Δεδομένων και στο XML-αρχείο.
				</div>
				<?php
							if (@$radioXMLUniquePKErrors) {
								?>
					<div class="errMsg">
						<b>Οι παρακάτω τιμές του πρωταρχικού κλειδιού στο XML-αρχείο δεν είναι μοναδικές:</b><br><br>
						<?php
										echo $strXMLUniquePKErrors;
										?>
					</div>
				<?php
							}
							if (@$radioTableUniquePKErrors) {
								?>
					<div class="errMsg">
						<b>Οι παρακάτω τιμές του πρωταρχικού κλειδιού στον πίνακα της Βάση Δεδομένων δεν είναι μοναδικές:</b><br><br>
						<?php
										echo $strTableUniquePKErrors;
										?>
					</div>
				<?php
							}
						} else {
							?>
				<div class="textMsg">
					Οι τιμές του πρωταρχικού κλειδιού είναι μοναδικές.
				</div>
			<?php
					}
				}

				/**
				 * Check the compatibility of the entire XML-File
				 */


				if ($radioCheckXMLFileCompatibility) {
					?>
			<h2>Έλεγχος όλων των κόμβων στοιχείων του XML-αρχείου.</h2>
			<?php
					if ($radioXMLTypeErrors) {
						?>
				<div class="errMsg">
					<?php if (!empty(@$strXMLTypeErrors)) {
									echo "<b>" . lngIncompatibleValuesFieldsType . "</b>: <br>" . $strXMLTypeErrors . "<br><br>";
								} ?>
				</div>
			<?php
					} else {
						?>
				<div class="textMsg">
					<b><?= lngCompatibleNamesAndFieldsType ?></b>
				</div>
			<?php
					}
				}

				/**
				 * Update Messages
				 */

				if (!empty(@$_POST["Update"])) { ?>

			<h2>Ανανέωση της Βάσης Δεδομένων</h2>
			<?php
					if ($radioSuccessfulUpdate) {
						$strPartial = "";
						$strInfoPartial = "";
						if (intval($intNonUpdatedRecords) > 0) {
							$strPartial = '<span style="color:#c00">μερική</span>';
							$strInfoPartial = '<br><b><span style="color:#c00">Κάποιες καταγραφές του XML-αρχείου δεν ανανεώθηκαν:</span></b><br>';
							$strInfoPartial = $strInfoPartial . "Σύνολο μη ανανεωμένων καταγραφών: " . $intNonUpdatedRecords . "<br>";
							$strInfoPartial = $strInfoPartial . "Δεν βρέθηκε αντίστοιχο <b>Πρωτραχικό Κλειδί</b> ή <b>Όνομα Πεδίου</b> στον Πίνακα της βάσης για τις παρακάτω καταγραφές του XML-αρχείου:";
							$strInfoPartial = $strInfoPartial . '<div class="msgError">' . $strNonUpdatedRecords . "</div>";
						}
						?>
				<div class="textMsg">
					<b>Η ανανέωση της βάσης δεδομένων έγινε με <?= $strPartial ?> επιτυχία:</b><br>
					Σύνολο ανανεωμένων καταγραφών: <?= $intUpdates ?><br>
					<?php if(@$sxNobody) { ?>
					Σύνολο αποτυχιμένων καταγραφών: <?= @$intUpdateErrors ?><br>
					Σύνολο (μη σημαντικών) λαθών τύπου πεδίων: <?//= $intFieldTypeErrors . '<div style="padding: 5px 20px; color: #000">' . $strErrorsDesc . "</div>" ?>
					<?php }?>
					<?= $strInfoPartial ?>
				</div>
			<?php
					} else {
						?>
				<div class="errMsg">
					<b>Η ανανέωση της βάσης δεδομένων απέτυχε</b>.<br>
					Σύνολο ανανεωμένων καταγραφών: <?= $intUpdates ?><br>
					Σύνολο αποτυχιμένων καταγραφών: <?= $intNonUpdatedRecords ?><br>
					Περιγραφή αποτυχημένων καταγραφών: <?= $strNonUpdatedRecords ?>
				</div>
			<?php
					}
				}

				/**
				 * Add Messages
				 */

				if (@$_POST["AddToDB"] != "") {
					?>
			<h2>Πρόσθεση στην Βάσης Δεδομένων</h2>
			<?php
					if ($radioSuccessfulAdding) {
						$strPartial = "";
						if ($intNonAddedRecords) {
							$strPartial = '<span style="color:#c00">μερική</span>';
						}
						?>
				<div class="textMsg">
					<b>Η πρόσθεση στη βάσης δεδομένων έγινε με <?= $strPartial ?> επιτυχία:</b><br>
					Σύνολο νέων καταγραφών που προστέθηκαν: <?= $intAdds ?><br>
					Σύνολο καταγραφών που δεν προστέθηκαν: <?= $intNonAddedRecords ?><br>
					<?php if ($intDouble > 0) {?>
					Σύνολο δiπλών καταγραφών που δεν προστέθηκαν: <?= $intDouble ?>.<br>
					<?php }?>
					Περιγραφή καταγραφών που δεν προστέθηκαν: 
					<div style="padding: 5px 20px"><?= $strNonAddedRecords ?></div>
				</div>
			<?php
					} else {
						?>
				<div class="errMsg">
					<b>Η πρόσθεση στη βάσης δεδομένων απέτυχε.</b><br><br>
					Σύνολο καταγραφών: <?= $intAdds + $intDouble ?>.<br>
					<?php if ($intDouble > 0) { ?>
						Σύνολο δυπλών καταγραφών που δεν προστέθηκαν: <?= $intDouble ?>.<br><br>
					<?php } ?>
					Περιγραφή καταγραφών που δεν προστέθηκαν: 
					<p style="padding-left: 40px"><?= $strNonAddedRecords ?></p>
				</div>
		<?php
				}
			}
			?>
	</div>
<?php } ?>