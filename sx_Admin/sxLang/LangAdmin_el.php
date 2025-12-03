<?php 
//===== 0611/0708/0809/0908/2015-01/2017-08
CONST lngVersion = "Version V.6";
// CONST lngProgramName = "Public Sphere Content Management System";
CONST lngProgramName = "Public Sphere CMS - Σύστημα Διαχείρισης Βάσης Δεδομένων";
CONST lngSiteTitle = "Public Sphere CMS - Σύστημα Διαχείρισης Βάσης Δεδομένων";
CONST lngLoginError = "Λάθος κωδικός ή όνομα χρήστη!";
 
//################################################
//#### LANGUAGE GREEK 
//################################################
CONST lngLanguage = "Γλώσσσα";
CONST lngProductCatalog = "Κατάλογος παραγγελιών";
CONST lngMaxItems = "Μέγιστος αριθμός τεμαχίων";
CONST lngFax = "FAX";
CONST lngTotalProducts = "Σύνολο προϊόντων";
CONST lngPostalCode = "Ταχυδρομικός κώδικας";
CONST lngSmallPhotos = "Μικρές φωτογραφίες";


function sx_getCapitals($strText) {
    $sxGR = array (
        array("ά","Ά","Α"),
        array("έ","Έ","Ε"),
        array("ή","Ή","Η"),
        array("ί","Ί","Ι"),
        array("ό","Ό","Ο"),
        array("ύ","Ύ","Υ"),
        array("ώ","Ώ","Ω")
    );
    for ($r = 0; $r < 7; $r++) {
        for ($c = 0; $c < 2; $c++) {
            if (strpos($strText,$sxGR[$r][$c],0) > 0) {
                $strText = str_replace($sxGR[$r][$c],$sxGR[$r][2],$strText);
            }
        }
    }
    if (strpos($strText,"ς",0) > 0) {
        $strText=str_replace("ς","Σ",$strText);
    }
    return mb_strtoupper($strText, "UTF-8");
}

$lng_MonthNames = array(
    "Ιανουάριος",
    "Φεβρουάριος",
    "Μάρτιος",
    "Απρίλιος",
    "Μάιος",
    "Ιούνιος",
    "Ιούλιος",
    "Αύγουστος",
    "Σεπτέμβριος",
    "Οκτώβριος",
    "Νοέμβριος",
    "Δεκέμβριος");
define("lng_MonthNames", $lng_MonthNames);

$lng_MonthNamesGen = array(
    "Ιανουαρίου",
    "Φεβρουαρίου",
    "Μαρτίου",
    "Απριλίου",
    "Μαϊου",
    "Ιουνίου",
    "Ιουλίου",
    "Αυγούστου",
    "Σεπτεμβρίου",
    "Οκτωβρίου",
    "Νοεμβρίου",
    "Δεκεμβρίου");
define("lng_MonthNamesGen", $lng_MonthNamesGen);

$lng_DayNames = array(
    "Δευτέρα",
    "Τρίτη",
    "Τετάρτη",
    "Πέμπτη",
    "Παρασκευή",
    "Σάββατο",
    "Κυριακή"
);
define("lng_DayNames", $lng_DayNames);

function getCapitals($strTitleText) {
 
	$strGrFont[1][0][0] =  "ά";
	$strGrFont[1][1][0] =  "Ά";
	$strGrFont[1][0][1] =  "Α";
	$strGrFont[2][0][0] =  "έ";
	$strGrFont[2][1][0] =  "Έ";
	$strGrFont[2][0][1] =  "Ε";
	$strGrFont[3][0][0] =  "ή";
	$strGrFont[3][1][0] =  "Ή";
	$strGrFont[3][0][1] =  "Η";
	$strGrFont[4][0][0] =  "ί";
	$strGrFont[4][1][0] =  "Ί";
	$strGrFont[4][0][1] =  "Ι";
	$strGrFont[5][0][0] =  "ό";
	$strGrFont[5][1][0] =  "Ό";
	$strGrFont[5][0][1] =  "Ο";
	$strGrFont[6][0][0] =  "ύ";
	$strGrFont[6][1][0] =  "Ύ";
	$strGrFont[6][0][1] =  "Υ";
	$strGrFont[7][0][0] =  "ώ";
	$strGrFont[7][1][0] =  "Ώ";
	$strGrFont[7][0][1] =  "Ω";
 
	for ($i=1; $i<=7; $i  ) {
		for ($x=0; $x<=1; $x  ) {
			if (strpos($strTitleText,$strGrFont[$i][$x][0]) > 0) {
				$strTitleText = str_replace($strGrFont[$i][$x][0],$strGrFont[$i][0][1],$strTitleText);
			}
		}
	}
	if (strpos($strTitleText,"ς") > 0) {
		$strTitleText = str_replace("ς","Σ",$strTitleText);
	}
	return  mb_strtoupper($strTitleText);
}
 
