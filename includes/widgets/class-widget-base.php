<?php
/**
 * Base widget class.
 *
 * @package EmargyElements\Widgets
 */

namespace EmargyElements\Widgets;

use Elementor\Widget_Base as Elementor_Widget_Base;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Base widget class.
 */
abstract class Widget_Base extends Elementor_Widget_Base {

    /**
     * Get widget category.
     *
     * @return string Widget category.
     */
    public function get_categories() {
        return array( 'emargy-elements' );
    }

    /**
     * Get widget keywords.
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return array( 'emargy', 'elements' );
    }
    
    /**
     * Get script dependencies.
     *
     * @return array Script dependencies.
     */
    public function get_script_depends() {
        return array();
    }
    
    /**
     * Get style dependencies.
     *
     * @return array Style dependencies.
     */
    public function get_style_depends() {
        return array();
    }
}