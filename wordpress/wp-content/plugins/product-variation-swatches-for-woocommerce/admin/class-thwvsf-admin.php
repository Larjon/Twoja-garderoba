<?php
/**
* The admin-specific functionality of the plugin.
*
* @link       https://themehigh.com
* @since      1.0.0
*
* @package    product-variation-swatches-for-woocommerce
* @subpackage product-variation-swatches-for-woocommerce/admin
*/
if(!defined('WPINC')){  die; }

if(!class_exists('THWVSF_Admin')):

    class THWVSF_Admin {
     private $plugin_name;
     private $version;
     private $taxonomy;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action( 'admin_init', array($this,'define_admin_hooks'));
    }

    public function enqueue_styles_and_scripts($hook) {
        if(strpos($hook, 'product_page_th_product_variation_swatches_for_woocommerce') === false){
            if(!($hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-tags.php' || $hook == 'term.php' || $hook == 'product_page_product_attributes')){
                return;
            }
        }

        $debug_mode = apply_filters('thwvsf_debug_mode', false);
        $suffix = $debug_mode ? '' : '.min';
        
        $this->enqueue_styles($suffix,$hook);
        $this->enqueue_scripts($suffix);
    }

    private function enqueue_styles($suffix,$hook) {

        wp_enqueue_style('woocommerce_admin_styles', THWVSF_WOO_ASSETS_URL.'css/admin.css');
        wp_enqueue_style('thwvsf-admin-style', THWVSF_ASSETS_URL_ADMIN . 'css/thwvsf-admin'.$suffix.'.css', $this->version);
        //wp_enqueue_style('roboto','//fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
    }

    private function enqueue_scripts($suffix) {
        $deps = array('jquery', 'jquery-ui-dialog', 'jquery-tiptip','wc-enhanced-select', 'select2', 'wp-color-picker',);
        wp_enqueue_media();
        wp_enqueue_script( 'thwvsf-admin-script', THWVSF_ASSETS_URL_ADMIN . 'js/thwvsf-admin'.$suffix.'.js', $deps, $this->version, false ); 

        $placeholder_image = THWVSF_ASSETS_URL_ADMIN . '/images/placeholder.svg';
        $thwvsf_var = array(

            'admin_url' => admin_url(),
            'admin_path'=> plugins_url( '/', __FILE__ ),
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
            'ajax_banner_nonce' => wp_create_nonce('thwvsf_upgrade_notice'),
            'placeholder_image' => $placeholder_image,
            'upload_image'      => esc_url(THWVSF_ASSETS_URL_ADMIN .'/images/upload.svg'),
            'remove_image'      =>  esc_url(THWVSF_ASSETS_URL_ADMIN .'/images/remove.svg'),
        );
        
        wp_localize_script('thwvsf-admin-script','thwvsf_var',$thwvsf_var);
    }

    public function admin_menu() {
        $page_title = __('WooCommerce Product Variation Swatches', 'product-variation-swatches-for-woocommerce');
        $menu_title = __('Swatches Options','product-variation-swatches-for-woocommerce');
        $capability = THWVSF_Utils::thwvsf_capability();
        $this->screen_id = add_submenu_page('edit.php?post_type=product', $page_title, $menu_title, $capability, 'th_product_variation_swatches_for_woocommerce', array($this, 'output_settings'));
    }
    
    public function add_screen_id($ids){
        $ids[] = 'woocommerce_page_th_product_variation_swatches_for_woocommerce';
        $ids[] = strtolower( __('WooCommerce', 'woocommerce') ) .'_page_th_product_variation_swatches_for_woocommerce';
        return $ids;
    }

    public function plugin_action_links($links) {
        $premium_link = '<a href="https://www.themehigh.com/product/woocommerce-product-variation-swatches">'. __('Premium plugin') .'</a>';
        $settings_link = '<a href="'.admin_url('edit.php?post_type=product&page=th_product_variation_swatches_for_woocommerce').'">'. __('Settings','product-variation-swatches-for-woocommerce') .'</a>';
        array_unshift($links, $premium_link);
        array_unshift($links, $settings_link);

        if (array_key_exists('deactivate', $links)) {
            $links['deactivate'] = str_replace('<a', '<a class="thwvs-deactivate-link"', $links['deactivate']);
        }

        return $links;

        return $links;
    }

    public function dismiss_thwvsf_review_request_notice(){
        check_ajax_referer( 'thwvsf_notice_security', 'thwvsf_review_nonce' );
        $capability = THWVSF_Utils::thwvsf_capability();
        if(!current_user_can($capability)){
            wp_die(-1);
        }
         
        $now = time();
        update_user_meta( get_current_user_id(), 'thwvsf_review_skipped', true );
        update_user_meta( get_current_user_id(), 'thwvsf_review_skipped_time', $now );
    }

    public function skip_thwvsf_review_request_notice(){
        if(! check_ajax_referer( 'thwvsf_review_request_notice', 'security' )){
            die();
        }
        set_transient('thwvsf_skip_review_request_notice', true, apply_filters('thwvsf_skip_review_request_notice_lifespan',1 * DAY_IN_SECONDS));
    }

    public function plugin_row_meta( $links, $file ) {
        if(THWVSF_BASE_NAME == $file) {
            $doc_link = esc_url('https://www.themehigh.com/help-guides/');
            $support_link = esc_url('https://www.themehigh.com/help-guides/');
                
            $row_meta = array(
                'docs' => '<a href="'.$doc_link.'" target="_blank" aria-label="'.__('View plugin documentation','product-variation-swatches-for-woocommerce').'">'.__('Docs','product-variation-swatches-for-woocommerce').'</a>',
                'support' => '<a href="'.$support_link.'" target="_blank" aria-label="'. __('Visit premium customer support','product-variation-swatches-for-woocommerce' ) .'">'. __('Premium support','product-variation-swatches-for-woocommerce') .'</a>',
            );

            return array_merge( $links, $row_meta );
        }
        return (array) $links;
    }

    public function output_settings(){ 

        $tab  = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'global_product_attributes';

        if($tab == 'general_settings'){

            $general_settings = THWVSF_Admin_Settings_General::instance();   
            $general_settings->render_page(); 
        }else if($tab == 'swatches_design_settings'){
            $design_settings = THWVSF_Admin_Settings_Design::instance();  
            $design_settings->render_page();

        }else if($tab == 'pro'){
            
            $pro_details = THWVSF_Admin_Settings_Pro::instance();  
            $pro_details->render_page();

        }else{

            $attribute_settings = THWVSF_Admin_Settings_Attributes::instance();  
            $attribute_settings->render_page();

        }
    
    }
    
    public function define_admin_hooks(){

        add_action('admin_head', array( $this, 'review_banner_custom_css') );
        add_action( 'admin_init', array( $this, 'wvsf_notice_actions' ), 20 );
        add_action( 'admin_notices' ,array($this,'output_review_request_link'));
        add_action( 'wp_ajax_dismiss_thwvsf_review_request_notice', array($this, 'dismiss_thwvsf_review_request_notice'));

        add_filter( 'product_attributes_type_selector', array( $this,'add_attribute_types' ) );
        //Create select field in attribute to choose design
        add_action( 'woocommerce_after_edit_attribute_fields', array($this,'edit_design_types'));
        add_action( 'woocommerce_after_add_attribute_fields',array($this,'add_design_types') );
        //save design types
        add_action( 'woocommerce_attribute_added',array($this,'save_attribute_type_design'),10,2);
        add_action( 'woocommerce_attribute_updated',array($this,'update_attribute_type_design'),10,3);

        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $this->attr_taxonomies = $attribute_taxonomies;

        foreach ($attribute_taxonomies as $tax) {
            $this->product_attr_type = $tax->attribute_type;

            add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array( $this, 'add_attribute_fields' ) );
            add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', array( $this, 'edit_attribute_fields' ), 10, 2 );
            add_filter( 'manage_edit-pa_'.$tax->attribute_name.'_columns', array( $this, 'add_attribute_column' ));
            add_filter( 'manage_pa_' . $tax->attribute_name . '_custom_column', array( $this, 'add_attribute_column_content' ), 10, 3 );
        }
        add_action( 'created_term', array( $this, 'save_term_meta' ), 10, 3 );
        add_action( 'edit_term', array( $this, 'save_term_meta' ), 10, 3 );

        add_action( 'woocommerce_product_options_attributes',array($this,'thwvsf_popup_fields'));
        add_action( 'woocommerce_product_option_terms',array($this,'thwvsf_product_option_terms'), 20, 2 );

        add_filter('woocommerce_product_data_tabs',array($this,'new_tabs_for_swatches_settings') );
        add_action('woocommerce_product_data_panels',array($this,'output_custom_swatches_settings'));
        add_action('woocommerce_process_product_meta', array( $this, 'save_custom_fields' ), 10, 2);

        add_action('admin_footer-plugins.php', array($this, 'thwvs_deactivation_form'));
        add_action('wp_ajax_thwvs_deactivation_reason', array($this, 'thwvs_deactivation_reason'));
    }

    public function add_attribute_types( $types ) {
        $more_types = array(
          'color' => __( 'Color', 'product-variation-swatches-for-woocommerce' ),
          'image' => __( 'Image', 'product-variation-swatches-for-woocommerce' ),
          'label' => __( 'Button/Label', 'product-variation-swatches-for-woocommerce' ), 
          'radio' => __( 'Radio', 'product-variation-swatches-for-woocommerce' ),
        );

        $types = array_merge( $types, $more_types );
        return $types;
    }

    public function get_design_types(){

        $default_design_types = THWVSF_Admin_Utils::$sample_design_labels;

        $designs = THWVSF_Admin_Utils::get_design_styles();
      
        $design_types = $designs ?  $designs : $default_design_types;
       
        return $design_types;
    }

    //Add design Profiles
    public function add_design_types(){
        $free_design_keys = array('swatch_design_default', 'swatch_design_1', 'swatch_design_2', 'swatch_design_3');
        ?>
            <div class="form-field">
                <h2> <?php esc_html_e( 'Swatches Options', 'product-variation-swatches-for-woocommerce' ); ?> </h2>
            </div>
            <div class="form-field">
                <label for="attribute_design_type"><?php esc_html_e( 'Design Types', 'product-variation-swatches-for-woocommerce' ); ?></label>
                     <select name="attribute_design_type" id="attribute_design_type">

                        <?php foreach ($this->get_design_types() as $key => $value ) : 
                            if (in_array($key, $free_design_keys)){ ?>
                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>

                            <?php } 
                        endforeach; ?>
                                       
                     </select>
                <p class="description"><?php esc_html_e( "Determines how this attribute types are displayed.", 'product-variation-swatches-for-woocommerce' ); ?></p>
            </div>
        <?php
    }

    public function edit_design_types(){

        $free_design_keys = array('swatch_design_default', 'swatch_design_1', 'swatch_design_2', 'swatch_design_3');
        $attribute_id     = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
        $attr_design      = THWVSF_Utils::get_design_swatches_settings($attribute_id);
        ?>
        <tr class="form-field ">
            <th scope="row" valign="top">
                <label for="attribute_design_type"><?php esc_html_e( 'Design Types', 'product-variation-swatches-for-woocommerce' ); ?></label>
            </th>
            <td>
               <select name="attribute_design_type" id="attribute_design_type">
                    <?php foreach ( $this->get_design_types() as $key => $value ) : 
                        if (in_array($key, $free_design_keys)){ ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected($attr_design , $key ); ?>><?php echo esc_html( $value ); ?></option>
                        <?php } 
                    endforeach; ?>
                </select> 
                <p class="description"><?php esc_html_e( "Determines how this attribute's types are displayed.", 'product-variation-swatches-for-woocommerce' ); ?></p>
            </td>
        </tr>
        <?php
    }

    //save design types
    public function save_attribute_type_design($id, $data){
        $design_type          = isset( $_POST['attribute_design_type'] ) ? wc_clean( wp_unslash( $_POST['attribute_design_type'] ) ) : '';
        $design_settings      = THWVSF_Utils::get_design_swatches_settings();
        $design_settings      = is_array($design_settings) ? $design_settings : array();
        $design_settings[$id] = $design_type;

        update_option(THWVSF_Utils::OPTION_KEY_DESIGN_SETTINGS, $design_settings); 
    }

    public function update_attribute_type_design($id, $data, $old_slug){

        $design_type          = isset( $_POST['attribute_design_type'] ) ? wc_clean( wp_unslash( $_POST['attribute_design_type'] ) ) : '';
        $design_settings      = THWVSF_Utils::get_design_swatches_settings();
        $design_settings      = is_array($design_settings) ? $design_settings : array();
        $design_settings[$id] = $design_type;
        update_option(THWVSF_Utils::OPTION_KEY_DESIGN_SETTINGS, $design_settings);
    }

    public function add_attribute_fields($taxonomy){

        $attribute_type = $this->get_attribute_type($taxonomy);
        $this->product_attribute_fields($taxonomy,$attribute_type, 'new', 'add');                       
    }

    public function edit_attribute_fields($term, $taxonomy){
        $attribute_type  = $this->get_attribute_type($taxonomy);
        $term_fields     = array();
        $term_type_field = get_term_meta($term->term_id,'product_'.$taxonomy, true);

        $term_fields = array(
            'term_type_field' => $term_type_field ? $term_type_field : '',
        );
        $this->product_attribute_fields($taxonomy,$attribute_type, $term_fields,'edit');
    }

    public function get_attribute_type($taxonomy){
        foreach ($this->attr_taxonomies as $tax) {
            if('pa_'.$tax->attribute_name == $taxonomy){
                return($tax->attribute_type);
                break;
            }
        }
    }

    public function product_attribute_fields($taxonomy, $type, $value, $form){
        switch ( $type ) {
            case 'color':
                $this->add_color_field($value,$taxonomy);
                break;
            case 'image':
                $this->add_image_field($value,$taxonomy);
                break;
            case 'label' :
                $this->add_label_field($value,$taxonomy);
                break;
            default:
                break;
        }
    }

    private function add_color_field($value, $taxonomy){

        $term_type_field = is_array($value) && $value['term_type_field'] ? $value['term_type_field']:'';
        $label = __( 'Color', 'product-variation-swatches-for-woocommerce' );
        if($value == 'new'){ 
            ?>  
            <div class="thwvsf-types gbl-attr-color gbl-attr-terms gbl-attr-terms-new">
                <label><?php echo esc_html($label); ?></label>
                <div class="thwvsf_settings_fields_form thwvs-col-div">
                    <span class="thpladmin-colorpickpreview color_preview"></span>
                    <input type="text" name= "<?php echo'product_'.esc_attr($taxonomy) ; ?>" class="thpladmin-colorpick"/>
                </div> 
            </div>
            <?php

        } else {
            ?>
            <tr class="gbl-attr-terms gbl-attr-terms-edit" > 
                <th><?php echo esc_html($label); ?></th>
                <td>
                    <div class="thwvsf_settings_fields_form thwvs-col-div">
                        <span class="thpladmin-colorpickpreview color_preview" style="background:<?php echo esc_attr($term_type_field) ?>;"></span>
                        <input type="text"  name= "<?php echo'product_'.esc_attr($taxonomy ); ?>" class="thpladmin-colorpick" value="<?php echo esc_attr($term_type_field) ?>"/>
                    </div>         
                </td>
            </tr> 
            <?Php
        }
    }

    private function add_image_field($value, $taxonomy){
        $image = is_array($value) && $value['term_type_field'] ? wp_get_attachment_image_src( $value['term_type_field'] ) : '';
        $image = $image ? $image[0] : THWVSF_ASSETS_URL_ADMIN . '/images/placeholder.svg';
        $label = __( 'Image', 'product-variation-swatches-for-woocommerce' );

        if($value == 'new'){ 
            ?>
            <div class="thwvsf-types gbl-attr-img gbl-attr-terms gbl-attr-terms-new">
                <div class='thwvsf-upload-image'>
                    <label><?php echo esc_html($label); ?></label>
                    <div class="tawcvs-term-image-thumbnail">
                        <img class="i_index_media_img" src="<?php echo ( esc_url( $image )); ?>" width="50px" height="50px" alt="term-image"/>  <?php  ?>
                    </div>
                    <div style="line-height:60px;">
                        <input type="hidden" class="i_index_media" name="product_<?php echo esc_attr($taxonomy) ?>" value="">
           
                        <button type="button" class="thwvsf-upload-image-button button " onclick="thwvsf_upload_icon_image(this,event)">
                            <img class="thwvsf-upload-button" src="<?php echo esc_url(THWVSF_ASSETS_URL_ADMIN .'/images/upload.svg') ?>" alt="upload-button">
                            <?php // esc_html_e( 'Upload image', 'thwcvs' ); ?>
                        </button>

                        <button type="button" style="display:none" class="thwvsf_remove_image_button button " onclick="thwvsf_remove_icon_image(this,event)">
                            <img class="thwvsf-remove-button" src="<?php echo esc_url(THWVSF_ASSETS_URL_ADMIN .'/images/remove.svg')?>" alt="remove-button">
                        </button>
                    </div>
                </div>
            </div>
            <?php 

        }else{
            ?>
            <tr class="form-field gbl-attr-img gbl-attr-terms gbl-attr-terms-edit">
                <th><?php echo esc_html($label); ?></th>
                <td>
                    <div class = 'thwvsf-upload-image'>
                        <div class="tawcvs-term-image-thumbnail">
                            <img  class="i_index_media_img" src="<?php echo ( esc_url( $image )); ?>" width="50px" height="50px" alt="term-image"/>  <?php  ?>
                        </div>
                        <div style="line-height:60px;">
                            <input type="hidden" class="i_index_media"  name= "product_<?php echo esc_attr($taxonomy) ?>" value="">
               
                            <button type="button" class="thwvsf-upload-image-button  button" onclick="thwvsf_upload_icon_image(this,event)">
                                <img class="thwvsf-upload-button" src="<?php echo esc_url(THWVSF_ASSETS_URL_ADMIN .'/images/upload.svg') ?>" alt="upload-button">
                                <?php // esc_html_e( 'Upload image', 'thwcvs' ); ?>
                            </button>

                            <button type="button" style="<?php echo (is_array($value) && $value['term_type_field']  ? '' :'display:none'); ?> "  class="thwvsf_remove_image_button button " onclick="thwvsf_remove_icon_image(this,event)">
                                <img class="thwvsf-remove-button" src="<?php echo esc_url(THWVSF_ASSETS_URL_ADMIN .'/images/remove.svg')?>" alt="remove-button">
                            </button>
                        </div>
                    </div>
                </td>
            </tr> 
            <?Php
        }   
    }

    public function add_label_field($value, $taxonomy){  

        $label = __( 'Label', 'product-variation-swatches-for-woocommerce' );
        if($value == 'new'){
            ?>
            <div class="thwvsf-types gbl-attr-label gbl-attr-terms gbl-attr-terms-new">
                <label><?php echo esc_html($label); ?></label> 
                <input type="text" class="i_label" name="product_<?php echo esc_attr($taxonomy) ?>" value="" />
            </div>
            <?php
        }else{
            ?>
            <tr class="form-field gbl-attr-label gbl-attr-terms gbl-attr-terms-edit" > 
                <th><?php echo  esc_html($label); ?></th>
                <td>
                    <input type="text" class="i_label" name="product_<?php echo esc_attr($taxonomy) ?>" value="<?php echo esc_attr($value['term_type_field']) ?>" />
                </td>
            </tr> 
            <?Php
        } 
    }

    public function save_term_meta($term_id, $tt_id, $taxonomy){
        if( isset($_POST['product_'.$taxonomy] )  && !empty($_POST['product_'.$taxonomy] ) ){
            update_term_meta( $term_id,'product_'.$taxonomy, wc_clean(wp_unslash($_POST['product_'.$taxonomy])));
        }   
    }

    public function add_attribute_column($columns){
        $new_columns = array();

        if ( isset( $columns['cb'] ) ) {
            $new_columns['cb'] = $columns['cb'];
            unset( $columns['cb'] );
        }

        $new_columns['thumb'] = __( '', 'woocommerce' );

        $columns = array_merge( $new_columns, $columns );
       
        return $columns;
    }

    public function add_attribute_column_content($columns, $column, $term_id){
        $taxonomy = $_REQUEST['taxonomy'];
        $attr_type = $this->get_attribute_type($_REQUEST['taxonomy']);

        $value = get_term_meta( $term_id,'product_'.$taxonomy,true);

        switch ( $attr_type) {
            case 'color':
                printf( '<span class="th-term-color-preview" style="background-color:%s;"></span>', esc_attr( $value ) );
                break;

            case 'image':
                $image = $value ? wp_get_attachment_image_src( $value ) : '';
                $image = $image ? $image[0] : THWVSF_URL . 'admin/assets/images/placeholder.png';
                printf( '<img class="swatch-preview swatch-image" src="%s" width="44px" height="44px" alt="preview-image">', esc_url( $image ) );
                break;

            case 'label':
                printf( '<div class="swatch-preview swatch-label">%s</div>', esc_html( $value ) );
                break;
        }
    }

    public function get_attribute_by_taxonomy($taxonomy){

        global $wpdb;
        $attr = substr( $taxonomy, 3 );
        $attr = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attr'" );
    }

    public function thwvsf_product_option_terms($attribute_taxonomy, $i ) {

        if ( 'select' !== $attribute_taxonomy->attribute_type ) {
            global $post, $thepostid, $product_object;
            $taxonomy = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
            
            $product_id = $thepostid;
            if ( is_null( $thepostid ) && isset( $_POST[ 'post_id' ] ) ) {
                $product_id = absint( $_POST[ 'post_id' ] );
            }

            ?>
            <select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'woocommerce' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr( $i ); ?>][]">
            <?php
                $args      = array(
                    'orderby'    => 'name',
                    'hide_empty' => 0,
                );
            
                $all_terms = get_terms( $taxonomy, apply_filters( 'woocommerce_product_attribute_terms', $args ) );
                    if ( $all_terms ) :
                        $options = array();
                        foreach ($all_terms as $key ) {
                            $options[] = $key->term_id;
                        }

                        foreach ( $all_terms as $term ) :
                        
                            $options = ! empty( $options ) ? $options : array();

                            echo '<option value="' . esc_attr( $term->term_id ) . '" ' . wc_selected( has_term( absint( $term->term_id ), $taxonomy, $product_id ), true, false ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
                        endforeach;
                    endif;
                ?>
            </select>
           
            <button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></button>
            <button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></button>
            
            <?php
             $taxonomy = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
             $attr_type = $attribute_taxonomy->attribute_type;

            if ( (  $attribute_taxonomy->attribute_type == 'label' || $attribute_taxonomy->attribute_type == 'image' || $attribute_taxonomy->attribute_type == 'color')){ ?>
                <button class="button fr plus thwvsf_add_new_attribute"  data-attr_taxonomy="<?php echo esc_attr($taxonomy); ?>"  data-attr_type="<?php echo esc_attr($attr_type )?>"  data-dialog_title="<?php printf( esc_html__( 'Add new %s', '' ), esc_attr($attribute_taxonomy->attribute_label ) ) ?>">  <?php esc_html_e( 'Add new', '' ); ?>  </button> 

             <?php  

            }else{?>
                <button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'woocommerce' ); ?></button> <?php
            }
        }
    }

    public function new_tabs_for_swatches_settings($tabs){
        $tabs['thwvs_swatches_settings']     = array(
            'label'    => __( 'Swatches Settings', 'product-variation-swatches-for-woocommerce' ),
            'target'   => 'thwvs-product-attribute-settings',
            'class'    => array('variations_tab', 'show_if_variable', ),
            'priority' => 65,
        );
        return $tabs;
    }

    public function output_custom_swatches_settings(){
        
        global $post, $thepostid, $product_object,$wc_product_attributes;

        $saved_settings = get_post_meta($thepostid,'th_custom_attribute_settings', true);

        $type_options = array(

            'select' =>  __('Select', 'product-variation-swatches-for-woocommerce' ), 
            'color'  =>  __('Color', 'product-variation-swatches-for-woocommerce' ),
            'label'  =>  __('Button/Label', 'product-variation-swatches-for-woocommerce' ),
            'image'  =>  __('Image' , 'product-variation-swatches-for-woocommerce' ),
            'radio'  =>  __('Radio', 'product-variation-swatches-for-woocommerce')
        );

        $default_design_types = THWVSF_Admin_Utils::$sample_design_labels;
        $designs = THWVSF_Admin_Utils::get_design_styles();
        $design_types = $designs ?  $designs : $default_design_types;

        ?>
        <div id="thwvs-product-attribute-settings" class="panel wc-metaboxes-wrapper hidden">
            <div id="custom_variations_inner">
                <h2><?php esc_html_e( 'Custom Attribute Settings', 'product-variation-swatches-for-woocommerce' ); ?></h2>
                
                <?php 
                $attributes = $product_object->get_attributes();
                $i = -1;
                $has_custom_attribute = false;
                
                foreach ($attributes as $attribute){ 
                    $attribute_name = sanitize_title($attribute->get_name());
                    $type = '';
                    
                    $i++;
                    if ($attribute->is_taxonomy() == false){
                        $has_custom_attribute = true;
                        ?>
                    <div data-taxonomy="<?php echo esc_attr( $attribute->get_taxonomy() ); ?>" class="woocommerce_attribute wc-metabox closed" rel="<?php echo esc_attr( $attribute->get_position() ); ?>">
               
                        <h3>
                            <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'woocommerce' ); ?>"></div>
                            <strong class="attribute_name"><?php echo wc_attribute_label($attribute_name); ?></strong>
                        </h3>
                        <div class="thwvsf_custom_attribute wc-metabox-content  <?php echo 'thwvs-'.esc_attr($attribute_name); ?> hidden">
                            <table cellpadding="0" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            
                                            <p class="form-row form-row-full ">
                                                <label for="custom_attribute_type"><?php esc_html_e('Swatch Type','product-variation-swatches-for-woocommerce'); ?></label>
                                                <span class="woocommerce-help-tip" data-tip=" Determines how this custom attribute's values are displayed">
                                                </span>
                                                   <!--  <?php //echo wc_help_tip(" Determines how this custom attributes are displayed"); // WPCS: XSS ok. ?> -->
                    
                                                <select   name="<?php echo ('th_attribute_type_'.esc_attr($attribute_name)); ?>" class="select short th-attr-select" value = '' onchange="thwvsf_change_term_type(this,event)">
                                                    <?php 
                                                    $type = $this->get_custom_fields_settings($thepostid,$attribute_name,'type');
                                                   
                                                    foreach ($type_options as $key => $value) { 
                                                        $default = (isset($type) &&  $type == $key) ? 'selected' : '';
                                                        ?>
                                                        <option value="<?php echo esc_attr($key); ?>" <?php echo $default ?> > <?php echo esc_html($value); ?> </option>
                                                    <?php
                                                    }?>
                                                </select>
                                             
                                            </p>
                                        </td>
                                        
                                    </tr> 
                                    <tr>
                                        <td colspan="2">
                                            
                                            <p class="form-row form-row-full ">
                                                <label for="custom_attribute_type"><?php esc_html_e('Swatch Design Type','product-variation-swatches-for-woocommerce'); ?> </label>
                                                <span class="woocommerce-help-tip" data-tip=" Determines how this custom attribute types are displayed">
                                                   
                                                </span>   
                                                <select   name="<?php echo esc_attr('th_attribute_design_type_'. $attribute_name); ?>" class="select short th-attr-select" value = ''>
                                                    <?php 
                                                    $design_type = $this->get_custom_fields_settings($thepostid,$attribute_name,'design_type');
                                                    //$design_type = '';
                                                    foreach ($design_types as $key => $value) { 

                                                        $default = (isset($design_type) &&  $design_type == $key) ? 'selected' : '';
                                                        ?>
                                                        <option value="<?php echo esc_attr($key); ?>" <?php echo $default ?> > <?php echo esc_html($value); ?> </option>
                                                    <?php
                                                    }?>
                                                </select>
                                             
                                            </p>
                                        </td>
                                        
                                    </tr> 
                                    <tr>
                                        <th></th>
                                        
                                    </tr>
                                    
                                        <tr>
                                       <td>
                                         
                                         <?php  $this->custom_attribute_settings_field($attribute,$thepostid); ?>
                                       </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                     </div>
                    <?php }
                }

                if(!$has_custom_attribute){
                    ?>
                    <div class="inline notice woocommerce-message">

                        <p><?php esc_html_e('No custom attributes added yet.','woocommerce-product-variation-swatches');
                       esc_html_e(' You can add custom attributes from the', 'woocommerce-product-variation-swatches'); ?> <a onclick="thwvsfTriggerAttributeTab(this)" href="#woocommerce-product-data"><?php  esc_html_e(' Attributes','woocommerce-product-variation-swatches'); ?> </a> <?php esc_html_e('tab','woocommerce-product-variation-swatches'); ?></p>
                    </div>
                   <?php
                }
                ?>

            </div>
        </div> <?php
    }

    public function custom_attribute_settings_field($attribute, $post_id){

        $attribute_name = sanitize_title($attribute->get_name());
        $type = $this->get_custom_fields_settings($post_id,$attribute_name,'type');        
        $this->output_field_label($type,$attribute,$post_id);
        $this->output_field_image($type,$attribute,$post_id);
        $this->output_field_color($type,$attribute,$post_id);
    }

    public function output_field_label($type, $attribute, $post_id){
        $attribute_name = sanitize_title($attribute->get_name());
        $display_status = $type == 'label' ?'display: table': 'display: none' ;
        ?>
        <table class="thwvsf-custom-table thwvsf-custom-table-label" style="<?php echo $display_status ; ?>">
            <?php
            $i= 0;
            foreach ($attribute->get_options() as $term) {
                $css = $i==0 ? 'display:table-row-group' :'';
                $open = $i==0 ? 'open' :'';
                ?>
                <tr class="thwvsf-term-name">
                    <td colspan="2">
                        <h3 class="thwvsf-local-head <?php echo $open;?>" data-type="<?php echo esc_attr($type); ?>" data-term_name="<?php echo  esc_attr($term); ?>" onclick="thwvsf_open_body(this,event)"><?php echo esc_html($term); ?></h3>
                        <table class="thwvsf-local-body-table">
                            <tbody class="thwvsf-local-body thwvsf-local-body-<?php echo esc_attr($term); ?>" style="<?php echo esc_attr($css); ?>">
                                <tr> 
                                    <td width="30%"><?php _e('Term Name', 'product-variation-swatches-for-woocommerce') ?></td>
                                    <td width="70%"><?php echo esc_html($term); ?></td>
                                </tr>
                                <tr class="form-field"> 
                                    <td><?php esc_html_e('Label Text', 'product-variation-swatches-for-woocommerce') ?></td>
                                    <td>
                                        <?php $term_field = $type == 'label' ? $this->get_custom_fields_settings($post_id,$attribute_name,$term,'term_value') : ''; 
                                            $term_field = ($term_field) ? $term_field : '';
                                        ?>
                                        <input type="text" class="i_label" name="<?php echo esc_attr(sanitize_title('label_'.$attribute_name.'_term_'.$term)); ?>" style="width:275px;" value="<?php echo esc_attr($term_field); ?>">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                
                <?php 
                $i++;
            }
            ?>
        </table>
        <?php
    }

    public function output_field_image($type, $attribute, $post_id){
        $attribute_name = sanitize_title($attribute->get_name());
        $display_status = $type == 'image' ?'display:table': 'display: none' ;
        ?>
        <table class="thwvsf-custom-table thwvsf-custom-table-image" style="<?php echo esc_attr($display_status); ?>">
        <?php
            $i = 0;
            foreach ($attribute->get_options() as $term) {
                $css = $i==0 ? 'display:table-row-group' :'';
                $open = $i==0 ? 'open' :'';
                ?>
                <tr class="thwvsf-term-name">
                    <td colspan="2">
                        <h3 class="thwvsf-local-head <?php echo $open;?>" data-term_name="<?php echo $term; ?>" onclick="thwvsf_open_body(this,event)"><?php echo esc_html($term); ?></h3>
                        <table class="thwvsf-local-body-table">
                            <tbody class="thwvsf-local-body thwvsf-local-body-<?php echo esc_attr($term); ?>" style="<?php echo $css; ?>">
                                <tr> 
                                    <td width="30%">Term Name</td>
                                    <td width="70%"><?php echo $term; ?></td>
                                </tr>
                                <tr class="form-field"> <td><?php _e('Term Image', 'product-variation-swatches-for-woocommerce') ?></td>
                                    <td>
                                        <?php $term_field = $this->get_custom_fields_settings($post_id,$attribute_name,$term,'term_value'); 

                                            $term_field = ($term_field) ? $term_field : '';

                                            $image =  $type == 'image' ?  $this->get_custom_fields_settings($post_id,$attribute_name,$term,'term_value') : ''; 
                                            $image = ($image) ? wp_get_attachment_image_src($image) : ''; 
                                            $remove_img = ($image)  ? 'display:inline' :'display:none';
                                            // $image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
                                            $image = $image ? $image[0] : THWVSF_ASSETS_URL_ADMIN . '/images/placeholder.svg';
                                        ?>

                                        <div class = 'thwvsf-upload-image'>
                                    
                                            <div class="tawcvs-term-image-thumbnail" style="float:left;margin-right:10px;">
                                                <img  class="i_index_media_img" src="<?php echo ( esc_url( $image )); ?>" width="60px" height="60px" alt="term-image"/>  <?php  ?>
                                            </div>

                                            <div style="line-height:30px;">
                                                <input type="hidden" class="i_index_media"  name= "<?php echo esc_attr(sanitize_title('image_'.$attribute_name.'_term_'.$term)); ?>" value="<?php echo $term_field; ?>">
                                   
                                                <button type="button" class="thwvsf-upload-image-button button " onclick="thwvsf_upload_icon_image(this,event)">
                                                    <img class="thwvsf-upload-button" src="<?php echo ( esc_url(THWVSF_ASSETS_URL_ADMIN .'/images/upload.svg')) ?>" alt="upload-button">
                                                </button>
                                                <button type="button" style="<?php echo $remove_img; ?>" class="thwvsf_remove_image_button button " onclick="thwvsf_remove_icon_image(this,event)">
                                                    <img class="thwvsf-remove-button" src="<?php echo ( esc_url(THWVSF_ASSETS_URL_ADMIN .'/images/remove.svg'))?>" alt="remove-button">
                                                </button> 
                                                
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                
                <?php
                $i++;
            }?>    
        </table>
        <?php
    }

    public function output_field_color($type, $attribute, $post_id){

        $attribute_name = sanitize_title($attribute->get_name());
        $display_status = $type == 'color' ?'display: table': 'display: none' ;
        ?>
        <table class="thwvsf-custom-table thwvsf-custom-table-color" style="<?php echo $display_status; ?>">
            <?php
            $i = 0;
            foreach ($attribute->get_options() as $term) {
                $css = $i==0 ? 'display:table-row-group' :'';
                $open = $i==0 ? 'open' :'';
                ?>
                <tr class="thwvsf-term-name">
                    <td colspan="2">
                        <h3 class="thwvsf-local-head <?php echo $open;?>" data-term_name="<?php echo esc_attr($term); ?>" onclick="thwvsf_open_body(this,event)"><?php echo esc_html($term); ?></h3>
                        <table class="thwvsf-local-body-table">
                            <tbody class="thwvsf-local-body thwvsf-local-body-<?php echo $term; ?>" style="<?php echo $css; ?>">
                                <tr>
                                    <td width="30%"><?php esc_html_e('Term Name', 'product-variation-swatches-for-woocommerce') ?></td>
                                    <td width="70%"><?php echo esc_html($term); ?></td>
                                </tr>
                                <?php 
                                $color_type = $this->get_custom_fields_settings($post_id,$attribute_name,$term,'color_type');
                                $color_type = $color_type ? $color_type : '';
                                ?>

                                <tr>
                                    <td>Term Color</td>
                                    <td class = "th-custom-attr-color-td"><?php
                                        $term_field = $type == 'color' ? $this->get_custom_fields_settings($post_id,$attribute_name,$term,'term_value') : ''; 
                                        $term_field = ($term_field) ? $term_field : '' ; ?>

                                        <div class="thwvsf_settings_fields_form thwvs-col-div" style="margin-bottom: 5px">
                                            <span class="thpladmin-colorpickpreview color_preview" style="background-color: <?php echo $term_field; ?> ;"></span>
                                            <input type="text"   name= "<?php echo esc_attr(sanitize_title('color_'.$attribute_name.'_term_'.$term)); ?>" class="thpladmin-colorpick" value="<?php echo esc_attr($term_field); ?>" style="width:250px;"/>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php
                $i++;
            } ?>
        </table><?php
    }

    public function get_custom_fields_settings($post_id, $attribute=false, $term=false, $term_key=false){

        $saved_settings = get_post_meta($post_id,'th_custom_attribute_settings', true);

        if(is_array($saved_settings)){
            if($attribute){
                if(isset($saved_settings[$attribute])){
                    $attr_settings = $saved_settings[$attribute];

                    if(is_array($attr_settings) && $term){
                        if($term === 'type' || $term ==='tooltip_type' || $term ==='radio-type' ||  $term ==='design_type'){
                            $term_types =  (isset($attr_settings[$term])) ?   $attr_settings[$term] :  false;
                            return $term_types; 
                        }else{
                            $term_settings = isset($attr_settings[$term]) ? $attr_settings[$term] : '';
                            if(is_array($term_settings) && $term_key){
                                $settings_value = isset($term_settings[$term_key]) ? $term_settings[$term_key]: '';
                                return  $settings_value;
                            }else{
                                return false;
                            }
                            return $term_settings;
                        }                       
                    }
                    return $attr_settings;
                }
                return false;
            }
            return $saved_settings;
        }else{
            return false;
        }
    }
 
    public function thwvsf_popup_fields(){
      
        $image = THWVSF_ASSETS_URL_ADMIN . '/images/placeholder.svg';
        ?>
        <div class="thwvsf-attribte-dialog thwvsf-attribte-dialog-color " style = "display:none;">
            <table>
     
                <tr>
                    <td><span><?php _e('Name:', 'product-variation-swatches-for-woocommerce');?></span></td>
                    <td><input type="text"  name= "attribute_name" class="thwvsf-class" value="" style="width:225px; height:40px;"/></td>
                </tr>
                <tr>
                    <td><span><?php _e('Color:', 'product-variation-swatches-for-woocommerce');?></span></td>
                    <td class="locl-attr-terms">
                        <div class="thwvsf_settings_fields_form thwvs-col-div">
                            <span class="thpladmin-colorpickpreview color_preview"></span>
                            <input type="text" name= "attribute_type" class="thpladmin-colorpick" style="width:225px; height:40px;"/>
                        </div> 
                    </td>
                </tr>
            </table>
        </div>

        <div class="thwvsf-attribte-dialog thwvsf-attribte-dialog-image" style = "display:none;">
            <table>
                <tr>
                    <td> <span><?php esc_html_e('Name:', 'product-variation-swatches-for-woocommerce');?></span></td>
                    <td><input type="text" name= "attribute_name" class="thwvsf-class" value="" style="width:216px"/></td>
                </tr>
                <tr valign="top">
                    <td><span><?php esc_html_e('Image:', 'product-variation-swatches-for-woocommerce');?></span> </td>
                    <td>
                        <div class = 'thwvsf-upload-image'>
                            <div class="thwvsf-term-image-thumbnail" style="float:left; margin-right:10px;">
                                <img  class="i_index_media_img" src="<?php echo ( esc_url( $image )); ?>" width="60px" height="60px" alt="term-images"/>
                            </div>

                            <input type="hidden" class="i_index_media thwvsf-class"  name= "attribute_type" value="">
                            <button type="button" class="thwvsf-upload-image-button button " onclick="thwvsf_upload_icon_image(this,event)">
                                <img class="thwvsf-upload-button" src="<?php echo ( esc_url(THWVSF_ASSETS_URL_ADMIN .'/images/upload.svg')) ?>" alt="upload-button">
                            </button>
                            <button type="button" style="display:none" class="thwvsf_remove_image_button button " onclick="thwvsf_remove_icon_image(this,event)">
                                <img class="thwvsf-remove-button" src="<?php echo ( esc_url( THWVSF_ASSETS_URL_ADMIN .'/images/remove.svg'))?>" alt="remove-button">
                            </button> 
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="thwvsf-attribte-dialog thwvsf-attribte-dialog-label" style = "display:none;">
            <table>
                <tr>
                    <td><span><?php  esc_html_e('Name:', 'product-variation-swatches-for-woocommerce');?></span></td>
                    <td><input type="text" name= "attribute_name" class="thwvsf-class" value="" /></td>
                </tr>
                <tr>
                    <td><span><?php  esc_html_e('Label:', 'product-variation-swatches-for-woocommerce');?></span> </td>
                    <td>
                        <input type="text" name="attribute_type" class="thwvsf-class" value="" />
                    </td>
                </tr>    
            </table>
        </div>

        <?php
    }

    public function save_custom_fields($post_id, $post){
        
        $product = wc_get_product( $post_id );
        $local_attr_settings = array();

        foreach ($product->get_attributes() as $attribute ) {

            if ($attribute->is_taxonomy() == false) {

                $attr_settings         = array();
                $attr_name             = sanitize_title($attribute->get_name());
                $type_key              = 'th_attribute_type_'.$attr_name;
                $attr_settings['type'] = isset($_POST[$type_key]) ? sanitize_text_field($_POST[$type_key]) : '';

                $tt_key = sanitize_title('th_tooltip_type_'.$attr_name);
                $attr_settings['tooltip_type'] = isset($_POST[$tt_key]) ? sanitize_text_field($_POST[$tt_key]) : '';

                $design_type_key = sanitize_title('th_attribute_design_type_'.$attr_name);
                $attr_settings['design_type']   = isset($_POST[$design_type_key]) ? sanitize_text_field($_POST[$design_type_key]) : '';

                if($attr_settings['type'] == 'radio'){
                   $radio_style_key = sanitize_title($attr_name.'_radio_button_style');
                    $attr_settings['radio-type'] = isset($_POST[$radio_style_key ]) ? sanitize_text_field($_POST[$radio_style_key]) : '';
                }else{
                    $term_settings = array();
                    foreach ($attribute->get_options() as $term) {
                        $term_settings['name'] = $term;

                        if($attr_settings['type'] == 'color'){
                            $color_type_key        = sanitize_title($attr_name.'_color_type_'.$term);
                            $term_settings['color_type'] = isset($_POST[ $color_type_key]) ? sanitize_text_field($_POST[$color_type_key]) : '';
                        }

                        $term_key = sanitize_title($attr_settings['type'].'_'.$attr_name.'_term_'.$term);
                        $term_settings['term_value'] = isset($_POST[$term_key]) ? sanitize_text_field($_POST[$term_key]): '';
                        $attr_settings[$term] = $term_settings;
                    }
                }

                $local_attr_settings[$attr_name] = $attr_settings;
            }
        }

        update_post_meta( $post_id,'th_custom_attribute_settings',$local_attr_settings);     
    }

    /*public function remove_admin_notices(){

       $current_screen = get_current_screen();
       if($current_screen->id === 'product_page_th_product_variation_swatches_for_woocommerce'){

            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        }
    }*/

    public function wvsf_notice_actions(){

        if( !(isset($_GET['thwvsf_remind']) || isset($_GET['thwvsf_dismiss']) || isset($_GET['thwvsf_reviewed']))) {
            return;
        }

        $nonse      = isset($_GET['thwvsf_review_nonce']) ? $_GET['thwvsf_review_nonce'] : false;
        $capability = THWVSF_Utils::thwvsf_capability();

        if(!wp_verify_nonce($nonse, 'thwvsf_notice_security') || !current_user_can($capability)){
            die();
        }

        $now = time();

        $thwvsf_remind = isset($_GET['thwvsf_remind']) ? sanitize_text_field( wp_unslash($_GET['thwvsf_remind'])) : false;
        if($thwvsf_remind){
            update_user_meta( get_current_user_id(), 'thwvsf_review_skipped', true );
            update_user_meta( get_current_user_id(), 'thwvsf_review_skipped_time', $now );
        }

        $thwvsf_dismiss = isset($_GET['thwvsf_dismiss']) ? sanitize_text_field( wp_unslash($_GET['thwvsf_dismiss'])) : false;
        if($thwvsf_dismiss){
            update_user_meta( get_current_user_id(), 'thwvsf_review_dismissed', true );
            update_user_meta( get_current_user_id(), 'thwvsf_review_dismissed_time', $now );
        }

        $thwvsf_reviewed = isset($_GET['thwvsf_reviewed']) ? sanitize_text_field( wp_unslash($_GET['thwvsf_reviewed'])) : false;
        if($thwvsf_reviewed){
            update_user_meta( get_current_user_id(), 'thwvsf_reviewed', true );
        }
    }

    public function output_review_request_link(){

        if(!apply_filters('thwvsf_show_dismissable_admin_notice', true)){
            return;
        }

        /*$current_screen = get_current_screen();
        if($current_screen->id !== 'product_page_th_product_variation_swatches_for_woocommerce'){
            return;
        }*/

        $thwvsf_reviewed = get_user_meta( get_current_user_id(), 'thwvsf_reviewed', true );
        if($thwvsf_reviewed){
            return;
        }

        $now = time();
        $dismiss_life  = apply_filters('thwvsf_dismissed_review_request_notice_lifespan', 6 * MONTH_IN_SECONDS);
        $reminder_life = apply_filters('thwvsf_skip_review_request_notice_lifespan',  7 * DAY_IN_SECONDS );

        $is_dismissed   = get_user_meta( get_current_user_id(), 'thwvsf_review_dismissed', true );
        $dismisal_time  = get_user_meta( get_current_user_id(), 'thwvsf_review_dismissed_time', true );
        $dismisal_time  = $dismisal_time ? $dismisal_time : 0;
        $dismissed_time = $now - $dismisal_time;
        if( $is_dismissed && ($dismissed_time < $dismiss_life) ){
            return;
        }

        $is_skipped    = get_user_meta( get_current_user_id(), 'thwvsf_review_skipped', true );
        $skipping_time = get_user_meta( get_current_user_id(), 'thwvsf_review_skipped_time', true );
        $skipping_time = $skipping_time ? $skipping_time : 0;
        $remind_time   = $now - $skipping_time;
        if($is_skipped && ($remind_time < $reminder_life) ){
            return;
        }
        
        $thwvsf_since = get_option('thwvsf_since');
        if(!$thwvsf_since){
            $now = time();
            update_option('thwvsf_since', $now, 'no' );
        }

        $thwvsf_since = $thwvsf_since ? $thwvsf_since : $now;
        $render_time  = apply_filters('thwvsf_show_review_banner_render_time' , 7 * DAY_IN_SECONDS);
        $render_time  = $thwvsf_since + $render_time;
        if($now > $render_time ){
            $this->render_review_request_notice();
        }
    }

    public function review_banner_custom_css(){

        ?>
        <style>
            .thwvsf-review-wrapper {
                padding: 15px 28px 26px 10px !important;
                margin-top: 35px;
            }

            #thwvsf_review_request_notice{
                margin-bottom: 20px;
            }

            .thwvsf-review-image {
                float: left;
            }

            .thwvsf-review-content {
                padding-right: 180px;
            }
            .thwvsf-review-content p {
                padding-bottom: 14px;
            }
            .thwvsf-notice-action{ 
                padding: 8px 18px 8px 18px;
                background: #fff;
                color:#007cba;
                border-radius: 5px;
                border: 1px solid  #007cba;
            }
            .thwvsf-notice-action.thwvsf-yes {
                background-color: #007cba;
                color: #fff;
            }
            .thwvsf-notice-action:hover:not(.thwvsf-yes) {
                background-color: #f2f5f6;
            }
            .thwvsf-notice-action.thwvsf-yes:hover {
                opacity: .9;
            }

            .thwvsf-themehigh-logo {
                position: absolute;
                right: 20px;
                top: calc(50% - 13px);
            }
            .thwvsf-notice-action {
                background-repeat: no-repeat;
                padding-left: 40px;
                background-position: 18px 8px;
                cursor: pointer;
            }
            .thwvsf-yes{
                background-image: url(<?php echo THWVSF_ASSETS_URL_ADMIN; ?>images/tick.svg);
            }
            .thwvsf-remind{
                background-image: url(<?php echo THWVSF_ASSETS_URL_ADMIN; ?>images/reminder.svg);
            }
            .thwvsf-dismiss{
                background-image: url(<?php echo THWVSF_ASSETS_URL_ADMIN; ?>images/close.svg);
            }
            .thwvsf-done{
                background-image: url(<?php echo THWVSF_ASSETS_URL_ADMIN; ?>images/done.svg);
            }

        </style>
        <?php    
    }

    private function render_review_request_notice(){

        /*$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general_settings'; 
        $url = 'edit.php?post_type=product&page=th_product_variation_swatches_for_woocommerce';
        if($tab && !empty($tab)){
            $url    .= '&tab='. $tab;
        }
        $admin_url  = admin_url($url);*/

        $remind_url   = add_query_arg(array('thwvsf_remind' => true , 'thwvsf_review_nonce' => wp_create_nonce('thwvsf_notice_security')));
        $dismiss_url  = add_query_arg(array('thwvsf_dismiss' => true, 'thwvsf_review_nonce' => wp_create_nonce( 'thwvsf_notice_security')));
        $reviewed_url = add_query_arg(array('thwvsf_reviewed' => true , 'thwvsf_review_nonce' => wp_create_nonce( 'thwvsf_notice_security')));
        ?>
        <div id="thwvsf_review_request_notice" class="notice notice-info thpladmin-notice is-dismissible thwvsf-review-wrapper" data-nonce="<?php echo wp_create_nonce( 'thwvsf_notice_security'); ?>">
            <div class="thwvsf-review-image">
                <img src="<?php echo esc_url(THWVSF_ASSETS_URL_ADMIN .'images/review-left.png'); ?>" alt="themehigh">
            </div>
            <div class="thwvsf-review-content">
                <h3><?php esc_html_e('Tell us how it was!', 'woocommerce-product-variation-swatches'); ?></h3>
                <p><?php  esc_html_e('Thank you for choosing the Variation Swatches Plugin. We would love to hear about your experience while using it. Could you please leave us a review on WordPress to help us spread the word and boost our motivation?', 'product-variation-swatches-for-woocommerce'); ?></p>
                <div class="action-row">
                    <a class="thwvsf-notice-action thwvsf-yes" onclick="window.open('https://wordpress.org/support/plugin/product-variation-swatches-for-woocommerce/reviews/?rate=5#new-post', '_blank')" style="margin-right:16px; text-decoration: none">
                        <?php esc_html_e("Ok, You deserve it", 'product-variation-swatches-for-woocommerce'); ?>
                    </a>
                    <a class="thwvsf-notice-action thwvsf-done" href="<?php echo esc_url($reviewed_url); ?>" style="margin-right:16px; text-decoration: none">
                        <?php _e('Already, Did', 'product-variation-swatches-for-woocommerce'); ?>
                    </a>

                    <a class="thwvsf-notice-action thwvsf-remind" href="<?php echo esc_url($remind_url); ?>" style="margin-right:16px; text-decoration: none">
                        <?php esc_html_e('Maybe later', 'product-variation-swatches-for-woocommerce'); ?></a>

                    <a class="thwvsf-notice-action thwvsf-dismiss" href="<?php echo esc_url($dismiss_url); ?>" style="margin-right:16px; text-decoration: none"><?php esc_html_e("Nah, Never", 'product-variation-swatches-for-woocommerce'); ?></a>
                </div>
            </div>
            <div class="thwvsf-themehigh-logo">
                <span class="logo" style="float: right">
                    <a target="_blank" href="https://www.themehigh.com">
                        <img src="<?php echo esc_url(THWVSF_ASSETS_URL_ADMIN.'images/logo.svg'); ?>" style="height:19px;margin-top:4px;" alt="themehigh"/>
                    </a>
                </span>
            </div>
        </div>

        <?php
    }

    public function thwvs_deactivation_form(){
        $is_snooze_time = get_user_meta( get_current_user_id(), 'thwvsf_deactivation_snooze', true );
        $now = time();

        if($is_snooze_time && ($now < $is_snooze_time)){
            return;
        }

        $deactivation_reasons = $this->get_deactivation_reasons();
        ?>
        <div id="thwvs_deactivation_form" class="thpladmin-modal-mask">
            <div class="thpladmin-modal">
                <div class="modal-container">
                    <!-- <span class="modal-close" onclick="thwvsfCloseModal(this)">×</span> -->
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="model-header">
                                <img class="th-logo" src="<?php echo esc_url(THWVSF_URL .'admin/assets/images/themehigh.svg'); ?>" alt="themehigh-logo">
                                <span><?php echo __('Quick Feedback', 'product-variation-swatches-for-woocommerce'); ?></span>
                            </div>

                            <!-- <div class="get-support-version-b">
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
                                <a class="thwvs-link thwvs-right-link thwvs-active" target="_blank" href="https://help.themehigh.com/hc/en-us/requests/new"><?php echo __('Get Support', 'product-variation-swatches-for-woocommerce'); ?></a>
                            </div> -->

                            <main class="form-container main-full">
                                <p class="thwvs-title-text"><?php echo __('If you have a moment, please let us know why you want to deactivate this plugin', 'product-variation-swatches-for-woocommerce'); ?></p>
                                <ul class="deactivation-reason" data-nonce="<?php echo wp_create_nonce('thwvs_deactivate_nonce'); ?>">
                                    <?php 
                                    if($deactivation_reasons){
                                        foreach($deactivation_reasons as $key => $reason){
                                            $reason_type = isset($reason['reason_type']) ? $reason['reason_type'] : '';
                                            $reason_placeholder = isset($reason['reason_placeholder']) ? $reason['reason_placeholder'] : '';
                                            ?>
                                            <li data-type="<?php echo esc_attr($reason_type); ?>" data-placeholder="<?php echo esc_attr($reason_placeholder); ?> ">
                                                <label>
                                                    <input type="radio" name="selected-reason" value="<?php echo esc_attr($key); ?>">
                                                    <span><?php echo esc_html($reason['radio_label']); ?></span>
                                                </label>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                                <p class="thwvs-privacy-cnt"><?php echo __('This form is only for getting your valuable feedback. We do not collect your personal data. To know more read our ', 'product-variation-swatches-for-woocommerce'); ?> <a class="thwvs-privacy-link" target="_blank" href="<?php echo esc_url('https://www.themehigh.com/privacy-policy/');?>"><?php echo __('Privacy Policy', 'product-variation-swatches-for-woocommerce'); ?></a></p>
                            </main>
                            <footer class="modal-footer">
                                <div class="thwvs-left">
                                    <a class="thwvs-link thwvs-left-link thwvs-deactivate" href="#"><?php echo __('Skip & Deactivate', 'product-variation-swatches-for-woocommerce'); ?></a>
                                </div>
                                <div class="thwvs-right">
                                    <a class="thwvs-link thwvs-right-link thwvs-active" target="_blank" href="https://help.themehigh.com/hc/en-us/requests/new"><?php echo __('Get Support', 'product-variation-swatches-for-woocommerce'); ?></a>
                                    <a class="thwvs-link thwvs-right-link thwvs-active thwvs-submit-deactivate" href="#"><?php echo __('Submit and Deactivate', 'product-variation-swatches-for-woocommerce'); ?></a>
                                    <a class="thwvs-link thwvs-right-link thwvs-close" href="#"><?php echo __('Cancel', 'product-variation-swatches-for-woocommerce'); ?></a>
                                </div>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style type="text/css">
            .th-logo{
                margin-right: 10px;
            }
            .thpladmin-modal-mask{
                position: fixed;
                background-color: rgba(17,30,60,0.6);
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 9999;
                overflow: scroll;
                transition: opacity 250ms ease-in-out;
            }
            .thpladmin-modal-mask{
                display: none;
            }
            .thpladmin-modal .modal-container{
                position: absolute;
                background: #fff;
                border-radius: 2px;
                overflow: hidden;
                left: 50%;
                top: 50%;
                transform: translate(-50%,-50%);
                width: 50%;
                max-width: 960px;
                /*min-height: 560px;*/
                /*height: 80vh;*/
                /*max-height: 640px;*/
                animation: appear-down 250ms ease-in-out;
                border-radius: 15px;
            }
            .model-header {
                padding: 21px;
            }
            .thpladmin-modal .model-header span {
                font-size: 18px;
                font-weight: bold;
            }
            .thpladmin-modal .model-header {
                padding: 21px;
                background: #ECECEC;
            }
            .thpladmin-modal .form-container {
                margin-left: 23px;
                clear: both;
            }
            .thpladmin-modal .deactivation-reason input {
                margin-right: 13px;
            }
            .thpladmin-modal .thwvs-privacy-cnt {
                color: #919191;
                font-size: 12px;
                margin-bottom: 31px;
                margin-top: 18px;
                max-width: 75%;
            }
            .thpladmin-modal .deactivation-reason li {
                margin-bottom: 17px;
            }
            .thpladmin-modal .modal-footer {
                padding: 20px;
                border-top: 1px solid #E7E7E7;
                float: left;
                width: 100%;
                box-sizing: border-box;
            }
            .thwvs-left {
                float: left;
            }
            .thwvs-right {
                float: right;
            }
            .thwvs-link {
                line-height: 31px;
                font-size: 12px;
            }
            .thwvs-left-link {
                font-style: italic;
            }
            .thwvs-right-link {
                padding: 0px 20px;
                border: 1px solid;
                display: inline-block;
                text-decoration: none;
                border-radius: 5px;
            }
            .thwvs-right-link.thwvs-active {
                background: #0773AC;
                color: #fff;
            }
            .thwvs-title-text {
                color: #2F2F2F;
                font-weight: 500;
                font-size: 15px;
            }
            .reason-input {
                margin-left: 31px;
                margin-top: 11px;
                width: 70%;
            }
            .reason-input input {
                width: 100%;
                height: 40px;
            }
            .reason-input textarea {
                width: 100%;
                min-height: 80px;
            }
            input.th-snooze-checkbox {
                width: 15px;
                height: 15px;
            }
            input.th-snooze-checkbox:checked:before {
                width: 1.2rem;
                height: 1.2rem;
            }
            .th-snooze-select {
                margin-left: 20px;
                width: 172px;
            }

            /* Version B */
            .get-support-version-b {
                width: 100%;
                padding-left: 23px;
                clear: both;
                float: left;
                box-sizing: border-box;
                background: #0673ab;
                color: #fff;
                margin-bottom: 20px;
            }
            .get-support-version-b p {
                font-size: 12px;
                line-height: 17px;
                width: 70%;
                display: inline-block;
                margin: 0px;
                padding: 15px 0px;
            }
            .get-support-version-b .thwvs-right-link {
                background-image: url(<?php echo esc_url(THWVSF_URL .'admin/assets/css/get_support_icon.svg'); ?>);
                background-repeat: no-repeat;
                background-position: 11px 10px;
                padding-left: 31px;
                color: #0773AC;
                background-color: #fff;
                float: right;
                margin-top: 17px;
                margin-right: 20px;
            }
            .thwvs-privacy-link {
                font-style: italic;
            }
        </style>

        <script type="text/javascript">
            (function($){
                var popup = $("#thwvs_deactivation_form");
                var deactivation_link = '';

                $('.thwvs-deactivate-link').on('click', function(e){
                    e.preventDefault();
                    deactivation_link = $(this).attr('href');
                    popup.css("display", "block");
                    popup.find('a.thwvs-deactivate').attr('href', deactivation_link);
                });

                popup.on('click', 'input[type="radio"]', function () {
                    var parent = $(this).parents('li:first');
                    popup.find('.reason-input').remove();

                    var type = parent.data('type');
                    var placeholder = parent.data('placeholder');

                    var reason_input = '';
                    if('text' == type){
                        reason_input += '<div class="reason-input">';
                        reason_input += '<input type="text" placeholder="'+ placeholder +'">';
                        reason_input += '</div>';
                    }else if('textarea' == type){
                        reason_input += '<div class="reason-input">';
                        reason_input += '<textarea row="5" placeholder="'+ placeholder +'">';
                        reason_input += '</textarea>';
                        reason_input += '</div>';
                    }else if('checkbox' == type){
                        reason_input += '<div class="reason-input ">';
                        reason_input += '<input type="checkbox" id="th-snooze" name="th-snooze" class="th-snooze-checkbox">';
                        reason_input += '<label for="th-snooze">Snooze this panel while troubleshooting</label>';
                        reason_input += '<select name="th-snooze-time" class="th-snooze-select" disabled>';
                        reason_input += '<option value="<?php echo HOUR_IN_SECONDS ?>">1 Hour</option>';
                        reason_input += '<option value="<?php echo 12*HOUR_IN_SECONDS ?>">12 Hour</option>';
                        reason_input += '<option value="<?php echo DAY_IN_SECONDS ?>">24 Hour</option>';
                        reason_input += '<option value="<?php echo WEEK_IN_SECONDS ?>">1 Week</option>';
                        reason_input += '<option value="<?php echo MONTH_IN_SECONDS ?>">1 Month</option>';
                        reason_input += '</select>';
                        reason_input += '</div>';
                    }

                    if(reason_input !== ''){
                        parent.append($(reason_input));
                    }
                });

                popup.on('click', '.thwvs-close', function () {
                    popup.css("display", "none");
                });

                popup.on('click', '.thwvs-submit-deactivate', function (e) {
                    e.preventDefault();
                    var button = $(this);
                    if (button.hasClass('disabled')) {
                        return;
                    }
                    var radio = $('.deactivation-reason input[type="radio"]:checked');
                    var parent_li = radio.parents('li:first');
                    var parent_ul = radio.parents('ul:first');
                    var input = parent_li.find('textarea, input[type="text"]');
                    var wvs_deacive_nonce = parent_ul.data('nonce');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'thwvs_deactivation_reason',
                            reason: (0 === radio.length) ? 'none' : radio.val(),
                            comments: (0 !== input.length) ? input.val().trim() : '',
                            security: wvs_deacive_nonce,
                        },
                        beforeSend: function () {
                            button.addClass('disabled');
                            button.text('Processing...');
                        },
                        complete: function () {
                            window.location.href = deactivation_link;
                        }
                    });
                });

                popup.on('click', '#th-snooze', function () {
                    if($(this).is(':checked')){
                        popup.find('.th-snooze-select').prop("disabled", false);
                    }else{
                        popup.find('.th-snooze-select').prop("disabled", true);
                    }
                });

            }(jQuery))
        </script>

        <?php 
    }

    private function get_deactivation_reasons(){
        return array(
            'found_better_plugin' => array(
                'radio_val'          => 'found_better_plugin',
                'radio_label'        => __('I found a better Plugin', 'product-variation-swatches-for-woocommerce'),
                'reason_type'        => 'text',
                'reason_placeholder' => __('Could you please mention the plugin?', 'product-variation-swatches-for-woocommerce'),
            ),

            'hard_to_use' => array(
                'radio_val'          => 'hard_to_use',
                'radio_label'        => __('It was hard to use', 'product-variation-swatches-for-woocommerce'),
                'reason_type'        => 'text',
                'reason_placeholder' => __('How can we improve your experience?', 'product-variation-swatches-for-woocommerce'),
            ),

            'feature_missing'=> array(
                'radio_val'          => 'feature_missing',
                'radio_label'        => __('A specific feature is missing', 'product-variation-swatches-for-woocommerce'),
                'reason_type'        => 'text',
                'reason_placeholder' => __('Type in the feature', 'product-variation-swatches-for-woocommerce'),
            ),

            'not_working_as_expected'=> array(
                'radio_val'          => 'not_working_as_expected',
                'radio_label'        => __('The plugin didn’t work as expected', 'product-variation-swatches-for-woocommerce'),
                'reason_type'        => 'text',
                'reason_placeholder' => __('Specify the issue', 'product-variation-swatches-for-woocommerce'),
            ),

            'temporary' => array(
                'radio_val'          => 'temporary',
                'radio_label'        => __('It’s a temporary deactivation - I’m troubleshooting an issue', 'product-variation-swatches-for-woocommerce'),
                'reason_type'        => 'checkbox',
                'reason_placeholder' => __('Could you please mention the plugin?', 'product-variation-swatches-for-woocommerce'),
            ),

            'other' => array(
                'radio_val'          => 'other',
                'radio_label'        => __('Other', 'product-variation-swatches-for-woocommerce'),
                'reason_type'        => 'textarea',
                'reason_placeholder' => __('Kindly tell us your reason, so that we can improve', 'product-variation-swatches-for-woocommerce'),
            ),
        );
    }

    public function thwvs_deactivation_reason(){
        global $wpdb;

        check_ajax_referer('thwvs_deactivate_nonce', 'security');

        if(!isset($_POST['reason'])){
            return;
        }

        if($_POST['reason'] === 'temporary'){

            $snooze_period = isset($_POST['th-snooze-time']) && $_POST['th-snooze-time'] ? $_POST['th-snooze-time'] : MINUTE_IN_SECONDS ;
            $time_now = time();
            $snooze_time = $time_now + $snooze_period;

            update_user_meta(get_current_user_id(), 'thwvsf_deactivation_snooze', $snooze_time);

            return;
        }
        
        $data = array(
            'plugin'        => 'wpvs',
            'reason'        => sanitize_text_field($_POST['reason']),
            'comments'      => isset($_POST['comments']) ? sanitize_textarea_field(wp_unslash($_POST['comments'])) : '',
            'date'          => gmdate("M d, Y h:i:s A"),
            'software'      => $_SERVER['SERVER_SOFTWARE'],
            'php_version'   => phpversion(),
            'mysql_version' => $wpdb->db_version(),
            'wp_version'    => get_bloginfo('version'),
            'wc_version'    => (!defined('WC_VERSION')) ? '' : WC_VERSION,
            'locale'        => get_locale(),
            'multisite'     => is_multisite() ? 'Yes' : 'No',
            'plugin_version'=> THWVSF_VERSION
        );

        $response = wp_remote_post('https://feedback.themehigh.in/api/add_feedbacks', array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => false,
            'headers'     => array( 'Content-Type' => 'application/json' ),
            'body'        => json_encode($data),
            'cookies'     => array()
                )
        );

        wp_send_json_success();
    }
 
}
endif;