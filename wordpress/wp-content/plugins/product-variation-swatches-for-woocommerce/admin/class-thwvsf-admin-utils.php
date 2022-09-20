<?php
/**
 * The admin settings page common utility functionalities.
 *
 * @link       https://themehigh.com
 * @since      2.0.0
 *
 * @package    woocommerce-product-variation-swatches
 * @subpackage woocommerce-product-variation-swatches/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWVSF_Admin_Utils')):

class THWVSF_Admin_Utils {

	static $sample_design_labels = array(

		'swatch_design_default' => 'Default Design',
		'swatch_design_1' => 'Design 1',
		'swatch_design_2' => 'Design 2',
		'swatch_design_3' => 'Design 3',

	);

	static $DESIGN_PROPS = array(

		'design_name'  => array( 'name'=>'design_name', 'value' => ''),
		'icon_height'  => array('name'=>'icon_height','value' => '45px'),
		'icon_width'   => array('name'=>'icon_width','value'=>'45px'),
		'icon_shape'   => array('name'=>'icon_shape','value'=>'square'),

		'icon_label_height'  => array('name'=>'icon_height','value' => '45px'),
		'icon_label_width'   => array('name'=>'icon_width','value'=>'auto'),
		'label_size'             => array('name'=>'label_size','value' => '16px'),
		'label_background_color' => array('name'=>'label_background_color', 'value' => '#fff'),
		'label_text_color'       => array('name'=>'label_text_color', 'value' => '#000'),
			
		// Active and Hover Settings fields
		'icon_border_color'          => array('name'=>'icon_border_color', 'value'=>'#d1d7da'),
		'icon_border_color_hover'    => array('name'=>'icon_border_color_hover', 'value'=>'#aaaaaa'),
		'icon_border_color_selected' => array('name'=>'icon_border_color_selected','value' => '#827d7d'),
		'icon_border_width_hover'    => array('name'=>'icon_border_width_hover','value'=>'3px'),
		'icon_border_width_selected' => array('name'=>'icon_border_width_selected','value'=>'2px'),

		'common_selection_style' => array('name'=>'common_selection_style', 'value'=>'border' ),
		'tick_color'             => array('name'=>'tick_color', 'value'=>'#ffffff' ),
		'tick_size'              => array('name'=>'tick_size', 'value'=>'15px' ),

		'label_selection_style'            => array('name'=>'label_selection_style', 'value'=>'border' ),
		'label_background_color_hover'     => array('name'=>'label_background_color_hover','value'=>'#ffffff'),
		'label_text_color_hover'           => array('name'=>'label_text_color_hover','value'=>'#000000'),
		'label_background_color_selection' => array('name'=>'label_background_color_selection','value'=>'#000000'),
		'label_text_color_selection'       => array('name'=>'label_text_color_selection','value'=>'#ffffff'),
		'label_tick_color'                 => array('name'=>'label_tick_color', 'value'=>'#000000' ),
		'label_tick_size'                  => array('name'=>'label_tick_size', 'value'=>'15px' ),
		'enable_swatch_dropdown'           => array('name'=>'enable_swatch_dropdown', 'value'=>0,'value_type'=>'boolean'),
		// Tooltip Settings fields
		'tooltip_enable'                => array('name'=>'tooltip_enable', 'value'=>0,'value_type'=>'boolean'), 
		'tooltip_text_background_color' => array('name'=>'tooltip_text_background_color','value' => '#000000'),
		'tooltip_text_color'            => array('name'=>'tooltip_text_color','value' => '#ffffff'),
		'tooltip_text_size'             => array('name'=>'tooltip_text_size','value' => '16px'),

	);

	static $GLOBAL_PROPS = array(

		'auto_convert'                       => array('name'=>'auto_convert', 'value' =>0),
		'clear_select'                       => array('name'=>'clear_select', 'value' =>'yes'),
		'show_selected_variation_name'       => array('name'=>'show_item_on_label', 'value' =>0),
		'ajax_variation_threshold'           => array('name'=>'ajax_variation_threshold', 'value' =>'30'),
		'disable_style_sheet'                => array('name'=>'disable_style_sheet', 'value' =>0),
		'behavior_for_unavailable_variation' => array('name'=> 'behavior_for_unavailable_variation','value' => 'blur_with_cross'),
		'behavior_of_out_of_stock'           => array('name'=> 'behavior_of_out_of_stock','value' => 'default'),
		'swatches_on_additional_info'        => array('name'=>'swatches_on_additional_info', 'value' =>0),
		'enable_lazy_load'                   => array('name'=>'enable_lazy_load', 'value' =>0),

	);

	public static function get_sample_design_templates($settings){

		$sample_designs = array();

	    foreach (self::$sample_design_labels as $key => $label) {

	    	$sample_design       = array();
	    	$sample_designs[$key] = THWVSF_Admin_Utils::get_property_set($settings, $label);
	    }	

	    return $sample_designs;
	}

	public static function get_property_set($settings_design = array(),$label = false){

		$props_set = array();
			
		foreach(self::$DESIGN_PROPS as $pname => $props){

			$pvalue =  isset($settings_design[$pname]) ? $settings_design[$pname] : $props['value'] ;

			if($pname === 'design_name'){

				$pvalue = ($pvalue === '' &&  $label) ? $label : $pvalue ;
			}
			
			if(isset($props['value_type']) && $props['value_type'] === 'array' && !empty($pvalue)){
				$pvalue = is_array($pvalue) ? $pvalue : explode(',', $pvalue);
			}
			
			if(isset($props['value_type']) && $props['value_type'] != 'boolean'){
				$pvalue = empty($pvalue) ? $props['value'] : $pvalue;
			}
			
			$props_set[$pname] = $pvalue;
		}
			
		return $props_set;
	}

	public static function get_property_json($desgn_id, $settings = false){ 

		$settings = $settings ? $settings : THWVSF_Utils::get_advanced_swatches_settings();

		if(is_array($settings) && isset($settings[$desgn_id])){

			$settings_design = $settings[$desgn_id];

		}else{

			$settings_design = is_array($settings) ? $settings : array();
		}

		$props_json = '';
		$props_set = self::get_property_set($settings_design);
		
		if($props_set){
			$props_json = json_encode($props_set, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		}
		return $props_json;
	}

	public static function get_global_settings_property_set($settings_design = array()){

		$props_set = array();

		foreach(self::$GLOBAL_PROPS as $pname => $props){

			$pvalue =  isset($settings_design[$pname]) ? $settings_design[$pname] : $props['value'] ;
			
			if(isset($props['value_type']) && $props['value_type'] === 'array' && !empty($pvalue)){
				$pvalue = is_array($pvalue) ? $pvalue : explode(',', $pvalue);
			}
			
			if(isset($props['value_type']) && $props['value_type'] != 'boolean'){
				$pvalue = empty($pvalue) ? $props['value'] : $pvalue;
			}
			
			$props_set[$pname] = $pvalue;
		}
			
		return $props_set;

	}

	public static function get_swatches_design_by_key($attr_id, $settings=false){

		$default_design =  'swatch_design_default';
		if(!$settings){

			$settings = THWVSF_Utils::get_design_swatches_settings();;
		}
		if($settings && is_array($settings)){

			if($attr_id){

				$swatch_design = isset($settings[$attr_id])? $settings[$attr_id] : $default_design ;

				return $swatch_design;
			}
			
		}
		return $default_design;
	}

	public static function get_design_styles($design_type = false,$settings = false){

		$free_design_keys = array('swatch_design_default', 'swatch_design_1', 'swatch_design_2', 'swatch_design_3');
		if(!$settings){
			$settings =  THWVSF_Utils::get_advanced_swatches_settings();
		}

		$designs = array();
		if($settings && is_array($settings) && isset($settings['swatch_design_default'])){

			foreach ($settings as $key => $value) {
				if (in_array($key, $free_design_keys)){
				
					if($key !== 'swatch_global_settings'){

						$name = isset($value['design_name']) ? $value['design_name'] : '';

						if(empty($name)){
							if($key == 'swatch_design_default'){
								$name = 'Default Design';
							}else{
								$des_key = str_replace('swatch_design_','', $key) ;
								$name  = 'Design '.$des_key;
							} 
						} 
						$designs[$key] = $name;

						if($design_type && $key == $design_type){
							return $name;
						}
					}
				}	
			}
			return $designs;
		}
		return false ;
	}

	public static function get_design_name_from_sample($design_type){

		foreach (self::$sample_design_labels as $key => $label) {
			if($key === $design_type){
				return $label;
			}
		}
		return 'Default Design';
	}

}
endif;