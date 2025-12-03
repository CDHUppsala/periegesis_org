<?PHP
/**
 * NOT USED
 */
?>
<section>
    <div class="grid_cards_wrapper">
        <div class="align_center">
            <h1 class="head"><span>Annotation statistics</span></h1>
            <h3 id=stats_update></h3>
            <p>Statistics over the persons, places and events from Pausanias Periegesis that have hitherto been annotated in maps.</p>
        </div>
        <div id="stats" class="grid_cards">
            <img style="width: 134px; height: auto; margin: 0 auto" src="../imgPG/LoaderIcon.gif" alt="Loader">
        </div>
    </div>
</section>
<script>
    jQuery(function($) {
        function sx_load_json_statistics() {
            var html = '';
            var icon = ''
            // Ajax returns a JSON string
            $.ajax({
                url: "apps/ajax_statistics.php",
                type: "POST",
                cache: false,
                scriptCharset: "utf-8",
                success: function(result) {
                    //alert(result);
                    var arr_json = $.parseJSON(result);
                    $.each(arr_json, function(key, value) {
                        if (key == "Persons") {
                            icon = "../images/icons/Human-statue.svg";
                        } else if (key == "Places") {
                            icon = "../images/icons/Akropolis.svg";
                        } else if (key == "Events") {
                            icon = "../images/icons/Theater.svg";
                        } else {
                            icon = '';
                        }
                        if (icon != '') {
                            html += `<figure class="img_contain">`;
                            html += `<img src="${icon}" attr="" style="height:120px">`;
                            html += `<figcaption class="align_center">`;
                            html += `<h4> ${key}</h4>`;
                            html += `<p>${value.toLocaleString("en-SE")}</p>`;
                            html += `</figcaption></figure>`;
                        } else {
                            $("#stats_update").html(`As of ${new Date(value * 1000).toLocaleDateString("en-SE")}`);
                        }
                    });
                    $("#stats").html(html);
                },
                error: function(xhr, textStatus, error) {
                    alert(xhr.statusText + ' / ' + textStatus + ' / ' + error);
                }
            });
        };
        sx_load_json_statistics();
    });
</script>