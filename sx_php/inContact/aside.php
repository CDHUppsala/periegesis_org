<section>
	<h2 class="head"><span><?= lngContact ?></span></h2>
	<?php
	/**
	 * Variables from sx_config.php
	 */
	$strBiosInfo = "";
	$radioBreak = false;
	if (!empty($str_SiteAddress)) {
		$strBiosInfo .= $str_SiteAddress . "<br>";
	}
	if (!empty($str_SitePostalCode)) {
		$strBiosInfo .= $str_SitePostalCode;
		$radioBreak = true;
	}
	if (!empty($str_SiteCity)) {
		$strBiosInfo .= " " . $str_SiteCity;
		$radioBreak = true;
	}
	if (!empty($str_SiteCountry)) {
		$strBiosInfo .= ", " . $str_SiteCountry . "<br>";
	} elseif ($radioBreak) {
		$strBiosInfo .= "<br>";
	}

	if (!empty($str_SitePhone)) {
		$strBiosInfo .= "<b>" . lngPhone . ":</b> " . $str_SitePhone . "<br>";
	}
	if (!empty($str_SiteMobile)) {
		$strBiosInfo .= "<b>" . lngMobile . ":</b> " . $str_SiteMobile . "<br>";
	}
	if (!empty($str_SiteFax)) {
		$strBiosInfo .= "<b>" . lngFax . "</b> " . $str_SiteFax . "<br>";
	}
	if (!empty($str_OfficeHours)) {
		$strBiosInfo .= "<b>" . lngOfficeHours . ":</b> " . $str_OfficeHours . "<br>";
	}
	if (!empty($str_PhoneHours)) {
		$strBiosInfo .= "<b>" . lngPhoneHours . ":</b> " . $str_PhoneHours . "<br>";
	}
	?>
	<h3><?= str_SiteTitle ?></h3>
	<div class="text_normal text_small">
		<?php
		echo '<p>';
		echo $strBiosInfo;
		if ($str_SiteAdminEmail != "") {
			get_Email_In_Script($str_SiteAdminEmail, "");
		}elseif ($str_SiteEmail != "") {
			get_Email_In_Script($str_SiteEmail, "");
		}
		echo '</p>';
		?>
	</div>

	<?php
	if (is_array($arrOffices)) {
		$iRows = count($arrOffices);
		for ($r = 0; $r < $iRows; $r++) {
			$s_SubTitle = $arrOffices[$r][1];
			$s_SiteAddress = $arrOffices[$r][2];
			$s_PostalCode = $arrOffices[$r][3];
			$s_SiteCity = $arrOffices[$r][4];
			$s_SiteCountry = $arrOffices[$r][5];
			$s_SitePhone = $arrOffices[$r][6];
			$s_SiteMobile = $arrOffices[$r][7];
			$s_SiteFax = $arrOffices[$r][8];
			$s_SiteEmail = $arrOffices[$r][9];
			$s_OfficeHours = $arrOffices[$r][10];
			$s_PhoneHours = $arrOffices[$r][11];

			$s_BiosInfo = "";
			$radioBreak = false;
			if ($s_SiteAddress != "") {
				$s_BiosInfo = $s_SiteAddress . "<br>";
			}
			if ($s_PostalCode != "") {
				$s_BiosInfo .= $s_PostalCode;
				$radioBreak = true;
			}
			if ($s_SiteCity != "") {
				$s_BiosInfo .= " " . $s_SiteCity;
				$radioBreak = true;
			}
			if (isset($s_SiteCountry)) {
				$s_BiosInfo .= ", " . $s_SiteCountry . "<br>";
			} elseif ($radioBreak) {
				$s_BiosInfo .= "<br>";
			}

			if ($s_SitePhone != "") {
				$s_BiosInfo .= "<b>" . lngPhone . ":</b> " . $s_SitePhone . "<br>";
			}
			if ($s_SiteMobile != "") {
				$s_BiosInfo .= "<b>" . lngMobile . ":</b> " . $s_SiteMobile . "<br>";
			}
			if ($s_SiteFax != "") {
				$s_BiosInfo .= "<b>Fax:</b> " . $s_SiteFax . "<br>";
			}
			if ($s_OfficeHours != "") {
				$s_BiosInfo .= "<b>" . lngOfficeHours . ":</b> " . $s_OfficeHours . "<br>";
			}
			if ($s_PhoneHours != "") {
				$s_BiosInfo .= "<b>" . lngPhoneHours . ":</b> " . $s_PhoneHours . "<br>";
			}

			if ($s_SubTitle != "") {
				echo '<h3>' . $s_SubTitle . '</h3>';
			}
			echo '<div class="text_normal text_small">';
			echo '<p>';
			echo $s_BiosInfo;
			if ($s_SiteEmail != "") {
				get_Email_In_Script($s_SiteEmail, "");
			}
			echo '</p>';
			echo "</div>";
		}
	} ?>
</section>
<?php
include __DIR__ . "/map_contact.php";

if ($radio_UseAdvertises) {
	//== Place: Top,  Bottom
	get_Main_Advertisements("Bottom");
}
?>