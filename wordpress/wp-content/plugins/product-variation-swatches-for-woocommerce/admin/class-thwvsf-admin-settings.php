<?php
/**
 * The admin settings page specific functionality of the plugin.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package     product-variation-swatches-for-woocommerce
 * @subpackage  product-variation-swatches-for-woocommerce/admin
 */
if(!defined('WPINC')){ die; }

if(!class_exists('THWVSF_Admin_Settings')):

abstract class THWVSF_Admin_Settings {
	protected $page_id = '';	
	public static $section_id = '';
	
	protected $tabs = '';
	protected $sections = '';
	
	public function __construct($page, $section = '') {
		$this->page_id = $page;
		$this->tabs = array( 

			'global_product_attributes' => __('Product Attributes', 'product-variation-swatches-for-woocommerce'),
			'swatches_design_settings'  => __('Swatches Designs','product-variation-swatches-for-woocommerce'),
			'general_settings'          => __('Global Settings','product-variation-swatches-for-woocommerce'),
			'pro'                       => __('Premium Features','product-variation-swatches-for-woocommerce'), 

		);
	}
	
	public function get_tabs(){
		return $this->tabs;
	}

	public function get_current_tab(){
		return $this->page_id;
	}

	public function render_tabs(){

		echo '<h2 class="settings-heading th-swatches"> Product Variation Swatches </h2>';
		$current_tab = $this->get_current_tab();
		$tabs = $this->get_tabs();

		if(empty($tabs)){
			return;
		}
		?>

		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper thwvs-tab-wrapper">
			<?php 
			foreach( $tabs as $id => $label ){

				$active = ( $current_tab == $id ) ? 'nav-tab-active' : '';
				$label = $label;
				echo '<a class="nav-tab '.$active.'" href="'. $this->get_admin_url($id) .'">'.$label.'</a>';

			} ?>
		</h2>
		<?php	
	}

	public function get_admin_url($tab = false){

		$url = 'edit.php?post_type=product&page=th_product_variation_swatches_for_woocommerce';
		if($tab && !empty($tab)){
			$url .= '&tab='. $tab;
		}
		return admin_url($url);
	}
		
	public function render_form_field_element($field, $atts = array(), $design_id = false,$render_cell = true){
		
		if($field && is_array($field)){
			$args = shortcode_atts( array(
				'label_cell_props' => '',
				'input_cell_props' => '',
				'label_cell_colspan' => '',
				'input_cell_colspan' => '',
			), $atts );
		
			$ftype     = isset($field['type']) ? $field['type'] : 'text';
			$flabel    = isset($field['label']) && !empty($field['label']) ? __($field['label'],'') : '';
			$sub_label = isset($field['sub_label']) && !empty($field['sub_label']) ? __($field['sub_label'],'') : '';
			$tooltip   = isset($field['hint_text']) && !empty($field['hint_text']) ? __($field['hint_text'],'') : '';
			
			$field_html = '';
			
			if($ftype == 'text'){
				$field_html = $this->render_form_field_element_inputtext($field, $atts,$design_id );
				
			}else if($ftype == 'number'){
				$field_html = $this->render_form_field_element_inputnumber($field, $atts,$design_id );
				
			}else if($ftype == 'textarea'){
				$field_html = $this->render_form_field_element_textarea($field, $atts,$design_id );
				   
			}else if($ftype == 'select'){
				$field_html = $this->render_form_field_element_select($field, $atts,$design_id );     
				
			}else if($ftype == 'multiselect'){
				$field_html = $this->render_form_field_element_multiselect($field, $atts,$design_id );     
				
			}else if($ftype == 'colorpicker'){
				$field_html = $this->render_form_field_element_colorpicker($field, $atts,$design_id );              
            
			}else if($ftype == 'checkbox'){
				$field_html = $this->render_form_field_element_checkbox($field, $atts,$design_id  ,$render_cell);   
				//$flabel 	= '&nbsp;';  

			}else if($ftype == 'radio'){
				$field_html = $this->render_form_field_element_radio($field, $atts,$design_id );		
			}
			
			if($render_cell){
				$required_html = isset($field['required']) && $field['required'] ? '<abbr class="required" title="required">*</abbr>' : '';
				
				$label_cell_props = !empty($args['label_cell_props']) ? $args['label_cell_props'] : '';
				$input_cell_props = !empty($args['input_cell_props']) ? $args['input_cell_props'] : '';
				
				?>
				<td <?php echo $label_cell_props ?> >
					<?php echo $flabel; echo $required_html; 
					if($sub_label){
						?>
						<br/><span class="thpladmin-subtitle"><?php echo $sub_label; ?></span>
						<?php
					}
					?>
				</td>
			
				<?php $this->render_form_fragment_tooltip($tooltip); ?>
				<td <?php echo $input_cell_props ?> ><?php echo $field_html; ?></td>
				<?php
			}else{
				echo $field_html;
			}
		}
	}
	
