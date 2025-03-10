<?php
/**
 * Timeline Slider widget class.
 *
 * @package EmargyElements\Widgets
 */

namespace EmargyElements\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Icons_Manager;
use Elementor\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Timeline Slider widget class.
 */
class Timeline_Slider extends Widget_Base {

    /**
     * Get widget name.
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'emargy_timeline_slider';
    }

    /**
     * Get widget title.
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Timeline Slider', 'emargy-elements' );
    }

    /**
     * Get widget icon.
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-slider-3d';
    }

    /**
     * Get widget categories.
     *
     * @return array Widget categories.
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
        return array( 'timeline', 'slider', 'posts', 'dynamic', 'emargy' );
    }

    /**
     * Get script dependencies.
     *
     * @return array Script dependencies.
     */
    public function get_script_depends() {
        return array( 'emargy-elements-timeline-slider' );
    }

    /**
     * Get style dependencies.
     *
     * @return array Style dependencies.
     */
    public function get_style_depends() {
        return array( 'emargy-elements-timeline-slider' );
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'section_content',
            array(
                'label' => esc_html__( 'Content', 'emargy-elements' ),
            )
        );

        // Use Query Control for dynamic content
        $this->add_control(
            'post_type',
            [
                'label' => esc_html__( 'Source', 'emargy-elements' ),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_post_types(),
                'default' => 'post',
            ]
        );

        if ( class_exists('ElementorPro\Modules\QueryControl\Module') ) {
            // If Elementor Pro is active, use the Query module
            $this->add_control(
                'posts_query_id',
                [
                    'label' => esc_html__( 'Posts Query', 'emargy-elements' ),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'placeholder' => esc_html__( 'cpt_query', 'emargy-elements' ),
                ]
            );

            $this->add_control(
                'posts_per_page',
                [
                    'label' => esc_html__( 'Posts Per Page', 'emargy-elements' ),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 5,
                    'min' => 1,
                    'max' => 20,
                ]
            );
        } else {
            // If Elementor Pro is not active, use a simpler control
            $this->add_control(
                'posts_per_page',
                [
                    'label' => esc_html__( 'Posts Per Page', 'emargy-elements' ),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 5,
                    'min' => 1,
                    'max' => 20,
                ]
            );

            $this->add_control(
                'orderby',
                [
                    'label' => esc_html__( 'Order By', 'emargy-elements' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'date',
                    'options' => [
                        'date' => esc_html__( 'Date', 'emargy-elements' ),
                        'title' => esc_html__( 'Title', 'emargy-elements' ),
                        'rand' => esc_html__( 'Random', 'emargy-elements' ),
                        'menu_order' => esc_html__( 'Menu Order', 'emargy-elements' ),
                    ],
                ]
            );

            $this->add_control(
                'order',
                [
                    'label' => esc_html__( 'Order', 'emargy-elements' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'desc',
                    'options' => [
                        'asc' => esc_html__( 'ASC', 'emargy-elements' ),
                        'desc' => esc_html__( 'DESC', 'emargy-elements' ),
                    ],
                ]
            );
        }

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'exclude' => [ 'custom' ],
                'default' => 'large',
                'prefix_class' => 'elementor-thumbnail-',
            ]
        );

