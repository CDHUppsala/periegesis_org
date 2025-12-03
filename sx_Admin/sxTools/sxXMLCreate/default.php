<?php
include realpath(dirname(dirname(__DIR__)) ."/functionsLanguage.php");
include PROJECT_ADMIN ."/login/lockPage.php";
include PROJECT_ADMIN ."/login/adminLevelPages.php";
include PROJECT_ADMIN ."/functionsDBConn.php";

include "functions.php"; 
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Public Sphere - Creation XML Files from Database</title>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</head>

<body class="body">
	<header id="header">
		<h2>Public Sphere: - Create XML-Files from Tables in Database</h2>
	</header>
	<h1>Δημιουργία XML-αρχείου και XSD-αρχείου</h1>
	<div class="maxWidth">
		<form target="_blank" method="POST" name="chooseTable" action="sxCreateXMLFile.php">
			<fieldset>
				<table id="formTable" border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td align="right">Επίλεξε πίνακα από την βάση δεδομένων:</td>
						<td colspan="2">
							<select size="1" name="xmlTable">
								<option value="">Select Table</option>
								<?php
								$rs = sx_getTableList();
								foreach ($rs as $table) {
									$loopTable = $table[0];
									$strSelected = "";
									if ($loopTable == @$request_Table) {
										$strSelected = "selected ";
									} ?>
									<option <?= $strSelected ?>value="<?= $loopTable ?>"><?= $loopTable ?></option>
								<?php
									$rs = null;
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right">Statement WHERE: </td>
						<td colspan="2"><input type="text" name="WhereStatement" value="" size="40"></td>
					</tr>
					<tr>
						<td align="right">Statement ORDER BY: </td>
						<td colspan="2"><input type="text" name="OrderByStatement" value="" size="40"></td>
					</tr>
					<tr>
						<td align="right" valign="top">Τύπος Αρχείου: </td>
						<td>
							<input type="radio" name="TypeXML" value="XML" checked>XML<br>
							<input type="radio" name="TypeXML" value="XSD">XSD
						</td>
						<td align="right"><input type="submit" value="Δημιούργησε XML" name="xml"></td>
					</tr>
				</table>
			</fieldset>
		</form>

		<h2>Σύντομες οδηγίες</h2>
		<div class="text paddingBottom">
			<p><b>Μπορείς να ανανεώσεις ταυτόχρονα όλες τις καταγραφές κάθε πίνακα της βάσης δεδομένων μέσω ενός XML-αρχείο:</b></p>
			<ol>
				<li>Επίλεξε πρώτα έναν <b>πίνακα</b>, μετά τον <b>τύπο</b> του αρχείου και κάνε τέλος κλικ στο <b>Δημιούργησε XML</b>.
					<ul type="disc">
						<li>Αν γνωρίζεις τα ονόματα των πεδίων του πίνακα, και λίγο από την γλώσσα<b> SQL, </b>μπορείς να κάνεις
							κάποιες επιλογές όρων (Statement <b>WHERE</b>) και ταξινόμησης (Statement <b>ORDER BY</b>). Χρησιμοποίησε τις δυνατότητες αυτέ με προσοχή, καθώς μπορούν να παράγουν λάθος.</li>
						<li>Γράψε τους όρους και τα κριτήρια χωρίς τους προσδιορισμούς <b>WHERE</b> και <b>ORDER BY</b>.</li>
						<li>Οι επιλογές όρων και ταξινόμησης αφορούν μόνον στο <b>XML-Αρχείο</b>.</li>
					</ul>
				</li>
				<li>Δημιούργησε ξεχωριστά το XML-αρχείο και το XSD-αρχείο.
					<ul type="disc">
						<li>Και τα δύο αρχεία <b>δημιουργούνται</b> στο Server και θα σου δοθεί η επιλογή να τα <b>αποθηκεύσεις</b> στο υπολογιστή σου για περαιτέρω χρήση.</li>
						<li>Δες στο κάτω μέρος του φυλλομετρητή σου για την επιλογή <b>αποθήκευσης</b>.</li>
					</ul>
				</li>
				<li>Το XSD-αρχείο προσδιορίζει τον τύπο πληροφοριών (αριθμός, ημερομηνία, κείμενο, κλπ) του κάθε στοιχείου του XML-αρχείου
					(ή πεδίου του πίνακα της Βάσης Δεδομένων) και χρησιμοποιείτε αυτόματα από προγράμματα που ανοίγουν το XML-αρχείο (Excel, κλπ.)
					για να προσδιορίσουν τον τύπο του κάθε πεδίου.
					<ul type="disc">
						<li>Τα αρχεία πρέπει επομένως να αποθηκευτούν στον <b>ίδιο φάκελο</b>.</li>
					</ul>
				</li>
				<li>Επεξεργάσου το XML-αρχείο μέσω ενός XML-Editor (π.χ. Microsoft Excel) και αποθήκευσέ το ξανά σε <b>καθαρή μορφή </b>
					XML, χωρίς σχεδιασμούς (από το Microsoft Excel, π.χ., επίλεξε Data + XML + Export).</li>
				<li>Από την διαχείριση (Εργαλεία + Φόρτωση αρχείων) φόρτωσε το αρχείο στον φάκελο
					<b>ArchiveImport</b>. Αν το αρχείο είναι μεγάλο (&gt;= 10ΜΒ), πρέπει να το φορτώσεις με άλλο τρόπο. Επικοινώνησε με τον διαχειριστή του SERVER.</li>
				<li>Κάνε κλικ στην εφαρμογή Upload XML Files, άνοιξε το αρχείο που φόρτωσες στο ArchiveImport και ακολούθησε τις οδηγίες για να ανανεώσεις τον αντίστοιχο
					πίνακα της Βάσης Δεδομένων.</li>
				<li>
					<div class="msgInfo"><b>Obs!</b> Δημιούργησε ένα <b>αντίγραφο ασφαλείας</b> της βάσης δεδομένων πριν από κάθε ανανέωση ενός πίνακα (Εργαλεία + Backup Βάση Δεδομένων)</div>
				</li>
			</ol>
		</div>
	</div>

</body>

</html>