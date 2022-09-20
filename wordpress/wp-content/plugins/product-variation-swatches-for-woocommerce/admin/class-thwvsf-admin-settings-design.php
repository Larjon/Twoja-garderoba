<?php
/**
 * The admin advanced settings page functionality of the plugin.
 *
 * @link       https://themehigh.com
 * @since      2.0.0
 *
 * @package     product-variation-swatches-for-woocommerce
 * @subpackage  product-variation-swatches-for-woocommerce/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWVSF_Admin_Settings_Design')):

class THWVSF_Admin_Settings_Design extends THWVSF_Admin_Settings {

	protected static $_instance = null;
	private $settings_fields    = NULL;

	private $cell_props_C  = array();
	private $cell_props_CP = array();
	private $cell_props_S  = array();
	private $cell_props_CB = array();	

	public function __construct() {
		parent::__construct('swatches_design_settings');
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

			'label_cell_props' => 'class="titledesc" scope="row" style="width: 25%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
		);

		$this->cell_props_CP = array( 

			'label_cell_props' => 'class="titledesc" scope="row" style="width: 25%;"', 
			'input_cell_props' => 'class="forminp inp-color"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
		);

		$this->cell_props_S = array(

			'label_cell_props' => 'class="titledesc" scope="row" style="width: 25%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
		);

		$this->cell_props_CB = array(
	
			'label_cell_props' => 'class="titledesc" scope="row" style="width: 25%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
			'label_props' => 'class = "thwvsadmin-slider-label" style="margin-right: 52px;"'
		);
		
		$this->settings_fields = $this->get_design_settings_fields();

	}

	public function render_page(){
		
		$this->render_tabs(); 
		$this->render_contents();
	}

	public function get_design_settings_fields(){

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

		$icon_shapes = array(
			'round' => __('Round', 'product-variation-swatches-for-woocommerce'),
			'square' => __('Square', 'product-variation-swatches-for-woocommerce'),
		);

		$selction_style = array(
			'border'=> __('Border Style', 'product-variation-swatches-for-woocommerce'),
			'enlarge' => __('Enlarge Style', 'product-variation-swatches-for-woocommerce'),
		);

		$comn_selctn_style = array(

			'border'           => __('Borders on selection', 'product-variation-swatches-for-woocommerce'),
			'border_with_tick' => __('Checkmark with border', 'product-variation-swatches-for-woocommerce'),
		);

		$label_selectn_style = array(
			'border'            => __('Borders on selection', 'product-variation-swatches-for-woocommerc'),
			'border_with_tick'  => __('Checkmark with border', 'product-variation-swatches-for-woocommerc'),
			'background_color'  => __('Background with font color', 'product-variation-swatches-for-woocommerc')
		);

		return array(
			//'last_active_tab' => array('type' => 'hidden', 'name'=>'last_active_tab', 'value' => '0'),
			// Common Attribute Settings
		
			'design_name' => array('type'=>'text', 'name'=>'design_name', 'label'=>__('Design Name', 'woocommerce-product-variation-swatches'),'value' => ' '),
			'icon_height' => array('type'=>'text', 'name'=>'icon_height', 'value' => '45px','label'=>__('Icon Height', 'product-variation-swatches-for-woocommerce')),
			'icon_width' => array('type'=>'text', 'name'=>'icon_width','label'=>__('Icon Width', 'product-variation-swatches-for-woocommerce'),'value'=>'45px'),
			'icon_shape' => array('type'=>'select', 'name'=>'icon_shape','options' =>$icon_shapes,'label'=>__('Icon Shape', 'product-variation-swatches-for-woocommerce'),'value'=>'square'),

			'common_selection_style'  => array('name'=>'common_selection_style', 'id'=>'common_selection_style' ,'label'=>__('Selection Styles', 'woocommerce-product-variation-swatches'), 'type'=>'select', 'hint_text'=>__('Selection style apply for swatch type color,image', 'woocommerce-product-variation-swatches'), 'onchange'=>'thwvsfShowcheckStyles(this)','options' => $comn_selctn_style),
			'tick_color' => array('name'=>'tick_color', 'label'=>__('Tick Color', 'woocommerce-product-variation-swatches'), 'type'=>'colorpicker','value' => '#ffffff'),
			'tick_size' => array('name'=>'tick_size', 'label'=>__('Tick Size', 'woocommerce-product-variation-swatches'), 'type'=>'text','value' => '15px'),

			'label_selection_style'  => array('name'=>'label_selection_style', 'id'=>'label_selection_style' ,'label'=>__('Button/Label Selection Styles', 'woocommerce-product-variation-swatches'), 'type'=>'select', 'hint_text'=>__('Selection style apply for swatch type Label/Button', 'woocommerce-product-variation-swatches'),'onchange'=>'thwvsfShowLabelSelectionStyles(this)' ,'options' => $label_selectn_style),
			'label_background_color_hover' => array('name'=>'label_background_color_hover', 'label'=>__('Background Color on Hover', 'woocommerce-product-variation-swatches'), 'type'=>'colorpicker','value'=>'#ffffff'),
			'label_text_color_hover' => array('name'=>'label_text_color_hover', 'label'=>__('Text Color on Hover', 'woocommerce-product-variation-swatches'), 'type'=>'colorpicker','value' => '#000000'),
			'label_background_color_selection' => array('name'=>'label_background_color_selection', 'label'=>__('Background Color on Selection', 'woocommerce-product-variation-swatches'), 'type'=>'colorpicker','value'=>'#000000'),
			'label_text_color_selection' => array('name'=>'label_text_color_selection', 'label'=>__('Text Color on Selection', 'woocommerce-product-variation-swatches'), 'type'=>'colorpicker','value' => '#ffffff'),

			'label_tick_color' => array('name'=>'label_tick_color', 'label'=>__('Tick Color', 'woocommerce-product-variation-swatches'), 'type'=>'colorpicker','value' => '#000000'),
			'label_tick_size' => array('name'=>'label_tick_size', 'label'=>__('Tick Size', 'woocommerce-product-variation-swatches'), 'type'=>'text','value' => '15px'),

			// Label attribute Settings

			'icon_label_height' => array('type'=>'text', 'name'=>'icon_label_height', 'label'=>__('Icon Height', 'product-variation-swatches-for-woocommerce'),'value' => '45px'),
			'icon_label_width' => array('type'=>'text', 'name'=>'icon_label_width','label'=>__('Icon Width', 'product-variation-swatches-for-woocommerce'),'value'=>'auto'),
			'label_size' => array('type'=>'text', 'name'=>'label_size', 'label'=>__('Font Size', 'product-variation-swatches-for-woocommerce'),'value' => '16px'),
			'label_background_color' => array('name'=>'label_background_color', 'label'=>__('Background Color', 'product-variation-swatches-for-woocommerce'), 'type'=>'colorpicker', 'required'=>0, 'value' => '#fff'),
			'label_text_color' => array('name'=>'label_text_color', 'label'=>__('Text Color', 'product-variation-swatches-for-woocommerce'), 'type'=>'colorpicker', 'required'=>0, 'value' => '#000'),

			'enable_swatch_dropdown' =>array('name'=>'enable_swatch_dropdown', 'label'=>__('Enable Swatch DropDown', 'product-variation-swatches-for-woocommerce'), 'type'=>'checkbox','hint_text'=>'', 'value'=>'yes', 'checked'=>0),

			// Tooltip Settings
			'tooltip_enable' =>array('name'=>'tooltip_enable', 'label'=>__('Enable Tooltip (Attribute term name will be displayed as Tooltip)', 'product-variation-swatches-for-woocommerce'), 'type'=>'checkbox','hint_text'=>'', 'value'=>'yes', 'checked'=>0),
			'tooltip_text_background_color' => array('name'=>'tooltip_text_background_color', 'label'=>__('Term Name Background Color', 'product-variation-swatches-for-woocommerce'),'type'=>'colorpicker','value' => '#000000'),
			'tooltip_text_color' => array('name'=>'tooltip_text_color', 'label'=>__('Term Name Text Color', 'product-variation-swatches-for-woocommerce'),'type'=>'colorpicker','value' => '#ffffff'),

			// Active variation Style

			'icon_border_color' => array('name'=>'icon_border_color', 'label'=>__('Border Color', 'product-variation-swatches-for-woocommerce'),'type'=>'colorpicker','value' => '#d1d7da'),
			'icon_border_color_selected' => array('name'=>'icon_border_color_selected', 'label'=>__('Border Color On Selected', 'product-variation-swatches-for-woocommerce'),'type'=>'colorpicker','value' => '#8b98a6'),
			'icon_border_color_hover' => array('name'=>'icon_border_color_hover', 'label'=>__('Border Color On Hover', 'product-variation-swatches-for-woocommerce'),'type'=>'colorpicker','value' => '#b7bfc6'),
			
			// Other settings

		);
	}


	public function render_contents(){

		if(isset($_POST['design_save_settings']))
			$this->save_design_settings();

		if(isset($_POST['design_reset_settings']))
			$this->reset_design_settings();

		$settings = THWVSF_Utils::get_advanced_swatches_settings();	
		$design_templates = array();
		if(is_array($settings) && isset($settings['swatch_design_default'])){
			$design_templates = $settings;
		}else{
			$design_templates = THWVSF_Admin_Utils::get_sample_design_templates($settings);
		}
		?>
	    <div class="thwvs-design-templates thwvs-paraent-template">

	    	<div class = "th-template-description">
				<p>Swatches design lets you edit the display style of the attribute, such as icon size, style, hover and border details, etc.
	    		</br>
				Note: If you are an existing user, you can get the already created design from the Default Design tab.</p>
	    	</div>
		    
	    	<div class="thwvs-template-preview-wrapper"> <?php 

	    		$des_keys = array();
	    		$free_design_keys = array('swatch_design_default', 'swatch_design_1', 'swatch_design_2', 'swatch_design_3');

	    		foreach ($design_templates as $key => $settings) { 

	    			$des_name = '';

	    			if (in_array($key, $free_design_keys)){

		    			if ($key != 'swatch_global_settings'){

		    				$props_json = THWVSF_Admin_Utils::get_property_json($key);
		    				$additional_class = '';

		    				if ($key != 'swatch_design_default'){

		    					$des_key    = str_replace('swatch_design_','', $key);
		    					$des_keys[] = $des_key;
		    					$des_name = __('Design  '.$des_key,'product-variation-swatches-for-woocommerce' ); 
		    					$additional_class = 'thwvs-all-temp';
		    				}else{
		    					$des_name = 'Default Design';
		    					$additional_class = 'thwvs-default-temp';
		    				}

		    				$label = (isset($settings['design_name']) && $settings['design_name'] != '') ? $settings['design_name'] : $des_name;
		    				?>
			    			<div class="thwvs-template-box">
			    				
			    				<div class="thwvs-template-name <?php echo $additional_class; ?>">
			    						<img class="thwvs-dot-element" src="<?php echo esc_url(THWVSF_ASSETS_URL_ADMIN.'images/dots.svg'); ?>"/>
			    					<p class="thwvs-label"><?php echo esc_html($label); ?></p>
			    					<?php $label = htmlspecialchars(addslashes($label), ENT_QUOTES); ?>
			    				
				    				<div class="thwvs-edit-element" data-block-name="<?php echo esc_attr($key); ?>" onclick='thwvsfEditDesignForm(this,<?php echo $props_json; ?>, "<?php echo $key ; ?>",  "<?php echo ($label) ; ?>")'>
				    					<span class="icon icon-edit" > </span>
										
									</div>

								</div>
			    			</div>

		    				<?php 
		    			}
		    		}
	    		} 

	    		?>
	    	</div>
	    </div>
	   	<div class="thwvs-design-templates thwvs-template-popup">
		  
	    	<?php $this->output_design_form_pp(); ?> 
	    </div>
	    <?php
	}

	private function save_design_settings(){

		$nonse = isset($_REQUEST['thwvsf_security']) ? $_REQUEST['thwvsf_security'] : false;
		$capability = THWVSF_Utils::thwvsf_capability();
		if(!wp_verify_nonce($nonse, 'thwvs_add_design_type') || !current_user_can($capability)){
			die();
		}

		$settings = $this->prepare_design_field_from_posted_data($_POST, $this->settings_fields);

		$result   = $this->save_settings($settings);

		if ($result == true) {
			echo '<div class="updated"><p>'. __('Your changes were saved.','woocommerce-product-variation-swatches') .'</p></div>';
		} else {
			echo '<div class="error"><p>'. __('Your changes were not saved due to an error (or you made none!).','woocommerce-product-variation-swatches') .'</p></div>';
		}
	}

	private function save_settings($settings){

		$result = update_option(THWVSF_Utils::OPTION_KEY_ADVANCED_SETTINGS, $settings);
		return $result;

	}

	private function reset_design_settings(){
		$nonse = isset($_REQUEST['thwvsf_security']) ? $_REQUEST['thwvsf_security'] : false;
		$capability = THWVSF_Utils::thwvsf_capability();
		if(!wp_verify_nonce($nonse, 'thwvs_add_design_type') || !current_user_can($capability)){
			die();
		}

		$settings = $this->prepare_reset_design_field($_POST, $this->settings_fields);
		$result   = $this->save_settings($settings);

		if ($result == true) {
			echo '<div class="updated"><p>'. __('Settings successfully reset.','woocommerce-product-variation-swatches') .'</p></div>';
		} else {
			echo '<div class="error"><p>'. __('Your changes were not saved due to an error (or you made none!).','woocommerce-product-variation-swatches') .'</p></div>';
		}
	}

	public function prepare_design_field_from_posted_data($posted, $props){

		$all_settings = array();
		
		$advanced_settings = THWVSF_Utils::get_advanced_swatches_settings();

		$available_settings = array(

			'swatch_design_default'  => THWVSF_Admin_Utils::get_property_set($advanced_settings),
			'swatch_global_settings' => THWVSF_Admin_Utils::get_global_settings_property_set($advanced_settings),
			'swatch_design_1'        => THWVSF_Admin_Utils::get_property_set(),
			'swatch_design_2'        => THWVSF_Admin_Utils::get_property_set(),
			'swatch_design_3'        => THWVSF_Admin_Utils::get_property_set(),
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

		$design_key = isset($posted['thwvsf_design_id']) ? wc_clean(wp_unslash($posted['thwvsf_design_id'])) : 'design_default';
		//$design_key = 'swatch_'.$design_key;
		$all_settings[$design_key] = $field;
		return $all_settings;
	}

	public function prepare_reset_design_field($posted, $props){

		$all_settings = array();
		$advanced_settings = THWVSF_Utils::get_advanced_swatches_settings();

		$available_settings = array(

			'swatch_design_default'  => THWVSF_Admin_Utils::get_property_set($advanced_settings),
			'swatch_global_settings' => THWVSF_Admin_Utils::get_global_settings_property_set($advanced_settings),
			'swatch_design_1'        =>  THWVSF_Admin_Utils::get_property_set(),
			'swatch_design_2'        => THWVSF_Admin_Utils::get_property_set(),
			'swatch_design_3'        => THWVSF_Admin_Utils::get_property_set(),
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

		if(isset($posted['thwvsf_design_id'])){

			$reset_design                = wc_clean(wp_unslash($posted['thwvsf_design_id']));
			$all_settings[$reset_design] = THWVSF_Admin_Utils::get_property_set();
		}

		return $all_settings;
	}

	private function output_design_form_pp(){		
		?>
        <div  id = "thwvs_design_form_pp" class="popup-wrapper dismiss">
        	<div class="thwvsadmin-popup">
				<div class="popup-container">
					<div class="popup-content">
						<div class="popup-body">
							<form method="post" id="thwvs_design_form" action="">
								<?php wp_nonce_field( 'thwvs_add_design_type','thwvsf_security' ); ?>
								<div class="form-pp-content pp-content">
									<aside>
										<!-- <span class="pp-close"  onclick="thwvsCloseDesignPopup(this)"> -->
											<img class="thwvs-close-element pp-close" src="<?php echo THWVSF_ASSETS_URL_ADMIN.'images/popup-arrow.svg'; ?>" onclick="thwvsfCloseDesignPopup(this)"/>
										<!-- </span> -->
										<side-title class="pp-title">Default Design</side-title>
										<ul class="pp_nav_tabs">

											<li class="pp-nav-tab active first pp-nav-link-basic" data-index="0">
												<div class="pp-tab-link">

													<div class="tab-icon-element"> <span class="tab-icon icon-common"> </span></div>
													<span class = "tab-text text-common">Common Attribute Styling</span>
													<img class="thwvs-active-element active-arrow" src="<?php echo THWVSF_ASSETS_URL_ADMIN.'images/tab-arrow.svg'; ?>" />
											   </div>
											</li>

											<li class="pp-nav-tab pp-nav-link-basic" data-index="1">
												<div class="pp-tab-link">

													<div class="tab-icon-element"> <span class="tab-icon icon-border"> </span></div>
													<span class = "tab-text text-border">Hover and Border Styling</span>
													<img class="thwvs-active-element active-arrow" src="<?php echo THWVSF_ASSETS_URL_ADMIN.'images/tab-arrow.svg'; ?>" />
											   </div>
											</li>

											<li class="pp-nav-tab pp-nav-link-basic" data-index="2">
												<div class="pp-tab-link">

													<div class="tab-icon-element"> <span class="tab-icon icon-tooltip"> </span></div>
													<span class = "tab-text text-tooltip">Tooltip Styling</span>
													<img class="thwvs-active-element active-arrow" src="<?php echo THWVSF_ASSETS_URL_ADMIN.'images/tab-arrow.svg'; ?>" />
											   </div>
											</li>

											<li class="pp-nav-tab pp-nav-link-basic" data-index="3">
												<div class="pp-tab-link">

													<div class="tab-icon-element"> <span class="tab-icon icon-specific"> </span></div>
													<span class = "tab-text text-specific">Swatch Type Specific Styling</span>
													<img class="thwvs-active-element active-arrow" src="<?php echo THWVSF_ASSETS_URL_ADMIN.'images/tab-arrow.svg'; ?>" />
											   </div>
											</li>
										
										</ul>

										<div class="btn-toolbar">
										
											<input type="submit" class="save-btn btn-primary-alt" name="design_save_settings" class="button-primary" value="<?php _e('Save', 'woocommerce-product-variation-swatches'); ?>"/>

											<input type="submit" class="reset-btn btn-primary-alt" name="design_reset_settings" class="button-primary" value="<?php _e('Reset', 'woocommerce-product-variation-swatches'); ?>"   onclick="return confirm('Are you sure you want to reset the Design? all the changes you have made will be reset to its default ');">	
											
										</div>
									</aside>

									<main class="form-container main-full">
										
										<input type="hidden" name="f_action" value="" />
										<input type = "hidden" name = "thwvsf_design_id" value="" />

										<div class="data-panel data_panel_0">
											<?php $this->render_form_common_attribute_settings(); ?>
										</div>
										<div class="data-panel data_panel_1">
											<?php $this->render_form_tab_hover_and_border_settings(); ?>
										</div>
										<div class="data-panel data_panel_2">
											<?php $this->render_form_tab_tooltip_settings(); ?>
										</div>
										<div class="data-panel data_panel_3">
											<?php $this->render_form_tab_swatch_type_specific_styling(); ?>
										</div>
									</main>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		
        <?php
	}

	private function render_form_common_attribute_settings( ){

		$this->render_form_tab_main_title('Common Attribute Styling');
		
		?>
		<div style="display: inherit;" class="data-panel-content">
			<table class="thwvs-pp-table">
				<tbody>
					
					<tr>
						<?php
						$this->render_form_field_element($this->settings_fields['design_name'], $this->cell_props_C);
						
						?>							
					</tr>					
					<tr>
						<?php
						$this->render_form_field_element($this->settings_fields['icon_height'], $this->cell_props_C);
						
						?>							
					</tr>
					<tr>
						<?php
						$this->render_form_field_element($this->settings_fields['icon_width'], $this->cell_props_C);
						
						?>							
					</tr>
					<tr>
						<?php
						$this->render_form_field_element($this->settings_fields['icon_shape'], $this->cell_props_C);
						?>							
					</tr>
			
				</tbody>
			</table>
		</div>
		<?php 
	}

	private function render_form_tab_hover_and_border_settings(){

		$this->render_form_tab_main_title('Border Styling');
		
		?>
		<div style="display: inherit;" class="data-panel-content">
			<table class="thwvs-pp-table">
				<tbody>
					<tr>
						<?php
						$this->render_form_field_element($this->settings_fields['icon_border_color'], $this->cell_props_CP);
						
						?>							
					</tr>
					<tr>
						<?php
						$this->render_form_field_element($this->settings_fields['icon_border_color_hover'], $this->cell_props_CP);
						
						?>							
					</tr>
					<tr>
						<?php
						$this->render_form_field_element($this->settings_fields['icon_border_color_selected'], $this->cell_props_CP);
						
						?>							
					</tr>

					<?php $this->render_form_tab_sub_title('Selection Style'); ?>
					<tr>
						<?php
						$this->render_form_field_element($this->settings_fields['common_selection_style'], $this->cell_props_S);
						?>
					</tr>
					<tr class = "tick_prop">
						<?php
						$this->render_form_field_element($this->settings_fields['tick_color'], $this->cell_props_CP);
						?>
					</tr>
					<tr class = "tick_prop">
						<?php
						$this->render_form_field_element($this->settings_fields['tick_size'], $this->cell_props_C);
						?>
					</tr>
					
				</tbody>
			</table>
		</div>
		<?php 
	} 

	private function render_form_tab_tooltip_settings(){

		$this->render_form_tab_main_title('Tooltip Settings');
		
		?>
		<div style="display: inherit;" class="data-panel-content">
			
			<table class="thwvs-pp-table">
				<tbody>

					<tr>
						<?php $this->render_form_field_element($this->settings_fields['tooltip_enable'], $this->cell_props_CB);
						?>							
					</tr>
					
					<tr>
						<?php $this->render_form_field_element($this->settings_fields['tooltip_text_color'], $this->cell_props_CP);?>							
					</tr>
					<tr>
						<?php	$this->render_form_field_element($this->settings_fields['tooltip_text_background_color'], $this->cell_props_CP);
						?>							
					</tr>
				</tbody>
			</table>
		</div>
		<?php 
	}

	private function render_form_tab_swatch_type_specific_styling(){

		$this->render_form_tab_main_title('Button/Label Swatches');
		?>
		<div style="display: inherit;" class="data-panel-content">
			<table class="thwvs-pp-table">
				<tbody>

					<tr>
						<?php $this->render_form_field_element($this->settings_fields['icon_label_height'], $this->cell_props_C); ?>
					</tr>

					<tr>
						<?php $this->render_form_field_element($this->settings_fields['icon_label_width'], $this->cell_props_C); ?>
					</tr>
					
					<tr>
						<?php $this->render_form_field_element($this->settings_fields['label_size'], $this->cell_props_C); ?>
					</tr>

					<tr>
						<?php $this->render_form_field_element($this->settings_fields['label_background_color'], $this->cell_props_CP); ?>
					</tr>

					<tr>
						<?php $this->render_form_field_element($this->settings_fields['label_text_color'], $this->cell_props_CP); ?>
					</tr>

					<?php $this->render_form_tab_sub_title('Selection Style for Button/Label Swatch'); ?>

					<tr>
						<?php
						$this->render_form_field_element($this->settings_fields['label_selection_style'], $this->cell_props_S);
						?>
					</tr>
					<tr class = "label_tick_prop">
						<?php
						$this->render_form_field_element($this->settings_fields['label_tick_color'], $this->cell_props_CP);
						?>
					</tr>
					<tr class = "label_tick_prop">
						<?php
						$this->render_form_field_element($this->settings_fields['label_tick_size'], $this->cell_props_C);
						?>
					</tr>
					<tr class='label_background_prop'>
						<?php
						$this->render_form_field_element($this->settings_fields['label_background_color_hover'], $this->cell_props_CP);
						?>							
					</tr>
					<tr class='label_background_prop'>
						<?php
						$this->render_form_field_element($this->settings_fields['label_text_color_hover'], $this->cell_props_CP);
						?>							
					</tr>
					<tr class='label_background_prop'>
						<?php
						$this->render_form_field_element($this->settings_fields['label_background_color_selection'], $this->cell_props_CP);
						?>							
					</tr>
					<tr class='label_background_prop'>
						<?php
						$this->render_form_field_element($this->settings_fields['label_text_color_selection'], $this->cell_props_CP);
						?>							
					</tr>
					<?php $this->render_form_tab_sub_title('Color/Image Swatches'); ?>
					<tr>
						<?php $this->render_form_field_element($this->settings_fields['enable_swatch_dropdown'], $this->cell_props_CB);
						?>							
					</tr>

		        </tbody>
	    	</table>
		</div>
	    <?php 
	}

}
endif;