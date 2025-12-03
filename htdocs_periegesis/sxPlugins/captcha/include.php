<?php
$iSuffix = rand(0,5)
?>

<img id="captcha_image" alt="Captcha" src="../sxPlugins/captcha/captcha_<?php echo $iSuffix ?>.php?rand=<?php echo rand(); ?>" />
<a class="refresh_captcha" href="javascript:void(0)" onclick="sx_refreshCaptcha(); return false;">
    <img title="Refresh Captcha" src="../imgPG/sx_svg/sx_svg/sx_reload.svg" alt="Refresh" />
</a>
<script>
    function sx_refreshCaptcha() {
        document.getElementById("captcha_image").src = '../sxPlugins/captcha/captcha_<?php echo $iSuffix ?>.php?<?php echo microtime();?>';
    }
</script>
