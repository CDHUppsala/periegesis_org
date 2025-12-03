<?php

/**
 * Creates a Drop-Down menu with lists upp to three levels of classification of Texts:
 * - It can be used even if texts are classified inte two or one level
 * - The links to texts are placed on the last/lowest level of classification
 * @param array $arr : an array with database rows,order by groups and,
 *      eventually, by categories and subcategories, if any.
 * @param mixed $path : the site page where texts of the selected level will be opened
 * @return void : HTML lists as a string
 */
function sx_getHeaderNavList_ToSubcategories($arr, $path)
{
    $iRows = count($arr);
    $levelUL1 = false;
    $levelUL2 = false;
    $loop1 = -1;
    $loop2 = -1;
    for ($iRow = 0; $iRow < $iRows; $iRow++) {
        $intMenuGroupID = (int) $arr[$iRow][0];
        $strMenuGroupName = $arr[$iRow][1];
        $intMenuCatID = (int) $arr[$iRow][2];
        $strMenuCatName = $arr[$iRow][3];
        $intMenuSubCatID = (int) $arr[$iRow][4];
        $strMenuSubCatName = $arr[$iRow][5];
        if (intval($intMenuGroupID) != intval($loop1)) {
            if ($levelUL2) {
                echo "</ul></li>";
            }
            if ($levelUL1) {
                echo "</ul></li>";
            }
            $levelUL1 = false;
            $levelUL2 = false;
            if (intval($intMenuCatID) == 0) {
                echo '<li><a href="' . $path . 'gid=' . $intMenuGroupID . '">' . $strMenuGroupName . '</a></li>';
            } else {
                $levelUL1 = true;
                echo '<li><span>' . $strMenuGroupName . '</span>';
                echo "<ul>";
            }
        }
        if ($levelUL1 && intval($intMenuCatID) != intval($loop2)) {
            if ($levelUL2) {
                echo "</ul></li>";
            }
            $levelUL2 = false;
            echo '<li><a href="' . $path . 'cid=' . $intMenuCatID . '">' . $strMenuCatName . '</a>';
            if (intval($intMenuSubCatID) == 0) {
                echo "</li>";
            } else {
                echo "<ul>";
                $levelUL2 = true;
            }
        }
        if ($levelUL2) {
            echo '<li><a href="' . $path . 'scid=' . $intMenuSubCatID . '">' . $strMenuSubCatName . '</a></li>';
        }
        $loop1 = $intMenuGroupID;
        $loop2 = $intMenuCatID;
    }
    if ($levelUL2) {
        echo "</ul></li>";
    }
    if ($levelUL1) {
        echo "</ul></li>";
    }
}

/**
 * Creates Wide-Screen menus separately for every Text Group:
 * - Categories and subcategories belonging to a group are all shown in one wide-screen drop-down menu.
 * - It can be used even if texts are classified inte two or one level
 * - The links to texts are placed on the last/lowest level of classification
 * @param array $arr : an array with database rows,order by groups and,
 *      eventually, by categories and subcategories, if any.
 * @param mixed $path : the site page where texts of the selected level will be opened
 * @return void : HTML lists as a string
 */
