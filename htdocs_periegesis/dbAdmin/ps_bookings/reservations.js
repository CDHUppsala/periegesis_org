$(function () {
    var isMouseDown = false;
    var current_TD, current_TR;
    var start_index = -10;
    var end_index = -10;

    /**
     * Draw a dark line between the days, depending on minutes inderval (data-colspan)
     */
    //var css_colspan = $("#jq_Reservations").attr('data-colspan');
    //$('.reservations tr td:nth-child(' + css_colspan + 'n + 2)').css('border-right', '1px solid #999')

    $("#jq_Reservations .tbody_rooms td")
        .mousedown(function () {
            isMouseDown = true;
            current_TD = $(this);
            current_TR = $(this).parent('tr');
            reservation_date = current_TR.attr('data-date');
            start_index = $(this).index() - 3 // substract th-elements;
            start_time = $(this).attr('data-date');

            $(this).toggleClass("bg_gray");
            return false; // prevent text selection
        })
        .mouseover(function () {
            if (isMouseDown && current_TR.index() == $(this).parent('tr').index()) {
                $(this).toggleClass("bg_gray");
                end_index = $(this).index() - 3 // substract th-elements;
                end_time = $(this).attr('data-date');

            }
        })
        .mouseup(function (e) {
            isMouseDown = false;
            var radioContinue = false;
            // Check if selected period contains reservations
            if (current_TD.attr('colspan') == undefined && start_index <= end_index) {
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
                    .find('input[name=RoomID]').val(current_TR.attr('data-roomid'))
                    .end()
                    .find('input[name=CheckinDate]').val(start_time)
                    .end()
                    .find('input[name=CheckoutDate]').val(end_time)
                    .end()
                    .find('input[name=Price]').val(current_TR.attr('data-price'))
                    .end()
                    .find('input[name=Persons]').val(current_TR.attr('data-beds'))
                    .end()
                    .find('input[name=NewPrice]').val(0)
                    .end()
                    .find('select[name=Paid]').val(0)
                    .end()
                    .find('input[name=CustomerName]').val('')
                    .end()
                    .find('input[name=CustomerPhone]').val('')
                    .end()
                    .find('input[name=CustomerEmail]').val('')
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
            url: "ajax_booking.php",
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
                start_index = -10;
                end_index = -10;
            },
            error: function (xhr, status, error) {
                $("#jq_cancel").click();
                alert(error);
                start_index = -10;
                end_index = -10;
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
        start_index = -10;
        end_index = -10;
    });

    /*
    ===============================
        Edit / Delete Reservations
    ===============================
    */
    $("#jq_Reservations td").on("click", ".jq_EditReservation", function () {
        var parent_DIV = $(this).parent();
        $("#jq_ReservationFormEdit")
            .find('input[name=BookingID]').val(parent_DIV.attr('data-bookingid'))
            .end()
            .find('input[name=CustomerID]').val(parent_DIV.attr('data-customerid'))
            .end()
            .find('input[name=AdminID]').val(parent_DIV.attr('data-adminid'))
            .end()
            .find('input[name=RoomID]').val(parent_DIV.attr('data-roomID'))
            .end()
            .find('input[name=CheckinDate]').val(parent_DIV.attr('data-checkin'))
            .end()
            .find('input[name=CheckoutDate]').val(parent_DIV.attr('data-checkout'))
            .end()
            .find('input[name=Persons]').val(parent_DIV.attr('data-persons'))
            .end()
            .find('input[name=Price]').val(parent_DIV.attr('data-price'))
            .end()
            .find('input[name=NewPrice]').val(parent_DIV.attr('data-new_price'))
            .end()
            .find('select[name=Paid]').val(parent_DIV.attr('data-paid'))
            .end()
            .find('input[name=CustomerName]').val(parent_DIV.attr('data-name'))
            .end()
            .find('input[name=CustomerPhone]').val(parent_DIV.attr('data-phone'))
            .end()
            .find('input[name=CustomerEmail]').val(parent_DIV.attr('data-email'))
            .end()
            .parent().fadeIn(300);
    })
    $("#jq_update").click(function (e) {
        e.preventDefault();
        $.ajax({
            url: "ajax_update.php",
            cache: false,
            scriptCharset: "utf-8",
            type: 'POST',
            data: $('#jq_ReservationFormEdit').serialize(),
            success: function (result) {
                this_Form = $('#jq_ReservationFormEdit');
                var this_RoomID = this_Form.find('input[name="RoomID"]').val();
                var this_StartDate = this_Form.find('input[name="CheckinDate"]').val();

                $("#jq_Reservations tr[data-roomid=" + this_RoomID + "]")
                    .find("td[data-date='" + this_StartDate + "']")
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
        var this_RoomID = this_Form.find('input[name="RoomID"]').val();
        var this_StartDate = this_Form.find('input[name="CheckinDate"]').val();
        var this_EndDate = this_Form.find('input[name="CheckoutDate"]').val();

        // Get the index of start and end TD to remove reservation from the Table Cells
        this_TR = $("#jq_Reservations tr[data-roomid='" + this_RoomID + "']");
        td_start_index = this_TR.find("td[data-date='" + this_StartDate + "']").index() - 3;
        td_end_index = this_TR.find("td[data-date='" + this_EndDate + "']").index() - 3;
        this_TR.find('td').eq(td_start_index).removeAttr('colspan').html('')
        for (c = td_start_index; c < td_end_index; c++) {
            this_TR.find('td').eq(c + 1).removeClass('dipslay_none');
        }
        // Delete reservation from the database
        var $iBookingID = this_Form.find('input[name="BookingID"]').val();
        $.ajax({
            url: "ajax_delete.php",
            cache: false,
            scriptCharset: "utf-8",
            type: 'POST',
            data: "BookingID=" + $iBookingID,
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

    $(".jq_FilterBeds").click(function () {
        var iBeds = $(this).closest('tr').attr('data-beds')
        $('.tbody_rooms').find('tr')
            .fadeToggle(400)
            .end()
            .find('tr[data-beds=' + iBeds + ']')
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
                .next('.tbody_rooms').fadeIn(400)
                .siblings('.tbody_rooms')
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
                .siblings('.tbody_rooms')
                .fadeIn(400);
        }
    })

    $(".jq_SelectDate").click(function () {
        var selected_date = $(this).parent().attr('data-select_date')
        $(this).parent().toggleClass('selected_day');
        $('.tbody_rooms').find('td[data-date="' + selected_date + '"]')
            .toggleClass('selected_day');
    })

});