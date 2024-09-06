jQuery(function($){
    $('.event_type-of-room #select_typeOfRoom').change(function() {
        var selectedOption = $(this).find(':selected');
        var price = selectedOption.data('price');
        var pricenew = selectedOption.data('pricenew');
        if(pricenew !=""){
            price =  pricenew;
        }
        var quantity = selectedOption.data('quantity');
        var description_number = selectedOption.data('description');
        $('.form-booking .quantity .qty').attr('max', quantity);
        $('.form-booking .quantity .qty').val(1);
        var rooms = $('.form-booking .event_type-of-room input[name="rooms"]').val();
        var title_price = $('.form-booking .event_type-of-room input[name="title_price"]').val();
        var description = $('.form-booking .event_type-of-room input[name="descrription_type"]').val();
        var currency = selectedOption.data('currency');
        // var contentPrice = ;
        // var contentDescription = $('.content-event .description-typeroom');
        var nameTypeOfRoomInput = $('.form-booking .name_typeOfRoom');
        var priceTypeOfRoomInput = $('.form-booking .price_typeOfRoom');
        var fooEventsTypeOfRoomInput = $('.form-booking .fooEvents_typeOfRoom');
        var description_numberTypeOfRoomInput = $('.form-booking .description_number');
        var description_typeDetail = description + ": "+description_number;
        nameTypeOfRoomInput.val(selectedOption.val());
        priceTypeOfRoomInput.val(price);
        fooEventsTypeOfRoomInput.val(quantity);
        description_numberTypeOfRoomInput.val(description_typeDetail);
        $('.content-event .price-typeroom').html(title_price +' : '+ price + currency + '/' +rooms);
        $('.content-event .description-typeroom').html(description + ": "+description_number);
    }); 
});