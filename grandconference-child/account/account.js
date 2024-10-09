jQuery(function($){
    // Override default email validation to enforce stricter rules
    $.validator.addMethod("strictEmail", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
    }, "Veuillez entrer une adresse email valide.");

    // validate
    $("form#login").validate({
        rules: {
            username: {
                required: true,
            },
            password: {
                required: true,
            }
        },
        messages: {
            username: {
                required: $('.error-required').val(),
            },
            password: {
                required: $('.error-required').val(),
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    // validate
    $("#register-client").validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
                required: true,
                strictEmail: true
            },
            password: {
                required: true,
                minlength: 8
            },
            password_confirm: {
                required: true,
                minlength: 8,
                equalTo: "#password" // Ensures the confirmation matches the password
            }
        },
        messages: {
            first_name: {
                required: $('.error-required').val(),
            },
            last_name: {
                required: $('.error-required').val(),
            },
            email: {
                required: $('.error-required').val(),
                strictEmail: $('.error-valid-email').val(),
            },
            password: {
                required: $('.error-required').val(),
                minlength: $('.error-min-pw').val(),
            },
            password_confirm: {
                required: $('.error-required').val(),
                minlength: $('.error-min-pw').val(),
                equalTo: $('.error-equa-pw').val(),
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    // validate
    $("#edit-client").validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
                required: true,
                strictEmail: true
            },
            password: {
                minlength: 8
            },
            password_confirm: {
                minlength: 8,
                equalTo: "#password"
            }
        },
        messages: {
            first_name: {
                required: $('.error-required').val(),
            },
            last_name: {
                required: $('.error-required').val(),
            },
            email: {
                required: $('.error-required').val(),
                strictEmail: $('.error-valid-email').val(),
            },
            password: {
                minlength: $('.error-min-pw').val(),
            },
            password_confirm: {
                minlength: $('.error-min-pw').val(),
                equalTo: $('.error-equa-pw').val(),
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    
    // js ajax login
    $('form#login').on('click','.submit_button',function(e){ 
        e.preventDefault();
        var formStatus = $('form#login').validate().form();
        $('form#login .message').hide();
        if (formStatus == true) {
            $("body").addClass("ajax-load"); 
            $.ajax({
                url: jaxsr.url,
                type :'POST',
                data: { 
                    'action': 'ajaxlogin', 
                    'username': $('form#login #username').val(), 
                    'password': $('form#login #password').val(), 
                    'security': $('form#login #security').val() },
                success: function(response){
                    if(response.data == true){
                        $('form#login .message').text($('.message-success').val());
                        $('form#login .message').css('color','#008000');
                        window.location.href = $('.referring-url').val();
                    }
                    $('form#login .message').show();
                    $("body").removeClass("ajax-load");
                }
            });
        }
    });

    // js ajax register 
    $('form#register-client').on('click','.submit_button',function(e){  
        e.preventDefault();
        var formStatus = $('form#register-client').validate().form();
        $('form#register-client .message').hide();
        if (formStatus == true) {
            $("body").addClass("ajax-load"); 
            $.ajax({
                url: jaxsr.url,
                type :'POST',
                data: { 
                    'action': 'RegisterClient', 
                    'email': $('form#register-client #email').val(), 
                    'password': $('form#register-client #password').val(), 
                    'password_confirm': $('form#register-client #password_confirm').val(),
                    'first_name': $('form#register-client #first_name').val(),
                    'last_name': $('form#register-client #last_name').val()
                },
                success: function(response){
                    if(response.data == true){
                        $('form#register-client .message').text($('.success').val());
                        $('form#register-client .message').css('color','#008000');
                        window.location.href = $('.url-comple').val();
                    }else{
                        $('form#register-client .message').html(response.data);
                        $('form#register-client .message').css('color','red');
                    }
                    $('form#register-client .message').show();
                    $("body").removeClass("ajax-load");
                }
            });
        }
    });

    // js ajax edit client 
    $('form#edit-client').on('click', '.submit_button', function(e){  
        e.preventDefault();
        var formStatus = $('form#edit-client').validate().form(); // Ensure you have validation set up
        $('form#edit-client .message').hide();
        if (formStatus) {
            $("body").addClass("ajax-load");
            $.ajax({
                url: jaxsr.url,
                type: 'POST',
                data: {
                    'action': 'EditClient',
                    'email': $('form#edit-client #email').val(),
                    'password': $('form#edit-client #password').val(),
                    'password_confirm': $('form#edit-client #password_confirm').val(),
                    'first_name': $('form#edit-client #first_name').val(),
                    'last_name': $('form#edit-client #last_name').val(),
                    'edit_client_nonce': $('#edit_client_nonce').val() // Include nonce
                },
                success: function(response){
                    if (response.data === true) {
                        $('form#edit-client .message').text($('.success-update').val());
                        $('form#edit-client .message').css('color','#008000');
                        if(response.pw == true){
                            window.location.href = $('.url-login').val();
                        } 
                    } else {
                        $('form#edit-client .message').html(response.data);
                        $('form#edit-client .message').css('color','red');
                    }
                    $('form#edit-client .message').show();
                    $("body").removeClass("ajax-load");
                }
            });
        }
    });

    // refund order
    $('body').on('click','.refund-button', function() {
        var order_id = $(this).data('order-id');
        var message_id = $(this).data('message-id');
        var order_item = $(this).data('order-item');
        var order_price = $(this).data('order-price');
        var messageBox = $('#message-' + message_id);
        $("body").addClass("ajax-load");
        $.ajax({
            url: jaxsr.url,
            type: 'POST',
            data: {
                action: 'process_ajax_refund_modal',
                order_id: order_id,
                order_item: order_item,
                order_price: order_price,
                messageBox: message_id,
            },
            success: function(response) {
                $("body").removeClass("ajax-load");

                if (response.status == true) {
                    $(".refund-button-action").attr('data-order-id', response.order_id);
                    $(".refund-button-action").attr('data-message-id', response.message_id);
                    $(".refund-button-action").attr('data-order-item', response.order_item);
                    $(".refund-button-action").attr('data-order-price', response.order_price);
                    $("#modal-alert-refund .modal-body").html(response.message);
                    $("#modal-alert-refund").modal('show');
                } else {
                    messageBox.removeClass('success').addClass('error');
                    messageBox.text(response.message);
                    // Show the message
                    messageBox.slideDown();

                    // Hide the message after 5 seconds
                    setTimeout(function() {
                        messageBox.slideUp();
                    }, 3000);
                }
                // location.reload();
            },
            error: function() {
                $("body").removeClass("ajax-load");
                messageBox.removeClass('success').addClass('error');
                messageBox.text('Une erreur s\'est produite. Veuillez réessayer.');

                // Show the message
                messageBox.slideDown();

                // Hide the message after 5 seconds
                setTimeout(function() {
                    messageBox.slideUp();
                }, 3000);
            }
        });
    });

    // refund order
    $('body').on('click','.refund-button-action', function() {
        var order_id = $(this).data('order-id');
        var message_id = $(this).data('message-id');
        var order_item = $(this).data('order-item');
        var order_price = $(this).data('order-price');
        var messageBox = $('#message-' + message_id);
        $("#modal-alert-refund").modal('hide');
        $("body").addClass("ajax-load");
        $.ajax({
            url: jaxsr.url,
            type: 'POST',
            data: {
                action: 'process_ajax_refund',
                order_id: order_id,
                order_item: order_item,
                order_price: order_price,
                messageBox: message_id,
            },
            success: function(response) {
                $("body").removeClass("ajax-load");

                if (response.status == true) {
                    messageBox.removeClass('error').addClass('success');
                    messageBox.text(response.message);
                } else {
                    messageBox.removeClass('success').addClass('error');
                    messageBox.text(response.message);
                }

                // Show the message
                messageBox.slideDown();

                // Hide the message after 5 seconds
                setTimeout(function() {
                    messageBox.slideUp();
                }, 3000);
                location.reload();
            },
            error: function() {
                $("body").removeClass("ajax-load");
                messageBox.removeClass('success').addClass('error');
                messageBox.text('Une erreur s\'est produite. Veuillez réessayer.');

                // Show the message
                messageBox.slideDown();

                // Hide the message after 5 seconds
                setTimeout(function() {
                    messageBox.slideUp();
                }, 3000);
            }
        });
    });
    
});