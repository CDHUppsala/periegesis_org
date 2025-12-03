<?php
$sql = "SELECT GroupID, GroupName 
	FROM menu_dish_groups
	WHERE Hidden = 0 
	ORDER BY Sorting DESC ";
$rs = $conn->query($sql)->fetchAll(PDO::FETCH_NUM);
if ($rs) { ?>
	<section class="jqNavMainToBeCloned">
		<h2 class="head"><span><?= $str_MenuListTitle ?></span></h2>
		<div class="sxAccordionNav">
			<ul>
				<?php
				$radioGroupBG = False;
				$iRows = count($rs);
				for ($r = 0; $r < $iRows; $r++) {
				?>
					<li><a href="menu.php?mcid=<?= $rs[$r][0] ?>"><?= $rs[$r][1] ?></a></li>
				<?php
				} ?>
			</ul>
		</div>
	</section>
<?php
}
$rs = null;
?>