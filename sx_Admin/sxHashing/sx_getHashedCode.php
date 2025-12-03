<?php
include realpath(dirname(__DIR__) ."/functionsLanguage.php");
//include PROJECT_ADMIN ."/login/lockPage.php";
/**
 * Get hashed password
 */

$radioContinue = false;
$strError = "";

$strHashedPW = "";
$strTextPW = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && @$_POST["GetHashedPW"] == "Yes") {
	if (!isset($_POST["TextPW"])) {
		$strError = "Write a password with Min 8 and Max 32 characters!";
	} else {
		$radioContinue = true;
		$strTextPW = trim($_POST["TextPW"]);
	}

	if ($radioContinue) {
		if (strlen($strTextPW) < 8 || strlen($strTextPW) > 32) {
			$radioContinue = false;
			$strError = "Write a password with Min 8 and Max 32 characters!";
		}
	}

	if ($radioContinue) {
		$strHashedPW = password_hash($strTextPW, PASSWORD_DEFAULT);
	}
}

/**
 * Get random Code: x*9 random alphanumeric characters
 */

$strRandomCode = "";
$strRandomToken = "";
$iRange = @$_POST["Range"];
if (!is_numeric($iRange)) {
	$iRange = 16;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && @$_POST["GetRandomCode"] == "Yes") {
	$strRandomCode = sx_GetRandomCode(floor($iRange/3));
	$strRandomToken = sx_GetRandomToken($iRange/2);
}

?>
<!DOCTYPE HTML>
<HTML>

<HEAD>
	<link rel="stylesheet" href="<?php echo sx_ADMIN_DEV ?>css/sxCMS.css?v=2023">
</HEAD>

<body style="padding: 20px">

	<body>
		<h1>Hash password with the default PHP-Method (password_hash]</h1>
		<form name="getHashedCode" action="sx_getHashedCode.php" target="_top" method="post">
			<input type="hidden" name="GetHashedPW" value="Yes">
			<table class="cleanTable">

				<tr>
					<td>Text Password: </td>
					<td>
						<input type="text" name="TextPW" value="<?= $strTextPW ?>" pattern=".{6,32}" title="Must contain at least 8 and max 32 characters" required size="84">
					</td>
				</tr>
				<tr>
					<td>Hashed Password: </td>
					<td><input type="text" name="HashedPW" value="<?= $strHashedPW ?>" size="84"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input class="button" type="Submit" name="Action" value="Get Hashed PW"></td>
				</tr>
			</table>
		</form>
		<?php if ($radioContinue) { ?>
			<p style="background: #cfd; padding: 20px; color: #090">
				You can enter the <b>Hashed Password</b> in the corresponding table field.<br>
				Remember the <b>Text Password</b> for future login.
			</p>
		<?php
		}
		if (strlen($strError) > 0) { ?>
			<p style="color: #e00; padding: 20px; background: #fe9"><?= $strError ?></p>
		<?php } ?>
		<p>The <b>Text Password</b> must contain at least 8 and max 32 characters <b>without spaces</b> and can include:</p>
		<ul>
			<li>letters from the Greek or/and Latin alphabet,</li>
			<li>numbers (0-9) and</li>
			<li>sole occurrences of the hyphen symbol (-), Not two or more hyphens in sequence.</li>
		</ul>
		<p>You can also use a <b>phrase</b> with the words separated by <b>ONE</b> hyphen (e.g. AA-BB-CC).</p>


		<h1>Get Random Code for Deregistration or Unsubscribe Fields</h1>
		<form name="getRandomCode" action="sx_getHashedCode.php" target="_top" method="post">
			<input type="hidden" name="GetRandomCode" value="Yes">
			<p>Random Code:<br><input type="text" name="RandomCode" value="<?= $strRandomCode ?>" size="84"></p>
			<p>bin2hex Token:<br><input type="text" name="RandomToken" value="<?= $strRandomToken ?>" size="84"></p>
			<?php
			if (empty($iRange)) {
				$iRange = 16;
			}
			?>
			<div class="slidecontainer">
				<input type="range" min="8" max="128" value="<?=$iRange?>" class="slider" name="Range" id="Range">
				<p>Value: <span id="CodeLength"></span></p>
			</div>

			<script>
				var slider = document.getElementById("Range");
				var output = document.getElementById("CodeLength");
				output.innerHTML = slider.value;

				slider.oninput = function() {
					output.innerHTML = this.value;
				}
			</script>

			<p><input class="button" type="Submit" name="Action" value="Get Code"></p>
		</form>
		<h3>For Manually Adding Newsletters and Members</h3>
		<p>When you <b>manually</b> add Newsletters or Members, you must also add a <b>Deregistration Code</b> or <b>Unsubscribe Code</b>,
			which according the EU legislation must follow every email and give Members the option to <b>cancel </b> their subscription.</p>
		<p>When the Member clicks on the Deregistration Code, all information about the member is <b>automatically deleted</b> from the database.</p>
		<p><b>Copy</b> the Random Code and <b>Paste</b> it to the corresponding field.</p>
	</body>

</html>