	private function prepare_form_field_props($field, $atts = array(),$design_id=false){
		$field_props = '';
		$args = shortcode_atts( array(
			'input_width' => '',
			'input_name_prefix' => 'i_',
			'input_name_suffix' => '',
		), $atts );
		
		$ftype = isset($field['type']) ? $field['type'] : 'text';
		
		if($ftype == 'multiselect'){
			$args['input_name_suffix'] = $args['input_name_suffix'].'[]';
		}
		
		$fname  = $args['input_name_prefix'].$field['name'].$args['input_name_suffix'].$design_id;
		$fvalue = isset($field['value']) ? $field['value'] : '';
		
		$input_width  = $args['input_width'] ? 'width:'.$args['input_width'].';' : '';
		$fid=isset($field['id']) ? $field['id'] : '';
		$frequired=isset($field['required']) ? 'required' : '';

		$field_props  = 'name="'. $fname .'" value="'. $fvalue .'" style="'. $input_width .'"  '.$frequired.' ';
		$field_props .= ( isset($field['placeholder']) && !empty($field['placeholder']) ) ? ' placeholder="'.$field['placeholder'].'"' : '';
		$field_props .= ( isset($field['onchange']) && !empty($field['onchange']) ) ? ' onchange="'.$field['onchange'].'"' : '';
		if($ftype == 'number'){
			$fmin=isset($field['min']) ? $field['min'] : '';
			$fmax=isset($field['max']) ? $field['max'] : '';

			$field_props .= 'min="'. $fmin .'"max="'.$fmax.'"';
		}
		$field_props .=  isset($field['disabled']) ? 'disabled' : '';
		return $field_props;
	}
	
	private function render_form_field_element_inputtext($field, $atts = array(),$design_id=false){
		$field_html = '';
		if($field && is_array($field)){
			$field_props = $this->prepare_form_field_props($field, $atts,$design_id);
			$readonly = (isset($field['read-only']) && $field['read-only']== 'yes') ? 'readonly':'';
			
			$field_html = '<input type="text" '. $field_props.'  '.$readonly.' />';
		}
		return $field_html;
	}

	private function render_form_field_element_inputnumber($field, $atts = array(),$design_id=false){
		$field_html = '';
		if($field && is_array($field)){
			$field_props = $this->prepare_form_field_props($field, $atts,$design_id);
			$field_html = '<input type="number" '. $field_props .' />';
		}
		return $field_html;
	}
	
	private function render_form_field_element_textarea($field, $atts = array(),$design_id=false){
		$field_html = '';
		if($field && is_array($field)){
			$args = shortcode_atts( array(
				'rows' => '5',
				'cols' => '100',
			), $atts );
		
			$fvalue = isset($field['value']) ? $field['value'] : '';
			$field_props = $this->prepare_form_field_props($field, $atts,$design_id);
			$field_html = '<textarea '. $field_props .' rows="'.$args['rows'].'" cols="'.$args['cols'].'" >'.$fvalue.'</textarea>';
		}
		return $field_html;
	}
	
	private function render_form_field_element_select($field, $atts = array(),$design_id=false){
		$field_html = '';
		if($field && is_array($field)){
			$fvalue = isset($field['value']) ? $field['value'] : '';
			$field_props = $this->prepare_form_field_props($field, $atts,$design_id);
			
			$field_html = '<select '. $field_props .' >';
			foreach($field['options'] as $value => $label){
				$selected = $value === $fvalue ? 'selected' : '';
				$field_html .= '<option value="'. trim($value) .'" '.$selected.'>'. $label .'</option>';
			}
			$field_html .= '</select>';
		}
		return $field_html;
	}
	
	private function render_form_field_element_multiselect($field, $atts = array(),$design_id=false){
		$field_html = '';
		if($field && is_array($field)){
			$field_props = $this->prepare_form_field_props($field, $atts,$design_id);
			
			$field_html = '<select multiple="multiple" '. $field_props .' class="thpladmin-enhanced-multi-select" >';
			foreach($field['options'] as $value => $label){
				//$selected = $value === $fvalue ? 'selected' : '';
				$label = __($label, 'product-variation-swatches-for-woocommerce');
				$field_html .= '<option value="'. trim($value) .'" >'. $label .'</option>';
			}
			$field_html .= '</select>';
		}
		return $field_html;
	}

