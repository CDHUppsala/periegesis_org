function sx_LoadCaptcha() {
    $sx.ajax({
        url: "../sxPlugins/captcha/include.php?rand=<?= rand(); ?>",
        type: "POST",
        cache: false,
        scriptCharset: "utf-8",
        success: function(result) {
            $sx('#LoadCaptcha').html(result);
        },
        error: function(xhr, status, error) {
            alert(status + '\n' + xhr.responseText);
        }
    });
}

