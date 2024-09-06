jQuery(document).ready(function($) {
    // string date to timestamp
    function dateStringToTimestamp(dateString) {
        // Parse the date string
        var parts = dateString.split('-');
        var day = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10) - 1; // Months are zero-based in JavaScript
        var year = parseInt(parts[2], 10);
        // Create a Date object
        var date = new Date(year, month, day);
        // Get the timestamp
        var timestamp = date.getTime()/1000;
        return timestamp;
    }

    // Toggle visibility of the variations data when the edit button is clicked
    $("body").on("click", "#event_custom_box_id .button-edit", function (e) { 
        e.preventDefault();
        $(this).closest(".box-data-variations").find(".field-text-hotel").slideToggle(200);
        $(this).closest(".box-data-variations").find(".data-variations").slideToggle(200);
    });

    // Remove the variation box and corresponding hidden input when the remove button is clicked
    $("body").on("click", "#event_custom_box_id .button-remove", function (e) {
        e.preventDefault();
        var id_hotel = $(this).data("hotel");
        $("#hiddenInputs").find(id_hotel).remove();
        $(this).closest(".box-data-variations").remove();
    });

    // Set the date for the variation when the set date button is clicked, and open the modal calendar
    $("body").on("click", "#event_custom_box_id .button-setdate", function (e) {
        e.preventDefault();
        var id_variation = $(this).data('variation') + "_available";
        $(".modal-calendar").data('value', id_variation);
        $('#calendar-available').datepicker('refresh');
        $("body").addClass("open-modal");
    });

    // Close the modal calendar when the close icon is clicked
    $("body").on("click", "#event_custom_box_id .modal-calendar .close-icon", function (e) {
        e.preventDefault();
        $("body").removeClass("open-modal");
        $("body").removeClass("open-modal-stock");
    });

    // Close the modal set stock day when the close icon is clicked
    $("body").on("click", "#event_custom_box_id .modal-set-stock-day .close-icon", function (e) {
        e.preventDefault();
        $("body").removeClass("open-modal-stock");
    });

    // Close the modal calendar or modal set stock day when the background is clicked
    $("body").on("click", "#event_custom_box_id .bg-modal-open", function (e) {
        e.preventDefault();
        $("body").removeClass("open-modal");
        $("body").removeClass("open-modal-stock");
    });

    // Close the modal set stock day when the background is clicked
    $("body").on("click", "#event_custom_box_id .bg-modal-open-stock", function (e) {
        e.preventDefault();
        $("body").removeClass("open-modal-stock");
    });

    // Save the selected date and stock for the variation when the save button is clicked
    $("body").on("click", ".save-each-date-select", function (e) {
        e.preventDefault();
        var data_date = $(".modal-set-stock-day .day-select").val(),
            stock = $(".modal-set-stock-day .stock-day").val(),
            id_variation = $('.modal-calendar').data('value');

        // Remove existing input with the same date
        $(''+id_variation+' .day-available input').each(function() {
            if($(this).val() == data_date){
                $(this).remove();
            }
        });

        // Add new input if the action day switch is checked
        if ($(".action-day-switch").is(':checked')) {
            $(''+id_variation+' .day-available').append("<input type='hidden' name='day' value='"+data_date+"' data-stock='"+stock+"'>");
        }
        
        $('#calendar-available').datepicker('refresh');
        $("body").removeClass("open-modal-stock");
    });

   // Attach a click event listener to elements with the class 'save-variation-data'
    $("body").on("click", ".save-variation-data", function (e) {
        // Prevent the default action of the click event
        e.preventDefault();
        $("#event_custom_box_id").addClass("ajax-load");
        var event_id = $(this).data('event');

        // Initialize an empty array to hold all the data to be posted
        var postData = [];

        // Loop through each '.box-data-variations' element inside '.wrap-box-data-variations'
        $('.wrap-box-data-variations .box-data-variations').each(function() {
            // Retrieve data attributes for hotel and product IDs
            var hotel_id = $(this).data('hotel'),
                product_id = $(this).data('product'),
                field_text = $(this).find('.field-text-hotel').val();

            // Create an object to hold the data for the current box
            var boxData = {
                hotel_id: hotel_id,
                product_id: product_id,
                field_text: field_text,
                variations_data: []  // Initialize an empty array for variations data
            };

            // Loop through each '.variations' element within the current box
            $(this).find('.variations').each(function() {
                // Retrieve the variations ID and input values for maximum and price
                var variations_id = $(this).data('variations'),
                    maximum = $(this).find('.maximum').val(),
                    price = $(this).find('.price').val();

                // Create an object to hold the data for the current variation
                var variations = {
                    variations_id: variations_id,
                    maximum: maximum,
                    price: price,
                    date_available: []  // Initialize an empty array for available dates
                };

                // Loop through each input within '.day-available' in the current variation
                $(this).find('.day-available input').each(function() {
                    // Retrieve the date value and stock data attribute
                    var data_date = $(this).val(),
                        stock = $(this).data('stock');

                    // Create an object to hold the data for the current date
                    var date_array = {
                        date: data_date,
                        timestamp: dateStringToTimestamp(data_date),
                        stock: stock,
                    };

                    // Add the current date object to the 'date_available' array of the variation
                    variations.date_available.push(date_array);
                });

                // Add the current variation object to the 'variations_data' array of the box
                boxData.variations_data.push(variations);
            });

            // Add the current box object to the 'postData' array
            postData.push(boxData);
        });
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'save_variation_data',
                postData: postData,
                event_id: event_id 
            },
            success: function (response) {
                $("#event_custom_box_id").removeClass("ajax-load");
            },
            error: function (err) {
                console.log(err);
            }
        });
    });

    // Handle hotel selection change and load the corresponding event data via AJAX
    $(".select-hotel-event").on("change", function () { 
        const values = [];
        $('.hotel-id').each(function() {
            var value = $(this).val();
            values.push(value);
        }); 
        var hotel_id = $(this).val();
        if (!values.includes(hotel_id)) {
            $("#event_custom_box_id").addClass("ajax-load");
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'select_event_hotel',
                    hotel_id: hotel_id 
                },
                success: function (response) {
                    if (response.success == true) {
                        $('.wrap-box-data-variations').append(response.html);
                        var hiddenInputs = $('#hiddenInputs');
                        hiddenInputs.append('<input type="hidden" id="' + response.hotel_id + '_hotel" class="hotel-id" name="hotel_id[]" value="' + response.hotel_id + '">');
                    }
                    $("#event_custom_box_id").removeClass("ajax-load");
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }
    });

    // Initialize datepicker with disabled dates
    // var dates = ['25-05-2024', '27-05-2024'];
    var dates = [];

    $('#calendar-available').datepicker({
        minDate: 0,
        beforeShowDay: function(date){
            var string = jQuery.datepicker.formatDate('dd-mm-yy', date);
            return [dates.indexOf(string) == -1];
        }
    });
});