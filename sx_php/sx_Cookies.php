<?php
if (!isset($_COOKIE["cookie_eu"]) && $radio_ShowAcceptCookies) { ?>
    <div class="accept_cookies jqAcceptCookies">
        <?php
        if (!empty($str_CookiesTitle)) {
            echo "<h3>$str_CookiesTitle</h3>";
        }
        if (!empty($str_CookiesNotes)) {
            echo "<p>$str_CookiesNotes</p>";
        } ?>
        <p><button class="jqRemoveAcceptCookies"><?= lngAcceptCookies ?></button>
            <?php if (!empty($str_CookiesPolicy)) { ?>
                <a href="sx_PrintPage.php?print=cookies" onclick="openCenteredWindow(this.href,'cookies','580','500');return false;"><?= lngMore ?></a>
            <?php } ?>
        </p>
    </div>
<?php
} ?>