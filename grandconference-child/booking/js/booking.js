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

    // get current day timestamp
    function getCurrentDayTimestamp() {
        // Get the current date
        var currentDate = new Date();
        // Set the time to midnight
        currentDate.setHours(0, 0, 0, 0);
        // Get the timestamp in milliseconds
        return currentDate.getTime()/1000;
    }

    // calendar booking
    $("#calendar-booking").datepicker({
        minDate: 0,
        dateFormat: "dd-mm-yy", // Set the date format to dd-mm-yy
        beforeShowDay: function(date) {
            var dateFormat = "dd-mm-yy"; // Define the date format
            var date1 = $.datepicker.parseDate(dateFormat, $("#start_day").val());
            var date2 = $.datepicker.parseDate(dateFormat, $("#end_day").val());
            return [true, date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2)) ? "dp-highlight" : ""];
        },
        onSelect: function(dateText, inst) {
            $("#calendar-booking").addClass("calendar-active");
            var lang = $(".content-details-hotel").data("lang");
            var dateFormat = "dd-mm-yy"; // Define the date format
            var date1 = $.datepicker.parseDate(dateFormat, $("#start_day").val());
            var date2 = $.datepicker.parseDate(dateFormat, $("#end_day").val());
            var selectedDate = $.datepicker.parseDate(dateFormat, dateText);
    
            if (!date1 || date2) {
                $("#start_day").val(dateText);
                $("#end_day").val("");
                $(this).datepicker();
            } else if (selectedDate < date1) {
                $("#end_day").val($("#start_day").val());
                $("#start_day").val(dateText);
                $(this).datepicker();
            } else {
                $("#end_day").val(dateText);
                $(this).datepicker();
            }
            var start_day = $("#start_day").val(),end_day = $("#end_day").val();
            if(start_day !="" && end_day !=""){
                $("body").addClass("ajax-load");
                start_day = dateStringToTimestamp(start_day);
                end_day = dateStringToTimestamp(end_day);
                var total = (end_day - start_day)/86400;
                var day_available_list = new Array();
                var data_day = new Array();
                var all_days_available = true;
                var day_available_list_new = new Array();
                
                $('.day-available input').each(function() {
                    var timestamp = $(this).data('timestamp');
                    var stock = $(this).data('stock');
                    day_available_list.push(timestamp);
                    day_available_list_new.push($(this).val());
                    data_day.push({ timestamp: timestamp, stock: stock });
                });

                // console.log(day_available_list_new);

                // Function to get stocks between a range of timestamps
                function getStocksBetween(startTimestamp, endTimestamp) {
                    return data_day.filter(function(item) {
                        return item.timestamp >= startTimestamp && item.timestamp <= endTimestamp;
                    }).map(function(item) {
                        return item.stock;
                    });
                }

                // Function to get the minimum stock value from the stocksInRange array
                function getMinStockValue(stocksInRange) {
                    if (stocksInRange.length === 0) {
                        return null; // or any other value indicating no stocks in range
                    }
                    return Math.min(...stocksInRange);
                }

                var n = 0, location = 0;
                for(var i=0;i<=total;i++){
                    var day_i = start_day + (86400*i);
                    // Create a new Date object from the timestamp
                    const date = new Date(day_i*1000);

                    // Get the day, month, and year from the Date object
                    const day = date.getDate();
                    // const month = date.getMonth() + 1; // Months are zero-indexed in JavaScript
                    const month = (date.getMonth() + 1).toString().padStart(2, '0');
                    const year = date.getFullYear();

                    // Format the date as '4-12-2024'
                    const formattedDate = `${day}-${month}-${year}`;

                    // console.log(formattedDate);
                    if (!day_available_list_new.includes(formattedDate)) {
                        n++;
                        location = i;
                    }
                }
            
                if((n==1 && location == total && start_day != end_day) || (n==0 && start_day != end_day)){
                    var price_js = $(".select_type_of_room").find(':selected').data('price')*total;
                    var currency = $("#currency").val();
                    var price_html = price_js+currency;
                    $('.woocommerce-notices-wrapper').hide();
                    var stocksInRange = getStocksBetween(start_day, end_day - 86400);
                    var minStockValue = getMinStockValue(stocksInRange);
                    $(".wrap-qty-js .qty").val(1);
                    $(".wrap-qty-js .qty").attr("max",minStockValue);
                    $(".wrap-qty-js").removeClass("disable");
                    $("#start_day").data('timestamp',start_day);
                    $("#end_day").data('timestamp',end_day);
                    $(".number-night").text(total);
                    setTimeout(function() { 
                        $(".js-price-html").text(price_html);
                        $(".price-typeroom-new").slideDown(100);
                        $(".description-typeroom-new").slideDown(100);
                        $(".add-to-cart-hotel").slideDown(100);
                    }, 501);
                }else{
                    $(".wrap-qty-js").addClass("disable");
                    if(start_day == end_day){
                        if(lang == "english"){
                            var message_selected = 'The selected date is not valid, please select 2 consecutive days';
                        }else{
                            var message_selected = 'La date sélectionnée n\'est pas valide, veuillez sélectionner 2 jours consécutifs';
                        }
                    }else{
                        if(lang == "english"){
                            var message_selected = 'The date selected is not valid, please select another date';
                        }else{
                            var message_selected = 'La date sélectionnée n\'est pas valide, veuillez sélectionner une autre date';
                        }
                    }

                    $('.woocommerce-notices-wrapper').html('<div class="woocommerce-error" role="alert">'+message_selected+'</div>');
                    setTimeout(function() { 
                        $('.woocommerce-notices-wrapper').show();
                    }, 501);
                }
                setTimeout(function() { 
                    $("body").removeClass("ajax-load");
                }, 500);
            }else{
                $(".wrap-qty-js").addClass("disable");
                $(".description-typeroom-new").hide();
                $(".price-typeroom-new").hide();
                $(".add-to-cart-hotel").hide();
            }
        }
    }); 

    // select type of room
    $(".select_type_of_room").on("change", function () { 
        $("#calendar-booking").removeClass("calendar-active");
        $("body").addClass("ajax-load");
        $('.woocommerce-notices-wrapper').hide();
        $(".add-to-cart-hotel").hide();
        $(".wrap-qty-js").addClass("disable");
        $('#calendar-booking').datepicker('setDate', null);
        var id_variation = $(this).find(':selected').val(),
        price = $(this).find(':selected').data('price'),
        maximum = $(this).find(':selected').data('maximum'),
        event_id = $(this).find(':selected').data('event'),
        hotel_id = $(this).find(':selected').data('hotel'),
        current_day_timestamp = getCurrentDayTimestamp();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'select_type_of_room',
                id_variation: id_variation,
                price: price,
                maximum: maximum,
                event_id: event_id,
                hotel_id: hotel_id,
                current_day_timestamp: current_day_timestamp
            },
            success: function (response) {
                var price_js = $(".select_type_of_room").find(':selected').data('price');
                var maximum = $(".select_type_of_room").find(':selected').data('maximum');
                var currency = $("#currency").val();
                var price_html = price_js+currency;
                $(".form-booking .day-available").html(response.day_available);
                setMinDay();
                $("#calendar-booking").datepicker("option", "minDate", $('#setminday').val());
                $('#calendar-booking').datepicker('refresh');
                $(".field-text-html").slideDown(100);
                $("#calendar-booking").slideDown(100);
                $(".wrap-qty-js").slideDown(100);
                $("body").removeClass("ajax-load");
            },
            error: function (err) {
                console.log(err);
            }
        });
    });

    // set min day
    function setMinDay() {
        var timestampArr = [];
        var minDate = 0;
    
        function getMinStockValue(stocksInRange) {
            if (stocksInRange.length === 0) {
                return null; // or any other value indicating no stocks in range
            }
            return Math.min(...stocksInRange);
        }
    
        $('.day-available input').each(function() {
            var timestamp = $(this).data('timestamp');
            timestampArr.push(timestamp);
        });
    
        var mintimestamp = getMinStockValue(timestampArr);
    
        $('.day-available input').each(function() {
            var timestamp = $(this).data('timestamp');
            if (timestamp == mintimestamp) {
                minDate = $(this).val();
                return false;
            }
        });
    
        $('#setminday').val(minDate);
    }

    // add hotel to cart
    $("body").on("click",".add-to-cart-hotel", function (e) { 
        e.preventDefault();
        $("body").addClass("ajax-load");
        $('.woocommerce-notices-wrapper').hide();
        var id_variation = $(".select_type_of_room").find(':selected').val(),
        price = $(".select_type_of_room").find(':selected').data('price'),
        maximum = $(".select_type_of_room").find(':selected').data('maximum'),
        event_id = $(".select_type_of_room").find(':selected').data('event'),
        hotel_id = $(".select_type_of_room").find(':selected').data('hotel'),
        start_day_string = $("#start_day").val(),
        start_day_timestamp = $("#start_day").data("timestamp"),
        end_day_string = $("#end_day").val(),
        end_day_timestamp = $("#end_day").data("timestamp"),
        quantity = $(".form-booking .qty").val(),
        current_day_timestamp = getCurrentDayTimestamp();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'add_to_cart_hotel',
                id_variation: id_variation,
                price: price,
                maximum: maximum,
                event_id: event_id,
                hotel_id: hotel_id,
                start_day_string: start_day_string,
                start_day_timestamp: start_day_timestamp,
                end_day_string: end_day_string,
                end_day_timestamp: end_day_timestamp,
                quantity: quantity,
                current_day_timestamp: current_day_timestamp
            },
            success: function (response) {
                if(response.success){
                    $('.woocommerce-notices-wrapper').html('<div class="woocommerce-message" role="alert">'+response.message+'</div>');
                }else{
                    $('.woocommerce-notices-wrapper').html('<ul class="woocommerce-error" role="alert"><li>'+response.message+'</li></ul>');
                }
                $('.woocommerce-notices-wrapper').show();
                $('.count-cart').html(response.quantity_total);
                $("body").removeClass("ajax-load");
            },
            error: function (err) {
                console.log(err);
            }
        });
    });

    // update cart
    let timeout;
    let ajaxInProgress = false;
    $("body").on("input",".woocommerce-cart-form .qty", function (e) { 
        if (!ajaxInProgress) {
            ajaxInProgress = true;
            if (timeout !== undefined) {
                clearTimeout(timeout);
            }
            let element = $(this); // store the context of this
            timeout = setTimeout(function() {
                $("[name='update_cart']").addClass('disabled');
                let quantity = element.val() - element.closest(".product-quantity").find(".input-variation").data("quantity"),
                    quantity_old = element.closest(".product-quantity").find(".input-variation").data("quantity"),
                    quantity_check = element.val(),
                    event_id = element.closest(".product-quantity").find(".input-variation").data("event"),
                    variation_id = element.closest(".product-quantity").find(".input-variation").data("variation"),
                    hotel_id = element.closest(".product-quantity").find(".input-variation").data("hotel"),
                    current_day_ts = element.closest(".product-quantity").find(".input-variation").data("day"),
                    start_day_ts = element.closest(".product-quantity").find(".input-variation").data("start"),
                    end_day_ts = element.closest(".product-quantity").find(".input-variation").data("end"),
                    id = element.closest(".product-quantity").attr('id'),
                    max = element.attr('max');
                
                $(".product-quantity").removeClass('error');
                $('.woocommerce-cart-form').css("position","relative");
        
                if (quantity_old != quantity_check && quantity_check != "") {
                    $('.woocommerce-cart-form').append('<div class="blockOverlay-custom" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255); opacity: 0.6; cursor: wait; position: absolute;"></div>');
                }
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cart_quantity_change',
                        quantity: quantity,
                        event_id: event_id,
                        variation_id: variation_id,
                        hotel_id: hotel_id,
                        current_day_ts: current_day_ts,
                        start_day_ts: start_day_ts,
                        end_day_ts: end_day_ts,
                        quantity_check: quantity_check,
                        quantity_old: quantity_old,
                        max: max,
                        id: id,
                    },
                    success: function (response) {
                        $(".woocommerce-notices-wrapper .woocommerce-message").remove();
                        if (response.success == true) {
                            $("[name='update_cart']").removeClass('disabled');
                            $("[name='update_cart']").trigger("click");
                        } else {  
                            if (response.same == false) {
                                var id = "#" + response.id;
                                $(id).addClass('error');
                                $(".blockOverlay-custom").remove();
                                $(".woocommerce-notices-wrapper").append('<div class="woocommerce-message error" role="alert">Quantity invalid.</div>');
                            }   
                        }
                    },
                    error: function (err) {
                        console.log(err);
                    },
                    complete: function() {
                        ajaxInProgress = false;
                    }
                });
            }, 800);
        }
    });

    // check stock before checkout
    $("body").on("click",".checkout-trigger", function (e) { 
        e.preventDefault();
        $('.modal-checkout-error').hide();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'check_stock_checkout'
            },
            success: function(response) {
                if (response.success) {
                    $('.cart_item').each(function() {
                        $(this).removeClass('error');
                    });
                    $('#place_order').trigger('click');
                } else {
                    var error = response.error;
                    $('.cart_item').each(function() {
                        var errorValue = $(this).data('error');
                        if ($.inArray(errorValue, error) !== -1) {
                            $(this).addClass('error');
                        }else{
                            $(this).removeClass('error');
                        }
                    });
                    $('.modal-checkout-error').show();
                }
            },
            error: function() {
               
            }
        });
    });

    // close modal
    $("body").on("click",".modal-checkout-error .close", function (e) { 
        $('.modal-checkout-error').hide();
    });
});