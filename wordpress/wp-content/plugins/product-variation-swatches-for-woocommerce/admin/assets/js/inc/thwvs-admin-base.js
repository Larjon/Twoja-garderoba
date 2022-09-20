var thwvsf_base = (function($, window, document) {
	'use strict';	
	
	function escapeHTML(html) {
	   var fn = function(tag) {
		   var charsToReplace = {
			   '&': '&amp;',
			   '<': '&lt;',
			   '>': '&gt;',
			   '"': '&#34;'
		   };
		   return charsToReplace[tag] || tag;
	   }
	   return html.replace(/[&<>"]/g, fn);
	}
	 	 
	function isHtmlIdValid(id) {
		//var re = /^[a-z]+[a-z0-9\_]*$/;
		var re = /^[a-z\_]+[a-z0-9\_]*$/;
		return re.test(id.trim());
	}
	
	function isValidHexColor(value) {      
		if ( preg_match( '/^#[a-f0-9]{6}$/i', value ) ) { // if user insert a HEX color with #     
			return true;
		}     
		return false;
	}
	
	function setup_tiptip_tooltips(){
		var tiptip_args = {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		};

		$('.tips').tipTip( tiptip_args );
	}
	
	function setup_color_picker(form) {
	 	
 		var i = 0;
        form.find(".thpladmin-colorpick").iris({

            change: function(event, ui) {

                $(this).parent().find(".thpladmin-colorpickpreview").css({
                    backgroundColor: ui.color.toString()
                })
                
            },
            hide: !0,
            border: !0
        }).click(function() {
        	if($(this).closest(".thwvsf_settings_fields_form").length  > 0){
        		$(".iris-picker").hide(), $(this).closest(".thwvsf_settings_fields_form").find(".iris-picker").show()
        	}else{
        		  $(".iris-picker").hide(), $(this).closest("td").find(".iris-picker").show()
        	}
          
           
        }), $("body").click(function() {
            $(".iris-picker").hide()
        }), $(".thpladmin-colorpick").click(function(event) {
            event.stopPropagation()
        })
        i++;
    }

	
	function setup_popup_tabs(form, selector_prefix){
		$("."+selector_prefix+"-tabs-menu a").click(function(event) {
			event.preventDefault();
			$(this).parent().addClass("current");
			$(this).parent().siblings().removeClass("current");
			var tab = $(this).attr("href");
			$("."+selector_prefix+"-tab-content").not(tab).css("display", "none");
			$(tab).fadeIn();
		});
	}
	
	function open_form_tab(elm, tab_id, form_type){
		var tabs_container = $("#thwvsf-tabs-container_"+form_type);
		
		$(elm).parent().addClass("current");
		$(elm).parent().siblings().removeClass("current");
		var tab = $("#"+tab_id+"_"+form_type);
		tabs_container.find(".thpladmin-tab-content").not(tab).css("display", "none");
		$(tab).fadeIn();
	}
	
	function prepare_field_order_indexes(elm) {
		$(elm+" tbody tr").each(function(index, el){
			$('input.f_order', el).val( parseInt( $(el).index(elm+" tbody tr") ) );
		});
	}

	
	function get_property_field_value(form, type, name){
		var value = '';
		
		switch(type) {
			case 'select':
				value = form.find("select[name=i_"+name+"]").val();
				value = value == null ? '' : value;
				break;
				
			case 'checkbox':
				value = form.find("input[name=i_"+name+"]").prop('checked');
				value = value ? 1 : 0;
				break;
				
			default:
				value = form.find("input[name=i_"+name+"]").val();
				value = value == null ? '' : value;
		}	
		
		return value;
	}
	
		
	function set_property_field_value(form, type, name, value, multiple){
		
		switch(type) {
			case 'select':
				if(multiple == 1 && typeof(value) === 'string'){
					value = value.split(",");
					name = name+"[]";
				}
				form.find('select[name="i_'+name+'"]').val(value);
				break;
				
			case 'checkbox':
				value = value == 'yes' || value == 1 ? true : false;
				form.find("input[name=i_"+name+"]").prop('checked', value);
				break;

			case 'colorpicker':

				form.find("input[name=i_"+name+"]").val(value);
				form.find('span.'+name+'_preview').css('background-color',value);
				break;

			case 'radio' : 

				form.find("input[name=i_"+name+"]").val(value);
				form.find($('.'+value)).addClass('rad-selected').siblings('.rad-selected').removeClass('rad-selected');
				break;

			default:
				form.find("input[name=i_"+name+"]").val(value);
		}	
	}

	var active_tab = 0;
	function setup_form_side_popup(){

		$('.pp_nav_tabs > li').click(function(){
			var index = $(this).data('index');
			var popup = $(this).closest('.popup-wrapper');
			open_tab(popup, $(this), index);
			active_tab = index;
		});
	}

	function open_tab(popup, link, index){
		var panel = popup.find('.data_panel_'+index);

		close_all_data_panel(popup);
		link.addClass('active');
		panel.css("display", "block");
	}

	
	function close_all_data_panel(popup){

		popup.find('.pp_nav_tabs > li').removeClass('active');

		popup.find('.data-panel').css("display", "none");

		popup.find('.global-tabs > li').removeClass('active');
	}
		
	return {
		escapeHTML : escapeHTML,
		isHtmlIdValid : isHtmlIdValid,
		isValidHexColor : isValidHexColor,
		setup_tiptip_tooltips : setup_tiptip_tooltips,
		setupColorPicker : setup_color_picker,
		setupPopupTabs : setup_popup_tabs,
		openFormTab : open_form_tab,
		get_property_field_value : get_property_field_value,
		set_property_field_value : set_property_field_value,
		setup_form_side_popup : setup_form_side_popup,
   	};
}(window.jQuery, window, document));


function thwvsOpenFormTab(elm,tab_id, form_type){
    thwvsf_base.openFormTab(elm, tab_id, form_type)
}