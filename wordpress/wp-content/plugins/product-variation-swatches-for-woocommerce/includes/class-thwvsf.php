<?php
/**
 * The file that defines the core plugin class.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    product-variation-swatches-for-woocommerce
 * @subpackage product-variation-swatches-for-woocommerce/includes
 */
if(!defined('WPINC')){	die; }

if(!class_exists('THWVSF')):

class THWVSF {
	const TEXT_DOMAIN = 'product-variation-swatches-for-woocommerce';

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'THWVSF_VERSION' ) ) {
			$this->version = THWVSF_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'product-variation-swatches-for-woocommerce';
		
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		
		add_action( 'plugins_loaded', array($this, 'load_plugin_textdomain' ));
		add_action( 'init', array($this, 'init' ));
	}
	
	public function init(){
		$this->define_constants();
		
		//Plugin API Support
		new THWVSF_WC_API_Handler();

		//Product import export
		if(apply_filters('thwvs_enable_swatches_export_import',true)){

			new THWVSF_Admin_Swatch_Import_Export();
		}

	}
	
	private function define_constants(){
		!defined('THWVSF_ASSETS_URL_ADMIN') && define('THWVSF_ASSETS_URL_ADMIN', THWVSF_URL . 'admin/assets/');
		!defined('THWVSF_ASSETS_URL_PUBLIC') && define('THWVSF_ASSETS_URL_PUBLIC', THWVSF_URL . 'public/assets/');
		!defined('THWVSF_WOO_ASSETS_URL') && define('THWVSF_WOO_ASSETS_URL', WC()->plugin_url() . '/assets/');
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - THWVSF_Admin. Defines all hooks for the admin area.
	 * - THWVSF_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		if(!function_exists('is_plugin_active')){
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-thwvsf-autoloader.php';
		
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-thwvsf-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-thwvsf-public.php';

		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-thwvsf-settings-designs.php';

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters('plugin_locale', get_locale(), self::TEXT_DOMAIN);
		
		load_textdomain(self::TEXT_DOMAIN, WP_LANG_DIR.'/product-variation-swatches-for-woocommerce/'.self::TEXT_DOMAIN.'-'.$locale.'.mo');
		load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname(plugin_basename( __FILE__ )) . '/languages/');
	}
	
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		
		$plugin_admin = new THWVSF_Admin( $this->get_plugin_name(), $this->get_version() );
		add_action( 'admin_enqueue_scripts', array($plugin_admin, 'enqueue_styles_and_scripts' ));
		add_action( 'admin_menu', array($plugin_admin, 'admin_menu' ));
		add_filter( 'woocommerce_screen_ids', array($plugin_admin, 'add_screen_id' ));
		add_filter( 'plugin_action_links_'.THWVSF_BASE_NAME, array($plugin_admin, 'plugin_action_links' ));
		add_filter( 'plugin_row_meta', array($plugin_admin, 'plugin_row_meta'), 10, 2 );
		//add_action( 'in_admin_header',array($plugin_admin,'remove_admin_notices'));
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new THWVSF_Public( $this->get_plugin_name(), $this->get_version() );

		add_action( 'wp_enqueue_scripts', array($plugin_public, 'enqueue_styles_and_scripts'),12);
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}

endif;