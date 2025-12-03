<?php
/*
	Top Menu includes Links to Site Languages, Applications, Users Login and Search
	Styles defined in sxNav_Top.css
    If the Apps Navigation should be visible in Mobiles,
    -   set the CONSTANT SX_appsNavigationVisibleInMobiles = true to
    -   add the class . nav_top_apps_visible and 
    -   remove the Apps Marker
*/
?>
<nav id="nav_top" aria-label="Languages, Applications, Login and Search">
    <div class="nav_top_flex">
        <?php
        $strClassVisible = '';
        if (defined('SX_appsNavigationVisibleInMobiles') && SX_appsNavigationVisibleInMobiles) {
            $strClassVisible = ' nav_top_apps_visible';
        } else { ?>
            <div class="nav_top_apps_Marker" id="jqNavTopAppsMarker">
                <svg class="sx_svg">
                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_menu_apps"></use>
                </svg>
            </div>
        <?php
        } ?>

        <div class="nav_top_apps<?php echo $strClassVisible ?>" id="jqNavTopApps">
            <ul>
                <?php
                if (sx_IncludeSurveys && $radio_IncludePSQ && !empty($str_PSQLinkTitle)) { ?>
                    <li><a href="surveys.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_surveys"></use>
                            </svg>
                            <?= $str_PSQLinkTitle ?></a></li>
                <?php
                }
                if (sx_includeMenu && $radio_IncludeMenu && !empty($str_MenuLinkTitle)) { ?>
                    <li><a href="menu.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_map"></use>
                            </svg>
                            <?= $str_MenuLinkTitle ?></a></li>
                <?php
                }
                if (sx_includeMusic && $radio_IncludeMusic && !empty($str_MusicLinkTitle)) { ?>
                    <li><a href="music.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_music_notes"></use>
                            </svg>
                            <?= $str_MusicLinkTitle ?></a></li>
                <?php
                }
                if (sx_includeFAQ && $radio_IncludeFAQ && !empty($str_FAQLinkTitle)) { ?>
                    <li><a href="faq.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_FAQ"></use>
                            </svg>
                            <?= $str_FAQLinkTitle ?></a></li>
                <?php
                }
                if (sx_includeReports && $radio_IncludeReports && !empty($str_ReportsLinkTitle)) { ?>
                    <li><a href="reports.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_book_open"></use>
                            </svg>
                            <?= $str_ReportsLinkTitle ?></a></li>
                <?php
                } 
                if (sx_includeBooks && $radio_IncludeBooks && !empty($str_BooksLinkTitle)) { ?>
                    <li><a href="books.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_library"></use>
                            </svg>
                            <?= $str_BooksLinkTitle ?></a></li>
                <?php
                }

                if (defined('SX_IncludeAppTextToMaps') && SX_IncludeAppTextToMaps) { ?>
                    <li><a href="map_periegesis.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_map"></use>
                            </svg>
                            <?= SX_IncludeAppTextToMapsTitle ?></a></li>
                <?php
                }

                if (defined('SX_IncludeAppSearchMaps') && SX_IncludeAppSearchMaps) { ?>
                    <li><a href="map_search.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_marker"></use>
                            </svg>
                            <?= SX_IncludeAppSearchMapsTitle ?></a></li>
                <?php
                }
                if (defined('SX_IncludeExternalLink') && SX_IncludeExternalLink) { ?>
                    <li><a href="<?php echo SX_IncludeExternalLinkURL ?>" target="_blank"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_new_window"></use>
                            </svg>
                            <?= SX_IncludeExternalLinkTitle ?></a></li>
                <?php
                }
                
                if (sx_includeFilms && $radio_IncludeFilms && sx_includeFilms_OnTop) { ?>
                    <li><a href="films.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_film"></use>
                            </svg>
                            <?= $str_FilmsLinkTitle ?></a></li>
                    <?php
                }

                if (sx_includeFolderGallery && $radio_UseFolderGallery) {
                    /**
                     * Both integrated and separate folder gallery is just for demo
                     * change the codes in production using "else", making inner galler default
                     */
                    if ($radio_UseFolderGallery) { ?>
                        <li><a href="photos.php"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_image_gallery"></use>
                                </svg>
                                <?= ($str_FolderGalleryMenuTitle) ?></a></li>
                    <?php
                    }
                    if ($radio_UseSeparateGallery) { ?>
                        <li><a href="ps_gallery_byfolder.php"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_image_gallery"></use>
                                </svg>
                                <?= ($str_FolderGalleryMenuTitle) ?></a></li>
                    <?php
                    }
                }

                if (sx_includeGallery && $radio_UseGallery) {
                    if (empty($str_GalleryMenuTitle)) {
                        $str_GalleryMenuTitle = lngGallery;
                    } ?>
                    <li><a href="ps_gallery.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_image_gallery"></use>
                            </svg>
                            <?= ($str_GalleryMenuTitle) ?></a></li>
                <?php
                }

                if (sx_includeMMGallery && $radio_UseMedia) { ?>
                    <li><a href="ps_media.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_video_gallery"></use>
                            </svg>
                            <?= ($str_MediaMenuTitle) ?></a></li>
                <?php
                }
                if (sx_includePDFArchive && $radio_UsePDF && sx_includePDFArchive_OnTop) { ?>
                    <li><a href="ps_PDF.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_PDF_archives"></use>
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
                if ($radio_UseLinks && sx_IncludeTopLinks) { ?>
                    <li><a href="links.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_link_rings"></use>
                            </svg>
                            <?= $str_LinksTitle ?></a></li>
                <?php
                }
                if (sx_IncludeTopContact) { ?>
                    <li><a href="contact.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_mail_open"></use>
                            </svg>
                            <?= lngContact ?></a></li>
                    <?php
                }
                if (sx_includeUsersLogin && $radio_IncludeUsersLogin && sx_includeUsersLogin_OnTop) {
                    if ($radio__UserSessionIsActive) { ?>
                        <li class="profile_menu"><a href="javascript:void(0)" class="jqToggleDataID" data-id="UserTopMenu">
                                <?= $_SESSION["Users_FirstName"] ?></a>
                            <ul id="UserTopMenu">
                                <li><a href="login.php?pg=edit"><svg class="sx_svg">
                                            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_user_profile"></use>
                                        </svg> <?= lngProfile ?></a></li>
                                <li><a href="login.php?pg=logout"><svg class="sx_svg">
                                            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_user_logout"></use>
                                        </svg> <?= lngLogout ?></a></li>
                            </ul>
                        </li>
                    <?php
                    } else { ?>
                        <li><a href="login.php"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_user"></use>
                                </svg>
                                <?= lngLogin ?></a></li>
                    <?php
                    }
                }
                if (sx_includeParticipantsLogin && $radio_UseParticipantsLogin) {
                    if ($radio__ParticipantSessionIsActive) { ?>
                        <li class="profile_menu">
                            <a href="javascript:void(0)" class="jqToggleDataID" data-id="ProfileMenu">
                                <?= $_SESSION["Part_FirstName"] ?></a>
                            <ul id="ProfileMenu">
                                <li><a href="conferences_login.php?pg=conference"><svg class="sx_svg">
                                            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_pencil"></use>
                                        </svg> <?= lngRegisterForConferences ?></a></li>
                                <li><a href="conferences_login.php?pg=edit"><svg class="sx_svg">
                                            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_user_profile"></use>
                                        </svg> <?= lngEditProfile ?></a></li>
                                <li><a href="conferences_login.php?pg=logout"><svg class="sx_svg">
                                            <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_user_logout"></use>
                                        </svg> <?= lngLogout ?></a></li>
                            </ul>
                        </li>
                    <?php
                    } else { ?>
                        <li><a href="conferences_login.php"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_user"></use>
                                </svg>
                                <?= lngLogin ?></a></li>
                    <?php
                    }
                }
                if ($radio_IncludeCourses && sx_IncludeCourses && sx_IncludeCoursesInTop) { ?>
                    <li><a href="courses.php"><svg class="sx_svg">
                                <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_user"></use>
                            </svg> <?= $str_CoursesLinkTitle ?></a></li>
                    <?php
                }

                if ($radio_UseSearch && sx_IncludeTopSearch) {
                    if (sx_Nobody) { ?>
                        <li><?php sx_getTopSearch() ?></li>
                    <?php
                    } else { ?>
                        <li><a href="search.php"><svg class="sx_svg">
                                    <use xlink:href="../imgPG/sx_svg/sx_symbols.svg?v=2025_09#sx_search"></use>
                                </svg> <?php echo lngSearch ?></a></li>
                    <?php
                    }
                }
                if ($radio_ShowSocialMedia && sx_ShowSocialMediaOnTop) { ?>
                    <li><?php get_Social_Media('Top'); ?></li>
                <?php
                }
                if (sx_RadioMultiLang) { ?>
                    <li>
                        <div id="langFlags">
                            <?php
                            sx_getFlags();
                            ?>
                        </div>
                    </li>
                <?php
                } ?>
            </ul>
        </div>
        <?php
        /**
         * Remove sx_Nobody if language will be always visible in mobiles
         * Remove then, also, language from the above list
         */
        if (sx_RadioMultiLang && sx_Nobody) { ?>
            <li>
                <div id="langFlags">
                    <?php
                    sx_getFlags();
                    ?>
                </div>
            </li>
        <?php
        } ?>

    </div>
</nav>