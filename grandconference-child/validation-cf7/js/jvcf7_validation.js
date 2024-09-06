// VALIDATION CODES
jQuery(document).ready(function(){
	
	jQuery('.wpcf7-validates-as-required').addClass('required');
	jQuery('.wpcf7-email').addClass('email');
	jQuery('.wpcf7-checkbox.wpcf7-validates-as-required input').addClass('required');
	jQuery('.wpcf7-radio input').addClass('required');
	
	jQuery('form.wpcf7-form').each(function(){
		jQuery(this).addClass(scriptData.jvcf7_default_settings.jvcf7_invalid_field_design);
		jQuery(this).addClass(scriptData.jvcf7_default_settings.jvcf7_show_label_error);
		jQuery(this).validate({
			ignore: ":hidden input, :hidden textarea, :hidden select", // FOR Hidden element
			onfocusout: function(element) { // ADD VALIDATION ON BLUR
		        this.element(element);  
		    },
			onfocusout: function(element) { // ADD VALIDATION ON BLUR
		        this.element(element);  
		    },
			errorPlacement: function(error, element) {
	            if (element.is(':checkbox') || element.is(':radio')){
	            	error.insertAfter(jQuery(element).parent().parent().parent());
	            } else {
	            	error.insertAfter(element);
	            }
         	}
		});
	});

	jQuery('#multiple-form .wpcf7-form-control.wpcf7-submit').click(function(e){ 
		console.log('123123');
		$jvcfpValidation 	=	jQuery(this).parents('form');		
		if (!jQuery($jvcfpValidation).valid()){
			e.preventDefault();
			$topErrorPosition 		= jQuery('#multiple-form .wpcf7-form-control.error').offset().top;
			$topErrorPosition		= parseInt($topErrorPosition) - 100;
			jQuery('body, html').animate({scrollTop:$topErrorPosition}, 'normal');
		}else{
			e.preventDefault();
			let formData = new FormData(jQuery(this).closest('.wpcf7-form')[0]);
			formData.append("action", "set_session_form_ticket");
			jQuery.ajax({
				url: '/wp-admin/admin-ajax.php',
				data: formData,
				type: 'POST',
				contentType: false,
				processData: false,
				success: function(response){
					var data = JSON.parse(response);
					if(data.redirect){
						jQuery('#next-form').trigger('click');
					}else{
						window.location.href = data.checkout_url;
					}
				},
				error: function(err){
					console.log(err);
				}
			});
		}
	});	

	jQuery('#multiple-form-room .submit-room').click(function(e){ 
		console.log("asdasdasd");
		$jvcfpValidation 	=	jQuery(this).parents('form');		
		if (!jQuery($jvcfpValidation).valid()){
			e.preventDefault();
			$topErrorPosition 		= jQuery('#multiple-form-room .wpcf7-form-control.error').offset().top;
			$topErrorPosition		= parseInt($topErrorPosition) - 100;
			jQuery('body, html').animate({scrollTop:$topErrorPosition}, 'normal');
		}else{
			e.preventDefault();
			let formData = new FormData(jQuery(this).closest('.wpcf7-form')[0]);
			var roomId = jQuery(this).closest('.toggle').data('room-id');
			console.log('roomId',roomId);
        	formData.append("rooms_id", roomId);
			formData.append("action", "set_session_form_room");
			jQuery.ajax({
				url: '/wp-admin/admin-ajax.php',
				data: formData,
				type: 'POST',
				contentType: false,
				processData: false,
				success: function(response){
					var data = JSON.parse(response);
					console.log(data);
					if(data.redirect){
						jQuery('#next-form-room').trigger('click');
					}else{
						window.location.href = data.room_checkout_url;
					}
				},
				error: function(err){
					console.log(err);
				}
			});
		}
	});	
});

jQuery.validator.addMethod("email", 
    function(value, element) {
        return this.optional(element) || /^[+\w-\.]+@([\w-]+\.)+[\w-]{2,10}$/i.test(value);
    },"Please enter a valid email address"
);

jQuery.validator.addMethod("tel", 
    function(value, element) {
        return this.optional(element) || /^[0-9\-\(\)\s]+$/i.test(value);
    },"Please enter a valid phone"
);

jQuery.validator.addMethod("letters_space", function(value, element) {
  return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
}, "Letters and space only");