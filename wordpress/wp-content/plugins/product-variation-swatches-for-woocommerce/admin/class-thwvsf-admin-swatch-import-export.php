<?php
/**
 * The swatches import-export functionalities.
 *
 * @link       https://themehigh.com
 * @since      2.1.0
 *
 * @package    product-variation-swatches-for-woocommerce
 * @subpackage product-variation-swatches-for-woocommerce/admin
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWVSF_Admin_Swatch_Import_Export')):

class THWVSF_Admin_Swatch_Import_Export {

	public function __construct() {

		//Export hooks
		add_filter( 'woocommerce_product_export_column_names', array($this,'add_additional_export_column_names'), 10, 2);
		add_filter( 'woocommerce_product_export_row_data', array($this,'prepare_swatches_for_export'), 10, 2);

		//Import Hooks
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'swatch_import_mapping_options'), 10, 2);
		add_filter( 'woocommerce_csv_product_import_mapping_special_columns', array( $this, 'swatch_import_mapping_special_columns' ), 10, 2);
		add_filter( 'woocommerce_product_importer_parsed_data', array( $this, 'swatch_importer_parsed_data'), 10, 2);	
		add_action( 'woocommerce_product_import_inserted_product_object', array( $this, 'swatch_import_inserted_product_object'), 10, 2);
	}

	//Export Functions

	public function add_additional_export_column_names( $column_names, $object){

		$args = array(
			'status'   => array( 'private', 'publish', 'draft', 'future', 'pending' ),
			// 'type'     => 'variable',
			'orderby'  => array(
				'ID' => 'ASC',
			),
			'return'   => 'objects',
			'paginate' => true,

		);

		$products = wc_get_products($args);

		foreach ( $products->products as $product ) {

			$attributes = $product->get_attributes();
		
			if ( count( $attributes ) ) {

				$i = 1;

				foreach ( $attributes as $attribute_name => $attribute ) {
					
					$column_names[ 'attributes:type' . $i ] = sprintf( __( 'Attribute %d type', 'product-variation-swatches-for-woocommerce' ), $i );
				
					$column_names[ 'attributes:value_swatch' . $i ] = sprintf( __( 'Attribute %d swatch(s)', 'product-variation-swatches-for-woocommerce'  ), $i );
					$i++;
				}
			}
		}

		return $column_names;
	}

	public function prepare_swatches_for_export($row, $product ){

		$attributes    = $product->get_attributes();

		if ( count( $attributes ) ) {
			$i = 1;
			foreach ( $attributes as $attribute_name => $attribute ) {
				
				
				if ( is_a( $attribute, 'WC_Product_Attribute' ) ) {
					
					$attr_type = $this->get_attribute_type($attribute, $product);
					
					$row[ 'attributes:type' . $i ] = $attr_type;

					if ( $attribute->is_taxonomy() ) {

						$terms  = $attribute->get_terms();
						
						$row[ 'attributes:value_swatch' . $i ]  = $this->implode_swatch_values($terms, $attr_type, $attribute->get_name());

					}else {

						$product_id          = $product->get_id();
						$local_attr_settings = get_post_meta($product_id,'th_custom_attribute_settings', true);
						$attr_name           = sanitize_title($attribute->get_name());
						$local_attr_swatches = isset($local_attr_settings[$attr_name]) ? $local_attr_settings[$attr_name] : array() ;

						$terms               = $attribute->get_options();
						$row[ 'attributes:value_swatch' . $i ] = $this->implode_custom_attribute_swatch_values($terms, $attr_type, $local_attr_swatches);

					}
						
				}

				$i++;
			}
		}

		return $row;
	}

	public function get_attribute_type( $attribute, $product){

		if ( $attribute->is_taxonomy() ) {

			$attr  = wc_get_attribute( $attribute->get_id() );
			return $attr->type;

		}else{

			$product_id          = $product->get_id();
			$local_attr_settings = get_post_meta($product_id,'th_custom_attribute_settings', true);
			$attr_name           = sanitize_title($attribute->get_name());

			if(is_array($local_attr_settings) && isset($local_attr_settings[$attr_name ])){

				$settings = $local_attr_settings[$attr_name];
				$type     = isset($settings['type']) ? $settings['type'] : '';
				return $type;
			}
			return '';
		}
		
	}

	public function implode_swatch_values($terms, $attr_type, $attr_name){

		$swatch_values = array();

		if($attr_type !== 'select' && $attr_type !== 'radio'){
		
			foreach ( $terms as $term ) {

				$data_val   = get_term_meta( $term->term_id,'product_'.$attr_name, true );
				
				switch ($attr_type) {

					case 'image':

						$image = wp_get_attachment_image_src( $data_val , 'full' );
						$swatch_values[] = $image ? $image[0] : '';
						
					break;
					
					default:

						$swatch_values[] =   $data_val;

					break;
				}
			}
		}

		return  $this->implode_values($swatch_values);
	}

	public function implode_custom_attribute_swatch_values( $terms, $attr_type, $local_attr_swatches){

		$swatch_values       = array();
		
		foreach ( $terms as $term ) {

			$term_swatches = isset( $local_attr_swatches[$term ]) ? $local_attr_swatches[$term] : array();
			$swatch_val    = isset( $term_swatches['term_value']) ? $term_swatches['term_value'] : '';

			if($attr_type !== 'select' && $attr_type !== 'radio'){

				switch ($attr_type) {

					case 'image':
					
						$image = $swatch_val ? wp_get_attachment_image_src( $swatch_val , 'full' ) : '';
						$swatch_values[] = $image ? $image[0] : '';
					
					break;
					default:
						$swatch_values[] = $swatch_val ;
					break;
				}
			}
		}

		return  $this->implode_values($swatch_values);
	}

	public function implode_values( $values ) {
		
		$values_to_implode = array();

		foreach ( $values as $value ) {
			$value               = (string) is_scalar( $value ) ? $value : '';
			$values_to_implode[] = str_replace( ',', '\\,', $value );
		}

		return implode( ', ', $values_to_implode );
	}

	//Import Functions

	public function swatch_import_mapping_options($options, $item ){

		$index = $item;

		if ( preg_match( '/\d+/', $item, $matches ) ) {
			$index = $matches[0];
		}

		$ad_options = array(

			'attribute_swatches'  => array(

				'name'    => __( 'Attribute Swatches', 'product-variation-swatches-for-woocommerce' ),

				'options' => array(

					'attributes:type' . $index                => __( 'Attribute type', 'product-variation-swatches-for-woocommerce' ),
					'attributes:swatch' . $index              => __( 'Attribute swatch(s)', 'product-variation-swatches-for-woocommerce' ),
				),
			),
		);

		$options = array_merge($options, $ad_options);

		return $options;
	}

	public function swatch_import_mapping_special_columns( $special_columns, $raw_headers){

		$ad_columns = array(
							
			__( 'Attribute %d type', 'product-variation-swatches-for-woocommerce' ) => 'attributes:type',
				
			__( 'Attribute %d swatch(s)', 'product-variation-swatches-for-woocommerce' ) => 'attributes:swatch',
							
		);

		$special_columns =  array_merge( $special_columns, $ad_columns);

		return $special_columns;
	}

	public function swatch_importer_parsed_data( $parsed_data, $object){

		$regex_match_data = array( '/attributes:type*/' , '/attributes:swatch*/');

		foreach ( $parsed_data as $index => $data) {

			foreach($regex_match_data as $regex){

				if ( preg_match( $regex, $index)){

					$parsed_data[$index] = $this->parse_comma_field($data);
				}
			}
			
		}

		$parsed_data = $this->expand_data( $parsed_data);
		
		return $parsed_data;
	}

	protected function parse_comma_field( $value ) {

		if ( empty( $value ) && '0' !== $value ) {
			return array();
		}

		$value = $this->unescape_data( $value );
		return array_map( 'wc_clean', $this->explode_values( $value ) );
	}

	protected function unescape_data( $value ) {
		$active_content_triggers = array( "'=", "'+", "'-", "'@" );

		if ( in_array( mb_substr( $value, 0, 2 ), $active_content_triggers, true ) ) {
			$value = mb_substr( $value, 1 );
		}

		return $value;
	}

	protected function explode_values( $value, $separator = ',' ) {

		$value  = str_replace( '\\,', '::separator::', $value );
		$values = explode( $separator, $value );
		$values = array_map( array( $this, 'explode_values_formatter' ) ,$values  );

		return $values;
	}

	protected function explode_values_formatter( $value ) {
		return trim( str_replace( '::separator::', ',', $value ) );
	}

	protected function expand_data( $data ) {
		
		$attributes = array();

		foreach ( $data as $key => $value ) {

			if ( $this->starts_with( $key, 'attributes:type' ) ) {

				if ( ! empty( $value ) ) {
					$attributes[ str_replace( 'attributes:type', '', $key ) ]['type'] = $value;
				}

				unset( $data[ $key ] );

			} elseif ( $this->starts_with( $key, 'attributes:swatch' ) ) {

				$attributes[ str_replace( 'attributes:swatch', '', $key ) ]['swatch'] = $value;
				unset( $data[ $key ] );
			}
		}

		if ( ! empty( $attributes ) ) {
			// Remove empty attributes and clear indexes.
			foreach ( $attributes as $attribute ) {
				if ( empty( $attribute['type'] ) ) {
					continue;
				}

				$data['raw_attributes_swatches'][] = $attribute;
			}
		}
		return $data;
	}

	protected function starts_with( $haystack, $needle ) {
		return substr( $haystack, 0, strlen( $needle ) ) === $needle;
	}

	public function swatch_import_inserted_product_object( $product, $data ){

		if ( isset( $data['raw_attributes_swatches'] ) ) {

			$attributes          = array();
			$existing_attributes = $product->get_attributes();
			$product_id          = $product->get_id();

			$local_attr_settings = array();


			foreach( $data['raw_attributes'] as $position => $attribute){

				$attribute_id = 0;

				if ( ! empty( $attribute['taxonomy'] ) ) {

					$attribute_id = $this->get_global_attribute_id( $attribute['name'] );
				}

				if($attribute_id){

					$this->set_global_attribute_swatches( $attribute_id, $data, $position, $product);

            	}else{

            		$local_attr_settings[sanitize_title($attribute['name'])] = $this->set_custom_attribute_swatches( $attribute, $data['raw_attributes_swatches'], $position, $product);
            	}				
			}

			if(is_array($local_attr_settings) && !empty($local_attr_settings)){

				update_post_meta( $product_id,'th_custom_attribute_settings',$local_attr_settings); 				
			}
		} 
	}

	protected function get_global_attribute_id($raw_name){

		global $wpdb, $wc_product_attributes;

		$attribute_labels = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_name' );
		$attribute_name   = array_search( $raw_name, $attribute_labels, true );

		if ( ! $attribute_name ) {

			$attribute_name = wc_sanitize_taxonomy_name( $raw_name );
		}

		$attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );

		return $attribute_id;

	}

	protected function set_global_attribute_swatches( $attribute_id, $data, $position, $product){

		$attribute_data = wc_get_attribute( $attribute_id );
		$swatches_data  = $data['raw_attributes_swatches'] ? $data['raw_attributes_swatches'] : '';

		$swatch_data  = isset($swatches_data[$position]) ? $swatches_data [$position] : array();

		//$term_list = wp_get_post_terms( $post->ID, 'my_taxonomy', array( 'fields' => 'all' ) );
		//print_r( $term_list );

		$type         = $swatch_data['type'][0];
		$type         = $type ? $type : 'select'; 

		wc_update_attribute( $attribute_id, array(

            'name'         => $attribute_data->name, 
            'slug'         => $attribute_data->slug,
            'type'         => $type,
            'order_by'     => $attribute_data->order_by,
            'has_archives' => $attribute_data->has_archives,
        ) );

	    if($type !== 'select' && $type !== 'radio'){

	        if ( !empty($swatch_data) && isset( $swatch_data['swatch'] ) ) {

				// $terms = get_terms( array(
				// 	'taxonomy'   => $attribute_data->slug,
				// 	'hide_empty' => false,
				// ) );

				$terms    = get_the_terms( $product->get_id(), $attribute_data->slug);
				$swatches = isset($swatch_data['swatch']) ? $swatch_data['swatch'] : array();

				if( is_array($terms )){
					
					foreach( $terms as $index => $term){

						$term_name = $term->name;
						$term_id   = $term->term_id;
						$term_slug = $term->slug;

						$term_data = isset($swatches[$index]) ? $swatches[$index] : '';

						if($term_data){

							switch ($type){

					            case 'image' :
					             	$url = $term_data;
									$image_id = $this->get_attachment_id_from_url( $url, $product->get_id() );
					                update_term_meta( $term_id,'product_'.$attribute_data->slug, $image_id);
					            break;

					            default: 
					            	
					                update_term_meta( $term_id,'product_'.$attribute_data->slug, $term_data);
					            break;
			        		}

			        	}
		        	}
		        }
	        }
	    }
	}

	protected function set_custom_attribute_swatches( $attribute, $data, $position, $product){

		$attributes   = $product->get_attributes();
		$swatch_data  = isset($data[$position]) ? $data[$position] : '' ;

		$attr_name           = sanitize_title($attribute['name']);
		$current_attr        = isset($attributes[$attr_name]) ? $attributes[$attr_name] : array();

		$attr_settings       = array();
		
		if($current_attr && $swatch_data){

			$type                  = $swatch_data['type'][0];
			$attr_settings['type'] = $type ? $type : 'select';
			$terms                 = $current_attr->get_options();
			$attr_name             = $current_attr->get_name();

			if($type !== 'select' && $type !== 'radio'){ 

				$swatches = isset($swatch_data['swatch']) ? $swatch_data['swatch'] : array();

				foreach( $terms as $index => $term){

					$term_settings = array();

					$term_data   = isset($swatches[$index]) ? $swatches[$index] : '';
 
					$term_settings['name'] = $term;

					if($term_data){

						switch ($type){

				            case 'image' :

				            	$url      = $term_data;
				            	$image_id = $this->get_attachment_id_from_url( $url, $product->get_id() );
				            	$term_settings['term_value'] = $image_id;
				            	
				            break;
				            default: 
				                $term_settings['term_value'] = $term_data;
				            break;
		        		}
		        	}

		        	$attr_settings[$term] = $term_settings;
				}
			}	
		}
		return $attr_settings;
	}

	protected function get_attachment_id_from_url( $url, $product_id ) {

		if ( empty( $url ) ) {
			return 0;
		}

		$id         = 0;
		$upload_dir = wp_upload_dir( null, false );
		$base_url   = $upload_dir['baseurl'] . '/';

		// Check first if attachment is inside the WordPress uploads directory, or we're given a filename only.
		if ( false !== strpos( $url, $base_url ) || false === strpos( $url, '://' ) ) {
			// Search for yyyy/mm/slug.extension or slug.extension - remove the base URL.
			$file = str_replace( $base_url, '', $url );
			$args = array(
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'fields'      => 'ids',
				'meta_query'  => array( // @codingStandardsIgnoreLine.
					'relation' => 'OR',
					array(
						'key'     => '_wp_attached_file',
						'value'   => '^' . $file,
						'compare' => 'REGEXP',
					),
					array(
						'key'     => '_wp_attached_file',
						'value'   => '/' . $file,
						'compare' => 'LIKE',
					),
					array(
						'key'     => '_wc_attachment_source',
						'value'   => '/' . $file,
						'compare' => 'LIKE',
					),
				),
			);
		} else {
			// This is an external URL, so compare to source.
			$args = array(
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'fields'      => 'ids',
				'meta_query'  => array( // @codingStandardsIgnoreLine.
					array(
						'value' => $url,
						'key'   => '_wc_attachment_source',
					),
				),
			);
		}

		$ids = get_posts( $args ); // @codingStandardsIgnoreLine.

		if ( $ids ) {
			$id = current( $ids );
		}

		// Upload if attachment does not exists.
		if ( ! $id && stristr( $url, '://' ) ) {
			$upload = wc_rest_upload_image_from_url( $url );

			if ( is_wp_error( $upload ) ) {
				throw new Exception( $upload->get_error_message(), 400 );
			}

			$id = wc_rest_set_uploaded_image_as_attachment( $upload, $product_id );

			if ( ! wp_attachment_is_image( $id ) ) {
				/* translators: %s: image URL */
				throw new Exception( sprintf( __( 'Not able to attach "%s".', 'woocommerce-product-variation-swatches' ), $url ), 400 );
			}

			// Save attachment source for future reference.
			update_post_meta( $id, '_wc_attachment_source', $url );
		}

		if ( ! $id ) {
			/* translators: %s: image URL */
			throw new Exception( sprintf( __( 'Unable to use image "%s".', 'woocommerce-product-variation-swatches' ), $url ), 400 );
		}

		return $id;
	}
	
}

endif;