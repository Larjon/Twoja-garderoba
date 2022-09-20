jQuery( function( $ ) {

	'use strict';

	var _extends = Object.assign || function (target) {
 		for (var i = 1; i < arguments.length; i++) {
  			var source = arguments[i]; for (var key in source) {
	   			if (Object.prototype.hasOwnProperty.call(source, key)) {
	    			target[key] = source[key]; 
	    		} 
	    	} 
    	}
    	 
    	return target; 
    };

	$('.product_attributes').on('click', 'button.thwvsf_add_new_attribute', function (event) {
		event.preventDefault();

		$('.thwvsf-class').val('');
		var placeholder = thwvsf_var.placeholder_image;
		$('.i_index_media_img').attr( 'src',placeholder);
		$('.thpladmin-colorpickpreview').css('background-color','');

		var popup_outer = $('.thwvsf-attribte-dialog');
		popup_outer.find("input[type=text]").val("");

		if(popup_outer.hasClass('thwvsf-attribte-dialog-image')){
			var remove_button = popup_outer.find('.thwvsf_remove_image_button');
			remove_button.hide();
		}

		var $wrapper  = $( this ).closest( '.woocommerce_attribute' ),
			attribute = $wrapper.data( 'taxonomy' ),
			taxonomy = $(this).data('attr_taxonomy'),
			type = ($(this).data('attr_type')),
			settings_div = $('.thwvsf_settings_fields_form');

		thwvsf_base.setupColorPicker(settings_div);
		var $popup_div = $('.thwvsf-attribte-dialog-'+type),
			height = type == 'color' ? 395 : 250;

		if($popup_div.length > 0){
			$popup_div.dialog({ 

		       'dialogClass'   	: 'wp-dialog thwvsf-popup',  
		       'title'         	: 'Add new term',         
		       'modal'         	: true,
		       'autoOpen'      	: false, 
		       'width'       	: 500, 
		       'minHeight'      : height,

		       'buttons': [{
	               text:'save',
	               "class":"button_class",
	               click: function() {
	               		save_new_term($wrapper, $(this), attribute);
	                	$(this).dialog('close');
	                }
	           }]
	 		});
			
			$( '.product_attributes' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
				
			$popup_div.dialog('open');
			$( '.product_attributes' ).unblock();	

			$('.ui-dialog.thwvsf-popup').css('z-index',99999999);
					
		}
	});

	function save_new_term($wrapper, $dialog, attribute){
		
		var new_attribute_name = '';
		var term_spec = {};

		new_attribute_name = $dialog.find('input[name = "attribute_name"]').val();
		term_spec['product_'+attribute] = $dialog.find('input[name = "attribute_type"]').val();
		
		
		if(new_attribute_name){
		    var ajax_data = _extends({
                action: 'woocommerce_add_new_attribute',
                taxonomy: attribute,
                term:new_attribute_name,
                security: woocommerce_admin_meta_boxes.add_attribute_nonce
            },term_spec);

			$.post(woocommerce_admin_meta_boxes.ajax_url, ajax_data, function (response) {
				
			
                if (response.error) {
                    window.alert(response.error);
                } else if (response.slug) {
                    $wrapper.find('select.attribute_values').append('<option value="' + response.term_id + '" selected="selected">' + response.name + '</option>');
                    $wrapper.find('select.attribute_values').change();
                }

                $('.product_attributes').unblock();
                    
			});
		} else {
			$( '.product_attributes' ).unblock();
		}
	}
           

});