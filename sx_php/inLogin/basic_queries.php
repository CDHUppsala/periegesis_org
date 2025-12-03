<?php 
$sql = "SELECT AllowOnlineRegistration, 
	UseAdministrationControl, 
	UseRegistrationCode, RegistrationCode, 
	UsersLoginTitle, UsersLoginNote, 
	UsersRegistrationTitle, UsersRegistrationNote, 
	UsersWelcomeTitle, UsersWelcome, 
	UsersConditionsTitle, UsersConditions  
	FROM users_setup ".str_LanguageWhere;
$stmt = $conn->query($sql);
$rs = $stmt->fetch(PDO::FETCH_ASSOC);
if (is_array($rs)) {
	$radioAllowOnlineRegistration = $rs["AllowOnlineRegistration"];
	$radioUseAdministrationControl = $rs["UseAdministrationControl"];
	$radioUseRegistrationCode = $rs["UseRegistrationCode"];
	$strRegistrationCode = $rs["RegistrationCode"];
	$strUsersLoginTitle = $rs["UsersLoginTitle"];
		$memoUsersLoginNote = $rs["UsersLoginNote"];
	$strUsersRegistrationTitle = $rs["UsersRegistrationTitle"];
		$memoUsersRegistrationNote = $rs["UsersRegistrationNote"];
	$strUsersWelcomeTitle = $rs["UsersWelcomeTitle"];
		$memoUsersWelcome = $rs["UsersWelcome"];
	$strUsersConditionsTitle = $rs["UsersConditionsTitle"];
		$memoUsersConditions = $rs["UsersConditions"];
}
$stmt = null;
$rs = null;
?>