	private function render_form_field_element_radio($field, $atts = array(),$design_id=false){
		$field_html = '';
		$args = shortcode_atts( array(
			'label_props' => '',
			'cell_props'  => 3,
			'render_input_cell' => false,
			'render_label_cell' => false,
		), $atts );

		$atts = array(
			'input_width' => 'auto',
		);

		if($field && is_array($field)){
			
			$fvalue = isset($field['value']) ? $field['value'] : '';
			
			$field_props = $this->prepare_form_field_props($field, $atts,$design_id);



			$field_html .= '<input type="hidden" name="i_' . $field['name'] . '"  value="'. trim($fvalue) .'" />';			

			foreach($field['options'] as $value => $label){


				$selected = $value === $fvalue ? 'rad-selected' : '';	
				
				$img_layout = '';
				$flabel = isset($label['name']) && !empty($label['name']) ? __($label['name'],'') : '';
				$onchange = ( isset($field['onchange']) && !empty($field['onchange']) ) ? ' onclick="'.$field['onchange'].'"' : '';
				$img_layout = isset($label['layout_image']) && !empty($label['layout_image']) ? $label['layout_image'] : '';				
				$field_html .='<label  '. $args['label_props'] .' '.$onchange.' class=" '.$value.' '.$selected.'" data-value="'. trim($value) .'"> ';

				$field_html .= '<img src= "'. THWVSF_URL . 'admin/assets/images/' . $img_layout.'"/>';
				$field_html .= $flabel.'</label>';
			}			
		}		
		return $field_html;
	}
	
	/*private function render_form_field_element_radio($field, $atts = array()){

		$field_html = '';
		$args = shortcode_atts( array(
			'label_props' => '',
			'cell_props'  => 3,
			'render_input_cell' => false,
			'render_label_cell' => false,
		), $atts );

		$atts = array(
			'input_width' => 'auto',
		);

		if($field && is_array($field)){
			
			$fvalue = isset($field['value']) ? $field['value'] : '';
			$field_html .= '<input type="hidden" name="i_' . $field['name'] . '"  value="'. trim($fvalue) .'" />';
			$field_props = $this->prepare_form_field_props($field, $atts);			

			foreach($field['options'] as $value => $label){
				$checked ='';
				$img_layout = '';

				//$flabel = isset($label) && !empty($label) ? THWMSC_i18n::t($label) : '';
				$flabel = isset($label['name']) && !empty($label['name']) ? __($label['name'],'') : '';
				$onchange = ( isset($field['onchange']) && !empty($field['onchange']) ) ? ' onchange="'.$field['onchange'].'"' : '';
				$img_layout = isset($label['layout_image']) && !empty($label['layout_image']) ? $label['layout_image'] : '';

				$checked = $value === $fvalue ? 'checked' : '';
				$selected = $value === $fvalue ? 'rad-selected' : '';				
				$field_html .='<label for="'. $value .'" '. $args['label_props'] .' class="'.$selected.'" > ';				

				$field_html .= '<input type="radio" name="i_' . $field['name'] . '" id="'. $value . '" value="'. trim($value) .'" ' . $checked . $onchange . '/>';
				//$field_html .= '<span class ="layout-icon ' . $value . '"></span>';
				$field_html .= '<img src= "'. THWVSF_URL . 'admin/assets/images/' . $img_layout.'"/>';
				$field_html .= $flabel.'</label>';
			}			
		}		
		return $field_html;
	}*/
	
	private function render_form_field_element_checkbox($field, $atts = array(), $design_id=false,$render_cell = true){
		$field_html = '';
		if($field && is_array($field)){
			$args = shortcode_atts( array(
				'label_props' => '',
				'cell_props'  => 3,
				'render_input_cell' => false,
			), $atts );
			
			$fid 	= 'i_'. $field['name'].$design_id;
			$flabel = isset($field['label']) && !empty($field['label']) ? __($field['label']) : '';
			$field_props  = $this->prepare_form_field_props($field, $atts,$design_id);
			$field_props .= $field['checked'] ? ' checked' : '';
			$field_html .= '<input type="checkbox" class="thwvs-checkbox" id="'. $fid .'" '. $field_props .' />';
			$field_html .= '<label for="'. $fid .'" '. $args['label_props'] .' ></label>';
		}

		 if(!$render_cell && $args['render_input_cell']){
			return '<td '. $args['cell_props'] .' >'. $field_html .'</td>';
		}else{
			return $field_html;
		}
		return $field_html;
	}
	