//Greek day names
//dim $sxDays[6];
$sxDays[0] = "Κυριακή";
$sxDays[1] = "Δευτέρα";
$sxDays[2] = "Τρίτη";
$sxDays[3] = "Τετάρτη";
$sxDays[4] = "Πέμπτη";
$sxDays[5] = "Παρασκευή";
$sxDays[6] = "Σάββατο";
 
//Greek month names
//dim $sxMonths[11];
$sxMonths[0] = "Ιανουάριος";
$sxMonths[1] = "Φεβρουάριος";
$sxMonths[2] = "Μάρτιος";
$sxMonths[3] = "Απρίλιος";
$sxMonths[4] = "Μάιος";
$sxMonths[5] = "Ιούνιος";
$sxMonths[6] = "Ιούλιος";
$sxMonths[7] = "Αύγουστος";
$sxMonths[8] = "Σεπτέμβριος";
$sxMonths[9] = "Οκτώβριος";
$sxMonths[10] = "Νοέμβριος";
$sxMonths[11] = "Δεκέμβριος";
 
//===================
//== Defines the language of the popcalendar
//===================
CONST langExtention = "_el";
 
CONST lngAdd = "Πρόσθεσε";
CONST lngAddAFileRow = "Πρόσθεσε μια σειρά";
CONST lngAddANewRecord = "Πρόσθεσε μια νέα καταγραφή";
CONST lngAddRecord = "Πρόσθεση καταγραφής	";
CONST lngAddress = "Διεύθυνση";
CONST lngAdvanceEdit = "Σύνθετη ανασύνταξη";
CONST lngAdvancePayment = "Προκαταβολή";
CONST lngAggregatedStatistics = "<b>Συνολικές</b> στατιστικές πληροφορίες";
CONST lngAll = "Όλες";
CONST lngAllDates = "Όλες οι ημερομηνίες";
CONST lngAllGroups = "Όλες οι ομάδες";
CONST lngAllowedFileTypes = "Επιτρεπόμενοι τύποι αρχείων";
CONST lngArchive = "Αρχείο";
CONST lngAscendingOrder = "Ανοιούσα τάξη";
CONST lngAsteriskFieldsRequired = "Η συμπλήρωση των πεδίων με αστερίσκο είναι αναγκαία.";
CONST lngAutoNumber = "Αυτόματος αριθμός";
CONST lngBackToRecodList = "Επιστροφή στην λίστα καταγραφών";
CONST lngBackupDatabases = "Αντέγραψε Βάση Δεδομένων";
CONST lngBackupDatabasesToTheFolder = "Αντέγραψε Βάση Δεδομένων στον φάκελο";
CONST lngByArea = "Ανά Περιοχή";
CONST lngByCustomer = "Ανά Πελάτη";
CONST lngCustomer = "Πελάτης";
CONST lngByDate = "Ανά Περίοδο";
CONST lngByeBye = "Γεια σου";
CONST lngByMonth = "Ανά Μήνα";
CONST lngByPeriod = "";
CONST lngByProduct = "Ανά Προϊόν";
CONST lngByQuarter = "Ανά Τρίμηνο";
CONST lngByWeek = "Ανά Εβδομάδα";
CONST lngByYear = "Ανά Έτος";
CONST lngByYearDay = "Ανά Μέρα του Χρόνου";
CONST lngChangeDescendingAscending = "Άλλαξε μεταξύ κατιούσας και ανιούσας τάξης";
CONST lngCharacterProblems = "Μπορεί να παρουσιάζει πρόβλημα αν το κείμενο περιέχει τους χαρακτήρες < και >. Χρησιμοποιείστε τους πάντα με ένα κενό, πριν και μετά τον κάθε χαρακτήρα.";
CONST lngCheckTheImagesYouWantToDelete = "Σημείωσε τις εικόνες που θέλεις να διαγράψης και κάνε κλικ στο <b>Διέγραψε σημειωμένες εικόνες</b> στο τέλος της σελίδας. Κάνε κλικ στο όνομα μιας εικόνας για να την δεις.";
CONST lngChooseTheDatabasesToBackup = "Σημείωσε τις Βάσεις Δεδομένων για αντιγραφή ασφαλίας";
CONST lngChooseTheDatabasesToRestore = "Σημείωσε τις Βάσεις Δεδομένων για επαναφορά";
CONST lngCity = "Πόλη";
CONST lngCleanCheckedBoxes = "Καθάρισε κουτάκια";
CONST lngCleanedText = "Καθαρισμένο Κείμενο";
CONST lngCleanText = "Καθάρισε Κείμενο";
CONST lngCleanTextDesciption = "Καθαρίζει κείμενο από κωδικούς του WORD και του HTML καθώς επίσης και από κενά διαστήματα και κενές σειρές.<br>Ξεχωρίζει τις παραγράφους με μια κενή σειρά.";
CONST lngCleanPreserveFormedText = "Δατήρησε διαμόρφωση κειμένου";
CONST lngCleanPreserveFormedTextDescription = "Διατηρεί όλες τις βασικές διαμορφώσεις του κειμένου καθαρίζοντας όλους τους περιττούς κωδικούς εκτός από επικεφαλίδες, πίνακες, λίστες, εικόνες και συνδέσεις καθώς και παχιά, πλάγια και υπογεγραμμένα γράμματα. Το φάρδος των πινάκων καθορίζεται αυτόματα.";
CONST lngClickOnAFileToDownloadIt = "Κάνε κλικ σε ένα αρχείο για να το κατεβάσεις";
CONST lngCode = "Κωδικός";
CONST lngColor = "Χρώμα";
CONST lngCompatibleNamesAndFieldsType = "Συμβατά ονόματα και τύποι πεδίων";
CONST lngCompleted = "Πλήρεις";
CONST lngCancelled = "Ακυρωμένες";
CONST lngCompleteHTMLFormation = "Πλήρης HTML-Διαμόρφωση";
CONST lngCompName = "Επιχείρηση";
CONST lngConfirmDelete = "Επιβεβαίωσε διαγραφή";
CONST lngCopyConfirm = "Επιβεβαίωσε αντιγραφή";
CONST lngCopyRecord = "Αντέγραψε την καταγραφή";
CONST lngCountry = "Χώρα";
CONST lngCreateInParentFolders = "Δημιούργησε στον Κυρίως φάκελο";
CONST lngCreateNewSubfolder = "Δημιούργησε";
CONST lngFolderCreate = "Πρόσθεσε υποφάκελο";
CONST lngFolderDelete = "Διέγραψε υποφάκελο";
CONST lngCreationOfNewSubfolder = "Δημιουργία νέου υποφακέλου";
CONST lngCustomerID = "Α.Α. Πελάτη";
CONST lngDatabase = "Βάση Δεδομένων";
CONST lngDate = "Ημερομηνία";
CONST lngDay = "Μέρα";
CONST lngDefineNewSubfolder = "Προσδιόρισε το όνομα του υποφακέλου";
CONST lngDeleteCheckedBoxes = "Διέγραψε σημειωμένες εικόνες";
CONST lngDeleteRecord = "Διαγραφή καταγραφής";
CONST lngDiscendingOrder = "Κατιούσα τάξη";
CONST lngDiscount = "Εκπτώσεις";
CONST lngDistributeByYear = "Κατανομή ανά έτος";
CONST lngDivision = "Διαίρεση";
CONST lngDownloadFiles = "Κατέβασε αρχεία";
CONST lngEdit = "Ανασύνταξε";
CONST lngEditRecord = "Ανασύνταξη καταγραφής";
CONST lngEmail = "Email";
CONST lngErrorMessage = "Error Message";
CONST lngExisingBackupFiles = "Υπαρκτά αντίγραφα";
CONST lngExistedSubfolders = "Υπάρχοντες υποφάκελοι στον Βασικό Φάκελο";
CONST lngExtendHeaders = "Επέκτεινε τίτλο";
CONST lngFile = "Αρχείο";
CONST lngFileBrowsing = "Σειρά Αρχείων";
CONST lngFileMultiple = "Μπορείς να προσθέσεις πολλαπλά αρχεία ταυτόχρονα.";
CONST lngFileNames = "Όνομα αρχείων";
CONST lngFilesNotDeletedNoWritePermission = "One or more files have not been deleted! Probably the files have no Write Permission.";
CONST lngFillFieldsWithAsterisk = "Πρέπει να συμπληρώσεις όλα τα πεδία που είναι σημειωμένα με αστερίσκο (*).";
CONST lngFirstPage = "Πρώτη σελίδα";
CONST lngFormAndCopyTheCleanedText = "Διαμόρφωσε και αντέγραψε το κείμενο για παραπέρα χρήση.";
CONST lngFrom = "Από το";
CONST lngGoToPage = "Πάνε στην σελίδα";
CONST lngGrossPrices = "Τιμές χοντρικής";
CONST lngGroups = "Ομάδες προϊόντων";
CONST lngHelp = "Βοήθεια";
CONST lngHTMLFormation = "Διαμόρφωσε HTML";
CONST lngID = "AA";
CONST lngIncompatibleValuesFieldsType = "Ασυμβατότητες μεταξύ του Πίνακα και του XML-αρχείου";
CONST lngInProcess = "Υπό Εκτέλεση";
CONST lngLast = "Τελευταίους";
CONST lngLastMonth = "Τελευταίος μήνας";
CONST lngLastPage = "Τελευταία σελίδα";
CONST lngLastQuarter = "Τελευταίο Τρίμηνο";
CONST lngLastSixMonths = "Τελευταίο εξάμηνο";
CONST lngLastYear = "Τελευταίος χρόνος";
CONST lngListOfRecords = "Λίστα καταγραφών";
CONST lngLoadArchives = "Φόρτωση αρχείων";
CONST lngLoadPhotos = "Φόρτωση φωτογραφιών";
CONST lngLoginButton = "Είσοδος";
CONST lngLogInError = "Λάθος κωδικός ή όνομα χρήστη!";
CONST lngLoginForWebMasters = "Είσοδος διαχειριστών της Βάσης";
CONST lngMaxCharacters = "Μέγιστος επιτρεπτός αριθμός χαρακτήρων";
CONST lngMaxNumber = "Μέγιστος επιτρεπτός αριθμός";
CONST lngMonth = "Μήνας";
CONST lngMonths = "μήνες";
CONST lngMostReadArticlesTitle = "Δημοφιλή άρθρα:";
CONST lngMostVisitedProducts = "Δημοφιλή προϊόντα";
CONST lngName = "Όνομα";
CONST lngNewOrder = "Νέα Παραγγελία";
CONST lngNewOrders = "Νέες Παραγγελίες";
CONST lngNewText = "Νέο Κείμενο";
CONST lngNextPage = "Επόμενη σελίδα";
CONST lngNo = "Όχι";
CONST lngNoDateField = "Καμιά ημερομηνία";
CONST lngNotAutoNumber = "Μη αυτόματος αριθμός";
CONST lngOf = "Από";
CONST lngOfTotal = "από Σύνολο";
CONST lngOnlyForWebMasters = "Η σελίδα αυτή είναι μόνον για διαχειριστές.";
CONST lngOpenArchive = "Άνοιξε αρχείο";
CONST lngOpenFolder = "Άνοιξε φάκελο";
CONST lngOrderByThisField = "Ταξινόμησε τον Πίνακα βάση αυτού του πεδίου";
CONST lngOrderResultsByThisField = "Ταξινόμησε τα αποτελέσματα βάση αυτού του πεδίου";
CONST lngOrder = "Παραγγελία";
CONST lngOrderDate = "Ημερομηνία Παραγγελίας";
CONST lngOrderDetails = "Λεπτομέρειες Παραγγελίας";
CONST lngDelivery = "Αποστολή";
CONST lngDeliveryInformation = "Πληροφορίες Αποστολής";
CONST lngReciever = "Παραλήπτης";
CONST lngSender = "Αποστολέας";
CONST lngOrderID = "Α.Α. Παραγγελίας";
CONST lngOrderProcessing = "Εκτέλεση Παραγγελιών";
CONST lngOrders = "Παραγγελίες";
CONST lngOrderStatus = "Κατάσταση";
CONST lngPage = "Σελίδα";
CONST lngPageSize = "Μέγεθος σελίδας";
CONST lngPaid = "Καταβληθέν";
CONST lngParentFoldersDoNoExist = "Οι Κύριοι φάκελοι εικόνων δεν υπάρχουν στο Σέρβερ!";
CONST lngPassword = "Password";
CONST lngPasteTextOnTextareaAndClickClear = "Προσκόλλησε ένα κείμενο στο παρακάτω πεδίο και πάτησε έπειτα το";
CONST lngPayAgent = "Αντιπρόσωπος πληρωμής";
CONST lngPayMethod = "Τρόπος Πληρωμής";
CONST lngPeriod = "Περίοδος";
CONST lngPhone = "Τηλέφωνο";
CONST lngPleaseWriteAName = "Παρακαλούμε, γράψτε ένα όνομα!";
CONST lngPreserveFormedText = "Από WORD<br>σε HTML";
CONST lngPreviousPage = "Προηγούμενη σελίδα";
CONST lngPrice = "Tιμή";
CONST lngPrintText = "Εκτύπωσε";
CONST lngProcessingNewOrders = "Κάνε κλικ σε μια από τις εικόνες για να <b>ανοίξεις</b> ή να <b>εκτυπώσεις</b> μια παραγγελία.";
CONST lngProductName = "Προϊόν";
CONST lngProducts = "Προϊόντα";
CONST lngProductStatistics = "Επισκέψεις ανά <b>Προϊόν</b>";
CONST lngQuantity = "Ποσότης";
CONST lngQuarter = "Τρίμηνο";
CONST lngReset = "Καθάρισε";
CONST lngResetApplications = "Επανεκκίνηση εφαρμογών";
CONST lngRestoreBackupedDatabases = "Επαναφορά αντιγράφων ασφαλίας";
CONST lngRestoreBackupedDatabasesFromFolder = "Επαναφορά αντιγράφων ασφαλίας Βάσεων Δεδομένων από φάκελο";
CONST lngResults = "Αποτελέσματα";
CONST lngRetailPrices = "Τιμές λιανικής";
CONST lngReturnToBackupDatabases = "Επιστροφή στην αντιγραφή ασφαλίας Βάσεων Δεδομένων";
CONST lngSaleStatistics = "Στατιστικά Πωλήσεων";
CONST lngSaveInExcel = "Αποθήκευσε σε EXCEL";
CONST lngSaveInHTML = "Αποθήκευσε σε HTML";
CONST lngSaveInWord = "Αποθήκευσε σε WORD";
CONST lngSearch = "Αναζήτησε";
CONST lngSearchInProductListToCreatePDF = "Δημιουργία PDF-καταλόγων προϊόντων";
CONST lngSearchMode = "Aναζήτηση";
CONST lngSearchPeriod = "Περίοδος αναζήτησης";
CONST lngSearchPeriodLast = "";
CONST lngSearchText = "Κείμενο αναζήτησης";
CONST lngSelectFolder = "Επίλεξε φάκελο";
CONST lngSelectGroupByField = "Επίλεξε πεδίο ομαδοποίησης";
CONST lngSelectParentFolder = "Επίλεξε βασικό φάκελο";
CONST lngSelectTable = "Επίλεξε πίνακα";
CONST lngSelectTableAndUploadFile = "Επίλεξε κατά ζεύγος αρχείο φόρτωσης και τον αντίστοιχο πίνακα";
CONST lngSelectUploadFile = "Επίλεξε αρχείο";
CONST lngSetPrefix = "Πρόθεμα";
CONST lngShippingCharge = "Μεταφορικά";
CONST lngShippingMethod = "Τρόπος Αποστολής";
CONST lngShowFiles = "Δείξε τα αρχεία";
CONST lngShowHTML = "Δείξε HTML";
CONST lngShowRecordsFromThisCategoryOnly = "Δείξε μόνον καταγραφές αυτής της κατηγορίας";
CONST lngSize = "Μέγεθος";
CONST lngState = "Νομός";
CONST lngStatus = "Κατάσταση";
CONST lngSubTotal = "Υποσύνολο";
CONST lngSuccessfulPayment = "Επιτυχής πληρωμή";
CONST lngTable = "Πίνακας";
CONST lngTaxAuthority = "ΔΟΥ";
CONST lngTaxNumber = "ΑΦΜ";
CONST lngTextStatistics = "Επισκέψεις ανά <b>Κείμενο</b>";
CONST lngTheFolderIsEmpty = "Ο φάκελος είναι άδειος";
CONST lngTheNameAllreadyExistsInFolder = "";
CONST lngTheRequestedFolderDoesNotExist = "Ο ζητούμενος φάκελος δεν υπάρχει";
CONST lngTheSubfolderHasBeenCreated = "Ο υποφάκελος δημιουργήθηκε με επιτυχία";
CONST lngThisLineWillNotPrint = "Η σειρά αυτή δεν περιλαμβάνεται στην εκτύπωση";
CONST lngToConnectImagesToSubfoleder = "Για σύνδεση σε αρχείο ενός νέου υποφακέλου, κατά την πρόσθεση μια νέας καταγραφής, γράψε πρώτα το όνομα του υποφακέλου πριν από το όνομα της του αρχείου (SubfolderName/FileName).";
CONST lngToFolder = "στον φάκελο";
CONST lngTotal = "Σύνολο";
CONST lngTotalDiscount = "Σύνολο Εκπτώσεων";
CONST lngTotalExtraDiscount = "Σύνολο Πρόσθετων Εκπτώσεων";
CONST lngTotalRecords = "Σύνολο καταγραφών";
CONST lngTotalVisits = "Σύνολο επισκέψεων";
CONST lngTotalVisitsSince = "Σύνολο επισκέψεων από";
CONST lngUnspecifiedErrorNoFoldersCreated = "Απροσδιόριστο λάθος - Δεν δημιουργήθηκε κανένας φάκελος. <br><br>Πιθανόν, ο φάκελος δεν έχει άδεια εγγραφής. Παρακαλώ, έλεγξε το επίπεδο ασφαλείας του φακέλου.";
CONST lngUpdate = "Ενημέρωσε";
CONST lngUpdateableMode = "Ανανέωση";
CONST lngUpdateBasic = "Βασική ενημέρωση προϊόντων";
CONST lngUpdateGeneral = "Πολλαπλή ενημέρωση καταγραφών";
CONST lngUpdateNew = "Ενημέρωση Νέων προϊόντων";
CONST lngUpdateOffers = "Ενημέρωση Προσφορών";
CONST lngUpdateSelected = "Ενημέρωση Επιλογών";
CONST lngUploadFiles = "Φόρτωσε αρχεία";
CONST lngUploadFilesToTheServer = "Φόρτωσε αρχεία στο Server";
CONST lngValidFolderNames = "Μπορείς να χρησιμοποίησεις λατινικά γράμματα, αριθμούς και κάτω παύλα. <b>Μη ξεκινάς</b> με αριθμό ή κάτω παύλα.";
CONST lngUsername = "User Name";
CONST lngValidateArchive = "Έλεγξε αρχείο";
CONST lngWarningAboutDatabaseRestoring = "<b>Προσοχή!</b><br>Βεβαιώσου ότι θέλεις να αντικαταστήσεις τις <b>ενεργές</b> Βάσεις Δεδομένων με Αντίγραφα Ασφαλείας.";
CONST lngVAT = "ΦΠΑ";
CONST lngWeek = "Εβδομάδα";
CONST lngVieOrder = "Δες Παραγγελία";
CONST lngViewFolderFiles = "Δες/κατέβασε, σβήσε αρχεία";
CONST lngViewFolderImages = "Δες/σβήσε φωτογραφίες";
CONST lngViewImagesNames = "Αντέγραψε ονόματα";
CONST lngViewRecord = "Δες καταγραφή";
CONST lngVisitsByDate = "Επισκέψεις ανά <b>Ημερομηνία</b>";
CONST lngVisitsStatistics = "Στατιστική επισκέψεων";
CONST lngVisitsToday = "Επισκέψεις σήμερα";
CONST lngYear = "Έτος";
CONST lngYes = "Ναι";
CONST lngYouAreLogouted = "Είσαι τώρα εξοστρακισμένος!";
 
