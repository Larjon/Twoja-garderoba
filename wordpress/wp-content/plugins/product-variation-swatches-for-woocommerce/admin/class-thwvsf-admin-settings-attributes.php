<?php
/**
 * The admin general settings page functionality of the plugin.
 *
 * @link       https://themehigh.com
 * @since      2.0.3
 *
 * @package    woocommerce-product-variation-swatches
 * @subpackage woocommerce-product-variation-swatches/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWVSF_Admin_Settings_Attributes')):

class THWVSF_Admin_Settings_Attributes extends THWVSF_Admin_Settings {

	protected static $_instance = null;

	private $cell_props_C   = array();
	private $cell_props_CP  = array();
	private $cell_props_S   = array();
	private $cell_props_CB  = array();
	private $cell_props_CBR = array();
	
	private $section_props = array();
	private $field_props = array();
	private $field_props_display = array();
	
	public function __construct() {
		parent::__construct('global_product_attributes', '');
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

			'label_cell_props' => 'class="titledesc" scope="row" style="width: 35%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
		);

		$this->cell_props_CP = array( 

			'label_cell_props' => 'class="titledesc" scope="row" style="width: 35%;"', 
			'input_cell_props' => 'class="forminp inp-color"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
		);

		$this->cell_props_S = array(

			'label_cell_props' => 'class="titledesc" scope="row" style="width: 35%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
		);

		$this->cell_props_CB = array(
	
			'label_cell_props' => 'class="titledesc" scope="row" style="width: 35%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
			'label_props' => 'class = "thwvsadmin-slider-label" style="margin-right: 52px;"'
		);

		$this->cell_props_CBR = array(
	
			'label_cell_props' => 'class="titledesc" scope="row" style="width: 35%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '260px', 
			'label_cell_th' => true, 
			
		);
		
		$this->field_props = $this->get_field_form_props();

	} 

	public function get_field_form_props(){

        $default_design_types = THWVSF_Admin_Utils::$sample_design_labels;
        $designs              = THWVSF_Admin_Utils::get_design_styles();
        $design_types         = $designs && is_array($designs) ?  $designs : $default_design_types;

        $Swatch_types = array(

          'select' => esc_html__( 'Default/Select', 'woocommerce-product-variation-swatches' ),
          'color'  => esc_html__( 'Color', 'woocommerce-product-variation-swatches' ),
          'image'  => esc_html__( 'Image', 'woocommerce-product-variation-swatches' ),
          'label'  => esc_html__( 'Label/Button', 'woocommerce-product-variation-swatches' ),
          'radio'  => esc_html__( 'Radio', 'woocommerce-product-variation-swatches' ),
          
        );		
		return array(

			'label' 	 => array('type'=>'text', 'name'=>'label', 'label'=>'Attribute Name',
			'read-only'=>'yes'),
			'name' 	 => array('type'=>'text', 'name'=>'name', 'label'=>'Attribute Slug',
			'read-only'=>'yes'),
			'type' => array('name'=>'type','type'=>'select','label'=>'Swatch Type','value'=>'none','options' => $Swatch_types, 'onchange'=>'thwvsfSwatchTypeChangeListner(this)'),
			'swatch_design_style' => array('name'=>'swatch_design_style', 'type'=>'select','label'=> 'Design Type','value'=>'none','options' => $design_types),
		);
	}
	
	public function render_page(){
		$this->render_tabs();
		$this->render_content();
	}

	public function render_content(){
			
		if(isset($_POST['attribute_save_settings'])){
			echo $this->update_attributes();
		}
		if(isset($_POST['attribute_reset_settings'])){

			$this->reset_attribute_settings();
		}

		$attribute_taxonomies = wc_get_attribute_taxonomies();
		$settings = THWVSF_Utils::get_advanced_swatches_settings();

		$design_templates = array();
		if(is_array($settings) && isset($settings['swatch_design_default'])){
			$design_templates = $settings;
		}else{
			$design_templates = THWVSF_Admin_Utils::get_sample_design_templates($settings);
		}
		?>
	    <div class="thwvs-design-templates thwvs-paraent-template">		
	    
	    	<div class="thwvs-template-preview-wrapper"> <?php 

	    		if(is_array($attribute_taxonomies) && !empty($attribute_taxonomies)){
	    			$designs = THWVSF_Utils::get_design_swatches_settings();
	    			$settings =  THWVSF_Utils::get_advanced_swatches_settings();

		    		foreach( $attribute_taxonomies as $attribute ) {

						$attr_id = $attribute->attribute_id;
						$name    = $attribute->attribute_name;
						$type    = $attribute->attribute_type;
						$label   = $attribute->attribute_label;

						$design_type = THWVSF_Admin_Utils::get_swatches_design_by_key($attr_id, $designs);
						$design_type = $design_type ? $design_type : 'swatch_design_default';
						$design_name = THWVSF_Admin_Utils::get_design_styles($design_type, $settings);
						$name_attr   = THWVSF_Admin_Utils::get_design_name_from_sample($design_type);
						$design_name = $design_name ? $design_name : $name_attr;

						$term_props      = $this->get_term_property_set_json($attribute);
						$term_props_json = wp_json_encode($term_props);
						$term_props_json = function_exists( 'wc_esc_json' ) ? wc_esc_json( $term_props_json) : _wp_specialchars( $term_props_json, ENT_QUOTES, 'UTF-8', true );
						?>
		    			<div class="thwvs-template-box thwvs-attr-box search-elements" data-search_name="<?php echo esc_attr($label); ?>">
		    				<div class="thwvs-template-name">
		    						<img class="thwvs-dot-element" src="<?php echo THWVSF_ASSETS_URL_ADMIN.'images/dots.svg'; ?>"/>
		    					<p class="thwvs-label"><?php echo $label; ?></p>

			    				<div class="thwvs-edit-element" data-block-name="<?php echo $name; ?>" data-terms="<?php echo $term_props_json; ?>" onclick = 'thwvsfOpenAttributeForm(this,"<?php echo $attr_id; ?>", "<?php echo $design_type; ?>")'>

			    					<span class="icon icon-edit" > </span>
							
								</div>
								<p class="attr-type-label"> <?php echo esc_html( wc_get_attribute_type_label($type) ); ?></p>
							</div>
							<p class="thwvs-design-name"><?php echo $design_name; ?></p>
		    			</div>
	    				<?php 
					}
				}

	    		?>
	    		<div class="thwvs-template-box thwvs-add-attribute-box">

	    			<a href="<?php echo admin_url('edit.php?post_type=product&page=product_attributes');?>">
		    				
    				<div class="thwvs-template-name thwvs-add-design-name">	
		    			<img class="thwvs-add-element" src="<?php echo THWVSF_ASSETS_URL_ADMIN.'images/add.svg'; ?>"/>
    					<p class="thwvs-add-new-label">Add New Attribute</p>
					</div>
					</a>
    			</div>

	    	</div>
	    </div>
		<div class="thwvs-design-templates thwvs-template-popup">
	    	<?php $this->output_edit_attribute_form_pp(); ?> 
	    </div>
	    <?php
	}

	private function output_edit_attribute_form_pp(){		
		?>
        <div  id = "thwvs_attribute_form_pp" class="popup-wrapper popup-attribute dismiss">

        	<div class="thwvsadmin-popup">
				<div class="popup-container">
					
					<form method="post" id="thwvs_attribute_form" action="">
						<?php wp_nonce_field( 'thwvsf_attribute_settings','thwvsf_security' ); ?>
						<div class="form-pp-content pp-content pp-attr-content">
							<img class="thwvs-close-element pp-close" src="<?php echo THWVSF_ASSETS_URL_ADMIN.'images/popup-arrow.svg'; ?>" onclick="thwvsfCloseDesignPopup(this)"/>
							<p class="attr-label">attribute</p>

							<div class="form-container form-edit-attr">
								
								<input type="hidden" name="f_action" value="" />
								<input type = "hidden" name = "thwvs_design_id" value="" />
								<input type = "hidden" name = "i_attr_id" value = "" />

								<div class="data-panel">
									<?php $this->render_attribute_form_fields_general(); ?>
								</div>
							</div>
						</div> 
						<div class="footer thwvs-attr-actions btn-toolbar">
 							<input type="submit" class="save-btn btn-primary-alt" name="attribute_save_settings" class="button-primary" value="<?php _e('Save', 'woocommerce-product-variation-swatches'); ?>">
					
 							<input type="submit" class="reset-btn btn-primary-alt" name="attribute_reset_settings" class="button-primary" value="<?php _e('Reset', 'woocommerce-product-variation-swatches'); ?>" onclick="return confirm('Are you sure you need to reset the Attribute? Once the attribute is reset, all the changes you have made will be reset to its original state with the default swatch type.');">	

						</div>
					</form>
					
				</div>

			</div>
		</div>
        <?php
	}

	private function render_attribute_form_fields_general(){
		?>
        <table class="thwvs-pp-table" width="100%">

        	<tr>  
            	<?php $this->render_form_field_element($this->field_props['label'], $this->cell_props_C);?>         
            </tr>
                	         
            <tr>  
            	<?php $this->render_form_field_element($this->field_props['name'], $this->cell_props_C);?>         
            </tr>
            <tr>
        		<?php  $this->render_form_field_element($this->field_props['type'], $this->cell_props_C); ?>         
            </tr>
            <tr>
        		<?php $this->render_form_field_element($this->field_props['swatch_design_style'], $this->cell_props_C); ?>
        	</tr>

        </table>  
        <div class="attr-terms" >
			
			<table id="thwvs_attribute_terms_color" class="thwvs_attribute_terms_settings thwvs-pp-table" width="100%" style="margin-top: 20px; display:none;"></table> 
			<table id="thwvs_attribute_terms_image" class="thwvs_attribute_terms_settings thwvs-pp-table" width="100%" style="margin-top: 20px; display:none;"></table>
			<table id="thwvs_attribute_terms_label" class="thwvs_attribute_terms_settings thwvs-pp-table" width="100%" style="margin-top: 20px; display:none;"></table>
		</div>
		<?php
	}

	public function get_term_property_set_json($attribute){
		
		$props_attributes = array();
		$name = $attribute->attribute_name;
		$type = $attribute->attribute_type;
		$attr_label = $attribute->attribute_label;

		$terms = get_terms( array(
    		'taxonomy' => 'pa_'.$name,
    		'hide_empty' => false,
		) );
		$props_terms = array();

		foreach ($terms as $term){
			
			$terms = array();
			 
            $term_field  = get_term_meta($term->term_id,'product_pa_'.$name,true);
            $image       = '';
            $color       = '';
            $label       = '';

            if($type === 'image'){

            	$image  = $term_field ? wp_get_attachment_image_src($term_field) : '';
            	$image  = $image ? $image[0] : '';

            }else if($type === 'color'){

				$color  = $term_field ? $term_field : '';

			}else if($type === 'label'){

				$label = $term_field ? $term_field : '';
			}

            $terms['term_id']    = $term->term_id;
			$terms['term_name']  = $term->name;
			$terms['slug']       = $term->slug;
			$terms['term_value'] = $term_field;
			$terms['image']      = $image;
			$terms['color']      = $color;
			$terms['label']      = esc_attr($label);

			$props_terms[] =  $terms;
		}
		$props_attributes['terms'] = $props_terms;
		$props_attributes['name']  = $name;
		$props_attributes['type']  = $type; 
		$props_attributes['label'] = $attr_label;

		return $props_attributes;
	}


	private function update_attributes() {

		$nonse = isset($_REQUEST['thwvsf_security']) ? $_REQUEST['thwvsf_security'] : false;
		$capability = THWVSF_Utils::thwvsf_capability();
		if(!wp_verify_nonce($nonse, 'thwvsf_attribute_settings') || !current_user_can($capability)){
			die();
		}

		try {

			$id           = isset($_POST['i_attr_id']) ? absint($_POST['i_attr_id']) : '';
			$design_type  = isset( $_POST['i_swatch_design_style'] ) ? wc_clean( wp_unslash( $_POST['i_swatch_design_style'] ) ) : '';
			$attr_type    = isset($_POST['i_type']) ? wc_clean(wp_unslash($_POST['i_type'])) : 'select';

			$result1 = isset($_POST['i_type']) ? $this->save_global_attribute_settings($id, $attr_type): '';
 			$result2 = $this->save_design_change($id, $design_type);
			if($result1 === $id ||  $result2 === true ) {
				echo '<div class="updated"><p>'. __('Your changes were saved.','woocommerce-product-variation-swatches') .'</p></div>';
			}else {
				echo '<div class="error"><p>'. __('Your changes were not saved due to an error (or you made none!).','woocommerce-product-variation-swatches') .'</p></div>';
			}
 			
		} catch (Exception $e) {
			echo '<div class="error"><p>'. __('Your changes were not saved due to an error.','woocommerce-product-variation-swatches') .'</p></div>';
		}
	}

	private function save_global_attribute_settings( $attribute_id, $attr_type){


		$attribute_data = wc_get_attribute( $attribute_id );
        $result = wc_update_attribute( $attribute_id, array(
		'name'         => $attribute_data->name, 
		'slug'         => $attribute_data->slug,
		'type'         => $attr_type, 
		'order_by'     => $attribute_data->order_by,
		'has_archives' => $attribute_data->has_archives,
		) );

        $terms = get_terms( array(
    		'taxonomy'   => $attribute_data->slug,
    		'hide_empty' => false,
		) );

		foreach ( $terms as $term) {
			$term_name = $term->name;
            $term_id   = $term->term_id;
            $term_slug = $term->slug;

            switch ($attr_type){
            	case 'color':  

	                $single_color = isset($_POST[ 'i_single_color_'.$term_slug]) ? wc_clean(wp_unslash($_POST[ 'i_single_color_'.$term_slug])) :'';
	                update_term_meta( $term_id,'product_'.$attribute_data->slug, $single_color);
	                break;

	            case 'image' :
	            	$image = isset($_POST[ 'i_product_image_'.$term_slug]) ? wc_clean(wp_unslash($_POST['i_product_image_'.$term_slug])) :'';
                    update_term_meta( $term_id,'product_'.$attribute_data->slug, $image);
                    break;
                case 'label': 

                	$label = isset($_POST[ 'i_label_'.$term_slug]) ? wc_clean(wp_unslash($_POST['i_label_'.$term_slug])) :'';
                    update_term_meta( $term_id,'product_'.$attribute_data->slug, $label);
                    break;
                default :
                	update_term_meta( $term_id,'product_'.$attribute_data->slug,'');
                	break;
            }

		}
		return $result;
		
	}
	private function save_design_change($id, $design_type ){

		$design_settings = THWVSF_Utils::get_design_swatches_settings();
        $design_settings = is_array($design_settings) ? $design_settings : array();
        $design_settings[$id] = $design_type;
        $result = update_option(THWVSF_Utils::OPTION_KEY_DESIGN_SETTINGS, $design_settings);
        return $result;
	}

	private function reset_attribute_settings(){

		$nonse = isset($_REQUEST['thwvsf_security']) ? $_REQUEST['thwvsf_security'] : false;
		$capability = THWVSF_Utils::thwvsf_capability();
		if(!wp_verify_nonce($nonse, 'thwvsf_attribute_settings') || !current_user_can($capability)){
			die();
		}
		
		$id          = isset($_POST['i_attr_id']) ? absint($_POST['i_attr_id']) : '';
		$design_type = isset( $_POST['i_swatch_design_style'] ) ? wc_clean( wp_unslash( $_POST['i_swatch_design_style'] ) ) : '';
		$design_type = 'swatch_design_default';
		$attr_type   = 'select';
		$result1     = $this->save_design_change($id, $design_type);
		$result2     = isset($_POST['i_type']) ? $this->save_global_attribute_settings($id, $attr_type): '';
		
		if ($result1 === $id || $result2 == true) {
			echo '<div class="updated"><p>'. __('Reset Attribute Settings .','woocommerce-product-variation-swatches') .'</p></div>';
		} else {
			echo '<div class="error"><p>'. __('Your changes were not saved due to an error (or you made none!).','woocommerce-product-variation-swatches') .'</p></div>';
		}
	}

	
}

endif;