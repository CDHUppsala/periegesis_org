<body>
	<?php

	if ($sPrint != "privacy" && $sPrint != "conditions" && $sPrint != "cookies") {
		echo "<h2>No Records Found </h2>";
		exit();
	}

	$strPrintFields = " UsePrivacyStatement, PrivacyStatementTitle, PrivacyStatementNotes ";
	$strReqValue = "privacy";
	if ($sPrint == "conditions") {
		$strPrintFields = " UseConditions, ConditionsTitle, ConditionsNotes ";
		$strReqValue = "conditions";
	} elseif ($sPrint == "cookies") {
		$strPrintFields = " ShowAcceptCookies, CookiesTitle, CookiesPolicy ";
		$strReqValue = "cookies";
	}

	if (empty($strExport)) { ?>
		<div style="margin: 20px;">
			<div style="font-family: Verdana, Arial, helvetica; font-size: 9pt;">
				<a href="index.php"><?= lngHomePage ?></a> |
				<a target="_top" href="sx_PrintPage.php?print=<?= $strReqValue ?>&export=print"><?= lngPrintText ?></a> |
				<a target="_top" href="sx_PrintPage.php?print=<?= $strReqValue ?>&export=word"><?= lngSaveInWord ?></a> |
				<a target="_top" href="sx_PrintPage.php?print=<?= $strReqValue ?>&export=html"><?= lngSaveInHTML ?></a>
			</div>
			<hr>
			<?php
		}

		$aResults = "";
		$sql = "SELECT " . $strPrintFields . "
		FROM site_config_basic " . str_LanguageWhere;
		//echo $sql;
		$stmt = $conn->query($sql);
		$rs = $stmt->fetch(PDO::FETCH_NUM);
		if ($rs) {
			$aResults = $rs;
		}
		$stmt = null;
		$rs = null;

		if (!is_array($aResults)) {
			echo "<h2>No Records Found</h2>";
		} else {
			$radioTemp = $aResults[0];
			$sTitle = $aResults[1];
			$memoText = $aResults[2];
			if ($radioTemp == False) {
				echo "<h2>No Records Found</h2>";
				exit();
			} else { ?>
				<h3><?= str_SiteTitle ?></h3>
				<h1><?= $sTitle ?></h1>
				<?= $memoText ?>
				<hr>
				<p style="text-align: center;">
					<?= lngPrintedDate ?>&nbsp;<?= Date('Y-m-d') ?><br>
					<?= lngFromWebPage ?>&nbsp;<b><?= str_SiteTitle ?></b><br>
					<?= sx_LOCATION ?>
				</p>
			<?php
			}
		}
		if ($strExport == "") { ?>
		</div>
	<?php } ?>
</body>

</html>
<?php

if ($strExport == "print") { ?>
	<script>
		window.print();
	</script>
<?php } ?>