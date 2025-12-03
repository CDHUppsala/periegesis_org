function sxChangeRadioValue(el, id) {
    radioEl = document.getElementById(id);
    if (el.checked == 1) {
        radioEl.value = 'Yes';
    } else {
        radioEl.value = 'No';
    }
}
$(document).ready(function () {
    var start = 0;
    $("table .reorder").on("click", function () {
        $('table td:nth-child(2)').each(function () {
            if (start == 0) {
                start = parseFloat($(this).text());
            }
            $(this).text(start);
            start++
        });
        start = 0;
    });

    $(".clone").on("click", function () {
        $tr = $(this).closest("tr").removeClass('red').clone(true, true);
        $tr.insertAfter($(this).closest("tr").addClass('red')).end().addClass('yellow');
        $("table .reorder").click();
    });
    $(".remove").on("click", function () {
        $(this).closest("tr").remove();
        $("table .reorder").click();
    });

    $(".up,.down,.top,.bottom").click(function () {
        var row = $(this).parents("tr:first");
        if ($(this).is(".up")) {
            row.insertBefore(row.prev());
        } else if ($(this).is(".down")) {
            row.insertAfter(row.next());
        } else if ($(this).is(".top")) {
            row.insertBefore($("table tr:first"));
        } else {
            row.insertAfter($("table tr:last"));
        }
    });

    $("table .jq_sessions").on("click", function () {
        var papers = $(this).attr('id-data');
        if (papers == 0) {
            papers = 3;
        }
        start = 0;
        $('table td:nth-child(6)').each(function (index) {
            if (start == 0) {
                start = parseFloat($(this).text());
            }
            $(this).text(start);
            if (((index + 1) % papers) == 0) {
                start++;
            }
        });
        start = 0;
        var add = true;
        var check = false;
        $('table tr').removeClass();
        var bg = 'gray';
        $('table tbody tr').each(function (index) {
            var txt = "Row";
            if (((index) % papers) == 0 && index > 0) {
                var num = index / papers;
                if ((num % 2 == 0)) {
                    add = true;
                    txt = index + '/True/' + num;
                } else {
                    add = false;
                    txt = (index + '/False/' + num);
                }
            }
            if (add) {
                $(this).addClass(bg);
            }
            //alert(txt);
        });

    });

    $('td.jq_time').on("click", function () {
        $(this).addClass('yellow');
        var value = $(this).text();
        var tr_index = $(this).closest('tr').index() + 1;
        var td_index = $(this).index();
        var offset = $(this).offset();
        var time_input = '<input type="time" id-tr="' + tr_index + '" id-td="' + td_index + '" id-value="' + value + '" value="' + value + '">';
        $('#change_content').css({
            'left': (offset.left - 5),
            'top': (offset.top + 44)
        }).find('input').remove().end().prepend(time_input).fadeIn(200);
    });

    $('td.jq_text').on("click", function () {
        $(this).addClass('yellow');
        var value = $(this).text();
        var tr_index = $(this).closest('tr').index() + 1;
        var td_index = $(this).index();
        var offset = $(this).offset();
        var time_input = '<input type="text" id-tr="' + tr_index + '" id-td="' + td_index + '" id-value="' + value + '" value="' + value + '">';
        $('#change_content').css({
            'left': (offset.left - 5),
            'top': (offset.top + 44)
        }).find('input').remove().end().prepend(time_input).fadeIn(200);
    });

    $(".jq_change_content").click(function () {
        var div = $(this).closest('div');
        var input = div.find('input');
        var tr = input.attr('id-tr');
        var td = input.attr('id-td');
        var org_value = input.attr('id-value');
        var type = input.attr('type');
        var value = input.val();
        if (value != org_value) {
            if (type == 'time' && value.length < 8) {
                $('table').find('tr').eq(tr).find('td').eq(td).text(value + ':00').removeClass('yellow').addClass('red');
            } else {
                $('table').find('tr').eq(tr).find('td').eq(td).text(value).removeClass('yellow').addClass('red');
            }
        } else {
            $('table').find('tr').eq(tr).find('td').eq(td).removeClass('yellow');
        }
        div.find('input').remove();
        div.fadeOut(200);
    });

    $(".js_info").on("click", function () {
        $('#' + $(this).attr('id-data')).fadeIn(300);
    });

    $(".jq_Hide").click(function () {
        $(this).closest('.info_box').fadeOut(300);
    });


    /**
     * Add the created Bluprint to the Blueprint Table
     */

    function getBluePrint() {
        var table = $('table');
        var data = [];

        table.find('tr').each(function (i, el) {
            // no thead
            if (i != 0) {
                var $tds = $(this).find('td');
                var row = [];
                $tds.each(function (c, el) {
                    if (c != 0) {
                        row.push($(this).text());
                    }
                });
                data.push(row);
            }

        });
        return data;
    }
    $('#Replace_Blueprint_Table').on("click", function (e) {
        e.preventDefault();
        $.ajaxSetup({
            cache: false
        });
        var arr_Data = getBluePrint();
        var json_Date = JSON.stringify(arr_Data);
        $.ajax({
            type: "POST",
            url: "ajax_SaveToBlueprint.php",
            data: {
                data: json_Date
            },
            scriptCharset: "utf-8",
            cache: false,
            success: function (result) {
                alert('The Program Schedule has been Successfully Saved in the Blueprint Table!')
                return;
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    })

});