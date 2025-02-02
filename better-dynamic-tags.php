<?php
/**
 * Plugin Name: Better Dynamic Tags for Elementor
 * Requires Plugins: elementor
 * Description: Dynamic Tags for Elementor.
 * Plugin URI: https://wpsmartwidgets.com/
 * Author: WP Smart Widgets
 * Author URI: https://wpsmartwidgets.com/
 * Version: 1.0.1
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Tested up to: 6.7
 * Elementor tested up to: 3.26.2
 * Text Domain: bdt-widget
 * Domain Path: /lang
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * GitHub Plugin URI: https://github.com/dara-mtl/dynamic-tags-for-elementor
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
	const VERSION                   = '1.0.1';
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
	const MINIMUM_PHP_VERSION       = '7.4';

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
	}

	/**
	 * Enqueue frontend styles.
	 */
	public function widget_styles() {
		wp_enqueue_style( 'bdt-widget-style', plugins_url( 'assets/css/elementor-bdt-widget.css', __FILE__ ), [], self::VERSION );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'better-post-filter-widgets-for-elementor' ),
			'<strong>' . esc_html__( 'Better Post and Filter Widgets for Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'better-post-filter-widgets-for-elementor' ),
			'<strong>' . esc_html__( 'Better Post and Filter Widgets for Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'better-post-filter-widgets-for-elementor' ),
			'<strong>' . esc_html__( 'Better Post and Filter Widgets for Elementor', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'better-post-filter-widgets-for-elementor' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

}

BDT_Elementor::instance();
