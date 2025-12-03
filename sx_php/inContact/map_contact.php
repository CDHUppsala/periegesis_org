<?php
function sx_GetCanvasMap($strMapLat, $strMapLong, $sTitle, $row)
{
	global $str_GoogleAPIKey;
	if (!empty($sTitle)) { ?>
		<h3><?= lngMap . ": " . $sTitle ?></h3>
	<?php
	} ?>
	<div id="mapCanvas_<?= $row ?>" style="height: 340px;"></div>
	<p class="text_xsmall align_right">
		<a href="https://www.google.com/maps/search/?api=1&query=<?= $strMapLat ?>,<?= $strMapLong ?>" target="_blank"><?= lngOpenInNewWindow ?> »»</a>
	</p>
	<script language="javascript">
		function initMap() {
			var myLatLng = {
				lat: <?= $strMapLat ?>,
				lng: <?= $strMapLong ?>
			};
			var map = new google.maps.Map(document.getElementById("mapCanvas_<?= $row ?>"), {
				zoom: 15,
				center: myLatLng
			});
			var marker = new google.maps.Marker({
				position: myLatLng,
				map: map,
				title: "<?= $sTitle ?>"
			});
		};
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=<?= $str_GoogleAPIKey ?>&callback=initMap" async defer></script>
	<?php
}

function sx_GetFrameMap($src, $sTitle)
{
	if (!empty($sTitle)) { ?>
		<h4><?= lngMap . ": " . $sTitle ?></h4>
	<?php
	} ?>
	<div class="ads">
		<iframe src="<?= $src ?>" width="100%" height="340" frameborder="0" allowfullscreen></iframe>
	</div>
<?php
}

if ($radio_UseMap) { ?>
	<section aria-label="Maps">
		<?php
		if (!empty($str_GoogleFrameMapSource)) {
			$str_GoogleFrameMapSource = strip_tags($str_GoogleFrameMapSource);
		}
		if (!empty($str_GoogleFrameMapSource)) {
			sx_GetFrameMap($str_GoogleFrameMapSource, str_SiteTitle);
		} elseif (!empty($str_MapLatitude) && !empty($str_MapLongitude)) {
			sx_GetCanvasMap($str_MapLatitude, $str_MapLongitude, str_SiteTitle, 0);
		}

		if (is_array($arrOffices)) {
			$iRows = count($arrOffices);
			for ($r = 0; $r < $iRows; $r++) {
				$strSubTitle = $arrOffices[$r][1];
				$strMapLatitude = $arrOffices[$r][13];
				$strMapLongitude = $arrOffices[$r][14];
				$strGoogleFrameMapSource = $arrOffices[$r][15];
				if (!empty($strGoogleFrameMapSource)) {
					$strGoogleFrameMapSource = strip_tags($strGoogleFrameMapSource);
				}
				if (!empty($strGoogleFrameMapSource)) {
					sx_GetFrameMap($strGoogleFrameMapSource, $strSubTitle);
				} elseif (!empty($strMapLatitude) && !empty($strMapLongitude)) {
					sx_GetCanvasMap($strMapLatitude, $strMapLongitude, $strSubTitle, ($r + 1));
				}
			}
		} ?>
	</section>
<?php
} ?>