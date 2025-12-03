<?php
/**
 * Include once as the file is also used (included) 
 * in Main (vertically) navigation files (in sxNav_Main)
 */
include_once __DIR__ . "/functions_Nav_Queries.php";
include __DIR__ . "/functions_Nav_Menu_Lists.php";

$strNavPath = "texts.php?";
?>
<div class="sxNavHeader" id="jqNavHeader">
    <ul>
        <li><a href="index.php"><?= lngHomePage ?></a></li>
        <?php
        if (sx_includeConferences) {
            $arr_Rows = sx_getComingConferences();
            if (is_array($arr_Rows)) { ?>
                <li><span><?= lngConferenceProgram ?></span>
                    <ul>
                        <?php
                        foreach ($arr_Rows as $row) { ?>
                            <li>
                                <a href="conferences.php?program=yes&confid=<?= $row['ConferenceID'] ?>"><?= $row['Title'] . " <span>(" . $row['Conference_Date'] ?>)</span></a>
                            </li>
                        <?php
                        } ?>
                    </ul>
                </li>
            <?php
            }
            $arr_Rows = null;
        }
        if ($radio_UseTextsAbout && $radio_TextAboutHeaderMenuByGroup) {
            sx_getAboutMenu_HeaderGroups($radio_ShowAboutTextsInHeader, true);
        }
        $arr_Rows = sx_getRowsNavBySubCategories();
        if (!empty($arr_Rows)) {
            sx_getHeaderNavList_ToSubcategories($arr_Rows, $strNavPath);
        }
        $arr_Rows = null;
        if ($radio_UseEvents) { ?>
            <li><a href="events.php"><?= $str_EventsMenuTitle ?></a></li>
            <?php
        }
        if ($radio_UseTextsAbout) {
            if ($radio_TextAboutHeaderMenuByGroup) {
                sx_getAboutMenu_HeaderGroups($radio_ShowAboutTextsInHeader, false);
            } else { ?>
                <li><a href="about.php"><?= $str_TextsAboutTitle ?></a></li>
            <?php
            }
        }
        if ($radio_IncludeCourses && sx_IncludeCourses && sx_IncludeCoursesInTop === false) { ?>
            <li><a href="courses.php"><?= $str_CoursesLinkTitle ?></a></li>
        <?php
        }
        if ($radio_UseMembersList) { ?>
            <li><a href="about.php?members=yes"><?= $str_MembersListTitle ?></a></li>
        <?php
        }
        if (sx_includeForum && $radio_IncludeForum && !empty($str_ForumLinkTitle)) { ?>
            <li><a href="forum.php"><?= $str_ForumLinkTitle ?></a></li>
        <?php
        }
        if (sx_IncludeTopContact == false) { ?>
            <li><a href="contact.php"><svg class="sx_svg">
                        <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_mail_open"></use>
                    </svg>
                    <?= lngContact ?></a></li>
        <?php
        }
        if (sx_includePDFArchive && $radio_UsePDF && sx_includePDFArchive_OnTop == false) { ?>
            <li><a href="ps_PDF.php"><svg class="sx_svg">
                        <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_PDF_archives"></use>
                    </svg>
                    <?php
                    if ($radio__UserSessionIsActive && !empty($str_MenuTitleHidden)) {
                        echo $str_MenuTitleHidden;
                    } else {
                        echo $str_PDFMenuTitle;
                    } ?>
                </a></li>
        <?php
        }
        if (sx_includeFilms && $radio_IncludeFilms && sx_includeFilms_OnTop == false) { ?>
            <li><a href="films.php"><svg class="sx_svg">
                        <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_film"></use>
                    </svg>
                    <?= $str_FilmsLinkTitle ?></a></li>
        <?php
        }
        if ($radio_UseLinks && sx_IncludeTopLinks == false) { ?>
            <li><a href="links.php"><svg class="sx_svg">
                        <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_link_rings"></use>
                    </svg>
                    <?= $str_LinksTitle ?></a></li>
            <?php
        }

        if (sx_includeUsersLogin && $radio_IncludeUsersLogin && sx_includeUsersLogin_OnTop == false) {
            if ($radio__UserSessionIsActive) { ?>
                <li class="profile_menu"><a href="javascript:void(0)" class="jqToggleDataID" data-id="UserTopMenu">
                        <?= $_SESSION["Users_FirstName"] ?></a>
                    <ul id="UserTopMenu">
                        <li><a href="login.php?pg=edit"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_user_profile"></use>
                                </svg> <?= (lngProfile) ?></a></li>
                        <li><a href="login.php?pg=logout"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_user_logout"></use>
                                </svg> <?= (lngLogout) ?></a></li>
                    </ul>
                </li>
            <?php
            } else { ?>
                <li><a href="login.php"><svg class="sx_svg">
                            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_user"></use>
                        </svg>
                        <?= lngLogin ?></a></li>
            <?php
            }
        }
        if (sx_includeParticipantsLogin && $radio_UseParticipantsLogin) {
            if (isset($_SESSION["Participants_" . sx_HOST])) { ?>
                <li class="profile_menu"><span class="jqToggleDataID" data-id="ProfileMenu">
                        <?= $_SESSION["Part_FirstName"] ?></span>
                    <ul id="ProfileMenu">
                        <li><a href="conferences_login.php?pg=conference"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_pencil"></use>
                                </svg> <?= lngRegisterForConferences ?></a></li>
                        <li><a href="conferences_login.php?pg=edit"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_user_profile"></use>
                                </svg> <?= lngEditProfile ?></a></li>
                        <li><a href="conferences_login.php?pg=logout"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_user_logout"></use>
                                </svg> <?= (lngLogout) ?></a></li>
                    </ul>
                </li>
            <?php
            } else { ?>
                <li><a href="conferences_login.php"><svg class="sx_svg">
                            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_04#sx_user"></use>
                        </svg>
                        <?= lngLogin ?></a>
                </li>
            <?php
            }
        }
        if (sx_IncludeTopSearch == false) { ?>
            <!--li><a href="search.php"><?php // echo lngSearch 
                                        ?></a></li-->
            <li><?php sx_getTopSearch() ?></li>
        <?php
        }


        ?>
    </ul>
</div>