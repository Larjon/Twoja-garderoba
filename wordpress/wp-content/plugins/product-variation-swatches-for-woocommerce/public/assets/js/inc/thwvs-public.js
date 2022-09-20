var thwvsf_public = (function($){
	'use strict';
	
	function initialize_thwvsf(){

		var enable_stock_alert           = thwvsf_public_var.enable_stock_alert,
			min_value_stock              = thwvsf_public_var.min_value_stock,
			clear_on_reselect            = thwvsf_public_var.clear_on_reselect,
			out_of_stock                 = thwvsf_public_var.out_of_stock,
			show_selected_variation_name = thwvsf_public_var.show_selected_variation_name,
			choose_option_text  	     = thwvsf_public_var.choose_option_text;

		var swatches_form = function( $form ) {
			var self = this;
			self.$form                = $form;
			this.variationData        = $form.data( 'product_variations' );
			this.$attributeFields     = $form.find( '.variations select' );
			self.$singleVariation     = $form.find( '.single_variation' );
			self.$singleVariationWrap = $form.find( '.single_variation_wrap' );
			//$form.on( 'change.thwvsf_variation_form', '.variations select', {swatches_form: this },this.onChangeselect_field );
			$form.on( 'click.thwvsf_variation_form', '.thwvsf-checkbox', { swatches_form : this }, this.onselect );
			$form.on( 'change.thwvsf_variation_form', 'input[type="radio"].thwvsf-rad', { swatches_form : this }, this.onselectradio);

			$form.on( 'check_variations.thwvsf_variation_form', { swatches_form : this }, this.onFindVariation );
			$form.on( 'click.thwvsf_variation_form', '.reset_variations', { swatches_form: this }, this.onReset );
			$form.on( 'change.thwvs_variation_form', '.variations .thwvs-select', { swatches_form: this }, this.onchangeselect);
			if(thwvsf_public_var.selectWoo_enable){
				self.enableSwatchDropDown(self.$attributeFields, $form);
			}
		};

		swatches_form.prototype.onReset = function( event ) {

			var form = event.data.swatches_form;
			form.$form.find('.thwvsf_fields .thwvsf-checkbox').removeClass( 'thwvsf-selected' );
			form.$form.find('.thwvsf_fields > span').removeClass( 'selected' );
			form.$form.find('.thwvsf_fields .thwvsf-checkbox').removeClass( 'deactive');
			form.$form.find('.thwvsf_fields .thwvsf-checkbox').removeClass( 'out_of_stock');

			form.$form.find('.thwvsf-rad').prop("checked", false);
			form.$form.find('.thwvsf-rad').attr('checked',false);
			form.$form.find('.thwvsf-rad-li > label').removeClass( 'thwvsf-selected' );
			var $element = $( this );
			
			var $button = $element.parents('.variations_form').siblings('.thwvsf_add_to_cart_button');	
			active_and_deactive_variation(form);
			disable_out_of_stock_variation(form);			
		};

		swatches_form.prototype.onselect = function( event ) {
			
			var form = event.data.swatches_form;
			var $element = $( this ),
				$select = $element.closest( '.thwvsf_fields' ).find( 'select' ),
				attribute_name = $select.data( 'attribute_name' ) || $select.attr( 'name' ),
				value = $element.data( 'value' ),
				clicked = attribute_name;
			selected.push(attribute_name);

			var opt_val = value;
			opt_val = (typeof(opt_val) === 'string') ? (opt_val.replace(/'/g, '\\\'').
               replace(/"/g, '\\"')) : opt_val;
			if ( ! $select.find( 'option[value="'+opt_val+'"]').length ) {
				$element.siblings( '.thwvsf-checkbox' ).removeClass( 'thwvsf-selected' );
				$select.val( '' ).change();
				alert('No combination');
				return false;
			}

			if ( $element.hasClass('thwvsf-selected') ) {
				if(clear_on_reselect != 'yes'){
					return false;
				}

				$select.val( '' );
				$element.removeClass('thwvsf-selected');
			} else {
				$element.addClass('thwvsf-selected').siblings('.thwvsf-selected').removeClass('thwvsf-selected');
				$select.val( value );
			}
			$select.change();
			active_and_deactive_variation(form);
			disable_out_of_stock_variation(form);
		}

		swatches_form.prototype.onselectradio = function( event ) {

			var form = event.data.swatches_form;
			var $element = $( this ),
				$select = $element.closest( '.thwvsf_fields' ).find( 'select' ),
				attribute_name = $select.data( 'attribute_name' ) || $select.attr( 'name' ),
				value = $element.data( 'value' );
			clicked = attribute_name;
			selected.push(attribute_name);	

			// Added for making the radion fields outer class thwvs-selected for hiding the all fields other than first 
			var parent_radio = $element.closest('.th-label-radio');
			var is_checked = $element.prop('checked');

			if(is_checked == true){
				parent_radio.addClass('thwvsf-selected').siblings().removeClass('thwvsf-selected');
			}
			
			$select.val( value );
			$select.change();

			// var form = event.data.swatches_form;
			// var $element = $( this ),
			// 	$select = $element.closest( '.thwvsf_fields' ).find( 'select' ),
			// 	attribute_name = $select.data( 'attribute_name' ) || $select.attr( 'name' ),
			// 	value = $element.data( 'value' );
			// clicked = attribute_name;
			// selected.push(attribute_name);	
			
			// $select.val( value );
			// $select.change();
		}

		swatches_form.prototype.onchangeselect = function( event,chosenAttributes ){

			var $element = $( this );
			
			if(show_selected_variation_name === 'yes'){
				
				var variation_name = ($element).children(':selected').text(),
				name_value     = ($element).val();
				show_selected_variation_name_beside_label($element, variation_name, name_value);
			}
		}

		function active_and_deactive_variation(form){

			var $attributeFields = form.$attributeFields,
				$addtocart_button = form.$form.find('.woocommerce-variation-add-to-cart');
			//var choosed_attr = $select.data( 'attribute_name' ) || $select.attr( 'name' );			
			$attributeFields.each( function( index, el ) {

				var current_attr_select     = $( el ),
					current_attr_name       = current_attr_select.data( 'attribute_name' ) || current_attr_select.attr( 'name' );

				if(current_attr_select.hasClass('wc-bundle') ){
					var current_attr_name  = 'attribute_'+current_attr_select.attr('id');
				}
				
				var $current_attr = form.$form.find('.'+ current_attr_name);
			
				$current_attr.addClass('deactive');
				var options = current_attr_select.children( 'option');

				options.each( function(i,option){
			 		var opt_val = option.value;			 		
			 		if(opt_val != ''){
			 			opt_val = opt_val.replace(/[^a-z0-9_-]/gi, "");
			 			var $current_opt = form.$form.find('.'+ current_attr_name + '[data-value="'+ opt_val +'"]');
			 			if($current_opt.length > 0 ){

			 				$current_opt.removeClass('deactive');
			 			}else{
			 				opt_val = opt_val.replace(/[^a-z0-9_-]/gi, "");
			 				
			 				var $current_opt = form.$form.find('.'+ current_attr_name +'.'+ opt_val);
			 				$current_opt.removeClass('deactive');
			 			}
			 		}
			 	});
				
			});	
		}

		function disable_out_of_stock_variation(form){
			
			var attributeFields = form.$attributeFields;	
			if(attributeFields.length == 1){
				var variations  = form.variationData;
				for ( var i = 0; i < variations.length; i++ ) {
					var variation = variations[i];
					var variation_attributes =  variation.attributes;

					var attribute_key = Object.keys(variation_attributes);
					var attr_item_name = attribute_key[0];

					var attr_item_name_class = attr_item_name.replace(/[^a-z0-9_-]/gi, "");

					var current_attr_select = $(attributeFields);

					if($(attributeFields).hasClass('wc-bundle') ){
						attr_item_name_class = 'attribute_'+current_attr_select.attr('id');
					}

					var attribute_value = variation_attributes[attr_item_name];
				
					var attribute_value_class = attribute_value.replace(/[^a-z0-9_-]/gi, "");
	 
					var is_in_stock = variation.is_in_stock;

					var attr_option_class = '';
					if(attr_item_name_class){

						attr_option_class = '.' + attr_item_name_class;
						if(attribute_value_class){
							attr_option_class = attr_option_class + '.' + attribute_value_class;
						}
					}else{
						attr_option_class = '.' + attr_item_name + '[data-value="'+ attribute_value +'"]';
					}

					if(!is_in_stock && out_of_stock != 'default'){
						form.$form.find(attr_option_class).addClass('out_of_stock');
						form.$form.find(attr_option_class).trigger('out_of_stock', [is_in_stock, attr_item_name]);
					}else{
						
						form.$form.find(attr_option_class).removeClass('out_of_stock');
					}
				}
			}else{
				disable_out_of_stock_variation_multiple(form, attributeFields);
			}
		}

		function disable_out_of_stock_variation_multiple(form, attributeFields){
			var total_attributes = attributeFields.length;

			var count = 0;
			var selected_terms = [];
			var selected_term_names = [];

			// Configure selected attributes
			attributeFields.each(function(index, element){
				var current_attr_select     = $(this);
				var current_attr_name       = current_attr_select.data( 'attribute_name' ) || current_attr_select.attr( 'name' );
				var selected_attribute_val =  current_attr_select.val();

				if(selected_attribute_val != ''){
					count = ++count;
					selected_terms[current_attr_name] = selected_attribute_val;
					selected_term_names[count] = current_attr_name;  
				}
			});

			// Remove out_of_stock for no selected terms
			if(count == 0  || count < total_attributes-1){
				form.$form.find('.thwvsf_fields .thwvsf-checkbox').removeClass( 'out_of_stock');
			}
			// Total variation
			var variations  = form.variationData;
			// Check the last item is remaining to select.
			if(count == total_attributes-1){

				// Itrate on each variations
				for ( var i = 0; i < variations.length; i++ ) {

					// Assign each variation
					var variation = variations[i];
					var variation_attributes = variation.attributes;

					var q = 0;
					$.each(variation_attributes, function(attr_item_name, attribute_value){

						// Check selected variation and avaialble varaiton are same
						if(variation_attributes[attr_item_name] == selected_terms[attr_item_name]){
							++q;

							// Check for last item is iterating
							if(q == total_attributes-1){

								// Again taking the current variation which is to be shown in the page.
								var current_variation = variation;
								var current_attributes = current_variation.attributes;

								for (var current_attr_name in current_attributes){
									if(jQuery.inArray(current_attr_name,selected_term_names) == -1){

										var current_attr_val = variation_attributes[current_attr_name];
										var attr_item_name_class = current_attr_name.replace(/[^a-z0-9_-]/gi, "");
										var attribute_value_class = current_attr_val.replace(/[^a-z0-9_-]/gi, "");

										var current_attr_select = $('select.cls_'+attr_item_name_class);
										
										if(current_attr_select.hasClass('wc-bundle') ){	
											attr_item_name_class  = 'attribute_'+current_attr_select.attr('id');
										}

										var attr_option_class = '';
										if(attribute_value_class){
											attr_option_class = '.' + attr_item_name_class + '.' + attribute_value_class
										}else{
											attr_option_class = '.' + attr_item_name_class + '[data-value="'+ attribute_value +'"]';
										}

										var is_in_stock = variation.is_in_stock;

										if(!is_in_stock && out_of_stock != 'default'){
											form.$form.find(attr_option_class).addClass('out_of_stock');
											form.$form.find(attr_option_class).trigger('out_of_stock', [is_in_stock, attr_item_name]);
										}else{
											form.$form.find(attr_option_class).removeClass('out_of_stock');
										}
									}
								}
							}
						}
					});
				}
			}else{

				form.$form.find('.thwvsf_fields .thwvsf-checkbox').removeClass( 'out_of_stock');
				for ( var i = 0; i < variations.length; i++ ) {

					var variation = variations[i],
						variation_attributes = variation.attributes;
					var is_in_stock = variation.is_in_stock;

					if(!is_in_stock && out_of_stock != 'default'){

						var q = 0;
						var current_variation  = variation,
						 	current_attributes = current_variation.attributes;

						$.each(current_attributes, function(os_attr_name, os_attr_value){

							if(selected_terms[os_attr_name] == os_attr_value){
								++q;
								if(q == total_attributes-1){

									$.each(current_attributes, function(de_attr_name, de_attr_value){

										if(selected_terms[de_attr_name] != de_attr_value){

											var current_os_attr_val = current_attributes[de_attr_name],
												attr_item_name_class = de_attr_name.replace(/[^a-z0-9_-]/gi, ""),
												attribute_value_class = current_os_attr_val.replace(/[^a-z0-9_-]/gi, "");

											var current_attr_select = $('select.cls_'+de_attr_name);
											if(current_attr_select.hasClass('wc-bundle') ){	
												attr_item_name_class  = 'attribute_'+current_attr_select.attr('id');
											}

											var attr_option_class = '';
											if(attribute_value_class){
												attr_option_class = '.' + attr_item_name_class + '.' + attribute_value_class
											}else{
												attr_option_class = '.' + attr_item_name_class + '[data-value="'+ os_attr_value +'"]';
											}

											form.$form.find(attr_option_class).addClass('out_of_stock');
											form.$form.find(attr_option_class).trigger('out_of_stock', [is_in_stock, os_attr_name]);

										}

									});	
								}
							}

						});	
					}	
				}
			}
		}
		
		$.fn.wc_set_variation_attr = function( attr, value ) {
			if ( undefined === this.attr( 'data-o_' + attr ) ) {
				this.attr( 'data-o_' + attr, ( ! this.attr( attr ) ) ? '' : this.attr( attr ) );
			}
			if ( false === value ) {
				this.removeAttr( attr );
			} else {
				this.attr( attr, value );
			}
		};

		swatches_form.prototype.onFindVariation = function( event ) {
			
			var form = event.data.swatches_form;
			var $attributeFields = form.$attributeFields;

			active_and_deactive_variation(form);
			disable_out_of_stock_variation(form);

			if(show_selected_variation_name === 'yes'){

				$attributeFields.each(function(index, element){

					var current_attr_select    = $(this);
					var current_attr_name      = current_attr_select.data( 'attribute_name' ) || current_attr_select.attr( 'name' );
					var selected_attribute_val =  current_attr_select.val();

					if(selected_attribute_val != ''){
						var variation_name = (current_attr_select).children(':selected').text(),
						name_value         = (current_attr_select).val();
						show_selected_variation_name_beside_label(current_attr_select, variation_name, name_value);
					}
				});	
			}
		}

		function show_selected_variation_name_beside_label(elm, variation_name, name_value){

			elm.closest('tr').find('.label').find('label').find('.variation_name_label').remove();
			var default_label = elm.closest('tr').find('.label').find('label'),
			    //variation_name_label_html = '<label class="variation_name_label" >   :  ' +  variation_name+' </label>';
			    variation_name_label_html = '<label class="variation_name_label" > '   +  thwvsf_public_var.change_separator       +    variation_name+' </label>';
		
			variation_name_label_html =  name_value ?  variation_name_label_html : '';
			default_label.append(variation_name_label_html);
		}

		swatches_form.prototype.enableSwatchDropDown = function($attributeFields, $form){
			$attributeFields.each( function( index, el ) {
				var attr_select = $( el );
				if(attr_select.hasClass('thwvsf-swatch-dropdown')){

					var swatch_type = attr_select.data('swatchtype');
					if(swatch_type === 'swatch_dropdown_color'){

						attr_select.selectWoo({
							allowClear        : true,
							placeholder       : choose_option_text,
     						dropdownCssClass  : "thwvsf_drop_swatch",
							templateResult    : format_swatch_color,
			 				templateSelection : format_swatch_color,
			 				selectionAdapter  : $.fn.selectWoo.amd.require('customSingleSelectionAdapter'),
						}).addClass('enhanced');
					}else{
						attr_select.selectWoo({
							allowClear        : true,
							placeholder       : choose_option_text,
							dropdownCssClass  : "thwvsf_drop_swatch",
							templateResult    : format_swatch_image,
							templateSelection : format_swatch_image,
							selectionAdapter  : $.fn.selectWoo.amd.require('customSingleSelectionAdapter'),
						}).addClass('enhanced');
					}
				}

			});
		}

		function format_swatch_color(option){
			if (!option.id) { return option.text; }
			var color = $(option.element).data('swatch'),
				$span = $('<span class = "thwvsf-drop-span thwvsf-drop-color" style="background-color:'+color+';"> </span><span class = "thwvsf-drop-label" >' + option.text +'</span>');
	  		return $span;
		}

		function format_swatch_image(option){
			if (!option.id) { return option.text; }
			var image = $(option.element).data('swatch'),		
				$span   = $('<span class = "thwvsf-drop-span thwvsf-drop-img"><img src="'+image+'" /> </span><span class = "thwvsf-drop-label" >' + option.text + '</span>');
			return $span;
		}
		if(thwvsf_public_var.selectWoo_enable){
			if ( $.fn.selectWoo && $.fn.selectWoo.amd) {
				$.fn.selectWoo.amd.define('customSingleSelectionAdapter', [
					'select2/utils',
					'select2/selection/single',
				], function (Utils, SingleSelection) {
					const adapter = SingleSelection;
					adapter.prototype.update = function (data) {
					    if (data.length === 0) {
					      this.clear();
					      return;
					    }
					    var selection = data[0];
					    var $rendered = this.$selection.find('.select2-selection__rendered');
					    var formatted = this.display(selection, $rendered);
					    $rendered.empty().append(formatted);
					    $rendered.prop('title', selection.title || selection.text);
					};
					return adapter;
				});
			}
		}

		$.fn.thwvsf_variation_form = function() {
			
			new swatches_form( this );
			
			return this;
		};

		$(function() {
			if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
				$( '.variations_form' ).each( function() {
					
					$( this ).thwvsf_variation_form();
				});
			}
		});
			
		var clicked = null,
			selected = [];
	}

	function remove_selected_attribute_item($element){

		var default_label = $element.closest('tr').find('.thwvsf-wrapper-ul').data('default-label');
		$element.closest('tr').find('label').text(default_label);
		var attrbute_uls = $element.closest('tr').siblings('tr');

		attrbute_uls.each( function( index, el ) {

			var elm = $(el),
				default_label = elm.find('.thwvsf-wrapper-ul').data('default-label');
			
			elm.find('label').text(default_label);
		});
	}
	var execute =false;
 	initialize_thwvsf(), "flatsome" == thwvsf_public_var.is_quick_view ? $(document).on('click','.quick-view', function() {
 	  	if(!execute){
 	  		 $(document).on("mfpOpen", function() {
        	 initialize_thwvsf()
        	 execute = true;
        	});
 	  	}
    }) : "yith" == thwvsf_public_var.is_quick_view && $(document).on("qv_loader_stop", function() {
        initialize_thwvsf()
    })

    if("wpc_smart" == thwvsf_public_var.is_quick_view ){
    	$( document ).ajaxComplete( function( event, request, options ) {
			if ( request && 4 === request.readyState && 200 === request.status && ($(event.target).find('#woosq-popup').length > 0)) {
				initialize_thwvsf();
			}
		});
    }

    $(document).on('click', '.owp-quick-view', function(e) {
		var check = function(){
	    	var html = $('html');
	      	if(html.hasClass('owp-qv-open')){
	        	init_thwvsf();
	      	}else {
	        	setTimeout(check, 1000);
	      	}
	    }
	    check();
	});

    return {
		initialize_thwvsf : initialize_thwvsf,
	};

})(jQuery);

function init_thwvsf(){
	thwvsf_public.initialize_thwvsf();
}

