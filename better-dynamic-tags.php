<?php
/**
 * Plugin Name: Better Dynamic Tags for Elementor
 * Description: Dynamic Tags for Elementor.
 * Plugin URI: https://wpsmartwidgets.com/
 * Author: WP Smart Widgets
 * Author URI: https://wpsmartwidgets.com/
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Tested up to: 6.7.1
 * Elementor tested up to: 3.25.4
 * Text Domain: bdt-widget
 * Domain Path: /lang
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package BDT_Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main BDT Elementor Widgets Class
 *
 * @since 1.0.0
 */
final class BDT_Elementor {
	const VERSION                   = '1.0.3';
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
	const MINIMUM_PHP_VERSION       = '7.2';

	/**
	 * Holds the single instance of the class.
	 *
	 * @since 1.0.0
	 * @var BDT_Elementor|null
	 */
	private static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return BDT_Elementor Instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * BDT_Elementor constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
	}

	/**
	 * Load plugin textdomain.
	 */
	public function i18n() {
		load_plugin_textdomain( 'bdt-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Fires after all plugins are loaded.
	 */
	public function on_plugins_loaded() {
		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', [ $this, 'init' ] );
		}
	}

	/**
	 * Compatibility checks.
	 */
	public function is_compatible() {
		// Check if Elementor is installed and activated.
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check Elementor version.
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check PHP version.
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		$this->i18n();
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
		
		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/class-bdt-helper.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/class-bdt-dynamic-tag.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/classes/class-bdt-dynamic-group.php';
	}

	/**
	 * Enqueue frontend styles.
	 */
	public function widget_styles() {
		wp_enqueue_style( 'bdt-widget-style', plugins_url( 'assets/css/elementor-bdt-widget.css', __FILE__ ), [], self::VERSION );
	}

}

BDT_Elementor::instance();
