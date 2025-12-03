<section class="jqNavMainToBeCloned">
    <?php
    if ($radio___ForumMemberIsActive) { ?>
        <section>
            <h2 class="head"><?= lngWelcome ?>
                <br><span class="text_xxsmall"><?= mb_substr($_SESSION["Forum_FirstName"], 0, 1) . ". " . $_SESSION["Forum_LastName"] ?></span>
            </h2>
        </section>
    <?php
    } ?>

    <h2 class="head"><span><?= lngNavigation ?></span></h2>
    <nav class="nav_aside">
        <ul>
            <?php
            if ($radio___ForumMemberIsActive) { ?>
                <li><a href="forum_login.php?pg=logout"><?= lngLogout ?></a></li>
                <li><a href="forum_login.php?pg=leave"><?= lngLeave ?></a></li>
                <li><a href="forum_login.php?pg=edit"><?= lngChangeProfile ?></a></li>
            <?php
            } else { ?>
                <li><a href="forum_login.php?pg=login"><?= lngLogin ?></a></li>
                <li><a href="forum_login.php?pg=join"><?= lngJoin ?></a></li>
                <li><a href="forum_login.php?pg=forgot"><?= lngForgotPassword ?></a></li>
            <?php
            } ?>
            <li><a href="forum_login.php?pg=conditions"><?= lngParticipationTerms ?></a></li>
            <li><a href="forum.php"><?= lngForumPage ?></a></li>
        </ul>
    </nav>
</section>