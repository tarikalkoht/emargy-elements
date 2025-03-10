<?php
/**
 * Assets Manager class.
 *
 * @package EmargyElements
 */

namespace EmargyElements;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Assets Manager class.
 */
class Assets_Manager {

    /**
     * Constructor.
     */
    public function __construct() {
        // Register frontend styles and scripts.
        add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
        
        // Register admin styles and scripts.
        add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) );
        
        // Register editor styles and scripts.
        add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'register_editor_assets' ) );
    }

    /**
     * Register frontend assets.
     */
    public function register_frontend_assets() {
        // Register Timeline Slider CSS.
        wp_register_style(
            'emargy-elements-timeline-slider',
            EMARGY_ELEMENTS_ASSETS . 'css/timeline-slider.css',
            array(),
            EMARGY_ELEMENTS_VERSION
        );

        // Register Timeline Slider JS.
        wp_register_script(
            'emargy-elements-timeline-slider',
            EMARGY_ELEMENTS_ASSETS . 'js/timeline-slider.js',
            array( 'jquery' ),
            EMARGY_ELEMENTS_VERSION,
            true
        );
    }

    /**
     * Register admin assets.
     */
    public function register_admin_assets() {
        // Register admin CSS.
        wp_register_style(
            'emargy-elements-admin',
            EMARGY_ELEMENTS_ASSETS . 'css/admin.css',
            array(),
            EMARGY_ELEMENTS_VERSION
        );

        // Register admin JS.
        wp_register_script(
            'emargy-elements-admin',
            EMARGY_ELEMENTS_ASSETS . 'js/admin.js',
            array( 'jquery' ),
            EMARGY_ELEMENTS_VERSION,
            true
        );
    }

    /**
     * Register editor assets.
     */
    public function register_editor_assets() {
        // Enqueue editor styles if needed.
        wp_enqueue_style(
            'emargy-elements-editor',
            EMARGY_ELEMENTS_ASSETS . 'css/admin.css',
            array(),
            EMARGY_ELEMENTS_VERSION
        );
    }
}