<?php
/**
 * The admin advanced settings page functionality of the plugin.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package     product-variation-swatches-for-woocommerce
 * @subpackage  product-variation-swatches-for-woocommerce/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWVSF_Admin_Settings_General')):

class THWVSF_Admin_Settings_General extends THWVSF_Admin_Settings{

	protected static $_instance = null;	
	private $settings_fields    = NULL;
	private $cell_props_CB      = array();
	private $cell_props_TA      = array();

	private $field_form = null;
	
	public function __construct() {
		parent::__construct('general_settings');
		$this->init_constants();
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	} 
	
	public function init_constants(){

		$this->cell_props_C = array( 

			'label_cell_props' => 'class="titledesc" scope="row" style="width: 40%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
		);

		$this->cell_props_CP = array( 

			'label_cell_props' => 'class="titledesc" scope="row" style="width: 440%;"', 
			'input_cell_props' => 'class="forminp inp-color"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
		);

		$this->cell_props_S = array(

			'label_cell_props' => 'class="titledesc" scope="row" style="width: 40%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
		);

		$this->cell_props_CB = array(
	
			'label_cell_props' => 'class="titledesc" scope="row" style="width: 40%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
			'label_props' => 'class = "thwvsadmin-slider-label" style="margin-right: 52px;"'
		);
		
		$this->global_fields = $this->get_global_settings_fields();
	}

	public function get_global_settings_fields(){

		$behaviors = array(
			'hide' => __('Hide', 'product-variation-swatches-for-woocommerce'),
			'blur' => __('Blur','product-variation-swatches-for-woocommerce'),
			'blur_with_cross' => __('Blur With Cross','product-variation-swatches-for-woocommerce'),
		);

		$out_of_stock_behaviour = array(
			'default' => __('Default', 'product-variation-swatches-for-woocommerce'),
			'blur' => __('Blur','product-variation-swatches-for-woocommerce'),
			'blur_with_cross' => __('Blur With Cross','product-variation-swatches-for-woocommerce'),
		);
		return array(

			'auto_convert' => array('type'=>'checkbox', 'name'=>'auto_convert', 'label'=>__('Convert all Dropdown Swatches to Label Swatches', 'product-variation-swatches-for-woocommerce') ,'hint_text' => __('If a label is not provided, the term name will be treated as Label'), 'product-variation-swatches-for-woocommerce', 'value'=>'yes', 'checked'=>0),
			
			'ajax_variation_threshold' => array('type'=>'text', 'name'=>'ajax_variation_threshold', 'label'=>__('Ajax Variation Threshold', 'product-variation-swatches-for-woocommerce'),'value'=>'30','min'=>1,'hint_text'=>__('By default, if the no. of product variations is less than 30, the product availability check is through JavaScript. If greater than 30, the ajax method is used. This field can control the threshold value of 30.', 'product-variation-swatches-for-woocommerce')),

			'clear_select' =>array('name'=>'clear_select', 'label'=>__('Clear On Reselect', 'product-variation-swatches-for-woocommerce'), 'type'=>'checkbox','hint_text'=>'', 'value'=>'yes', 'checked'=>1),

			'disable_style_sheet' =>array('name'=>'disable_style_sheet', 'label'=>__('Disable Swatches Plugin Stylesheet (For applying the theme default stylesheet)', 'product-variation-swatches-for-woocommerce'), 'type'=>'checkbox','hint_text'=>'', 'value'=>'yes', 'checked'=>0),

			'behavior_for_unavailable_variation' => array('name' => 'behavior_for_unavailable_variation','type' => 'select','options' =>$behaviors,'label' => __('Behavior for Unavailable Variation', 'product-variation-swatches-for-woocommerce'),'value' => 'blur_with_cross' ),

			'behavior_of_out_of_stock' => array('name' => 'behavior_of_out_of_stock','type' => 'select','options' =>$out_of_stock_behaviour,'label' => __('Behavior for Out of Stock Variation', 'product-variation-swatches-for-woocommerce'),'value' => 'default' ),
			'swatches_on_additional_info' => array('name'=>'swatches_on_additional_info', 'label'=>__('Enable Swatches on Additional Info', 'product-variation-swatches-for-woocommerce'), 'type'=>'checkbox','hint_text'=>'', 'value'=>'yes', 'checked'=>0),
			'show_selected_variation_name' => array('name'=>'show_selected_variation_name', 'label'=>__('Show Selected Variation Name Beside Attribute Label', 'woocommerce-product-variation-swatches'), 'type'=>'checkbox','hint_text'=>'', 'value'=>'yes', 'checked'=>0),
			'enable_lazy_load' => array('name'=>'enable_lazy_load', 'label'=>__('Enable Lazy Load', 'woocommerce-product-variation-swatches'), 'type'=>'checkbox','hint_text'=>'', 'value'=>'yes', 'checked'=>0),
		);
	}

	public function render_page(){

		$this->render_tabs();
		$this->render_content();
	}
		
	public function save_advanced_settings($settings){
		$result = update_option(THWVSF_Utils::OPTION_KEY_ADVANCED_SETTINGS, $settings);
		return $result;
	}
	
	private function reset_settings(){

		$nonse = isset($_REQUEST['thwvsf_security']) ? $_REQUEST['thwvsf_security'] : false;
		$capability = THWVSF_Utils::thwvsf_capability();
		if(!wp_verify_nonce($nonse, 'thwvs_global_settings') || !current_user_can($capability)){
			die();
		}
		$settings = $this->prepare_reset_global_settings_data($_POST, $this->global_fields);
		$result   = $this->save_advanced_settings($settings);

		if ($result == true) {
			echo '<div class="updated"><p>'. __('Settings successfully reset.','') .'</p></div>';
		} else {
			echo '<div class="error"><p>'. __('Your changes were not saved due to an error (or you made none!).','') .'</p></div>';
		}
	}
	
	private function save_settings(){

		$nonse = isset($_REQUEST['thwvsf_security']) ? $_REQUEST['thwvsf_security'] : false;
		$capability = THWVSF_Utils::thwvsf_capability();
		if(!wp_verify_nonce($nonse, 'thwvs_global_settings') || !current_user_can($capability)){
			die();
		}

		$settings = $this->prepare_global_settings_from_posted_data($_POST, $this->global_fields);
		$result   = $this->save_advanced_settings($settings);

		if ($result == true) {
			echo '<div class="updated"><p>'. __('Your changes were saved.','') .'</p></div>';
		} else {
			echo '<div class="error"><p>'. __('Your changes were not saved due to an error (or you made none!).','') .'</p></div>';
		}
	}
	
	public function render_content(){

		$default_settings = array();
		$design_settings = array();

		if(isset($_POST['global_reset_settings']))
			$this->reset_settings();	
			
		if(isset($_POST['global_save_settings']))
			$this->save_settings();
			
		$settings = THWVSF_Utils::get_advanced_swatches_settings();	
		?><form method="post" id="thwvs_global_form" action="">
			<?php wp_nonce_field( 'thwvs_global_settings','thwvsf_security' ); ?>
			<div class="thwvsf_settings">

		        <div id="thwvsf_global_settings"  style="display: inherit;" class="global-data-content" >

		        	<?php $this->render_form_tab_main_title('Global Settings'); ?>
		        	<table class="thwvs-settings-table thwvs-pp-table">
	                    <tbody>
	                    	<?php $this->render_global_settings($settings); ?>
	                    </tbody>
	                </table> 

	                <div class="btn-toolbar">
										
						<input type="submit" class="save-btn btn-primary-alt" name="global_save_settings" class="button-primary" value="<?php _e('Save', 'woocommerce-product-variation-swatches'); ?>"/>

						<input type="submit" class="reset-btn btn-primary-alt" name="global_reset_settings" class="button-primary" value="<?php _e('Reset', 'woocommerce-product-variation-swatches'); ?>" onclick="return confirm('Are you sure you want to reset to default settings? all your changes will be deleted.');">	
											
					</div>

		        </div>  	
			</div>  
		</form>	 		
		<?php
	}

	private function prepare_global_settings_from_posted_data($posted, $props){

		$all_settings = array();
		$advanced_settings = THWVSF_Utils::get_advanced_swatches_settings();

		$available_settings = array(

			'swatch_design_default'  => THWVSF_Admin_Utils::get_property_set($advanced_settings),
			'swatch_global_settings' => THWVSF_Admin_Utils::get_global_settings_property_set($advanced_settings),
			'swatch_design_1'       =>  THWVSF_Admin_Utils::get_property_set(),
			'swatch_design_2'       => THWVSF_Admin_Utils::get_property_set(),
			'swatch_design_3'       => THWVSF_Admin_Utils::get_property_set(),
		);

		if($advanced_settings && is_array($advanced_settings)){

			if((!(isset($advanced_settings['swatch_design_default'])))){
				$all_settings = $available_settings;
			}else{
				$all_settings = $advanced_settings;
			}

		}else{

			$all_settings = $available_settings;
		}

		$field = array();	
		foreach( $props as $pname => $property ){
			$iname  = 'i_'.$pname;
			$pvalue = '';

			if($property['type'] === 'checkbox'){
				$pvalue = (isset($posted[$iname]) && ($posted[$iname] === 'yes'))? 'yes' : 'no';
			}else if(isset($posted[$iname])){
				$pvalue = is_array($posted[$iname]) ? implode(',', wc_clean(wp_unslash($posted[$iname]))) : wc_clean(wp_unslash($posted[$iname]));
			}

			$field[$pname] =  $pvalue;
		}

		$all_settings['swatch_global_settings'] = $field;
		return $all_settings;
	}

	private function prepare_reset_global_settings_data($posted, $props){

		$all_settings       = array();
		$advanced_settings  = THWVSF_Utils::get_advanced_swatches_settings();

		$available_settings = array(

			'swatch_design_default'  => THWVSF_Admin_Utils::get_property_set($advanced_settings),
			'swatch_global_settings' => THWVSF_Admin_Utils::get_global_settings_property_set($advanced_settings),
			'swatch_design_1'        =>  THWVSF_Admin_Utils::get_property_set(),
			'swatch_design_2'        => THWVSF_Admin_Utils::get_property_set(),
			'swatch_design_3'        => THWVSF_Admin_Utils::get_property_set()
		);

		if($advanced_settings && is_array($advanced_settings)){
			if((!(isset($advanced_settings['swatch_design_default'])))){
				$all_settings = $available_settings;
			}else{
				$all_settings = $advanced_settings;
			}

		}else{

			$all_settings = $available_settings;
		}

		$field = array();	
		foreach( $props as $pname => $property ){
			$iname  = 'i_'.$pname;
			$pvalue = '';

			if($property['type'] === 'checkbox'){
				$pvalue = 0;
				if($pname === 'clear_select'){
					$pvalue = 'yes';
				}
			}else if(isset($posted[$iname])){
				$pvalue = $property['value'];
			}

			$field[$pname] =  $pvalue;
		}

		$all_settings['swatch_global_settings'] = $field;
		return $all_settings;
	}

	/*******************************
		global settings
	*******************************/

	private function render_global_settings($settings){
		
		if(is_array($settings) && !empty($settings)){
			$settings = isset($settings['swatch_global_settings']) ? $settings['swatch_global_settings'] : $settings;
			if(is_array($settings) && $this->global_fields){
				foreach( $this->global_fields as $name => &$field ) {
					if($field['type'] != 'separator'){
						if( isset($settings[$name])){
							if($field['type'] === 'checkbox'){
								if($field['value'] === $settings[$name]){
									$field['checked'] = 1;
								}else{
									$field['checked'] = 0;
								}
							}else{
								$field['value'] = $settings[$name];
							}
						}
					}
				}
			}
		}
		?>
		<tr>
			<?php
			$this->render_form_field_element($this->global_fields['auto_convert'], $this->cell_props_CB);
			?>							
		</tr>
		<tr>
			<?php
			$this->render_form_field_element($this->global_fields['behavior_for_unavailable_variation'], $this->cell_props_S);
			?>							
		</tr>
		<tr>
			<?php
			$this->render_form_field_element($this->global_fields['behavior_of_out_of_stock'], $this->cell_props_S);
			?>							
		</tr>

		<tr>
			<?php
			$this->render_form_field_element($this->global_fields['clear_select'], $this->cell_props_CB);
			?>							
		</tr>
		<tr>
			<?php
			$this->render_form_field_element($this->global_fields['disable_style_sheet'], $this->cell_props_CB);
			?>							
		</tr>
		<tr>
			<?php
			$this->render_form_field_element($this->global_fields['show_selected_variation_name'], $this->cell_props_CB);
			?>							
		</tr>
		<tr>
			<?php
			$this->render_form_field_element($this->global_fields['swatches_on_additional_info'], $this->cell_props_CB);
			?>							
		</tr>
		<tr>
			<?php
			$this->render_form_field_element($this->global_fields['ajax_variation_threshold'], $this->cell_props_C);
			?>							
		</tr>
		<tr>
			<?php
			$this->render_form_field_element($this->global_fields['enable_lazy_load'], $this->cell_props_CB);
			?>							
		</tr>
		<?php
	}
}
endif;