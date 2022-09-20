<?php
/**
 *
 * @link       https://themehigh.com
 * @since      2.0.8
 *
 * @package    product-variation-swatches-for-woocommerce
 * @subpackage product-variation-swatches-for-woocommerce/includes
 */

if(!defined('WPINC')){	die; }

if(!class_exists('THWVSF_WC_API_Handler')):

class THWVSF_WC_API_Handler{

	public function __construct() {

		add_filter( 'woocommerce_rest_prepare_product_object', array($this,'rest_prepare_product_swatches'),10,3);
	}

	public function rest_prepare_product_swatches( $response, $object, $request ){

		if ( $object->is_type( 'variable' ) && $object->has_child() ) {

			$attribute_datas                  = $this->get_attribute_swatches($object);
			$response_data                    = $response->get_data();
			$response_data['thwvsf_swatches'] = $attribute_datas;
			$response->set_data( $response_data );
		}

		return $response;
	}

	protected function get_attribute_swatches( $product){

		$attributes = array();
		foreach ( $product->get_attributes() as $attribute ){
			
			$custom_attr_props = '';
			$attribute_name    = isset($attribute['name']) ? $attribute['name'] : '';

			if(! $attribute->is_taxonomy()) {

				$product_id        = $product->get_id();      
				$custom_props      = get_post_meta($product_id,'th_custom_attribute_settings',true);
				$attr_name         = sanitize_title($attribute_name);
				$custom_attr_props = is_array($custom_props) && isset($custom_props[$attr_name]) ? $custom_props[$attr_name] : array();
			}

			$id             = (isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy']) ? wc_attribute_taxonomy_id_by_name( $attribute_name ) : 0 ;
			$type           = $this->get_attribute_type( $id, $attribute, $custom_attr_props);
			$styles         = $this->get_swatches_styles( $id, $attribute, $custom_attr_props);

			$attributes[]   = array(

				'id'        => $id ,
				'name'      => $this->get_attribute_taxonomy_name( $attribute_name, $product ),
				'type'      => $type,
				'variation' => (bool) $attribute['is_variation'],
				'terms'     => $this->get_attribute_terms( $product->get_id(), $attribute, $type, $custom_attr_props),
				'styles'    => $styles,
			);
		}

		return $attributes;

		/*$attributes = array();
		if ( $product->is_type( 'variation' ) ) {

			$_product = wc_get_product( $product->get_parent_id() );
			foreach ( $product->get_variation_attributes() as $attribute_name => $attribute ) {
				$name = str_replace( 'attribute_', '', $attribute_name );

				if ( empty( $attribute ) && '0' !== $attribute ) {
					continue;
				}

				// Taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`.
				if ( 0 === strpos( $attribute_name, 'attribute_pa_' ) ) {
					$option_term  = get_term_by( 'slug', $attribute, $name );
					$attributes[] = array(
						'id'     => wc_attribute_taxonomy_id_by_name( $name ),
						'name'   => $this->get_attribute_taxonomy_name( $name, $_product ),
						'option' => $option_term && ! is_wp_error( $option_term ) ? $option_term->name : $attribute,
					);
				} else {
					$attributes[] = array(
						'id'     => 0,
						'name'   => $this->get_attribute_taxonomy_name( $name, $_product ),
						'option' => $attribute,
					);
				}
			}
		}*/
	}

	protected function get_attribute_taxonomy_name( $slug, $product ) {
		// Format slug so it matches attributes of the product.
		$slug       = wc_attribute_taxonomy_slug( $slug );
		$attributes = $product->get_attributes();
		$attribute  = false;

		// pa_ attributes.
		if ( isset( $attributes[ wc_attribute_taxonomy_name( $slug ) ] ) ) {
			$attribute = $attributes[ wc_attribute_taxonomy_name( $slug ) ];
		} elseif ( isset( $attributes[ $slug ] ) ) {
			$attribute = $attributes[ $slug ];
		}

		if ( ! $attribute ) {
			return $slug;
		}
		// Taxonomy attribute name.
		if ( $attribute->is_taxonomy() ) {
			$taxonomy = $attribute->get_taxonomy_object();
			return $taxonomy->attribute_label;
		}
		// Custom product attribute name.
		return $attribute->get_name();
	}

	protected function get_attribute_type( $id, $attribute, $custom_props = array()){

		$type = '';
		if(isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy']){
			$attr_data = wc_get_attribute( $id );
			$type      = $attr_data->type;

		}else{
			$type     = isset($custom_props['type']) ? $custom_props['type'] : '';
		}

    	return $type;
	}

	protected function get_attribute_terms( $product_id, $attribute, $type, $custom_props = array() ) {

		$term_properties = array(); 

		if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {

			$terms   = wc_get_product_terms( $product_id, $attribute['name'], array('fields' => 'all',) );

			foreach ( $terms as $term) {

				$swatch_value   =  get_term_meta($term->term_id, 'product_'.$attribute['name'], true );
				$swatch_value =	 $this->get_swatch_value($type, $swatch_value, $term->name);
				$s_key        = $type === 'color' || $type === 'label' || $type === 'image' ? $type : '';

				$term_properties[$term->slug] = array(

				    'id'             => $term->term_id,
				    'name'           => $term->name,
				    'slug'           => $term->slug,
				    'swatch '.$s_key => $swatch_value,
				);
			}

		} elseif ( isset( $attribute['value'] ) ) {

			$terms = array_map( 'trim', explode( '|', $attribute['value'] ));
			foreach ( $terms as $term) {

				$term_props   = isset($custom_props[$term]) ? $custom_props[$term] : '' ;

				$swatch_value = isset($term_props['term_value']) ? $term_props['term_value'] : '';
				$swatch_value =	$this->get_swatch_value($type, $swatch_value, $term);
				$s_key        = $type === 'color' || $type === 'label' || $type === 'image' ? $type : '';

				$term_properties[$term] = array(

				    'id'             => 0,
				    'name'           => $term,
				    //'slug'         => sanitize_title($term),
				    'swatch '.$s_key => $swatch_value,
				);
			}
		}

		return $term_properties;
	}

	protected function get_swatch_value( $type, $swatch_value, $term_name){

		switch ($type) {
        	case 'color':
       			$swatch_value = $swatch_value;
    		break;
            case 'image':
                $swatch_value = $swatch_value ? wp_get_attachment_image_src( $swatch_value ) : '';
	    		$swatch_value = $swatch_value ? $swatch_value[0] : '';
            break;
            case 'label' : 
            	$swatch_value = $swatch_value ? $swatch_value : $term_name;
            break;
            default:
                $swatch_value = '';
		}
		return $swatch_value;
	}

	protected function get_swatches_styles( $id, $attribute, $custom_props = array()){

		$design_type = '';

		if(isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy']){
			$design_type = THWVSF_Utils::get_design_swatches_settings($id);
			$design_type = $design_type ? $design_type : 'swatch_design_default';
		}else{
			$design_type = isset($custom_attr_props['design_type']) ? $custom_attr_props['design_type'] : 'swatch_design_default';
		}

		$settings = $design_type ? THWVSF_Utils::get_advanced_swatches_settings($design_type) : array();
		return $settings;
	}
}

endif;