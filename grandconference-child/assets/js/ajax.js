jQuery(function($){
    // add tickets to cart
    $('.form-add-cart-tickets').on('click','.single_add_to_cart_button',function(e){  
        e.preventDefault();
        var product_id = $(this).val();
        var quantity = $('.form-add-cart-tickets .qty').val();
        var event_id = $('.form-add-cart-tickets .event_id').val();
        $("body").addClass("ajax-load");
        $('.woocommerce-notices-wrapper').hide();
        $.ajax({
            url: jaxsr.url,
            type :'POST',
            data : {
                action : 'add_tickets_to_cart',
                product_id: product_id,
                event_id: event_id,
                quantity: quantity
            },
            success: function (response) {
                var data = JSON.parse(response);
                if(data.success){
                    $('.woocommerce-notices-wrapper').html('<div class="woocommerce-message" role="alert">'+data.message+'</div>');
                }else{
                    $('.woocommerce-notices-wrapper').html('<ul class="woocommerce-error" role="alert"><li>'+data.message+'</li></ul>');
                }
                $('.woocommerce-notices-wrapper').show();
                $('.count-cart').html(data.quantity_total);
                $("body").removeClass("ajax-load");
            },
            error: function(err){
                console.log(err);
            }
        });
    }); 

    // add hotel to cart
    $('.form-booking').on('click','.add_hotel_to_cart',function(e){  
        e.preventDefault();
        var selectedOption = $('.form-booking #select_typeOfRoom').val();
        if (!selectedOption) {
            $('.woocommerce-notices-wrapper').html('<ul class="woocommerce-error" role="alert"><li>Please select a room type.</li></ul>').show();
            return;
        }
        var product_id = $(this).val();
        var quantity = $('.form-booking .qty').val();
        var hotel_id = $('.form-booking .hotel_id').val();
        var name_typeOfRoom = $('.form-booking .name_typeOfRoom').val();
        var price_typeOfRoom = $('.form-booking .price_typeOfRoom').val();
        var fooEvents_typeOfRoom = $('.form-booking .fooEvents_typeOfRoom').val();
        var description_number = $('.form-booking .description_number').val();
        var event_id = $('.form-booking .event_id').val();
        $('.woocommerce-notices-wrapper').hide();
        $.ajax({
            url: jaxsr.url,
            type :'POST',
            data : {
                action : 'add_hotel_to_cart',
                product_id: product_id,
                hotel_id: hotel_id,
                quantity: quantity,
                name_typeOfRoom: name_typeOfRoom,
                price_typeOfRoom: price_typeOfRoom,
                fooEvents_typeOfRoom: fooEvents_typeOfRoom,
                description_number: description_number,
                event_id: event_id,
            },
            success: function (response) {
                var data = JSON.parse(response);
                if(data.success){
                    $('.woocommerce-notices-wrapper').html('<div class="woocommerce-message" role="alert">'+data.message+'</div>');
                }else{
                    $('.woocommerce-notices-wrapper').html('<ul class="woocommerce-error" role="alert"><li>'+data.message+'</li></ul>');
                }
                $('.woocommerce-notices-wrapper').show();
                $('.count-cart').html(data.quantity_total);
            },
            error: function(err){
                console.log(err);
            }
        });
    }); 
});