function sx_getHeaderNavList_ToSubcategories_WideByGroup($arr, $path)
{
    $iRows = count($arr);
    $levelUL1 = False;
    $levelUL2 = False;
    $loop1 = -1;
    $loop2 = -1;
    for ($iRow = 0; $iRow < $iRows; $iRow++) {
        $intMenuGroupID = (int) $arr[$iRow][0];
        $strMenuGroupName = $arr[$iRow][1];
        $intMenuCatID = (int) $arr[$iRow][2];
        $strMenuCatName = $arr[$iRow][3];
        $intMenuSubCatID = (int) $arr[$iRow][4];
        $strMenuSubCatName = $arr[$iRow][5];
        if ($intMenuGroupID != $loop1) {
            if ($levelUL2) {
                echo "</ul></li>";
            }
            if ($levelUL1) {
                echo "</ul></li>";
            }
            $levelUL1 = False;
            $levelUL2 = False;
            if ($intMenuCatID == 0) {
                echo '<li><a href="' . $path . 'gid=' . $intMenuGroupID . '">' . $strMenuGroupName . '</a></li>';
            } else {
                $levelUL1 = True;
                echo '<li class="static"><span>' . $strMenuGroupName . '</span>';
                echo '<ul class="wide">';
            }
        }
        if ($levelUL1 && $intMenuCatID != $loop2) {
            if ($levelUL2) {
                echo "</ul></li>";
            }
            $levelUL2 = False;
            echo '<li><a href="' . $path . 'cid=' . $intMenuCatID . '">' . $strMenuCatName . '</a>';
            if ($intMenuSubCatID == 0) {
                echo "</li>";
            } else {
                $levelUL2 = True;
                echo "<ul>";
            }
        }
        if ($levelUL2) {
            echo '<li><a href="' . $path . 'scid=' . $intMenuSubCatID . '">' . $strMenuSubCatName . '</a></li>';
        }
        $loop1 = $intMenuGroupID;
        $loop2 = $intMenuCatID;
    }
    if ($levelUL2) {
        echo "</ul></li>";
    }
    if ($levelUL1) {
        echo "</ul></li>";
    }
}

/**
 * Creates a single Wide-Screen menu with lists upp to three levels of classification of Texts:
 * - Groups, categories and subcategories are all shown in one wide-screen drop-down menu.
 * - It can be used even if texts are classified inte two or one level
 * - The links to texts are placed on the last/lowest level of classification
 * @param array $arr : an array with database rows,order by groups and,
 *      eventually, by categories and subcategories, if any.
 * @param mixed $path : the site page where texts of the selected level will be opened
 * @return void : HTML lists as a string
 */
function sx_getHeaderNavList_ToSubcategories_Wide($arr, $path, $navName)
{ ?>
    <li class="static"><span><?= $navName ?></span>
        <ul class="wide">
            <?php
            $iRows = count($arr);
            $levelUL1 = False;
            $levelUL2 = False;
            $loop1 = -1;
            $loop2 = -1;
            for ($iRow = 0; $iRow < $iRows; $iRow++) {
                $intMenuGroupID = (int) $arr[$iRow][0];
                $strMenuGroupName = $arr[$iRow][1];
                $intMenuCatID = (int) $arr[$iRow][2];
                $strMenuCatName = $arr[$iRow][3];
                $intMenuSubCatID = (int) $arr[$iRow][4];
                $strMenuSubCatName = $arr[$iRow][5];
                if ($intMenuGroupID != $loop1) {
                    if ($levelUL2) {
                        echo "</ul></li>";
                    }
                    if ($levelUL1) {
                        echo "</ul></li>";
                    }
                    $levelUL1 = False;
                    $levelUL2 = False;
                    echo '<li><a href="' . $path . 'gid=' . $intMenuGroupID . '">' . $strMenuGroupName . '</a>';
                    if ($intMenuCatID == 0) {
                        echo "</li>";
                    } else {
                        $levelUL1 = True;
                        echo "<ul>";
                    }
                }
                if ($levelUL1 && $intMenuCatID != $loop2) {
                    if ($levelUL2) {
                        echo "</ul></li>";
                    }
                    $levelUL2 = False;
                    echo '<li><a href="' . $path . 'cid=' . $intMenuCatID . '">' . $strMenuCatName . '</a>';
                    if ($intMenuSubCatID == 0) {
                        echo "</li>";
                    } else {
                        $levelUL2 = True;
                        echo "<ul>";
                    }
                }
                if ($levelUL2) {
                    echo '<li><a href="' . $path . 'scid=' . $intMenuSubCatID . '">' . $strMenuSubCatName . '</a></li>';
                }
                $loop1 = $intMenuGroupID;
                $loop2 = $intMenuCatID;
            }
            if ($levelUL2) {
                echo "</ul></li>";
            }
            if ($levelUL1) {
                echo "</ul></li>";
            }
            ?>
        </ul>
    </li>

<?php
}