//Additions
//================
CONST lngRecordImagesClickToViews = "Φωτογραφίες καταγραφών";
CONST lngCopyImages = "Αντιγραφή ονόματος φωτογραφιών";
CONST lngMarkToCopyImages = "Σημείωσε ένα ή περισσότερα αρχεία. Κάνε <b>διπλό κλικ</b> σε ένα σχετικό πεδίο για να τα αντιγράψεις. Με <b>διπλό κλικ</b> μπορείς έπειτα να προσθέσεις στο ίδιο πεδίο και άλλα αρχεία.";
CONST lngMultipleUpload = "Φόρτωσε πολλαπλά αρχεία";
CONST lngMultipleUploadSelections = "Επίλεξε πρώτα φάκελο προορισμού και κάνε μετά κλικ στο «Επίλεξε φάκελο». Επίλεξε μετά πολλαπλά αρχεία για άμεση φόρτωση.";
CONST lngUpdateSliders = "Ενημέρωση Slider";
CONST lngUpdatePublishedTexts = "Ενημέρωση δημοσιευμένων κειμένων";
CONST lngShowImages = "Δείξε εικόνες";
CONST lngHideImages = "Κρύψε εικόνες";
CONST lngCreateXMLSiteMaps = "Δημιουργία XML-Sitemaps";
 
