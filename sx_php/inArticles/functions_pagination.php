<?php

/**
 * Page navigation by form (selct the page number)
 * @param mixed $path
 * @param mixed $intCurrentPage
 * @param mixed $intPageCount
 * @return void
 */
function sx_getPageNavigation_ByForm($path, $intCurrentPage, $intPageCount)
{ ?>
    <form action="<?= $path ?>" name="goToWhateverPage" method="post">
        <label><?= lngPage . ": " ?></label>
        <select aria-label="Select Page" size="1" name="page">
            <?php for ($i = 1; $i <= $intPageCount; $i++) {
                $strSelected = "";
                if (intval($i) == intval($intCurrentPage)) {
                    $strSelected = "selected ";
                } ?>
                <option <?= $strSelected ?>value="<?= $i ?>"><?= $i ?></option>
            <?php
            } ?>
        </select>
        <input type="submit" name="goTopage" value="&#10095;&#10095;">
    </form>
<?php
}

/**
 * Page navigation by arrows (next-previous page)
 * @param mixed $path
 * @param mixed $intCurrentPage
 * @param mixed $intPageCount
 * @return void : a HTNL list with links to pages.
 */
function sx_getPageNavigation_ByArrows($path, $intCurrentPage, $intPageCount): void
{ ?>
    <ul>
        <li><a title="<?= lngFirstPage ?>" href="<?= $path . "page=1" ?>">&#10094;&#10094;&#10094;&#10094;</a></li>
        <?php if ($intCurrentPage > 1) { ?>
            <li><a title="<?= lngPreviousPage ?>" href="<?= $path . "page=" . ($intCurrentPage - 1) ?>">&#10094;&#10094;</a></li>
        <?php } else { ?>
            <li><span>&#10094;&#10094;</span></li>
        <?php } ?>
        <li><?= $intCurrentPage ?> / <?= $intPageCount ?></li>
        <?php if ($intCurrentPage < $intPageCount) { ?>
            <li><a title="<?= lngNextPage ?>" href="<?= $path . "page=" . ($intCurrentPage + 1) ?>">&#10095;&#10095;</a></li>
        <?php } else { ?>
            <li><span>&#10095;&#10095;</span></li>
        <?php } ?>
        <li><a title="<?= lngLastPage ?>" href="<?= $path . "page=" . $intPageCount ?>">&#10095;&#10095;&#10095;&#10095;</a></li>
    </ul>
<?php
}

