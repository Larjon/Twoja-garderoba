var thwvsf_public_base = (function($, window, document) {
	'use strict';
	
	
	function isEmpty(val){
		return (val === undefined || val == null || val.length <= 0) ? true : false;
	}
	
	/********************************************
	***** CHARACTER COUNT FUNCTIONS - START *****
	********************************************/
	function display_char_count(elm, isCount){
		var fid = elm.prop('id');
        var len = elm.val().length;
		var displayElm = $('#'+fid+"-char-count");
		
		if(isCount){
			displayElm.text('('+len+' characters)');
		}else{
			var maxLen = elm.prop('maxlength');
			var left = maxLen-len;
			displayElm.text('('+left+' characters left)');
			if(rem < 0){
				displayElm.css('color', 'red');
			}
		}
	}
    /******************************************
	***** CHARACTER COUNT FUNCTIONS - END *****
	******************************************/
	
	function set_field_value_by_elm(elm, type, value){
		switch(type){
			case 'radio':
				elm.val([value]);
				break;
			case 'checkbox':
				if(elm.data('multiple') == 1){
					value = value ? value : [];
					elm.val([value]);
				}else{
					console.log(value);
					elm.val([value]);
				}
				break;
			case 'select':
				if(elm.prop('multiple')){
					elm.val(value);
				}else{
					elm.val([value]);
				}
				break;
			case 'country':
				elm.val([value]).change();
				break;
			case 'state':
				elm.val([value]).change();
				break;
			case 'multiselect':
			
				if(elm.prop('multiple')){
					if(typeof(value) != "undefined"){
						elm.val(value.split(',')).change();
					}
				}else{
					elm.val([value]);
				}
				break;
			default:
				elm.val(value);
				break;
		}
	}
	
	function get_field_value(type, elm, name){
		var value = '';
		switch(type){
			case 'radio':
				value = $("input[type=radio][name="+name+"]:checked").val();
				break;
			case 'checkbox':
				if(elm.data('multiple') == 1){
					var valueArr = [];
					$("input[type=checkbox][name='"+name+"[]']:checked").each(function(){
					   valueArr.push($(this).val());
					});
					value = valueArr;//.toString();
				}else{
					value = $("input[type=checkbox][name="+name+"]:checked").val();
				}
				break;
			case 'select':
				value = elm.val();
				break;
			case 'multiselect':
				value = elm.val();
				break;
			default:
				value = elm.val();
				break;
		}
		return value;
	}
	
	return {
		
		display_char_count : display_char_count,
		set_field_value_by_elm : set_field_value_by_elm,
		get_field_value : get_field_value,
	};
}(window.jQuery, window, document));