//Additions Statistics
//================
CONST lngHowAccountVisitors = "Πώς μετριέται μια επίσκεψη";
CONST lngHowAccountVisitorsNote = "<p>Με την είσοδο ενός επισκέπτη στην ιστοσελίδα, αρχίζει μια περίοδος (Session) η οποία διαρκεί 20 λεπτά και ανανεώνεται κάθε φορά που ο επισκέπτης ανοίγει μια νέα σελίδα.</p><p>Όλη η διάρκεια της περιόδου (με τις ανανεώσεις της) μετριέται ως μια επίσκεψη. Αν ο επισκέπτης απομακρυνθεί από την ιστοσελίδα και επιστρέψει μετά από 20 λεπτά, μετριέται ως νέα επίσκεψη. Αν όμως επιστρέψει σε λιγότερο από 20 λεπτά (και έχει την ίδια IP-Διεύθυνση), δεν μετριέται ως νέα επίσκεψη.</p>";
CONST lngHide = "Κρύψε";
 
CONST lngTextsPublished = "Άρθρα δημοσιευμένα";
CONST lngAllTexts = "Όλα τα άρθρα";
CONST lngTextID = "ΑΑ Κειμένου";
CONST lngToday = "Σημερινά";
CONST lngTodayYesterday = "Xθες και σήμερα";
CONST lngLastWeek = "Τελευταία εβδομάδα";
CONST lngTextsVisitsByDate = "Επισκέψεις κειμένων ανά <b>ημερομηνία δημοσίευσης</b>";
 
