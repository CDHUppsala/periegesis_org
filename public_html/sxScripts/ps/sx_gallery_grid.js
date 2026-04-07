/*
Not used

*/

$sx(function () {

    function sx_show_loading(parent) {
        parent.css({
            "background-position": "50% 4rem"
        })
    }

    function sx_hide_loading(parent) {
        parent.css({
            "background-position": "-1000% 4rem"
        })
    }

    $sx('imgs_by_row span').on('click', function () {
        var columns = $sx(this).attr('data-id');
        var photo_grid = $sx('.photo_grid');
        var parent = photo_grid.parent();
        sx_show_loading(parent);
        photo_grid.fadeOut(300, function () {
            if (columns == 1) {
                photo_grid.css({'grid-template-columns': '1fr', 'gap': '1rem'})
                    .fadeIn(300, function () {
                        sx_hide_loading(parent);
                    });
            } else if (columns == 3) {
                photo_grid.css({'grid-template-columns': '1fr 1fr 1fr', 'gap': '0.5rem'})
                    .fadeIn(300, function () {
                        sx_hide_loading(parent);
                    });
            } else if (columns == 5) {
                photo_grid.css({'grid-template-columns': '1fr 1fr 1fr 1fr 1fr', 'gap': '0.25rem'})
                    .fadeIn(300, function () {
                        sx_hide_loading(parent);
                    });
            } else {
                photo_grid.css({'grid-template-columns': '1fr 1fr', 'gap': '1rem'})
                    .fadeIn(300, function () {
                        sx_hide_loading(parent);
                    });
            };
        });
    });

});
