<?php
/**
 * Core plugin class.
 *
 * @package EmargyElements
 */

namespace EmargyElements;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class.
 */
class Emargy_Elements {

    /**
     * Instance of Assets_Manager.
     *
     * @var Assets_Manager
     */
    private $assets_manager;

    /**
     * Constructor.
     */
    public function __construct() {
        // Initialize assets manager.
        $this->assets_manager = new Assets_Manager();
        
        // Register widgets.
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        
        // Register widget categories.
        add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_categories' ) );
    }

    /**
     * Register Elementor widgets.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
     */
    public function register_widgets( $widgets_manager ) {
        // Load base widget class.
        require_once EMARGY_ELEMENTS_PATH . 'includes/widgets/class-widget-base.php';
        
        // Load and register the Timeline Slider widget.
        require_once EMARGY_ELEMENTS_PATH . 'includes/widgets/class-timeline-slider.php';
        $widgets_manager->register( new Widgets\Timeline_Slider() );
    }

    /**
     * Register widget categories.
     *
     * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
     */
    public function register_widget_categories( $elements_manager ) {
        $elements_manager->add_category(
            'emargy-elements',
            array(
                'title' => esc_html__( 'Emargy Elements', 'emargy-elements' ),
                'icon'  => 'fa fa-plug',
            )
        );
    }
}