<?php
namespace ElementorExtras\Modules\Calendar\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Group_Control_Transition;
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\Calendar\Module as Module;
use ElementorExtras\Modules\CustomFields\Module as CustomFieldsModule;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Calendar
 *
 * @since 2.0.0
 */
class Calendar extends Extras_Widget {

	/**
	 * _events
	 *
	 * @since  2.0.0
	 * @var    array
	 */
	protected $_events;

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-calendar';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Calendar', 'elementor-extras' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-post-calendar';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'moment',
			'clndr',
			'wp-util',
		];
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_sources',
			[
				'label' 	=> __( 'Events', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'source',
				[
					'label'			=> __( 'Source', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'manual',
					'options'		=> [
						'manual' 	=> __( 'Manual', 'elementor-extras' ),
						'posts' 	=> __( 'Posts', 'elementor-extras' ),
					],
				]
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'title',
				[
					'label'		=> __( 'Title', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Conference', 'elementor-extras' ),
				]
			);

			$repeater->add_control(
				'link',
				[
					'label' 	=> __( 'Link', 'elementor-extras' ),
					'type' 		=> Controls_Manager::URL,
					'placeholder' => __( 'https://your-link.com', 'elementor-extras' ),
					'default' => [
						'url' => '#',
					],
				]
			);

			$repeater->add_control(
				'start',
				[
					'label'		=> __( 'Start Date', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
					],
					'default' 	=> date( 'Y-m-d H:i', strtotime( '+1 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
				]
			);

			$repeater->add_control(
				'end',
				[
					'label'		=> __( 'End Date', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
					],
					'default' 	=> date( 'Y-m-d H:i', strtotime( '+3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
				]
			);

			$this->add_control(
				'events',
				[
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[],
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> '{{{ title }}}',
					'condition'		=> [
						'source'	=> 'manual',
					],
				]
			);

			$this->add_control(
				'post_type',
				[	
					'label'		=> __( 'Post Type', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'post',
					'condition'		=> [
						'source'	=> 'posts',
					],
					'options'	=> Utils::get_public_post_types_options( true ),
				]
			);

			$customfields = new CustomFieldsModule();

			$this->add_control(
				'post_dates_field_type',
				[	
					'label'		=> __( 'Fetch Dates From', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'post_date',
					'condition'		=> [
						'source'	=> 'posts',
					],
					'options'	=> array_merge(
						[ 'post_date' => __( 'Post Date', 'elementor-extras' ), ],
						$customfields->get_field_types()
					),
				]
			);

			foreach ( Utils::get_public_post_types_options() as $post_type => $label ) {
				$post_type_label = $label;
				foreach ( $customfields->get_field_types() as $field_type => $label ) {

					$fields_options = [
						'placeholder'	=> sprintf( __( 'Search %s Date Fields', 'elementor-extras' ), $post_type_label ),
						'description'	=> sprintf( __( 'Search %s fields by label or name', 'elementor-extras' ), strtolower( $field_type ) ),
						'type' 			=> 'ee-query',
						'options' 		=> [],
						'label_block' 	=> false,
						'multiple' 		=> false,
						'post_type' 	=> $post_type,
						'query_type' 	=> $field_type,
						'query_options' => [
							'field_type'	=> [
								'date',
							],
							'show_group' => true,
						],
						'condition'		=> [
							'source'				=> 'posts',
							'post_dates_field_type' => $field_type,
							'post_type' 			=> $post_type,
						],
					];

					$this->add_control(
						'post_start_date_' . $field_type . '_' . $post_type,
						array_merge( $fields_options, [ 'label' => __( 'Start Date', 'elementor-extras' ), ] )
					);

					$this->add_control(
						'post_end_date_' . $field_type . '_' . $post_type,
						array_merge( $fields_options, [ 'label' => __( 'End Date', 'elementor-extras' ), ] )
					);
				}
			}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_calendar',
			[
				'label' 	=> __( 'Calendar', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'display_heading',
				[
					'label'		=> __( 'Display', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'skin',
				[
					'label' 		=> __( 'Skin', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'default',
					'options'	=> [
						'default' 	=> __( 'Default', 'elementor-extras' ),
						'compact' 	=> __( 'Compact', 'elementor-extras' ),
					],
					'prefix_class' 	=> 'ee-calendar-skin--',
				]
			);

			$this->add_control(
				'first_day',
				[	
					'label'		=> __( 'First Day', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '1',
					'options'	=> [
						'0' => __( 'Sunday', 'elementor-extras' ),
						'1' => __( 'Monday', 'elementor-extras' ),
						'2' => __( 'Tuesday', 'elementor-extras' ),
						'3' => __( 'Wednesday', 'elementor-extras' ),
						'4' => __( 'Thursday', 'elementor-extras' ),
						'5' => __( 'Friday', 'elementor-extras' ),
						'6' => __( 'Saturday', 'elementor-extras' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'constrain_start',
				[
					'label'		=> __( 'Earliest Month', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
						'dateFormat' => 'Y-m',
					],
					'label_block' => false,
					'default' 	=> '',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'constrain_end',
				[
					'label'		=> __( 'Latest Month', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
						'dateFormat' => 'Y-m',
					],
					'label_block' => false,
					'default' 	=> '',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'default_current_month',
				[
					'label' 		=> __( 'Default to Current Month', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'default_month',
				[
					'label'		=> __( 'Default Month', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => [
						'enableTime' => false,
						'dateFormat' => 'Y-m',
					],
					'condition'	=> [
						'default_current_month' => ''
					],
					'label_block' => false,
					'default' 	=> '',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'show_adjacent_months',
				[
					'label' 		=> __( 'Show Adjacent Days', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'navigation_heading',
				[
					'label'		=> __( 'Navigation', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'click_adjacent',
				[
					'label' 		=> __( 'Adjacent Click', 'elementor-extras' ),
					'description'	=> __( 'Clicking on days adjacent to current month navigates to corresponding month', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'condition'		=> [
						'show_adjacent_months!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'links_heading',
				[
					'label'		=> __( 'Links', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'	=> [
						'source' => 'posts',
					],
				]
			);

			$this->add_control(
				'link',
				[
					'label' 		=> __( 'Enable Links', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'	=> [
						'source' => 'posts',
					],
				]
			);

			$this->add_control(
				'link_is_external',
				[
					'label' 		=> __( 'Open in new window', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'	=> [
						'source' => 'posts',
					],
				]
			);

			$this->add_control(
				'link_no_follow',
				[
					'label' 		=> __( 'Add nofollow', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'	=> [
						'source' => 'posts',
					],
				]
			);

			$this->add_control(
				'days_heading',
				[
					'label'		=> __( 'Days', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'day_monday',
				[
					'label'		=> __( 'Monday', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Mon', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'day_tuesday',
				[
					'label'		=> __( 'Tuesday', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Tue', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'day_wednesday',
				[
					'label'		=> __( 'Wednesday', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Wed', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'day_thursday',
				[
					'label'		=> __( 'Thursday', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Thu', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'day_friday',
				[
					'label'		=> __( 'Friday', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Fri', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'day_saturday',
				[
					'label'		=> __( 'Saturday', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Sat', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'day_sunday',
				[
					'label'		=> __( 'Sunday', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Sun', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'link_archive',
				[
					'label' 		=> __( 'Link to Archive', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'condition'		=> [
						'source'				=> 'posts',
						'post_dates_field_type' => 'post_date',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'months_heading',
				[
					'label'		=> __( 'Months', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'month_january',
				[
					'label'		=> __( 'January', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'January', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_february',
				[
					'label'		=> __( 'February', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'February', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_march',
				[
					'label'		=> __( 'March', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'March', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_april',
				[
					'label'		=> __( 'April', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'April', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_may',
				[
					'label'		=> __( 'May', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'May', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_june',
				[
					'label'		=> __( 'June', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'June', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_july',
				[
					'label'		=> __( 'July', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'July', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_august',
				[
					'label'		=> __( 'August', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'August', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_september',
				[
					'label'		=> __( 'September', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'September', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_october',
				[
					'label'		=> __( 'October', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'October', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_november',
				[
					'label'		=> __( 'November', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'November', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'month_december',
				[
					'label'		=> __( 'December', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'December', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'events_heading',
				[
					'label'		=> __( 'Events List', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'event_order',
				[	
					'label'		=> __( 'Order', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'ASC',
					'options'	=> [
						'ASC'	=> __( 'Ascending', 'elementor-extras' ),
						'DESC'	=> __( 'Descending', 'elementor-extras' ),
					],
					'condition' => [
						'skin' => 'compact',
					],
				]
			);

			$this->add_control(
				'event_list_heading',
				[
					'label'		=> __( 'Heading', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Posts this month', 'elementor-extras' ),
					'condition' => [
						'skin' => 'compact',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'event_title_wrap',
				[
					'label' 		=> __( 'Title Wrapping', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'no-wrap',
					'options'	=> [
						'no-wrap' 	=> __( 'Single Line', 'elementor-extras' ),
						'wrap' 		=> __( 'Wrap', 'elementor-extras' ),
					],
					'prefix_class' 	=> 'ee-calendar-event-title--',
				]
			);

			$this->add_control(
				'event_date_format',
				[
					'label'		=> __( 'Date Format', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'description' => sprintf( '<a href="https://momentjs.com/docs/#/displaying/format/" target="_blank">%s</a>', __( 'Documentation on ISO date and time formatting', 'elementor-extras' ) ),
					'default' 	=> 'MMMM Do',
					'condition' => [
						'skin' => 'compact',
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_calendar',
			[
				'label' => __( 'Calendar', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'calendar_width',
				[
					'label' 	=> __( 'Max. Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 300,
							'max' 	=> 1000,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar' => 'max-width: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'calendar_padding',
				[
					'label' 	=> __( 'Padding', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar' => 'padding: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'calendar_spacing_horizontal',
				[
					'label' 	=> __( 'Horizontal Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 50,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__cell__content' => 'padding-left: {{SIZE}}px; padding-right: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'calendar_spacing_vertical',
				[
					'label' 	=> __( 'Vertical Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 50,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__cell__content' => 'padding-top: {{SIZE}}px; padding-bottom: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'calendar_align',
				[
					'label' 		=> __( 'Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
					'options' 		=> [
						'left' 			=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'calendar_background_color',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .clndr' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'calendar_border_radius',
				[
					'label' 	=> __( 'Border Radius', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 20,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .clndr,
						 {{WRAPPER}} .ee-calendar' => 'border-radius: {{SIZE}}px;',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'calendar_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-calendar',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'calendar_box_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-calendar',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'calendar_typography',
					'selector' 	=> '{{WRAPPER}} .ee-calendar__cell__content',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

			$this->add_control(
				'separators_heading',
				[
					'label'		=> __( 'Separators', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->start_controls_tabs( 'separators' );

			$this->start_controls_tab( 'separators_horizontal', [ 'label' => __( 'Horizontal', 'elementor-extras' ) ] );

				$this->add_control(
					'separators_horizontal_style',
					[
						'label' 	=> __( 'Style', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SELECT,
						'default'	=> '',
						'options' 	=> [
							'' 			=> __( 'None', 'elementor-extras' ),
							'solid' 	=> _x( 'Solid', 'Border Control', 'elementor-extras' ),
							'double' 	=> _x( 'Double', 'Border Control', 'elementor-extras' ),
							'dotted' 	=> _x( 'Dotted', 'Border Control', 'elementor-extras' ),
							'dashed' 	=> _x( 'Dashed', 'Border Control', 'elementor-extras' ),
							'groove' 	=> _x( 'Groove', 'Border Control', 'elementor-extras' ),
						],
						'selectors' => [
							'{{WRAPPER}} tr:not(:last-child) td.ee-calendar__cell' => 'border-bottom-style: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'separators_horizontal_width',
					[
						'label' 	=> __( 'Width', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default'	=> [
							'size'	=> 1,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 5,
								'step'	=> 1,
							],
						],
						'condition'	=> [
							'separators_horizontal_style!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} tr:not(:last-child) td.ee-calendar__cell' => 'border-bottom-width: {{SIZE}}px;',
						],
					]
				);

				$this->add_control(
					'separators_horizontal_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition'	=> [
							'separators_horizontal_style!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} tr:not(:last-child) td.ee-calendar__cell' => 'border-bottom-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'separators_vertical', [ 'label' => __( 'Vertical', 'elementor-extras' ) ] );

				$this->add_control(
					'separators_vertical_style',
					[
						'label' 	=> __( 'Style', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SELECT,
						'default'	=> '',
						'options' 	=> [
							'' 			=> __( 'None', 'elementor-extras' ),
							'solid' 	=> _x( 'Solid', 'Border Control', 'elementor-extras' ),
							'double' 	=> _x( 'Double', 'Border Control', 'elementor-extras' ),
							'dotted' 	=> _x( 'Dotted', 'Border Control', 'elementor-extras' ),
							'dashed' 	=> _x( 'Dashed', 'Border Control', 'elementor-extras' ),
							'groove' 	=> _x( 'Groove', 'Border Control', 'elementor-extras' ),
						],
						'selectors' => [
							'{{WRAPPER}} td:not(:first-child).ee-calendar__cell' => 'border-left-style: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'separators_vertical_width',
					[
						'label' 	=> __( 'Width', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default'	=> [
							'size'	=> 1,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 5,
								'step'	=> 1,
							],
						],
						'condition'	=> [
							'separators_vertical_style!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} td:not(:first-child).ee-calendar__cell' => 'border-left-width: {{SIZE}}px;',
						],
					]
				);

				$this->add_control(
					'separators_vertical_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition'	=> [
							'separators_vertical_style!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} td:not(:first-child).ee-calendar__cell' => 'border-left-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label' => __( 'Navigation', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'navigation_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-calendar__controls' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__controls' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_background_color',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__controls' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_month_spacing',
				[
					'label' 	=> __( 'Month Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 12,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}}.ee-calendar-arrows-position--left .ee-calendar__controls__month' => 'margin-left: {{SIZE}}px;',
						'{{WRAPPER}}.ee-calendar-arrows-position--sides .ee-calendar__controls__month' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px;',
						'{{WRAPPER}}.ee-calendar-arrows-position--right .ee-calendar__controls__month' => 'margin-right: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_text_align',
				[
					'label' 		=> __( 'Align Text', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__controls__month' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'navigation_typography',
					'selector' 	=> '{{WRAPPER}} .ee-calendar__controls__month',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

			$this->add_control(
				'navigation_buttons_heading',
				[
					'label'		=> __( 'Buttons', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'arrows_position',
				[	
					'label'		=> __( 'Position', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'sides',
					'options'	=> [
						'sides' => __( 'Sides', 'elementor-extras' ),
						'left' 	=> __( 'Left', 'elementor-extras' ),
						'right' => __( 'Right', 'elementor-extras' ),
					],
					'prefix_class' => 'ee-calendar-arrows-position--',
				]
			);

			$this->add_responsive_control(
				'navigation_buttons_spacing',
				[
					'label' 	=> __( 'Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 12,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'condition'	=> [
						'arrows_position!' => 'sides',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__button--previous' => 'margin-right: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'buttons_size',
				[
					'label' 	=> __( 'Size', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 6,
							'max' 	=> 48,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__controls__button' => 'font-size: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'buttons_padding',
				[
					'label' 	=> __( 'Padding', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 3,
							'step'	=> 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__controls__button' => 'padding: {{SIZE}}em;',
					],
				]
			);

			$this->add_control(
				'buttons_border_radius',
				[
					'label' 	=> __( 'Border Radius', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__controls__button' => 'border-radius: {{SIZE}}%;',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'buttons',
					'selector' 	=> '{{WRAPPER}} .ee-calendar__controls__button',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'buttons' );

			$this->start_controls_tab( 'buttons_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'button_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__controls__button' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__controls__button' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'buttons_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'button_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__controls__button:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__controls__button:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'navigation_separator_heading',
				[
					'label'		=> __( 'Separator', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'navigation_separator_style',
				[
					'label' 	=> __( 'Style', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> '',
					'options' 	=> [
						'' 			=> __( 'None', 'elementor-extras' ),
						'solid' 	=> _x( 'Solid', 'Border Control', 'elementor-extras' ),
						'double' 	=> _x( 'Double', 'Border Control', 'elementor-extras' ),
						'dotted' 	=> _x( 'Dotted', 'Border Control', 'elementor-extras' ),
						'dashed' 	=> _x( 'Dashed', 'Border Control', 'elementor-extras' ),
						'groove' 	=> _x( 'Groove', 'Border Control', 'elementor-extras' ),
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__controls' => 'border-bottom-style: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'navigation_separator_width',
				[
					'label' 	=> __( 'Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 1,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step'	=> 1,
						],
					],
					'condition'	=> [
						'navigation_separator_style!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__controls' => 'border-bottom-width: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'navigation_separator_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'condition'	=> [
						'navigation_separator_style!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__controls' => 'border-bottom-color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_header',
			[
				'label' => __( 'Header', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'header_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__table__head' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'header_background_color',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__table__head' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'header_text_align',
				[
					'label' 		=> __( 'Align Text', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__table__head .ee-calendar__cell__content' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'header_days',
					'selector' 	=> '{{WRAPPER}} .ee-calendar__table__head .ee-calendar__cell__content',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

			$this->add_control(
				'header_separator_heading',
				[
					'label'		=> __( 'Separator', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'header_separator_style',
				[
					'label' 	=> __( 'Style', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> '',
					'options' 	=> [
						'' 			=> __( 'None', 'elementor-extras' ),
						'solid' 	=> _x( 'Solid', 'Border Control', 'elementor-extras' ),
						'double' 	=> _x( 'Double', 'Border Control', 'elementor-extras' ),
						'dotted' 	=> _x( 'Dotted', 'Border Control', 'elementor-extras' ),
						'dashed' 	=> _x( 'Dashed', 'Border Control', 'elementor-extras' ),
						'groove' 	=> _x( 'Groove', 'Border Control', 'elementor-extras' ),
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__table__head' => 'border-bottom-style: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'header_separator_width',
				[
					'label' 	=> __( 'Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 1,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step'	=> 1,
						],
					],
					'condition'	=> [
						'header_separator_style!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__table__head' => 'border-bottom-width: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'header_separator_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'condition'	=> [
						'header_separator_style!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__table__head' => 'border-bottom-color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_days',
			[
				'label' => __( 'Days', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'days_padding',
				[
					'label' 	=> __( 'Padding', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 12,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__month' => 'padding: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'days_border_radius',
				[
					'label' 	=> __( 'Border Radius', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 1000,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__day__wrapper' => 'border-radius: {{SIZE}}px;',
						'{{WRAPPER}} .ee-calendar__day__header' => 'border-radius: {{SIZE}}px {{SIZE}}px 0 0;',
					],
				]
			);

			$this->add_responsive_control(
				'days_text_align',
				[
					'label' 		=> __( 'Align Text', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'condition' => [
						'skin' => 'default',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__day__content' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'days_all_heading',
				[
					'label'		=> __( 'All Days', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'days_all_padding_horizontal',
				[
					'label' 	=> __( 'Horizontal Padding', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 50,
							'step'	=> 1,
						],
					],
					'condition' => [
						'skin' => 'default',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__day__header,
						 {{WRAPPER}} .ee-calendar__day__event__name,
						 {{WRAPPER}} .ee-calendar__day__event__name:before' => 'padding-left: {{SIZE}}px; padding-right: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'days_all_padding_vertical',
				[
					'label' 	=> __( 'Vertical Padding', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 50,
							'step'	=> 1,
						],
					],
					'condition' => [
						'skin' => 'default',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__day__header,
						 {{WRAPPER}} .ee-calendar__day__event__name,
						 {{WRAPPER}} .ee-calendar__day__event__name:before' => 'padding-top: {{SIZE}}px; padding-bottom: {{SIZE}}px;',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'days',
					'selector' 	=> '{{WRAPPER}} .ee-calendar__day__wrapper',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'days' );

			$this->start_controls_tab( 'days_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'day_all_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day__wrapper' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_all_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day__wrapper' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'days_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'day_all_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day__wrapper:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_all_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day__wrapper:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'days_event_heading',
				[
					'label'		=> __( 'Event', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->start_controls_tabs( 'days_event' );

			$this->start_controls_tab( 'days_event_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'day_event_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#ffffff',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper,
							 {{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__event__name:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_event_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper,
							 {{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__event__name:before' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'day_event_box_shadow',
						'selector' 	=> '{{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper',
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'days_event_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'day_event_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper:hover,
							 {{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_event_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper:hover,
							 {{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name:before' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'day_event_box_shadow_hover',
						'selector' 	=> '{{WRAPPER}} .ee-calendar__day--event .ee-calendar__day__wrapper:hover',
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'days_today_heading',
				[
					'label'		=> __( 'Today', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->start_controls_tabs( 'days_today' );

			$this->start_controls_tab( 'days_today_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'day_today_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#ffffff',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__wrapper,
							 {{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__event__name:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_today_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_SECONDARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__wrapper,
							 {{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__event__name:before' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'days_today_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'day_today_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__wrapper:hover,
							 {{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_today_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__wrapper:hover,
							 {{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--today .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name:before' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'days_passed_heading',
				[
					'label'		=> __( 'Passed', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->start_controls_tabs( 'days_passed' );

			$this->start_controls_tab( 'days_passed_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'day_passed_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__wrapper,
							 {{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__event__name:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_passed_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__wrapper,
							 {{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__event__name:before' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'days_passed_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'day_passed_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__wrapper:hover,
							 {{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_passed_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__wrapper:hover,
							 {{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--passed .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name:before' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'days_passed_events_heading',
				[
					'label'		=> __( 'Passed Events', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->start_controls_tabs( 'days_passed_events' );

			$this->start_controls_tab( 'days_passed_events_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'day_passed_events_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__wrapper,
							 {{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__event__name:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_passed_events_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__wrapper,
							 {{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__event__name:before' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'days_passed_events_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'day_passed_events_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__wrapper:hover,
							 {{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name:before' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'day_passed_events_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__wrapper:hover,
							 {{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name,
							 {{WRAPPER}} .ee-calendar__day--passed.ee-calendar__day--event .ee-calendar__day__wrapper:hover .ee-calendar__day__event__name:before' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'days_adjacent_heading',
				[
					'label'		=> __( 'Adjacent', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'day_adjacent_opacity',
				[
					'label' 	=> __( 'Opacity', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__day--adjacent .ee-calendar__day__wrapper' => 'opacity: {{SIZE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_events',
			[
				'label' => __( 'Events', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin' => 'compact',
				],
			]
		);

			$this->add_control(
				'events_header_heading',
				[
					'label'		=> __( 'Header', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'condition' => [
						'skin' => 'compact',
					],
				]
			);

			$this->add_responsive_control(
				'events_header_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-calendar__events__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'events_header_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__events__header' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'events_header_background_color',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__events__header' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'events_header_title_spacing',
				[
					'label' 	=> __( 'Title Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size' 	=> 12,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'condition' => [
						'skin' => 'compact',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__events__header__title' => 'margin-right: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'events_header_text_align',
				[
					'label' 		=> __( 'Align Text', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'left',
					'options' 		=> [
						'left' 			=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'condition' => [
						'skin' => 'compact',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__table__head .ee-calendar__events__header__title' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'events_header',
					'selector' 	=> '{{WRAPPER}} .ee-calendar__events__header__title',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

			$this->add_control(
				'events_separator_heading',
				[
					'label'		=> __( 'Separator', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'skin' => 'compact',
					],
				]
			);

			$this->add_control(
				'events_separator_style',
				[
					'label' 	=> __( 'Style', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> '',
					'options' 	=> [
						'' 			=> __( 'None', 'elementor-extras' ),
						'solid' 	=> _x( 'Solid', 'Border Control', 'elementor-extras' ),
						'double' 	=> _x( 'Double', 'Border Control', 'elementor-extras' ),
						'dotted' 	=> _x( 'Dotted', 'Border Control', 'elementor-extras' ),
						'dashed' 	=> _x( 'Dashed', 'Border Control', 'elementor-extras' ),
						'groove' 	=> _x( 'Groove', 'Border Control', 'elementor-extras' ),
					],
					'condition' => [
						'skin' => 'compact',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__events__event:not(:last-child)' => 'border-bottom-style: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'events_separator_width',
				[
					'label' 	=> __( 'Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 1,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step'	=> 1,
						],
					],
					'condition'	=> [
						'skin' => 'compact',
						'events_separator_style!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__events__event:not(:last-child)' => 'border-bottom-width: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'events_separator_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'condition'	=> [
						'skin' => 'compact',
						'events_separator_style!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-calendar__events__event:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'events_list_heading',
				[
					'label'		=> __( 'List', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'skin' => 'compact',
					],
				]
			);

			$this->add_responsive_control(
				'event_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-calendar__events__event' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'event_tabs' );

			$this->start_controls_tab( 'event_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'event_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__events__event' => 'color: {{VALUE}};',
						],
						'condition' => [
							'skin' => 'compact',
						],
					]
				);

				$this->add_control(
					'event_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__events__event' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'skin' => 'compact',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'event_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'event_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__events__event:hover' => 'color: {{VALUE}};',
						],
						'condition' => [
							'skin' => 'compact',
						],
					]
				);

				$this->add_control(
					'event_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-calendar__events__event:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'skin' => 'compact',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		switch ( $settings['source'] ) {
			case 'manual' :
				$this->setup_manual();
				break;

			case 'posts' :
				$this->get_posts_data();
				break;

			default :
				$this->setup_manual();
		}

		$this->render_data();
	}

	/**
	 * Get Post Data
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function get_posts_data() {
		$settings 		= $this->get_settings_for_display();
		$events 		= [];
		$args 			= [
			'post_type' 		=> $settings['post_type'],
			'post_status' 		=> 'publish',
			'posts_per_page' 	=> -1,
			'numberposts'		=> -1,
			'suppress_filters'  => false,
			'order'				=> $settings['event_order'],
		];

		if ( 'post_date' !== $settings['post_dates_field_type'] ) {
			$data_type = 'field';
		} else {
			$data_type = 'post';
		}

		$args = call_user_func_array( [ $this, 'parse_' . $data_type . '_query_args' ], [ $args, $settings['post_dates_field_type'] ] );

		/**
		 * Posts Args Filter
		 *
		 * Filters the query args when fetching posts
		 *
		 * @since 2.2.0
		 * @param array 			$args 		The query args
		 * @param WP_Post 			$post 		The widget settings
		 */
		$args = apply_filters( 'elementor_extras/widgets/calendar/events/query/args', $args, $settings );

		$posts = get_posts( $args );

		foreach ( $posts as $post ) {

			$field_data = call_user_func_array( [ $this, 'get_' . $data_type . '_dates_args' ], [ $post ] );

			if ( ! $field_data || ! $field_data['start_date'] ) {
				continue;
			}

			$event_post_id 		= $post->ID;
			$event_title 		= $post->post_title;
			$event_link 		= '' !== $settings['link'] ? get_permalink( $post->ID ) : '';
			$event_target 		= '' !== $settings['link_is_external'] ? '_blank' : '';
			$event_rel 			= '' !== $settings['link_no_follow'] ? 'nofollow' : '';
			$event_start_date 	= $field_data['start_date'];
			$event_end_date 	= $field_data['end_date'];
			$event_archive 		= $field_data['archive'];

			$event = [
				'post_id' 	=> $event_post_id,
				'title' 	=> $event_title,
				'start' 	=> $event_start_date,
				'end' 		=> $event_end_date,
				'link'		=> $event_link,
				'target' 	=> $event_target,
				'rel'		=> $event_rel,
				'archive' 	=> $event_archive,
			];

			/**
			 * Posts Events Filter
			 *
			 * Provides access to events setup from posts
			 *
			 * @since 2.2.0
			 * @param array 			$event 		The event settings
			 * @param WP_Post 			$post 		The event post object
			 */
			$events[] = apply_filters( 'elementor_extras/widgets/calendar/events/event', $event, $post );
		}

		/**
		 * Posts Events Filter
		 *
		 * Provides access to events setup from posts
		 *
		 * @since 2.2.0
		 * @param array 			$events 	The array of events
		 * @param array 			$settings 	The widget settings
		 */
		$this->_events = apply_filters( 'elementor_extras/widgets/calendar/events', $events, $settings );
	}

	/**
	 * Parse query args for posts
	 *
	 * @since  2.2.42
	 * @return array
	 */
	protected function parse_post_query_args( $args, $type ) {
		return $args;
	}

	/**
	 * Parse query args for custom fields
	 *
	 * @since  2.2.42
	 * @return array
	 */
	protected function parse_field_query_args( $args, $type ) {

		if ( 'acf' !== $type ) {
			return $args;
		}

		$settings = $this->get_settings();

		$start_date_key = $settings['post_start_date_' . $settings['post_dates_field_type'] . '_' . $settings['post_type'] ];

		if ( $start_date_key ) {
			$field_object = get_field_object( $start_date_key );

			$args['orderby'] = 'meta_value';
			$args['meta_key'] = $field_object['name'];
		}

		return $args;
	}

	/**
	 * Get Post Dates Args
	 *
	 * @since  2.2.42
	 * @return array
	 */
	protected function get_post_dates_args( $post ) {
		$data = [
			'start_date' 	=> false,
			'end_date' 		=> false,
			'archive' 		=> false,
		];

		if ( $post ) {
			$data['start_date'] = $end_date = $post->post_date;
			$data['archive'] = get_day_link( get_the_date( 'Y', $post ), get_the_date( 'm', $post ), get_the_date( 'd', $post ) );
		}

		return $data;
	}

	/**
	 * Get Fields Dates Args
	 *
	 * @since  2.2.42
	 * @return array|bool
	 */
	protected function get_field_dates_args( $post ) {
		$settings 		= $this->get_settings();
		$customfields 	= new CustomFieldsModule();
		$data 			= [
			'start_date' 	=> false,
			'end_date' 		=> false,
			'archive' 		=> false,
		];

		if ( $post ) {
			$field = $customfields->get_component( $settings['post_dates_field_type'] );

			if ( ! $field ) {
				return false;
			}

			$start_date_key 	= $settings['post_start_date_' . $settings['post_dates_field_type'] . '_' . $settings['post_type'] ];
			$end_date_key 		= $settings['post_end_date_' . $settings['post_dates_field_type'] . '_' . $settings['post_type'] ];

			$data['start_date'] = $field->get_field_value( $post->ID, $start_date_key );
			$data['end_date'] 	= $field->get_field_value( $post->ID, $end_date_key );
			$data['archive'] 	= '';
		}

		return $data;
	}

	/**
	 * Setup Manual
	 *
	 * Sets up events data
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function setup_manual() {
		$settings = $this->get_settings_for_display();
		$events = [];

		if ( empty( $settings['events'] ) )
			return;

		foreach ( $settings['events'] as $index => $event ) {
			$events[] = [
				'title' 	=> $event['title'],
				'start' 	=> $event['start'],
				'end' 		=> $event['end'],
				'link'		=> ( '' !== $event['link']['url'] ) ? $event['link']['url'] : '',
				'target' 	=> $event['link']['is_external'] ? '_blank' : '_self',
				'rel'		=> ! empty( $event['link']['nofollow'] ) ? 'nofollow' : '',
				'archive' 	=> false,
			];
		}

		/**
		 * Manual Events Filter
		 *
		 * Provides access to manually set events date
		 *
		 * @since 2.2.0
		 * @param string 			$events 	The array of events
		 * @param string 			$settings 	The widget settings
		 */
		$this->_events = apply_filters( 'elementor_extras/widgets/calendar/events/manual', $events, $settings );
	}

	/**
	 * Render data
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_data() {
		$settings = $this->get_settings_for_display();

		if ( empty( $this->_events ) ) {
			echo $this->render_placeholder( [
				'body' => __( 'You have no events in your calendar. Check the settings and make sure the source fields for the dates of the events are setup correctly.', 'elementor-extras' ),
			] );

			return;
		}

		$this->add_render_attribute( [
			'calendar' => [
				'class' => [
					'ee-calendar',
				],
			],
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'calendar' ); ?>>
			<?php foreach ( $this->_events as $index => $event ) {

				if ( ! $event['start'] )
					continue;
				
				$title 		= $event['title'];
				$start 		= $event['start'];
				$end 		= ( ! empty( $event['end'] ) ) ? $event['end'] : $event['start'];
				$link 		= $event['link'];
				$target 	= $event['target'];
				$rel 		= $event['rel'];
				$archive 	= $event['archive'];

				$event_key 	= $this->get_repeater_setting_key( 'event', 'events', $index );

				$this->add_render_attribute( $event_key, [
					'class' 			=> 'ee-calendar-event',
					'data-archive' 		=> $archive,
					'data-target' 		=> $target,
					'data-rel' 			=> $rel,
					'data-link' 		=> $link,
					'data-start' 		=> $start,
					'data-end' 			=> $end,
					'data-before'		=> $this->get_before_title( $event ),
					'data-after'		=> $this->get_after_title( $event ),
				] );
			?>
			<div <?php echo $this->get_render_attribute_string( $event_key ); ?>><?php
				echo $title;
			?></div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Output before title
	 *
	 * @since  2.2.0
	 * @return void
	 */
	public function get_before_title( $event ) {

		ob_start();

		/**
		 * Before title.
		 *
		 * Fires before printing the title of the event.
		 *
		 * @since 2.2.0
		 *
		 * @param array $event The event data.
		 */
		do_action( 'elementor_extras/widgets/calendar/event/before_title', $event );

		return ob_get_clean();
	}

	/**
	 * Output after title
	 *
	 * @since  2.2.0
	 * @return void
	 */
	public function get_after_title( $event ) {

		ob_start();

		/**
		 * Before title.
		 *
		 * Fires after printing the title of the event.
		 *
		 * @since 2.2.0
		 *
		 * @param array $event The event data.
		 */
		do_action( 'elementor_extras/widgets/calendar/event/after_title', $event );

		return ob_get_clean();
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _content_template() {}
}