//New
CONST lngSearchIDNumber = "Γράψε το Αύξοντα Αριθμό μιας καταγραφής ή λέξη για αναζήτηση σε πεδία κειμένων";
CONST lngSearchTitleOrID = "ΑΑ κειμένου ή λέξη";
CONST lngFolderEmptyToDelete = "0 φάκελος πρέπει να είναι άδειος γαι να μπορεί να διαγραφεί.";
CONST lngTheSubfolderHasBeenDeleted = "Ο υποφάκελος διεγράφηκε με επιτυχία";
CONST lngPayMethodExpenses = "Επιβάρυνση Πληρωμής";
 
CONST lngSelectGroup = "Επίλεξε ομάδα";
CONST lngAccessoryGroups = "Accessory Groups";
CONST lngAccessoryCategories = "Accessory Categories";
CONST lngCopyAccessories = "Copy Accessories";
 
CONST lngTools = "Εργαλεία";
CONST lngSubmitForm = "Αποστολή";
CONST lngNewsletters = "Ενημερωτικά Δελτία";
CONST lngNewslettersUnsubscribe = "Διαγράψτε το email μου από τα Ενημερωτικά σας Δελτία!";
 
CONST lngUploadImages = "Φόρτωσε φωτογραφίες";
CONST lngUploadResizedImages = "Διαμόρφωσε και φόρτωσε φωτογραφίες";
 
CONST lngShippedUnpaid = "Εστάλη - Εκκρεμεί πληρωμή";
CONST lngShipDate = "Ημερομηνία αποστολής";
CONST lngShipPromiseDate = "Προβλεπόμενη ημερομηνία άφιξης";
CONST lngShipped ="Εστάλη";
 
CONST lngInvoiceForm = "Δες σε μορφή Τιμολογίου";
// CONST lngInvoiceForm = "Τύπος παραστατικού";
CONST lngReceipt = "Απόδειξη";
CONST lngInvoice = "Τιμολόγιο";

const lngSendingFromSite = "Αποστολή από την ιστοσελίδα";

const lngUploadLargeFiles = "Φόρτωσε μεγάλα αρχεία";

CONST lngComingDates = "Ερχόμενες ημερομηνίες";

?>
