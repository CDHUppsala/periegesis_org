<script>
    /**@abstract
     * Global function that can be initiated from different jQuery onload instances
     *  - ($) get the jQery instance
     *  - $dataTable is defined by the initiating instance och can be different
     *    from instance to instance
     */
    const wiki_pedia = 'https://en.wikipedia.org/wiki/';
    const wiki_data = 'https://www.wikidata.org/wiki/';
    var radio_bookIsLoaded = false;

    var sx_activate_book_chapters_maps = function($, $dataTable) {
        $dataTableRows = $dataTable.find('tbody tr');

        if ($dataTableRows.find('span.jq_book_section').length) {
            $dataTableRows.find('span.jq_book_section').css({
                'color': 'blue',
                'cursor': 'pointer'
            });


            var html_overflow;
            let click_object = $dataTableRows.find('span.jq_book_section');
            click_object.on('click', function() {
                var book_id = $(this).text();
                $('#jq_ModalContentWide').addClass('sx_modal_content_wide');
                $('#jq_ModalContent').html('<p class="align_center"><img src="../imgPG/LoaderIcon.gif" alt="" style="margin: 4rem auto"></p>');
                $("#jq_Modal").slideDown(200);
                html_overflow = ($('html,body').css('overflow'));
                if (html_overflow == 'visible') {
                    $('html,body').css('overflow', 'hidden');
                }
                if (radio_bookIsLoaded === false) {
                    var data = '';
                    var url = "apps/ajax_pausanias.php";
                    $.post(url, data, function(data, status) {
                        $("#js_load_hidden_ajax").html(data);

                        var section_html = return_section_html(book_id)
                        $("#jq_ModalContent").html(section_html);
                        set_titles();
                        activate_navigation();
                        radio_bookIsLoaded = true;
                    });
                } else {
                    var section_html = return_section_html(book_id)
                    $("#jq_ModalContent").html(section_html);
                    set_titles();
                    activate_navigation();

                }

            });
        }

        function return_section_html(book_id) {
            var section_en = $('#js_Pausanias_Books_en p[id="' + book_id + '"]').html();
            var section_el = $('#js_Pausanias_Books_el p[id="' + book_id + '"]').html();

            prev_book_id = $('#js_Pausanias_Books_en p[id="' + book_id + '"]').prev().attr('id');
            next_book_id = $('#js_Pausanias_Books_en p[id="' + book_id + '"]').next().attr('id');

            var arr_book = book_id.split('.');
            var title = '<h3>' + $('#js_Pausanias_Books_en h2').text() + ' - ' + $('#js_Pausanias_Books_el h2').text() + '</h3>';
            var book_title = '<h4>Book: ' + arr_book[0] + ', Chapter: ' + arr_book[1] + ', Section: ' + arr_book[2] + '</h4>';
            var navigation = '<div class="flex_between">' + book_title +
                '<div style="color:blue; cursor:pointer" id="js_section_nav"><span class="js_previous" data-id="' + prev_book_id + '">Previous</span> | ' +
                '<span class="js_next" data-id="' + next_book_id + '">Next</span></div>';
            var html = '<div class="sx_modal_ajax_content" id="jq_ModalAjaxContent"><p>' + section_en + '</p>' +
                '<p class="polytonic_font">' + section_el + '</p></div>';

            return title + navigation + html;
        }

        $("#jq_ModalClose").click(function() {
            $("#jq_Modal").slideUp(300);
            if (html_overflow == 'visible') {
                $('html,body').css('overflow', 'visible');
            };
            $("#jq_ModalContent").html("");
            $('#jq_ModalContentWide').removeClass('sx_modal_content_wide');
        });

        var activate_navigation = function() {
            $('#js_section_nav span').on('click', function() {
                var book_id = $(this).attr('data-id');
                var section_html = return_section_html(book_id)
                $("#jq_ModalContent").html(section_html);
                set_titles();
                activate_navigation();
            })
        }

        var set_titles = function() {

            // Handle PL and PRN uniformly
            $('#jq_ModalAjaxContent pl, #jq_ModalAjaxContent prn').each(function() {

                var $el = $(this);

                // If an <a> exists inside → do NOTHING
                if ($el.find('a').length > 0) {
                    return; // skip to next element
                }

                // No anchor: add title/link logic
                var currTitle = $el.attr('title');
                var label = this.tagName === 'PL' ? 'PLACE' : 'PERSON';

                var site = 'WIKIDATA';
                var strID = $el.attr('id');

                if ($el.attr('data-wiki') !== undefined) {
                    site = 'WIKIPEDIA';
                    strID = $el.attr('data-wiki');
                }

                // Set the title
                $el.attr('title', label + ': ' + currTitle + ' (Open in ' + site + ', ID: ' + strID + ')');
            });


            // Click handler — but only for elements without <a>
            $('#jq_ModalAjaxContent').on('click', 'pl, prn', function(e) {

                // If the clicked item contains an <a>, do nothing 
                if ($(this).find('a').length > 0) {
                    return;
                }

                var $el = $(this);
                var url = wiki_data;
                var sufix = $el.attr('id');

                if ($el.attr('data-wiki') !== undefined) {
                    url = wiki_pedia;
                    sufix = $el.attr('data-wiki');
                }

                var win = window.open(url + sufix, '_blank');
                if (win) win.focus();
                else alert('Please allow popups for this website');
            });
        };

        if ($dataTableRows.find('span.jq_LoadMap').length) {
            $dataTableRows.find('span.jq_LoadMap').css({
                'color': 'green',
                'cursor': 'pointer'
            });
        }

        $('.jq_LoadMap').on('click', function(e) {
            e.preventDefault();
            if ($(this).attr('data-place').length) {
                $strTitle = $(this).attr('data-place');
            } else {
                $strTitle = $(this).closest('tr').find('td.Places').text();
            }
            $strDesc = $(this).closest('tr').find('td.TagsAndComments').text();
            $('#js_MapNotes').text($strTitle + ': ' + $strDesc);

            const lat = $(this).attr('data-lat');
            const lng = $(this).attr('data-lng');

            $('#jq_ModalMap').slideDown(300, function() {
                loadMapScenario(lat, lng, $strTitle);
            });
        })

        $("#jq_CloseModalMap").click(function() {
            $("#jq_ModalMap").slideUp(300, function() {
                if (sx_LeafletMap && typeof sx_LeafletMap.remove === 'function') {
                    try {
                        sx_LeafletMap.remove();
                    } catch (e) {}
                    sx_LeafletMap = null;
                }
            });
        });
    }
</script>