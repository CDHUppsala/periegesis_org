$(function () {
    var isMouseDown = false;
    var current_TD, current_TR;
    var start_index, end_index;

    var reservation_date = $("#jq_Reservations").attr('data-date');
    /**
     * Draw a dark line between the days, depending on minutes inderval (data-colspan)
     */
    var css_colspan = $("#jq_Reservations").attr('data-colspan');
    $('.reservations tr td:nth-child(' + css_colspan + 'n + 2)').css('border-right', '1px solid #999')

    $("#jq_Reservations td")
        .mousedown(function () {
            isMouseDown = true;
            current_TD = $(this);
            current_TR = $(this).parent('tr');
            start_index = $(this).index() - 2 // substract th-elements;
            start_time = $(this).attr('data-time');
            $(this).toggleClass("bg_gray");
            return false; // prevent text selection
        })
        .mouseover(function () {
            if (isMouseDown && current_TR.index() == $(this).parent('tr').index()) {
                $(this).toggleClass("bg_gray");
                end_index = $(this).index() - 2 // substract th-elements;
                end_time = $(this).attr('data-time');

            }
        })
        .mouseup(function () {
            isMouseDown = false;
            var radioContinue = false;
            // Check if selected period contains reservations
            if (current_TD.attr('colspan') == undefined && start_index < end_index) {
                radioContinue = true;
                for (c = start_index; c < end_index; c++) {
                    if (current_TR.find('td').eq(c + 1).attr('colspan') != undefined) {
                        radioContinue = false;
                    }
                }
            }
            if (radioContinue) {
                var cspan = 2;
                for (c = start_index; c < end_index; c++) {
                    current_TR.find('td').eq(start_index).attr('colspan', cspan);
                    current_TR.find('td').eq(c + 1).addClass('dipslay_none');
                    cspan++;
                }
                // Add values to form inputs
                $("#jq_ReservationForm")
                    .find('input[name=TableID]').val(current_TR.attr('data-table'))
                    .end()
                    .find('input[name=ReservationDate]').val(reservation_date)
                    .end()
                    .find('input[name=StartTime]').val(start_time)
                    .end()
                    .find('input[name=EndTime]').val(end_time)
                    .end()
                    .find('input[name=CustomerName]').val('')
                    .end()
                    .find('input[name=CustomerPhone]').val('')
                    .end()
                    .find('input[name=SeeatsNumber]').val('')
                    .end()
                    .parent().fadeIn(300);
            } else {
                $("#jq_Reservations td").removeClass('bg_gray');
            }
        });

    /*
    ===============================
        Add / Cancel Reservations
    ===============================
    */

    $("#jq_add").click(function (e) {
        e.preventDefault();
        $.ajax({
            url: "ajax_reservations.php",
            cache: false,
            scriptCharset: "utf-8",
            type: 'POST',
            data: $('#jq_ReservationForm').serialize(),
            success: function (result) {
                if (result == 'Reserved') {
                    $("#jq_cancel").click();
                    alert('The table is allready reserved for this time period!');
                } else {
                    current_TD.html(result);
                    $("#jq_ReservationForm").parent().fadeOut(300);
                    $("#jq_Reservations td").removeClass('bg_gray');
                }
            },
            error: function (xhr, status, error) {
                $("#jq_cancel").click();
                alert(xhr.responseText);
            }
        });
    });

    $("#jq_cancel").click(function (e) {
        $("#jq_Reservations td").removeClass('bg_gray');
        current_TD.removeAttr('colspan');
        for (c = start_index; c < end_index; c++) {
            current_TR.find('td').eq(c + 1).removeClass('dipslay_none');
        }
        $("#jq_ReservationForm").parent().fadeOut(300);
    });

    /*
    ===============================
        Edit / Delete Reservations
    ===============================
    */
    $("#jq_Reservations td").on("click", ".jq_EditReservation", function () {
        var parent_DIV = $(this).parent()
        $("#jq_ReservationFormEdit")
            .find('input[name=ReservationID]').val(parent_DIV.attr('data-resid'))
            .end()
            .find('input[name=TableID]').val(parent_DIV.attr('data-tableid'))
            .end()
            .find('input[name=ReservationDate]').val(parent_DIV.attr('data-date'))
            .end()
            .find('input[name=StartTime]').val(parent_DIV.attr('data-start'))
            .end()
            .find('input[name=EndTime]').val(parent_DIV.attr('data-end'))
            .end()
            .find('input[name=CustomerName]').val(parent_DIV.attr('data-name'))
            .end()
            .find('input[name=CustomerPhone]').val(parent_DIV.attr('data-phone'))
            .end()
            .find('input[name=SeeatsNumber]').val(parent_DIV.attr('data-seats'))
            .end()
            .parent().fadeIn(300);
    })

    $("#jq_update").click(function (e) {
        e.preventDefault();
        $.ajax({
            url: "ajax_reservations_update.php",
            cache: false,
            scriptCharset: "utf-8",
            type: 'POST',
            data: $('#jq_ReservationFormEdit').serialize(),
            success: function (result) {
                this_Form = $('#jq_ReservationFormEdit');
                var this_TableID = this_Form.find('input[name="TableID"]').val();
                var this_StartTime = this_Form.find('input[name="StartTime"]').val();

                $("#jq_Reservations tr[data-table=" + this_TableID + "]")
                    .find("td[data-time='" + this_StartTime + "']")
                    .html(result);
                $("#jq_close").click();
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    });


    $("#jq_delete").click(function (e) {
        e.preventDefault();
        // Get form values
        this_Form = $('#jq_ReservationFormEdit');
        var this_TableID = this_Form.find('input[name="TableID"]').val();
        var this_StartTime = this_Form.find('input[name="StartTime"]').val();
        var this_EndTime = this_Form.find('input[name="EndTime"]').val();

        // Get the index of start and end TD to remove reservation from the Table Cells
        this_TR = $("#jq_Reservations tr[data-table='" + this_TableID + "']");
        td_start_index = this_TR.find("td[data-time='" + this_StartTime + "']").index() - 2;
        td_end_index = this_TR.find("td[data-time='" + this_EndTime + "']").index() - 2;
        this_TR.find('td').eq(td_start_index).removeAttr('colspan').html('')
        for (c = td_start_index; c < td_end_index; c++) {
            this_TR.find('td').eq(c + 1).removeClass('dipslay_none');
        }
        // Delete reservation from the database
        var iReservationID = this_Form.find('input[name="ReservationID"]').val();
        $.ajax({
            url: "ajax_reservations_delete.php",
            cache: false,
            scriptCharset: "utf-8",
            type: 'POST',
            data: "ReservationID=" + iReservationID,
            success: function (result) {
                if (result == 'Success') {
                    this_Form.find("#jq_close").click();
                } else {
                    alert('Error: Record not Found! R:' + result)
                }
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    });


    $("#jq_close").click(function () {
        $("#jq_ReservationFormEdit").parent().fadeOut(300);
    });

    /*
    ===============================
        Filter Tables
    ===============================
    */

    $(".jq_TogggleUpDpwn").click(function () {
        $(this).toggleClass('toggle_up toggle_down')
            .closest('tbody')
            .next('tbody')
            .fadeToggle(400);
    });

    $(".jq_FilterSeats").click(function () {
        var iSeats = $(this).closest('tr').attr('data-seats')
        $('.tbody_items').find('tr')
            .fadeToggle(400)
            .end()
            .find('tr[data-seats=' + iSeats + ']')
            .fadeIn(400);
    })

    $('.jq_FilterLocation').click(function () {
        var txt = $(this).text();
        if (txt == "THIS") {
            $(this).text('ALL')
                .parent().find('.jq_TogggleUpDpwn')
                .removeClass('toggle_down')
                .addClass('toggle_up')
                .closest('tbody')
                .siblings('tbody')
                .find('.jq_TogggleUpDpwn')
                .removeClass('toggle_up')
                .addClass('toggle_down')
                .end()
                .find('.jq_FilterLocation').text('THIS')
                .end()
                .end()
                .next('.tbody_items').fadeIn(400)
                .siblings('.tbody_items')
                .fadeOut(400);
        } else {
            $(this).text('THIS')
                .parent().find('.jq_TogggleUpDpwn')
                .removeClass('toggle_down')
                .addClass('toggle_up')
                .closest('tbody')
                .siblings('tbody')
                .find('.jq_TogggleUpDpwn')
                .removeClass('toggle_down')
                .addClass('toggle_up')
                .end()
                .siblings('.tbody_items')
                .fadeIn(400);
        }
    });

    $(".jq_marking_cells").click(function () {
        var this_index = $(this).index() - 2
        $('.tbody_items tr').each(function() {
            $(this).find('td').eq(this_index).toggleClass('marked_cell');
        });
    });

});