/**
 * Creates a single Wide-Screen menu with lists upp to two levels of classification of Texts:
 * - Groups and categories are all shown in one wide-screen drop-down menu.
 * - It can be used even if texts are classified inte two or one level
 * - The links to texts are placed on the last/lowest level of classification
 * @param array $arr : an array with database rows,order by groups and,
 *      eventually, by categories and subcategories, if any.
 * @param mixed $path : the site page where texts of the selected level will be opened
 * @return void : HTML lists as a string
 */
function sx_getHeaderNavList_ToCategories_Wide($arr, $path, $navName)
{ ?>
    <li class="static"><span><?= $navName ?></span>
        <ul class="wide">
            <?php
            $iRows = count($arr);
            $loopID = -1;
            $subLoop = false;
            for ($iRow = 0; $iRow < $iRows; $iRow++) {
                $i_GroupID = (int) $arr[$iRow][0];
                $s_GroupName = $arr[$iRow][1];
                $i_CategoryID = (int) $arr[$iRow][2];
                $s_CategoryName = $arr[$iRow][3];

                if (intval($loopID) != intval($i_GroupID)) {
                    if (intval($i_CategoryID) == 0) {
                        if ($subLoop) {
                            echo "</ul></li>";
                            $subLoop = false;
                        } ?>
                        <li><a href="<?= $path ?>gid=<?= $i_GroupID ?>"><?= $s_GroupName ?></a></li>
                    <?php
                    } else {
                        if (intval($loopID) > 0) {
                            echo "</ul></li>";
                        }
                        $subLoop = true;
                        $loopID = $i_GroupID; ?>
                        <li><span><?= $s_GroupName ?></span>
                            <ul>
                            <?php
                        }
                    }
                    if (intval($loopID) == intval($i_GroupID)) { ?>
                            <li><a href="<?= $path ?>cid=<?= $i_CategoryID ?>"><?= $s_CategoryName ?></a></li>
                        <?php }
                    $loopID = $i_GroupID;
                }
                if ($subLoop) { ?>
                            </ul>
                        </li>
                    <?php } ?>
        </ul>
    </li>
    <?php
}

/**
 * Creates a navigation menu by Groups and categories for Texts classified into two levels:
 * - Groups are shown in the header menu and categories are shown as drop-down window.
 * - It can be used even if texts are classified inte one level
 * - The links to texts are placed on the last/lowest level of classification
 * @param array $arr : an array with database rows,order by groups and,
 *      eventually, by categories and subcategories, if any.
 * @param mixed $path : the site page where texts of the selected level will be opened
 * @return void : HTML lists as a string
 */
function sx_getHeaderNavList_ToCategories($arr, $path)
{
    $iRows = count($arr);
    $levelUL1 = false;
    $loop1 = -1;
    $loop2 = -1;
    for ($iRow = 0; $iRow < $iRows; $iRow++) {
        $intMenuGroupID = (int) $arr[$iRow][0];
        $strMenuGroupName = $arr[$iRow][1];
        $intMenuCatID = (int) $arr[$iRow][2];
        $strMenuCatName = $arr[$iRow][3];
        if (intval($intMenuGroupID) != intval($loop1)) {
            if ($levelUL1) {
                echo "</ul></li>";
            }
            $levelUL1 = false;
            if (intval($intMenuCatID) == 0) { ?>
                <li><a href="<?= $path ?>gid=<?= $intMenuGroupID ?>"><?= $strMenuGroupName ?></a></li>
            <?php
            } else {
                $levelUL1 = true;
                echo '<li><span>' . $strMenuGroupName . '</span>';
                echo '<ul>';
            }
        }
        if ($levelUL1 && intval($intMenuCatID) != intval($loop2)) { ?>
            <li><a href="<?= $path ?>cid=<?= $intMenuCatID ?>"><?= $strMenuCatName ?></a></li>
<?php
        }
        $loop1 = $intMenuGroupID;
        $loop2 = $intMenuCatID;
    }
    if ($levelUL1) {
        echo "</ul></li>";
    }
}
?>