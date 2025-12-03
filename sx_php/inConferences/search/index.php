<?php

include __DIR__ ."/form_Functions.php";
include __DIR__ ."/form_Process.php";
include __DIR__ ."/form_Search.php";


$radioSearch = True;
if ($strSearchWhere == "") {
	$radioSearch = False;
	echo '<h2 class="head"><span>' . lngSearchResults . "</span></h2>";
	echo "<p><b>" . lngSearchKeyWord . "</b></p>";
}

$aResults = null;
if ($radioSearch) {
	$arrBind = [];
	/**
	 * Get parameter values for the prepared statement
	 */
	if (!empty($arr_BindSearchWhere)) {
		$arrBind = $arr_BindSearchWhere;
	}

	$sql = "SELECT 
		p.ConferenceID AS Conference_ID,
		c.Title AS `Conference Title`,
		p.SessionID AS Session_ID,
		s.SessionTitle AS `Session Title`,
		p.PaperID AS Paper_ID,
		p.PaperTitle AS `Paper Title`,
		CONCAT(p.PaperAuthors, ', ', p.Speakers) AS Authors
	FROM ((conferences c
		LEFT JOIN conf_sessions s ON ((c.ConferenceID = s.ConferenceID)))
		LEFT JOIN conf_papers p ON ((s.SessionID = p.SessionID)))
	WHERE c.Hidden = 0 AND s.Hidden = 0 AND p.Hidden = 0 $strSearchWhere
	ORDER BY c.ConferenceID DESC, s.SessionDate, s.StartTime, s.SessionID 
	LIMIT 200 ";
	//echo $sql;
	//exit;
	$stmt = $conn->prepare($sql);
	$stmt->execute($arrBind);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if ($rows) :
		$aResults = $rows;
	endif;
	$stmt = null;
	$rows = null;

	if (empty($aResults)) {
		echo '<h2 class="head"><span>' . lngSearchResults . "</span></h2>";
		echo "</p><b>" . lngNotTextFoundNerrowSearch . "</b></p>";
	} else { ?>
	<section>
		<table id="table_search">
			<?php
			$sLink = null;
			echo "<tr>";
			foreach ($aResults[0] as $col_name => $val) {
				if (strpos($col_name, "_ID") == 0) {
					echo "<th>" . str_replace("_", " ", $col_name) . "</th>";
				}
			}
			echo "</tr>";
			foreach ($aResults as $row) {
				echo "<tr>";
				foreach ($row as $col_name => $val) {
					$sVal = $val;
					if (!empty($sLink)) {
						$sVal = '<a href="conferences.php?' . $sLink . '">' . $val . '</a>';
					}

					if (strpos($col_name, "_ID") > 0) {
						$sTemp = explode("_", $col_name)[0];
						if ($sTemp == "Conference") {
							$sLink = "confid=" . $val;
						} elseif ($sTemp == "Session") {
							$sLink = "sesid=" . $val;
						} elseif ($sTemp == "Paper") {
							$sLink = "paperid=" . $val;
						} else {
							$sLink = null;
						}
					} else {
						$sLink = null;
						echo "<td>$sVal</td>";
					}
				}
				echo "</tr>";
			}
			?>
		</table>
	</section>
<?php
	}
}

$aResults = null;

?>