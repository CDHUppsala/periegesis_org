<?php
$rs = sx_get_itemsList();
if (!empty($rs) && is_array($rs)) { ?>
    <section class="jqNavMainToBeCloned" id="items" aria-label="Menu of Products and Services">
        <h1 class="head"><span><?= $str_ItemsTitle ?></span></h1>
        <div class="grid_cards">
            <?php
            $iRows = count($rs);
            for ($iRow = 0; $iRow < $iRows; $iRow++) {
                $iItemID = $rs[$iRow]["ItemID"];
                $strItemTitle = $rs[$iRow]["ItemTitle"];
                $strMetaMedia = $rs[$iRow]["MetaMedia"];
                $memoMetaNotes = $rs[$iRow]["MetaNotes"];
            ?>
                <figure>
                    <?php
                    if (!empty($strMetaMedia)) {
                        if (strpos($strMetaMedia, ";") > 0) {
                            $strMetaMedia = substr($strMetaMedia, 0, strpos($strMetaMedia, ";"));
                        } ?>
                        <div class="img_wrapper">
                        <a href="items.php?itemid=<?= $iItemID ?>"><img alt=" <?= $strItemTitle ?>" src="../images/<?= $strMetaMedia ?>"></a>
                        </div>
                    <?php
                    } ?>
                    <figcaption>
                        <?php
                        echo '<h4><a href="items.php?itemid=' . $iItemID . '">' . $strItemTitle . '</a></h4>';
                        if(!empty($memoMetaNotes)) {
                            echo '<figcaption>'. $memoMetaNotes .'</figcaption>';
                        } ?>
                    </figcaption>
                </figure>
            <?php
            } ?>
        </div>
    </section>
<?php
}
$rs = null;
?>