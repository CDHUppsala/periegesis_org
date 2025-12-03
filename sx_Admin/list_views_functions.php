<?php

/**
 * ==================================================
 * PAGE Navigation FUNCTIONS
 * ==================================================
 */

/**
 * Page navigation using arrows only
 */

function sx_getArrowNav()
{
    echo '<a title="First Page" href="list_views.php?page=1">&#x276E&#x276E&#x276E&#x276E</a> |';
    if (i_CurrentPage > 1) {
        echo '<a title="Previous Page" href="list_views.php?page=' . (i_CurrentPage - 1) . '"> &#x276E&#x276E</a> |';
    } else {
        echo " &#x276E&#x276E |";
    }
    if (i_CurrentPage < int_PageCount) {
        echo '<a title="Next Page" href="list_views.php?page=' . (i_CurrentPage + 1) . '"> &#x276F&#x276F</a> |';
    } else {
        echo " &#x276F&#x276F |";
    }
    echo ' <a title="Last Page" href="list_views.php?page=' . int_PageCount . '">&#x276F&#x276F&#x276F&#x276F</a>';
}

/**
 * Information about current and total Pages
 */

function sx_getPageInformation_NU()
{
    echo lngPage . " <span>" . i_CurrentPage . "</span> ";
    echo lngOfTotal . " <span>" . int_PageCount . "</span> ";
}
/**
 * Page navigation using arrows
 * and Information about current and total Pages
 */

function sx_getArrowPageNav()
{
    echo '<a title="First Page" href="list_views.php?page=1">&#x276E&#x276E&#x276E&#x276E</a> |';
    if (i_CurrentPage > 1) {
        echo '<a title="Previous Page" href="list_views.php?page=' . (i_CurrentPage - 1) . '"> &#x276E&#x276E</a> ';
    } else {
        echo " &#x276E&#x276E ";
    }
    echo lngPage . " <span>" . i_CurrentPage . "</span> ";
    echo lngOfTotal . " <span>" . int_PageCount . "</span>";

    if (i_CurrentPage < int_PageCount) {
        echo '<a title="Next Page" href="list_views.php?page=' . (i_CurrentPage + 1) . '"> &#x276F&#x276F</a> |';
    } else {
        echo " &#x276F&#x276F |";
    }
    echo ' <a title="Last Page" href="list_views.php?page=' . int_PageCount . '">&#x276F&#x276F&#x276F&#x276F</a>';
}

/**
 * General form for page navigation and searching
 */

function sx_getPageAndSearchForm()
{ ?>
    <form class="row flex_justify_start" method="post" name="searchForm" action="list_views.php?searchForm=yes">
        <label><?= lngPage ?>:<br>
            <select size="1" name="page">
                <?php
                for ($z = 1; $z < intval(int_PageCount) + 1; $z++) {
                    $strSelected = "";
                    if ($z == i_CurrentPage) {
                        $strSelected = "selected ";
                    } ?>
                    <option <?= $strSelected ?>value="<?= $z ?>"><?= $z ?></option>
                <?php
                }
                ?>
            </select>
        </label>
        <label><?= lngSize ?>:<br>
            <select size="1" name="PageSize">
                <option valu="<?= int_PageSize ?>" selected><?= int_PageSize ?></option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="250">250</option>
                <option value="500">500</option>
            </select>
        </label>
        <?php
        if (isset($_SESSION["MinimalDate"])) { ?>
            <label><?= lngDate ?>:<br>
                <select name="SearchDate">
                    <option value="0"><?= lngAllDates ?></option>
                    <?php
                    $strSelected = "";
                    if (int_SearchDate == date('Y') + 10) {
                        $strSelected = "selected ";
                    } ?>
                    <option <?= $strSelected ?>value="<?= date('Y') + 10 ?>"><?= lngComingDates ?></option>
                    <?php
                    $strSelected = "";
                    if (int_SearchDate == 1) {
                        $strSelected = "selected ";
                    } ?>
                    <option <?= $strSelected ?>value="1"><?= lngLastMonth ?></option>
                    <?php
                    $strSelected = "";
                    if (int_SearchDate == 3) {
                        $strSelected = "selected ";
                    } ?>
                    <option <?= $strSelected ?>value="3"><?= lngLastQuarter ?></option>
                    <?php
                    $strSelected = "";
                    if (int_SearchDate == 6) {
                        $strSelected = "selected ";
                    } ?>
                    <option <?= $strSelected ?>value="6"><?= lngLastSixMonths ?></option>
                    <?php
                    $strSelected = "";
                    if (int_SearchDate == 12) {
                        $strSelected = "selected ";
                    } ?>
                    <option <?= $strSelected ?>value="12"><?= lngLastYear ?></option>
                    <?php
                    $h = sx_getYear(Date('Y-m-d'));
                    $x = sx_getYear($_SESSION["MinimalDate"]) - 1;
                    for ($i = $h; $i > $x; $i--) {
                        if (int_SearchDate == $i) {
                            $strSelected = "selected ";
                        } else {
                            $strSelected = "";
                        } ?>
                        <option <?= $strSelected ?>value="<?= $i ?>"><?= lngYear . " " . $i ?></option>
                    <?php } ?>
                </select>
            </label>
        <?php
        } ?>
        <label><?= lngSearch ?>:<br>
            <input title="<?= lngSearchIDNumber ?>" type="text" placeholder="<?= lngSearchTitleOrID ?>" name="SearchText" size="19">
        </label>
        <label><input class="button" type="submit" name="go" value="<?= lngSearch ?>"></label>
        <a class="button" href="list_views.php?RequestTable=<?= REQUEST_Table ?>">Clear All</a>
    </form>
<?php
} ?>