        $this->add_control(
            'play_icon',
            [
                'label' => esc_html__( 'Play Button Icon', 'emargy-elements' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-play-circle',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->end_controls_section();

        // Autoplay Section
        $this->start_controls_section(
            'section_autoplay',
            [
                'label' => esc_html__( 'Autoplay', 'emargy-elements' ),
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__( 'Autoplay', 'emargy-elements' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'emargy-elements' ),
                'label_off' => esc_html__( 'No', 'emargy-elements' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => esc_html__( 'Autoplay Speed', 'emargy-elements' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slider' => '--autoplay-speed: {{VALUE}}ms',
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => esc_html__( 'Pause on Hover', 'emargy-elements' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'emargy-elements' ),
                'label_off' => esc_html__( 'No', 'emargy-elements' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Main Slider Style Section
        $this->start_controls_section(
            'section_slider_style',
            [
                'label' => esc_html__( 'Slider Style', 'emargy-elements' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'slider_height',
            [
                'label' => esc_html__( 'Slider Height', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'vh', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slider' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'slider_background',
            [
                'label' => esc_html__( 'Background', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slider' => 'background-color: {{VALUE}};',
                ],
                'default' => '#f7f7f7',
            ]
        );

        $this->add_responsive_control(
            'slider_padding',
            [
                'label' => esc_html__( 'Padding', 'emargy-elements' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slider' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slider_border',
                'selector' => '{{WRAPPER}} .emargy-timeline-slider',
            ]
        );

        $this->add_responsive_control(
            'slider_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'emargy-elements' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slider' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'slider_box_shadow',
                'selector' => '{{WRAPPER}} .emargy-timeline-slider',
            ]
        );

        $this->end_controls_section();

        // Slide Item Style Section
        $this->start_controls_section(
            'section_slide_style',
            [
                'label' => esc_html__( 'Slide Item Style', 'emargy-elements' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'slide_spacing',
            [
                'label' => esc_html__( 'Spacing Between Slides', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slide' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'slide_padding',
            [
                'label' => esc_html__( 'Padding', 'emargy-elements' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'slide_border',
                'selector' => '{{WRAPPER}} .emargy-timeline-slide',
            ]
        );

        $this->add_responsive_control(
            'slide_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'emargy-elements' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'slide_box_shadow',
                'selector' => '{{WRAPPER}} .emargy-timeline-slide',
            ]
        );

        $this->add_control(
            'active_slide_scale',
            [
                'label' => esc_html__( 'Active Slide Scale', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '' ],
                'range' => [
                    '' => [
                        'min' => 1,
                        'max' => 2,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => '',
                    'size' => 1.2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slide.active' => 'transform: scale({{SIZE}});',
                ],
            ]
        );

        $this->add_control(
            'inactive_slide_opacity',
            [
                'label' => esc_html__( 'Inactive Slide Opacity', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '' ],
                'range' => [
                    '' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => '',
                    'size' => 0.7,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-slide:not(.active)' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Play Button Style Section
        $this->start_controls_section(
            'section_play_button_style',
            [
                'label' => esc_html__( 'Play Button Style', 'emargy-elements' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'play_button_size',
            [
                'label' => esc_html__( 'Size', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 60,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-play-button' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'play_button_styles' );

        $this->start_controls_tab(
            'play_button_normal',
            [
                'label' => esc_html__( 'Normal', 'emargy-elements' ),
            ]
        );

        $this->add_control(
            'play_button_color',
            [
                'label' => esc_html__( 'Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .emargy-play-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'play_button_background',
            [
'label' => esc_html__( 'Background Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.5)',
                'selectors' => [
                    '{{WRAPPER}} .emargy-play-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'play_button_hover',
            [
                'label' => esc_html__( 'Hover', 'emargy-elements' ),
            ]
        );

        $this->add_control(
            'play_button_hover_color',
            [
                'label' => esc_html__( 'Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .emargy-play-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'play_button_hover_background',
            [
                'label' => esc_html__( 'Background Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.8)',
                'selectors' => [
                    '{{WRAPPER}} .emargy-play-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'play_button_hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'emargy-elements' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'play_button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'emargy-elements' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-play-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'play_button_padding',
            [
                'label' => esc_html__( 'Padding', 'emargy-elements' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-play-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Timeline Style Section
        $this->start_controls_section(
            'section_timeline_style',
            [
                'label' => esc_html__( 'Timeline Style', 'emargy-elements' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'timeline_bar_height',
            [
                'label' => esc_html__( 'Bar Height', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-bar' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'timeline_bar_height',
            [
                'label' => esc_html__( 'Timeline Bar Height', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-bar' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'timeline_bar_color',
            [
                'label' => esc_html__('Timeline Bar Color', 'emargy-elements'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-bar' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'timeline_bar_color',
            [
                'label' => esc_html__( 'Bar Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#cccccc',
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-bar' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'timeline_margin',
            [
                'label' => esc_html__( 'Margin', 'emargy-elements' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '30',
                    'right' => '0',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
            ]
        );

        $this->add_control(
            'timeline_marker_size',
            [
                'label' => esc_html__( 'Marker Size', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-marker' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'marker_size',
            [
                'label' => esc_html__('Marker Size', 'emargy-elements'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-marker' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'timeline_marker_color',
            [
                'label' => esc_html__( 'Marker Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#888888',
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-marker' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'timeline_marker_active_color',
            [
                'label' => esc_html__( 'Active Marker Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-marker.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'timeline_handle_heading',
            [
                'label' => esc_html__( 'Slider Handle', 'emargy-elements' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'timeline_handle_size',
            [
                'label' => esc_html__( 'Handle Size', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 15,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-handle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'timeline_handle_color',
            [
                'label' => esc_html__( 'Handle Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-handle' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'timeline_handle_border',
                'selector' => '{{WRAPPER}} .emargy-timeline-handle',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'timeline_handle_shadow',
                'selector' => '{{WRAPPER}} .emargy-timeline-handle',
            ]
        );

        $this->end_controls_section();

        // Timeline Numbers Style Section
        $this->start_controls_section(
            'section_timeline_numbers_style',
            [
                'label' => esc_html__( 'Timeline Numbers Style', 'emargy-elements' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'timeline_numbers_typography',
                'selector' => '{{WRAPPER}} .emargy-timeline-number',
            ]
        );

        $this->add_control(
            'timeline_numbers_color',
            [
                'label' => esc_html__( 'Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#888888',
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'timeline_numbers_active_color',
            [
                'label' => esc_html__( 'Active Color', 'emargy-elements' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-number.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'timeline_numbers_spacing',
            [
                'label' => esc_html__( 'Spacing', 'emargy-elements' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .emargy-timeline-number' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get all registered post types.
     *
     * @return array
     */
    protected function get_post_types() {
        $post_types = get_post_types( ['public' => true], 'objects' );
        $options = [];

        foreach ( $post_types as $post_type ) {
            if ( 'attachment' === $post_type->name ) {
                continue;
            }
            $options[ $post_type->name ] = $post_type->label;
        }

        return $options;
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $post_type = $settings['post_type'];
        $posts_per_page = !empty($settings['posts_per_page']) ? intval($settings['posts_per_page']) : 10;
        $autoplay = $settings['autoplay'];
        $autoplay_speed = $settings['autoplay_speed'];
        $pause_on_hover = $settings['pause_on_hover'];
        $timeline_bar_height = $settings['timeline_bar_height']['size'];
        
        $posts = $this->get_posts($post_type, $posts_per_page);
        
        if ( empty( $posts ) ) {
            return;
        }

        $id_int = substr( $this->get_id_int(), 0, 3 );
        
        $this->add_render_attribute( 'timeline_slider', [
            'class' => 'emargy-timeline-slider',
            'id' => 'emargy-timeline-slider-' . $id_int,
            'data-autoplay' => $autoplay,
            'data-autoplay-speed' => $autoplay_speed,
            'data-pause-on-hover' => $pause_on_hover,
            'data-timeline-bar-height' => $timeline_bar_height,
        ]);

        ?>
        <div <?php $this->print_render_attribute_string( 'timeline_slider' ); ?>>
            <div class="emargy-slider-container">
                <div class="emargy-slider-wrapper">
                    <?php 
                    $count = 0;
                    $active_slide = 5; // Make the 6th slide active (06 in your example)
                    
                    foreach ( $posts as $post ) : 
                        $count++;
                        $active_class = ($count === $active_slide + 1) ? 'active' : '';
                        $post_thumbnail = get_the_post_thumbnail_url( $post->ID, $settings['thumbnail_size'] );
                        if ( ! $post_thumbnail ) {
                            $post_thumbnail = EMARGY_ELEMENTS_ASSETS . 'img/placeholder.jpg';
                        }
                    ?>
                        <div class="emargy-timeline-slide <?php echo esc_attr( $active_class ); ?>" data-index="<?php echo esc_attr( $count ); ?>">
                            <div class="emargy-slide-image">
                                <img src="<?php echo esc_url( $post_thumbnail ); ?>" alt="<?php echo esc_attr( $post->post_title ); ?>" />
                                <div class="emargy-play-button">
                                    <?php Icons_Manager::render_icon( $settings['play_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="emargy-timeline-container">
                <div class="emargy-timeline-bar">
                    <?php 
                    $count = 0;
                    foreach ( $posts as $post ) : 
                        $count++;
                        $active_class = ($count === $active_slide + 1) ? 'active' : '';
                        $position = (($count - 1) / (count($posts) - 1)) * 100;
                        if ( count($posts) === 1 ) {
                            $position = 50;
                        }
                    ?>
                        <div class="emargy-timeline-marker <?php echo esc_attr( $active_class ); ?>" 
                             data-index="<?php echo esc_attr( $count ); ?>" 
                             style="left: <?php echo esc_attr( $position ); ?>%"></div>
                    <?php endforeach; ?>
                    
                    <div class="emargy-timeline-handle" style="left: <?php echo (($active_slide) / (count($posts) - 1)) * 100; ?>%">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
                
                <div class="emargy-timeline-numbers">
                    <?php 
                    $count = 0;
                    foreach ( $posts as $post ) : 
                        $count++;
                        $active_class = ($count === $active_slide + 1) ? 'active' : '';
                        $position = (($count - 1) / (count($posts) - 1)) * 100;
                        if ( count($posts) === 1 ) {
                            $position = 50;
                        }
                        // Format number with leading zero
                        $number = sprintf( '%02d', $count );
                    ?>
                        <div class="emargy-timeline-number <?php echo esc_attr( $active_class ); ?>" 
                             data-index="<?php echo esc_attr( $count ); ?>" 
                             style="left: <?php echo esc_attr( $position ); ?>%">
                            <?php echo esc_html( $number ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="emargy-slider-dots">
                <?php 
                $count = 0;
                foreach ( $posts as $post ) : 
                    $count++;
                    $active_class = ($count === $active_slide + 1) ? 'active' : '';
                ?>
                    <div class="emargy-slider-dot <?php echo esc_attr( $active_class ); ?>" data-index="<?php echo esc_attr( $count ); ?>"></div>
                <?php endforeach; ?>
            </div>

            <div class="selected-count" style="text-align: center; color: white; font-size: 16px; margin-top: 10px;"></div>
        </div>
        <?php
    }

    /**
     * Get posts based on settings.
     *
     * @param string $post_type    Post type.
     * @param int    $posts_per_page Number of posts.
     *
     * @return array
     */
    protected function get_posts( $post_type, $posts_per_page ) {
        // Check if we have Elementor Pro Query Control module
        if ( class_exists( 'ElementorPro\Modules\QueryControl\Module' ) && ! empty( $this->get_settings_for_display( 'posts_query_id' ) ) ) {
            $query_args = [
                'post_type' => $post_type,
                'posts_per_page' => $posts_per_page,
            ];
            
            // Use Elementor Pro query
            $query_id = $this->get_settings_for_display( 'posts_query_id' );
            
            // Get the custom query from Elementor Pro
            $document = Plugin::$instance->documents->get( get_the_ID() );
            if ( $document ) {
                $query_args = $document->get_elements_data();
                // Process the query args (this is simplified - you'd need to traverse the data to find the query)
            }
        } else {
            // Use regular WP_Query
            $query_args = [
                'post_type' => $post_type,
                'posts_per_page' => $posts_per_page,
                'ignore_sticky_posts' => 1,
            ];
            
            // Add orderby and order if set
            $orderby = $this->get_settings_for_display( 'orderby' );
            if ( ! empty( $orderby ) ) {
                $query_args['orderby'] = $orderby;
            }
            
            $order = $this->get_settings_for_display( 'order' );
            if ( ! empty( $order ) ) {
                $query_args['order'] = $order;
            }
        }
        
        $query = new \WP_Query( $query_args );
        return $query->posts;
    }

    /**
     * Content template.
     */
    protected function content_template() {
        // This is used for live preview in the editor
    }
}