	private function render_form_field_element_colorpicker($field, $atts = array(),$design_id=false){
		$backgrnd =  isset($field['value']) ? $field['value'] : '';
		$field_html = '';
		if($field && is_array($field)){
			$field_props = $this->prepare_form_field_props($field, $atts,$design_id);
			
            $field_html  = '<input type="text" '. $field_props .' class="thpladmin-colorpick" />';
            $field_html .= '<span class="thwvs-admin-colorpickpreview thpladmin-colorpickpreview '.$field['name'].'_preview" style="background-color:'.$backgrnd.'"></span>';
		}
		return $field_html;
	}
	
	public function render_form_fragment_tooltip($tooltip = false){
		if($tooltip){

			?>
			<td style="width: 26px; padding:0px;">
				<a href="javascript:void(0)" title="<?php echo $tooltip; ?>" class="thpladmin_tooltip"><img src="<?php echo THWVSF_ASSETS_URL_ADMIN; ?>images/help.png" title=""/></a>
			</td>
			<?php
		}else{
			?>
			<td style="width: 26px; padding:0px;"></td>
			<?php 
		}
	}
	
	public function render_form_fragment_h_separator($atts = array()){
		$args = shortcode_atts( array(
			'colspan' 	   => 6,
			'padding-top'  => '5px',
			'border-style' => 'dashed',
    		'border-width' => '1px',
			'border-color' => '#e6e6e6',
			'content'	   => '',
		), $atts );
		
		$style  = $args['padding-top'] ? 'padding-top:'.$args['padding-top'].';' : '';
		$style .= $args['border-style'] ? ' border-bottom:'.$args['border-width'].' '.$args['border-style'].' '.$args['border-color'].';' : '';
		
		?>
        <tr><td colspan="<?php echo $args['colspan']; ?>" style="<?php echo $style; ?>"><?php echo $args['content']; ?></td></tr>
        <?php
	}
	
	/*private function output_h_separator($show_line = true){
		$style = $show_line ? 'margin: 5px 0; border-bottom: 1px dashed #ccc' : '';
		echo '<tr><td colspan="6" style="'.$style.'">&nbsp;</td></tr>';
	}*/
	
	public function render_field_form_fragment_h_spacing($padding = 5){
		$style = $padding ? 'padding-top:'.$padding.'px;' : '';
		?>
        <tr><td colspan="6" style="<?php echo $style ?>"></td></tr>
        <?php
	}
	
	public function render_form_field_blank($colspan = 3){
		?>
        <td colspan="<?php echo $colspan; ?>">&nbsp;</td>  
        <?php
	}
	
	public function render_form_section_separator($props, $atts=array()){
		?>
		<tr valign="top"><td colspan="<?php echo $props['colspan']; ?>" style="height:10px;"></td></tr>
		<tr valign="top"><td colspan="<?php echo $props['colspan']; ?>" class="thpladmin-form-section-title" ><?php echo $props['title']; ?></td></tr>
		<tr valign="top"><td colspan="<?php echo $props['colspan']; ?>" style="height:0px;"></td></tr>
		<?php
	}

	public function render_form_tab_main_title($title){
		?>
		<main-title classname="main-title">
			<!-- <button class="device-mobile btn--back Button">
				<i class="button-icon button-icon-before i-arrow-back"></i>
			</button>
			<span class="device-mobile main-title-icon text-primary"><i class="i-check drishy"></i><?php //echo $title; ?></span> -->
			<span class="device-desktop"><?php echo $title; ?></span>
		</main-title>
		<?php
	}

	public function render_form_tab_sub_title($title, $props=array()){

		$tooltip   = isset($props['hint_text']) && !empty($props['hint_text']) ? __($props['hint_text'],'') : '';
		?>
		<tr valign="top"><td colspan="3" style="height:10px;"></td></tr>
		<tr valign="top">
			<td colspan="3" class="thpladmin-form-section-title">
				<?php echo $title ; ?>

			</td>
			<!-- <td  class="thpladmin-form-section-title" ></td> -->
		</tr>
		<tr valign="top"><td colspan="3" style="height:0px;"></td></tr>
		<?php
	}
}

endif;