<script>
    jQuery(function($) {
        $(".jq_updateHelpByGroupTable").submit(function(e) {
            e.preventDefault();
            var sxURL = $(this).attr("data-url");
            var arrData = $(this).serialize();
            $.ajax({
                type: "POST",
                cache: false,
                url: sxURL,
                data: arrData,
                dataType: "html",
                scriptCharset: "utf-8",
                success: function(result) {
                    alert("Saving was successful" + result);
                },
                error: function(err) {
                    alert("Saving was unsuccessful! " + err);
                },
            });
        });
    });
</script>