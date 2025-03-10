<?php
/**
 * Plugin Name: Emargy Elements
 * Description: Custom Elementor widgets including Timeline Slider
 * Version: 1.0.0
 * Author: Emargy
 * Author URI: https://emargy.com
 * Text Domain: emargy-elements
 * Domain Path: /languages
 * Elementor tested up to: 3.16.0
 * Elementor Pro tested up to: 3.16.0
 *
 * @package EmargyElements
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'EMARGY_ELEMENTS_VERSION', '1.0.0' );
define( 'EMARGY_ELEMENTS_FILE', __FILE__ );
define( 'EMARGY_ELEMENTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'EMARGY_ELEMENTS_URL', plugins_url( '/', __FILE__ ) );
define( 'EMARGY_ELEMENTS_ASSETS', EMARGY_ELEMENTS_URL . 'assets/' );

/**
 * Main plugin class.
 */
final class Emargy_Elements_Plugin {

    /**
     * Instance of this class.
     *
     * @var Emargy_Elements_Plugin
     */
    private static $instance = null;

    /**
     * Get the instance of this class.
     *
     * @return Emargy_Elements_Plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files.
     */
    private function includes() {
        // Core plugin class.
        require_once EMARGY_ELEMENTS_PATH . 'includes/class-emargy-elements.php';
        
        // Assets manager.
        require_once EMARGY_ELEMENTS_PATH . 'includes/class-assets-manager.php';
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        // Load textdomain.
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        
        // Initialize the plugin.
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'emargy-elements', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Initialize the plugin if Elementor is active.
     */
    public function init() {
        // Check if Elementor is installed and activated.
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_missing_elementor' ) );
            return;
        }

        // Check for minimum Elementor version.
        if ( ! version_compare( ELEMENTOR_VERSION, '3.0.0', '>=' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
            return;
        }

        // Initialize the plugin components.
        new \EmargyElements\Emargy_Elements();
    }

    /**
     * Admin notice for missing Elementor.
     */
    public function admin_notice_missing_elementor() {
        $message = sprintf(
            /* translators: 1: Plugin name, 2: Elementor */
            esc_html__( '%1$s requires %2$s to be installed and activated.', 'emargy-elements' ),
            '<strong>' . esc_html__( 'Emargy Elements', 'emargy-elements' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'emargy-elements' ) . '</strong>'
        );

        printf( '<div class="notice notice-error"><p>%s</p></div>', $message );
    }

    /**
     * Admin notice for minimum Elementor version.
     */
    public function admin_notice_minimum_elementor_version() {
        $message = sprintf(
            /* translators: 1: Plugin name, 2: Elementor, 3: Required Elementor version */
            esc_html__( '%1$s requires %2$s version %3$s or greater.', 'emargy-elements' ),
            '<strong>' . esc_html__( 'Emargy Elements', 'emargy-elements' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'emargy-elements' ) . '</strong>',
            '3.0.0'
        );

        printf( '<div class="notice notice-error"><p>%s</p></div>', $message );
    }
}

// Initialize the plugin.
Emargy_Elements_Plugin::get_instance();