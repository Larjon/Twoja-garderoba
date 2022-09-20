<?php
/**
 * The common utility functionalities for the plugin.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    product-variation-swatches-for-woocommerce
 * @subpackage product-variation-swatches-for-woocommerce/includes/utils
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWVSF_Utils')):

class THWVSF_Utils {
	const OPTION_KEY_ADVANCED_SETTINGS = 'thwvs_swatches_advanced_settings';
	const OPTION_KEY_DESIGN_SETTINGS   = 'thwvs_swatches_design_settings';
	
	public static function get_advanced_swatches_settings($settings_key = false){
		$settings = get_option(self::OPTION_KEY_ADVANCED_SETTINGS,true);
		if($settings_key) {
			$settings_value = isset($settings[$settings_key])? $settings[$settings_key] : '';
			return empty($settings_value) ? false : $settings_value;
		}
		
		return empty($settings) ? false : $settings;
	}

	public static function get_global_swatches_settings($settings_key, $settings = false ){
		
		$settings = $settings ? $settings : get_option(self::OPTION_KEY_ADVANCED_SETTINGS,true);

		if($settings && is_array($settings)){

			$global_settings = isset($settings['swatch_global_settings']) ? $settings['swatch_global_settings'] :  $settings;
			$settings_value  = isset($global_settings[$settings_key ]) ? $global_settings[$settings_key ] : false;
			return $settings_value;

		} 
		return false;
	}

	public static function get_design_swatches_settings($attr_id = false){//, $settings_key = false){

		$settings = get_option(self::OPTION_KEY_DESIGN_SETTINGS,true);
		if($attr_id){
			$settings_values = isset($settings[$attr_id])? $settings[$attr_id] : array();

			/*if($settings_key){

				$settings_value = isset($settings_values[$settings_key])? $settings_values[$settings_key] : '';
				return empty($settings_value) ? false : $settings_value;
			}*/
			return empty($settings_values) ? false : $settings_values;
		}
		return empty($settings) ? false : $settings;
	}

	public static function is_quick_view_plugin_active(){
		$quick_view = false;
		if(self::is_flatsome_quick_view_enabled()){
			$quick_view = 'flatsome';
		}else if(self::is_yith_quick_view_enabled()){
			$quick_view = 'yith';
		}else if(self::is_astra_quick_view_enabled()){
			$quick_view = 'astra';
		}else if(self::is_wpc_quick_view_enable()){
			$quick_view = 'wpc_smart';
		}
		return apply_filters('thwvsf_is_quick_view_plugin_active', $quick_view);
	}
	
	public static function is_yith_quick_view_enabled(){
		return is_plugin_active('yith-woocommerce-quick-view/init.php');
	}
	
	public static function is_flatsome_quick_view_enabled(){
		return (get_option('template') === 'flatsome');
	}

	public static function is_astra_quick_view_enabled(){
		return is_plugin_active('astra-addon/astra-addon.php');
	}

	public static function is_wpc_quick_view_enable(){
		return is_plugin_active('woo-smart-quick-view/wpc-smart-quick-view.php');
	}

	public static function thwvsf_capability() {
		$allowed = array('manage_woocommerce', 'manage_options');
		$capability = apply_filters('thwvsf_required_capability', 'manage_woocommerce');

		if(!in_array($capability, $allowed)){
			$capability = 'manage_woocommerce';
		}
		return $capability;
	}
}

endif;