<?php
/**
 * Plugin Name:       Product Variation Swatches for Woocommerce
 * Plugin URI:        https://themehigh.com/product/woocommerce-product-variation-swatches
 * Description:       Product Variation Swatches for WooCommerce lets you add variation swatches for variable product attributes in your WooCommerce online store.
 * Version:           2.2.0
 * Author:            ThemeHigh
 * Author URI:        https://themehigh.com/
 *
 * Text Domain:       product-variation-swatches-for-woocommerce
 * Domain Path:       /languages
 *
 * WC requires at least: 4.0.0
 * WC tested up to: 6.8
 */

if(!defined('WPINC')){	die; }

if (!function_exists('is_woocommerce_active')){
	function is_woocommerce_active(){
	    $active_plugins = (array) get_option('active_plugins', array());
	    if(is_multisite()){
		   $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	    }
	    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins) || class_exists('WooCommerce');
	}
}

if(is_woocommerce_active()) {
	define('THWVSF_VERSION', '2.2.0');
	!defined('THWVSF_FILE') && define('THWVSF_FILE', __FILE__);
	!defined('THWVSF_PATH') && define('THWVSF_PATH', plugin_dir_path( __FILE__ ));
	!defined('THWVSF_URL') && define('THWVSF_URL', plugins_url( '/', __FILE__ ));
	!defined('THWVSF_BASE_NAME') && define('THWVSF_BASE_NAME', plugin_basename( __FILE__ ));
	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-thwvsf.php';
	
	/**
	 * Begins execution of the plugin.
	 */
	function run_thwvsf() {
		$plugin = new THWVSF();
	}
	run_thwvsf();
}

?>