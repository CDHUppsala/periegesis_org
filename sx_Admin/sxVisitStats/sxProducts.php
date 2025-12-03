<?php

function sx_getProductVisits($x)
{
	$conn = dbconn();
	$limit = is_numeric($x) ? (int) $x : 100;
	$sql = "SELECT v.ProductID, v.TotalVisits, p.ProductName 
		FROM visitsproducts AS v 
		INNER JOIN products AS p 
		ON v.ProductID = p.ProductID 
		ORDER BY v.TotalVisits DESC LIMIT ?";
	$smtp = $conn->prepare($sql);
	$smtp->execute([$limit]);
	$arr = $smtp->fetchAll(PDO::FETCH_ASSOC);

	$iRows = count($arr);
	if (!empty($arr)) { ?>
		<div id="statsBG">
			<h2><?= lngProductStatistics ?></h2>
			<ol>
				<?php  //dim $r;
				$intTotalTextVisits = 0;
				foreach ($arr as $row) {
					$intLoopID = $row['ProductID'] ?? 0;
					$intLoopValue = $row['TotalVisits'] ?? 0;
					$strProductName = $row['ProductName'] ?? '';
					$intTotalTextVisits += $intLoopValue ?>
					<li>
						<a target="_blank" href="../../<?= sx_DefaultSiteLang ?>/products.php?pid=<?= $intLoopID ?>"><span><?= number_format($intLoopValue, 0) ?></span>
							<span><?= lngID . ": " . $intLoopID ?></span> <?= $strProductName ?></a>
					</li>
				<?php
				}
				?>
			</ol>
			<div class="absolute">» <?= lngTotalVisits . ": " . number_format($intTotalTextVisits, 0) . " - " . lngTotalRecords . ": " . " (" . $iRows . ")" ?></div>
		</div>
<?php
	}
	$arr = null;
}
?>