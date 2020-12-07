<?php
namespace ElementorExtras\Modules\Popup\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Group_Control_Transition;
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\Popup\Skins;
use ElementorExtras\Modules\Popup\Module as Module;
use ElementorExtras\Modules\Image\Module as ImageModule;
use ElementorExtras\Modules\TemplatesControl\Module as TemplatesControl;

// Elementor Classes
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Popup
 *
 * @since 2.0.0
 */
class Popup extends Extras_Widget {

	/**
	 * Has template content
	 *
	 * @since  2.0.0
	 * @var    bool
	 */
	protected $_has_template_content = false;

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-popup';
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
		return __( 'Popup', 'elementor-extras' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-popup';
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
			'magnific-popup',
		];
	}

	/**
	 * Get Style Depends
	 * 
	 * A list of css files that the widgets is depended in
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_style_depends() {
		return [
			'magnific-popup',
		];
	}

	/**
	 * Register Skins
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_skins() {
		$this->add_skin( new Skins\Skin_Classic( $this ) );
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_controls() {
		$this->register_content_controls();
	}

	/**
	 * Register Content Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_content_controls() {

		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'popup_open',
				[
					'label' 		=> __( 'Keep Open in Editor', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			if ( current_user_can( 'administrator' ) ) {
				$this->add_control(
					'popup_open_admin',
					[
						'label' 		=> __( 'Always Show for Admins', 'elementor-extras' ),
						'description' 	=> __( 'Have the popup open every time you visit the page if you\'re an Admin. This will help you test the functionality on the frontend without actually losing the popup if it\'s not persistent.', 'elementor-extras' ),	
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'yes',
						'label_on' 		=> __( 'Yes', 'elementor-extras' ),
						'label_off' 	=> __( 'No', 'elementor-extras' ),
						'frontend_available' => true,
					]
				);
			}

			$this->add_control(
				'refresh_widgets',
				[
					'label' 		=> __( 'Refresh Popup Widgets', 'elementor-extras' ),
					'description'	=> __( 'If you are using templates as content for the popup, this option will refresh any frontend functionality for all elements inside the popup when opened. Turn this off if you notice strange behaviour or broken elements inside the popup.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_disable_on',
				[
					'label' 	=> __( 'Disable on', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> '',
					'options' 	=> [
						'' 		=> __( 'None', 'elementor-extras' ),
						'1025' 	=> __( 'Mobile & Tablet', 'elementor-extras' ),
						'768' 	=> __( 'Mobile', 'elementor-extras' ),
					],
					'separator' => 'before',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_animation',
				[
					'label' 	=> __( 'Animation', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'zoom-in',
					'options' 	=> Module::get_animation_options(),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_preloader',
				[
					'label' 		=> __( 'Preloader', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'separator'		=> 'before',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_prevent_scroll',
				[
					'label' 		=> __( 'Prevent Page Scroll', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_fixed',
				[
					'label' 		=> __( 'Fix On Scroll', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_no_overlay',
				[
					'label' 		=> __( 'Remove Overlay', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_close_on_content',
				[
					'label' 		=> __( 'Close On Content Click', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'	=> [
						'popup_type!' => 'iframe',
					],
				]
			);

			$this->add_control(
				'popup_close_on_bg',
				[
					'label' 		=> __( 'Close On Overlay Click', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
					'condition' => [
						'popup_no_overlay' => ''
					],
				]
			);

			$this->add_control(
				'popup_close_on_escape',
				[
					'label' 		=> __( 'Close On Escape Key', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_vertical_fit',
				[
					'label' 		=> __( 'Fit Vertically', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'	=> [
						'popup_type' => 'image',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_trigger',
			[
				'label' => __( 'Trigger', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'popup_trigger',
				[
					'label' 	=> __( 'Trigger', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'click',
					'options' 	=> [
						'click' 	=> __( 'Click', 'elementor-extras' ),
						'instant' 	=> __( 'Instant', 'elementor-extras' ),
						'scroll' 	=> __( 'Scroll', 'elementor-extras' ),
						'intent' 	=> __( 'Exit Intent', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_click_target',
				[
					'label' 	=> __( 'Click Target', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'text',
					'options' 	=> [
						'text' 	=> __( 'Text', 'elementor-extras' ),
						'id' 	=> __( 'Element ID', 'elementor-extras' ),
						'class' => __( 'Element Class', 'elementor-extras' ),
					],
					'condition'	=> [
						'popup_trigger' => 'click',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_click_element_id',
				[
					'label' 		=> __( 'Element CSS ID', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> '',
					'label_block' 	=> false,
					'frontend_available' => true,
					'title' 		=> __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor-extras' ),
					'condition'	=> [
						'popup_trigger' => 'click',
						'popup_click_target' => 'id',
					],
				]
			);

			$this->add_control(
				'popup_click_element_class',
				[
					'label' 		=> __( 'Element CSS Class', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> '',
					'label_block' 	=> false,
					'frontend_available' => true,
					'title' 		=> __( 'Add your custom class WITHOUT the DOT key. e.g: my-class', 'elementor-extras' ),
					'condition'	=> [
						'popup_trigger' => 'click',
						'popup_click_target' => 'class',
					],
				]
			);

			$this->add_control(
				'popup_scroll_type',
				[
					'label'			=> __( 'Scroll', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'amount',
					'separator' => 'before',
					'options' 	=> [
						'amount' 	=> __( 'Amount', 'elementor-extras' ),
						'element' 	=> __( 'Element', 'elementor-extras' ),
					],
					'condition'	=> [
						'popup_trigger' => 'scroll',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_scroll_amount',
				[
					'label'			=> __( 'Amount (px)', 'elementor-extras' ),
					'type'			=> Controls_Manager::NUMBER,
					'dynamic'		=> [ 'active' => true ],
					'default'		=> 200,
					'min'			=> 0,
					'step'			=> 10,
					'frontend_available' => true,
					'condition'	=> [
						'popup_trigger' => 'scroll',
						'popup_scroll_type' => 'amount',
					]
				]
			);

			$this->add_control(
				'popup_scroll_element',
				[
					'label' 		=> __( 'Element CSS ID', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> '',
					'label_block' 	=> false,
					'frontend_available' => true,
					'title' 		=> __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor-extras' ),
					'condition'	=> [
						'popup_trigger' => 'scroll',
						'popup_scroll_type' => 'element',
					]
				]
			);

			$this->add_control(
				'popup_delay',
				[
					'label'			=> __( 'Delay (ms)', 'elementor-extras' ),
					'type'			=> Controls_Manager::NUMBER,
					'separator' 	=> 'before',
					'default'		=> 3000,
					'min'			=> 0,
					'step'			=> 1000,
					'frontend_available' => true,
					'condition'	=> [
						'popup_trigger' => 'instant',
					]
				]
			);

			$this->add_control(
				'popup_intent_sensitivity',
				[
					'label' 		=> __( 'Intent Sensitivity', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'separator'		=> 'before',
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 1000,
						],
					],
					'frontend_available' => true,
					'condition'	=> [
						'popup_trigger' => 'intent',
					]
				]
			);

			$this->add_control(
				'popup_persist',
				[
					'label' 		=> __( 'Persist', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'description'	=> __( 'Turn this off if you want the popup to not show again after opening a number of times.', 'elementor-extras' ),
					'default' 		=> 'yes',
					'frontend_available' => true,
					'condition'	=> [
						'popup_trigger!' => 'click',
					]
				]
			);

			$this->add_control(
				'popup_times',
				[
					'label'			=> __( 'Max. Times to Show', 'elementor-extras' ),
					'description'   => __( 'How many times should the popup show at most for the specified trigger.', 'elementor-extras' ),
					'type'			=> Controls_Manager::NUMBER,
					'default'		=> 1,
					'min'			=> 1,
					'frontend_available' => true,
					'condition'	=> [
						'popup_trigger!' => 'click',
						'popup_persist' => '',
					],
				]
			);

			$this->add_control(
				'popup_days',
				[
					'label'			=> __( 'Days', 'elementor-extras' ),
					'description'   => __( 'How many days should the popup not show for a user after last open.', 'elementor-extras' ),
					'type'			=> Controls_Manager::NUMBER,
					'default'		=> '30',
					'min'			=> 0,
					'step'			=> 300,
					'frontend_available' => true,
					'condition'	=> [
						'popup_trigger!' => 'click',
						'popup_persist' => '',
					]
				]
			);

			$this->add_control(
				'popup_trigger_heading',
				[
					'label' 	=> __( 'Content', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'	=> [
						'popup_trigger' => 'click',
						'popup_click_target' => 'text',
					],
				]
			);

			$this->add_control(
				'popup_trigger_text',
				[
					'label' 	=> __( 'Text', 'elementor-extras' ),
					'dynamic'	=> [ 'active' => true ],
					'type' 		=> Controls_Manager::TEXT,
					'default'	=> __( 'Open modal', 'elementor-extras' ),
					'condition'	=> [
						'popup_trigger' => 'click',
						'popup_click_target' => 'text',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'popup_type',
				[
					'label' 	=> __( 'Type', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'text',
					'options' 	=> [
						'text' 		=> __( 'Text', 'elementor-extras' ),
						'image' 	=> __( 'Image', 'elementor-extras' ),
						'template' 	=> __( 'Template', 'elementor-extras' ),
						'iframe' 	=> __( 'Iframe', 'elementor-extras' ),
						'url' 		=> __( 'URL', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_title',
				[
					'label' 	=> __( 'Title', 'elementor-extras' ),
					'dynamic'	=> [ 'active' => true ],
					'type' 		=> Controls_Manager::TEXT,
					'default'	=> __( 'Popup Title', 'elementor-extras' ),
					'separator' => 'before',
					'condition'	=> [
						'popup_type' => 'text',
					]
				]
			);

			$this->add_control(
				'popup_title_tag',
				[
					'label' 	=> __( 'Title HTML Tag', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'h1' 	=> __( 'H1', 'elementor-extras' ),
						'h2' 	=> __( 'H2', 'elementor-extras' ),
						'h3' 	=> __( 'H3', 'elementor-extras' ),
						'h4' 	=> __( 'H4', 'elementor-extras' ),
						'h5' 	=> __( 'H5', 'elementor-extras' ),
						'h6' 	=> __( 'H6', 'elementor-extras' ),
						'div' 	=> __( 'div', 'elementor-extras' ),
						'span' 	=> __( 'span', 'elementor-extras' ),
						'p' 	=> __( 'p', 'elementor-extras' ),
					],
					'default' => 'h1',
					'condition'	=> [
						'popup_type' => 'text',
					]
				]
			);

			$this->add_control(
				'popup_content',
				[
					'label' 	=> __( 'Content', 'elementor-extras' ),
					'type' 		=> Controls_Manager::WYSIWYG,
					'dynamic'	=> [ 'active' => true ],
					'default' 	=> __( 'I am the content of a popup', 'elementor-extras' ),
					'condition'	=> [
						'popup_type' => 'text',
					]
				]
			);

			$this->add_control(
				'popup_iframe_type',
				[
					'label' => __( 'Iframe Type', 'elementor-extras' ),
					'type' => Controls_Manager::SELECT,
					'options' 	=> [
						'video' => __( 'Video', 'elementor-extras' ),
						'map' 	=> __( 'Google Map', 'elementor-extras' ),
					],
					'separator' => 'before',
					'default' => 'video',
					'condition'	=> [
						'popup_type' => 'iframe',
					]
				]
			);

			$this->add_control(
				'popup_video_url',
				[
					'label' 	=> __( 'YouTube Video URL', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'	=> [ 'active' => true ],
					'default'	=> 'https://www.youtube.com/watch?v=9uOETcuFjbE',
					'condition'	=> [
						'popup_type' => 'iframe',
						'popup_iframe_type' => 'video',
					]
				]
			);

			$this->add_control(
				'popup_map_url',
				[
					'label' 	=> __( 'Google Map URL', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'	=> [ 'active' => true ],
					'default'	=> 'https://maps.google.com/maps?q=221B+Baker+Street,+London,+United+Kingdom&hl=en&t=v&hnear=221B+Baker+St,+London+NW1+6XE,+United+Kingdom',
					'condition'	=> [
						'popup_type' => 'iframe',
						'popup_iframe_type' => 'map',
					]
				]
			);

			$this->add_control(
				'popup_image',
				[
					'label' 		=> __( 'Choose Image', 'elementor-extras' ),
					'type' 			=> Controls_Manager::MEDIA,
					'dynamic' 		=> [
						'active' 	=> true,
					],
					'default' 		=> [
						'url' 		=> Utils::get_placeholder_image_src(),
					],
					'separator' => 'before',
					'condition'		=> [
						'popup_type' => 'image',
					]
				]
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'popup_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
					'default' 	=> 'large',
					'separator' => 'none',
					'condition'	=> [
						'popup_type' => 'image',
					]
				]
			);

			$this->add_control(
				'popup_image_caption_type',
				[
					'label' 		=> __( 'Caption', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'options' 		=> [
						'' 				=> __( 'None', 'elementor-extras' ),
						'title' 		=> __( 'Title', 'elementor-extras' ),
						'caption' 		=> __( 'Caption', 'elementor-extras' ),
						'description' 	=> __( 'Description', 'elementor-extras' ),
					],
					'condition'	=> [
						'popup_type' => 'image',
					]
				]
			);

			$this->add_control(
				'popup_template',
				[
					'label' 		=> __( 'Template', 'elementor-extras' ),
					'type' 			=> 'ee-query',
					'query_type' 	=> 'templates',
					'label_block' 	=> false,
					'multiple' 		=> false,
					'condition' => [
						'popup_type' => 'template',
					],
				]
			);

			$this->add_control(
				'popup_url',
				[
					'label' 	=> __( 'URL', 'elementor-extras' ),
					'type' 		=> Controls_Manager::URL,
					'separator' => 'before',
					'dynamic'	=> [ 'active' => true ],
					'default'	=> [
						'url' => 'https://www.google.com/',
					],
					'condition'	=> [
						'popup_type' => 'url',
					],
				]
			);

			$this->add_control(
				'popup_url_container',
				[
					'label' 		=> __( 'Content Selector', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> '',
					'frontend_available' => true,
					'title' 		=> __( 'If you want to restrict content from the fetched url to a certain container, add a selector for that container here.', 'elementor-extras' ),
					'condition'	=> [
						'popup_type' => 'url',
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_close',
			[
				'label' => __( 'Close', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'popup_close_icon_heading',
				[
					'label' => __( 'Icon', 'elementor-extras' ),
					'type' 	=> Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'popup_close_position',
				[
					'label' 		=> __( 'Position', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'outside',
					'options' 		=> [
						''	 			=> __( 'Hide', 'elementor-extras' ),
						'inside' 		=> __( 'Inside', 'elementor-extras' ),
						'outside' 		=> __( 'Outside', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'popup_close_halign',
				[
					'label' 		=> __( 'Horizontal Placement', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'right',
					'options' 		=> [
						'left'    		=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-right',
						],
					],
					'frontend_available' => true,
					'condition'			=> [
						'popup_close_position!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'popup_close_valign',
				[
					'label' 		=> __( 'Vertical Placement', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'top',
					'options' 		=> [
						'top'    		=> [
							'title' 	=> __( 'Top', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'bottom' 		=> [
							'title' 	=> __( 'Bottom', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
					],
					'frontend_available' => true,
					'condition'			=> [
						'popup_close_position!' => '',
					],
				]
			);

			$this->add_control(
				'popup_close_button_heading',
				[
					'label' => __( 'Button', 'elementor-extras' ),
					'type' 	=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
					],
				]
			);

			$this->add_control(
				'popup_close_button_position',
				[
					'label' 		=> __( 'Position', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'default',
					'options' 		=> [
						'' 				=> __( 'Hide', 'elementor-extras' ),
						'default' 		=> __( 'In Footer', 'elementor-extras' ),
						'custom' 		=> __( 'Custom Selector', 'elementor-extras' ),
					],
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'popup_close_button_text',
				[
					'label' 	=> __( 'Button Text', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'	=> [ 'active' => true ],
					'default'	=> __( 'Close', 'elementor-extras' ),
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'default',
					],
				]
			);

			$this->add_control(
				'popup_close_button_custom_notice',
				[
					'label' => false,
					'type' 	=> Controls_Manager::RAW_HTML,
					'raw' 	=> __( 'Add your custom selector below and make sure the element resides inside the content of the popup. If you\'re using a template, edit it with Elementor and add the class to an element inside it.', 'elementor-extras' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'custom',
					],
				]
			);

			$this->add_control(
				'popup_close_button_selector',
				[
					'label' 		=> __( 'Element Selector', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> '',
					'label_block' 	=> false,
					'frontend_available' => true,
					'title' 		=> __( 'Add your custom id or class WITH the Pound or Dot key. e.g: #my-id or .my-class', 'elementor-extras' ),
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'custom',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_popup',
			[
				'label' => __( 'Popup', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'popup_valign',
				[
					'label' 		=> __( 'Vertical Placement', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'middle',
					'options' 		=> [
						'top'    		=> [
							'title' 	=> __( 'Top', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'middle' 		=> [
							'title' 	=> __( 'Middle', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'bottom' 		=> [
							'title' 	=> __( 'Bottom', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'popup_width',
				[
					'label' 		=> __( 'Max. Width', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%' ],
					'range' 		=> [
						'%' 		=> [
							'min' => 0,
							'max' => 100,
						],
						'px' 		=> [
							'min' => 100,
							'max' => 1000,
						],
					],
					'selectors' 	=> [
						'.ee-mfp-popup--overlay.mfp-wrap.ee-mfp-popup-{{ID}} .mfp-content,
						 .ee-mfp-popup--no-overlay.mfp-wrap.ee-mfp-popup-{{ID}}' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'popup_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' 	=> [
						'.mfp-wrap.ee-mfp-popup-{{ID}} .ee-popup__content,
						 .mfp-wrap.ee-mfp-popup-{{ID}} .mfp-figure,
						 .mfp-wrap.ee-mfp-popup-{{ID}} .mfp-iframe' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'popup_background',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-popup__content' => 'background-color: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_control(
				'popup_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-popup__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'popup_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__content,
									.ee-mfp-popup-{{ID}} .mfp-figure,
									.ee-mfp-popup-{{ID}} .mfp-iframe',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'popup_box_shadow',
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__content,
									.ee-mfp-popup-{{ID}} .mfp-figure',
					'separator'	=> '',
				]
			);

			$this->add_control(
				'popup_overlay_heading',
				[
					'label' 	=> __( 'Overlay', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'popup_no_overlay' => ''
					],
				]
			);

			$this->add_control(
				'popup_overlay_background',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.mfp-bg.ee-mfp-popup-{{ID}}' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'popup_no_overlay' => ''
					],
				]
			);

			$this->add_responsive_control(
				'popup_overlay_opacity',
				[
					'label' 	=> __( 'Opacity (%)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 0.8,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 1,
							'min' 	=> 0,
							'step' 	=> 0.01,
						],
					],
					'selectors' => [
						'.mfp-bg.ee-mfp-popup.mfp-ready:not(.mfp-removing).ee-mfp-popup-{{ID}}' => 'opacity: {{SIZE}}',
					],
					'condition' => [
						'popup_no_overlay' => ''
					],
				]
			);

			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name' => 'popup_overlay_filter',
					'selector' => '.mfp-bg.ee-mfp-popup-{{ID}}',
					'condition' => [
						'popup_no_overlay' => ''
					],
				]
			);

			$this->add_control(
				'popup_overlay_blend',
				[
					'label' 		=> __( 'Blend mode', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'normal',
					'options' => [
						'normal'			=> __( 'Normal', 'elementor-extras' ),
						'multiply'			=> __( 'Multiply', 'elementor-extras' ),
						'screen'			=> __( 'Screen', 'elementor-extras' ),
						'overlay'			=> __( 'Overlay', 'elementor-extras' ),
						'darken'			=> __( 'Darken', 'elementor-extras' ),
						'lighten'			=> __( 'Lighten', 'elementor-extras' ),
						'color'				=> __( 'Color', 'elementor-extras' ),
						'color-dodge'		=> __( 'Color Dodge', 'elementor-extras' ),
						'hue'				=> __( 'Hue', 'elementor-extras' ),
					],
					'selectors' 	=> [
						'.mfp-bg.ee-mfp-popup-{{ID}}' => 'mix-blend-mode: {{VALUE}};',
					],
					'condition' => [
						'popup_no_overlay' => ''
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_trigger',
			[
				'label' => __( 'Trigger', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'popup_trigger'	=> 'click',
					'popup_click_target' => 'text',
				],
			]
		);

			$this->add_responsive_control(
				'trigger_align',
				[
					'label' 		=> __( 'Text Align', 'elementor-extras' ),
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
						'{{WRAPPER}} .ee-popup--trigger-text' => 'text-align: {{VALUE}};',
					],
					'condition'		=> [
						'popup_trigger'	=> 'click',
						'popup_click_target' => 'text',
					],
				]
			);

			$this->add_control(
				'trigger_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-popup__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'		=> [
						'popup_trigger'	=> 'click',
						'popup_click_target' => 'text',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'popup_trigger',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-popup__trigger',
					'condition'		=> [
						'popup_trigger'	=> 'click',
						'popup_click_target' => 'text',
					],
				]
			);

			$this->add_control(
				'trigger_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-popup__trigger' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
					'condition'		=> [
						'popup_trigger'	=> 'click',
						'popup_click_target' => 'text',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'trigger_transition',
					'selector' 	=> '{{WRAPPER}} .ee-popup__trigger',
					'separator'	=> '',
					'condition'		=> [
						'popup_trigger'	=> 'click',
						'popup_click_target' => 'text',
					],
				]
			);

			$this->start_controls_tabs( 'trigger_default' );

			$this->start_controls_tab( 'trigger_tab_default', [
				'label' 	=> __( 'Default', 'elementor-extras' ),
				'selector' 	=> '{{WRAPPER}} .ee-popup__trigger',
				'condition'	=> [
					'popup_trigger'	=> 'click',
					'popup_click_target' => 'text',
				],
			] );

				$this->add_control(
					'trigger_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-popup__trigger' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'popup_trigger'	=> 'click',
							'popup_click_target' => 'text',
						],
					]
				);

				$this->add_control(
					'trigger_background',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-popup__trigger' => 'background-color: {{VALUE}};',
						],
						'condition'	=> [
							'popup_trigger'	=> 'click',
							'popup_click_target' => 'text',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'trigger_tab_hover', [
				'label' 	=> __( 'Hover', 'elementor-extras' ),
				'selector' 	=> '{{WRAPPER}} .ee-popup__trigger',
				'condition'	=> [
					'popup_trigger'	=> 'click',
					'popup_click_target' => 'text',
				],
			] );

				$this->add_control(
					'trigger_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-popup__trigger:hover' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'popup_trigger'	=> 'click',
							'popup_click_target' => 'text',
						],
					]
				);

				$this->add_control(
					'trigger_background_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-popup__trigger:hover' => 'background-color: {{VALUE}};',
						],
						'condition'	=> [
							'popup_trigger'	=> 'click',
							'popup_click_target' => 'text',
						],
					]
				);

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_header',
			[
				'label' => __( 'Header', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions'	=> $this->get_inline_conditions(),
			]
		);

			$this->add_control(
				'popup_header_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-popup__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_control(
				'popup_header_background',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-popup__header' => 'background-color: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'popup_header_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__header',
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_control(
				'popup_title_heading',
				[
					'label' 	=> __( 'Title', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_responsive_control(
				'popup_title_align',
				[
					'label' 		=> __( 'Text Align', 'elementor-extras' ),
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
						'.ee-mfp-popup-{{ID}} .ee-popup__header__title' => 'text-align: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'popup_title_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__header__title',
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_control(
				'popup_title_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-popup__header__title' => 'color: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_control(
				'popup_title_background',
				[
					'label' 	=> __( 'Background', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-popup__header__title' => 'background-color: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_responsive_control(
				'popup_title_distance',
				[
					'label' 		=> __( 'Distance', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-popup__header__title' => 'margin-bottom: {{SIZE}}px;',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_body',
			[
				'label' => __( 'Body', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions'	=> $this->get_inline_conditions(),
			]
		);

			$this->add_responsive_control(
				'popup_body_align',
				[
					'label' 		=> __( 'Text Align', 'elementor-extras' ),
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
						'.ee-mfp-popup-{{ID}} .ee-popup__content__body' => 'text-align: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_control(
				'popup_body_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-popup__content__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'popup_body_border',
					'separator' => 'before',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__content__body',
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_control(
				'popup_body_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-popup__content__body' => 'color: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_control(
				'popup_body_background',
				[
					'label' 	=> __( 'Background', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-popup__content__body' => 'background-color: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'popup_body_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__content__body',
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

		$this->end_controls_section();

		$footer_conditions = [
			[
				'name'		=> 'popup_close_button_position',
				'operator' 	=> '==',
				'value'		=> 'default',
			]
		];

		$this->start_controls_section(
			'section_style_footer',
			[
				'label' => __( 'Footer', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions'	=> $this->get_inline_conditions( $footer_conditions ),
			]
		);

			$this->add_control(
				'popup_footer_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-popup__footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'conditions'	=> $this->get_inline_conditions( $footer_conditions ),
				]
			);

			$this->add_control(
				'popup_footer_background',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-popup__footer' => 'background-color: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions( $footer_conditions ),
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'popup_footer_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__footer',
					'conditions'	=> $this->get_inline_conditions( $footer_conditions ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __( 'Image', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'popup_type' => 'image',
				],
			]
		);

			$this->add_control(
				'popup_image_heading',
				[
					'label' 	=> __( 'Image', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'	=> [
						'popup_type' => 'image',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name' => 'popup_image_filter',
					'selector' => '.ee-mfp-popup-{{ID}} .mfp-img',
				]
			);

			$this->add_control(
				'popup_caption_heading',
				[
					'label' 	=> __( 'Caption', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'	=> [
						'popup_type' => 'image',
						'popup_image_caption_type!' => '',
					],
				]
			);

			$this->add_control(
				'popup_caption_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .mfp-bottom-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'popup_type' => 'image',
						'popup_image_caption_type!' => '',
					],
				]
			);

			$this->add_control(
				'popup_caption_margin',
				[
					'label' 		=> __( 'Margin', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .mfp-bottom-bar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'popup_type' => 'image',
						'popup_image_caption_type!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'popup_caption_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .mfp-bottom-bar',
					'condition'	=> [
						'popup_type' => 'image',
						'popup_image_caption_type!' => '',
					],
				]
			);

			$this->add_control(
				'popup_caption_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .mfp-bottom-bar' => 'color: {{VALUE}};',
					],
					'conditions'	=> $this->get_inline_conditions(),
				]
			);

			$this->add_control(
				'popup_caption_background',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .mfp-bottom-bar' => 'background-color: {{VALUE}};',
					],
					'condition'	=> [
						'popup_type' => 'image',
						'popup_image_caption_type!' => '',
					],
				]
			);

			$this->add_control(
				'popup_caption_blend',
				[
					'label' 		=> __( 'Blend mode', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'normal',
					'options' => [
						'normal'			=> __( 'Normal', 'elementor-extras' ),
						'multiply'			=> __( 'Multiply', 'elementor-extras' ),
						'screen'			=> __( 'Screen', 'elementor-extras' ),
						'overlay'			=> __( 'Overlay', 'elementor-extras' ),
						'darken'			=> __( 'Darken', 'elementor-extras' ),
						'lighten'			=> __( 'Lighten', 'elementor-extras' ),
						'color'				=> __( 'Color', 'elementor-extras' ),
						'color-dodge'		=> __( 'Color Dodge', 'elementor-extras' ),
						'hue'				=> __( 'Hue', 'elementor-extras' ),
					],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .mfp-bottom-bar' => 'background-blend-mode: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'popup_caption_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .mfp-bottom-bar',
					'condition'	=> [
						'popup_type' => 'image',
						'popup_image_caption_type!' => '',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_close',
			[
				'label' => __( 'Close', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation'	=> 'or',
					'terms'		=> [
						[
							'name'		=> 'popup_close_position',
							'operator' 	=> '!=',
							'value'		=> '',
						],
						[
							'name'		=> 'popup_close_button_position',
							'operator' 	=> '!=',
							'value'		=> '',
						],
					]
				],
			]
		);

			$this->add_control(
				'popup_style_icon_heading',
				[
					'label' 	=> __( 'Icon', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'	=> [
						'popup_close_position!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'popup_icon_size',
				[
					'label' 		=> __( 'Size', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 1,
							'max' => 4,
							'step' => 0.1,
						],
					],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .mfp-close.ee-popup__close' => 'font-size: {{SIZE}}em;',
						'.ee-mfp-popup-{{ID}} .ee-popup__content' => 'margin: {{SIZE}}em auto;',
					],
					'condition'	=> [
						'popup_close_position!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'popup_icon_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 0.9,
							'step' => 0.01,
						],
					],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .mfp-close.ee-popup__close:before' => 'transform: scale(calc( 1 - {{SIZE}} ));',
					],
					'condition'	=> [
						'popup_close_position!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'popup_icon_margin',
				[
					'label' 		=> __( 'Distance', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .mfp-close.ee-popup__close' => 'margin: {{SIZE}}px;',
					],
					'condition'	=> [
						'popup_close_position!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'popup_icon_margins',
				[
					'label' 		=> __( 'Margin', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .mfp-close.ee-popup__close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'popup_close_position!' => '',
					],
				]
			);

			$this->add_control(
				'popup_icon_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .mfp-close.ee-popup__close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'popup_close_position!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'popup_icon',
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__close',
					'separator'	=> '',
					'condition'	=> [
						'popup_close_position!' => '',
					],
				]
			);

			$this->start_controls_tabs( 'icon_tabs_hover' );

			$this->start_controls_tab( 'icon_tab_default', [
				'label' => __( 'Default', 'elementor-extras' ),
				'condition'	=> [
					'popup_close_position!' => '',
				],
			] );

				$this->add_control(
					'popup_icon_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'.ee-mfp-popup-{{ID}} .mfp-close.ee-popup__close' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'popup_close_position!' => '',
						],
					]
				);

				$this->add_control(
					'popup_icon_background',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'.ee-mfp-popup-{{ID}} .mfp-close.ee-popup__close' => 'background-color: {{VALUE}};',
						],
						'condition'	=> [
							'popup_close_position!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'icon_tab_hover', [
				'label' => __( 'Hover', 'elementor-extras' ),
				'condition'	=> [
					'popup_close_position!' => '',
				],
			] );

				$this->add_control(
					'popup_icon_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'.ee-mfp-popup-{{ID}} .mfp-close.ee-popup__close:hover' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'popup_close_position!' => '',
						],
					]
				);

				$this->add_control(
					'popup_icon_background_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'.ee-mfp-popup-{{ID}} .mfp-close.ee-popup__close:hover' => 'background-color: {{VALUE}};',
						],
						'condition'	=> [
							'popup_close_position!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'popup_style_button_heading',
				[
					'label' 	=> __( 'Button', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'default',
					],
				]
			);

			$this->add_responsive_control(
				'popup_style_button_align',
				[
					'label' 		=> __( 'Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'flex-start' 	=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'flex-end' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-popup__footer__button' => 'justify-content: {{VALUE}};',
					],
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'default',
					],
				]
			);

			$this->add_control(
				'popup_button_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-popup__footer__button .ee-button-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'default',
					],
				]
			);

			$this->add_control(
				'popup_button_margin',
				[
					'label' 		=> __( 'Margin', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-popup__footer__button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'default',
					],
				]
			);

			$this->add_control(
				'popup_button_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-popup__footer__button .ee-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'default',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'popup_button_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__footer__button .ee-button',
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'default',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'popup_button_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__footer__button .ee-button',
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'default',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'popup_button',
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-popup__footer__button .ee-button',
					'separator'	=> '',
					'condition' => [
						'popup_type!' => [ 'image', 'iframe' ],
						'popup_close_button_position' => 'default',
					],
				]
			);

			$this->start_controls_tabs( 'button_tabs_hover' );

			$this->start_controls_tab( 'button_tab_default', [
				'label' => __( 'Default', 'elementor-extras' ),
				'condition' => [
					'popup_type!' => [ 'image', 'iframe' ],
					'popup_close_button_position' => 'default',
				],
			] );

				$this->add_control(
					'popup_button_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'.ee-mfp-popup-{{ID}} .ee-popup__footer__button .ee-button' => 'color: {{VALUE}};',
						],
						'condition' => [
							'popup_type!' => [ 'image', 'iframe' ],
							'popup_close_button_position' => 'default',
						],
					]
				);

				$this->add_control(
					'popup_button_background',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'.ee-mfp-popup-{{ID}} .ee-popup__footer__button .ee-button' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'popup_type!' => [ 'image', 'iframe' ],
							'popup_close_button_position' => 'default',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'button_tab_hover', [
				'label' => __( 'Hover', 'elementor-extras' ),
				'condition' => [
					'popup_type!' => [ 'image', 'iframe' ],
					'popup_close_button_position' => 'default',
				],
			] );

				$this->add_control(
					'popup_button_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'.ee-mfp-popup-{{ID}} .ee-popup__footer__button:hover .ee-button' => 'color: {{VALUE}};',
						],
						'condition' => [
							'popup_type!' => [ 'image', 'iframe' ],
							'popup_close_button_position' => 'default',
						],
					]
				);

				$this->add_control(
					'popup_button_background_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'.ee-mfp-popup-{{ID}} .ee-popup__footer__button:hover .ee-button' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'popup_type!' => [ 'image', 'iframe' ],
							'popup_close_button_position' => 'default',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Get Inline Conditions
	 * 
	 * Get generic conditions for inline trigger
	 *
	 * @since  2.0.0
	 * @return void
	 */
	private function get_inline_conditions( $new_terms = array() ) {
		$conditions = [
			'relation'	=> 'and',
			'terms'		=> [
				[
					'name'		=> 'popup_type',
					'operator' 	=> '!=',
					'value'		=> 'image',
				],
				[
					'name'		=> 'popup_type',
					'operator' 	=> '!=',
					'value'		=> 'iframe',
				],
			]
		];

		if ( ! empty( $new_terms ) ) {
			foreach( $new_terms as $term ) {
				$conditions['terms'][] = $term;
			}
		}

		return $conditions;
	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render() {

		$settings = $this->get_settings_for_display();

		$has_inline_trigger = 'click' === $settings['popup_trigger'] && 'text' == $settings['popup_click_target'];
		$content_link = '#ee_popup__trigger-' . $this->get_id();

		if ( 'iframe' === $settings['popup_type'] ) {

			if ( 'video' === $settings['popup_iframe_type'] )
				$content_link = $settings['popup_video_url'];

			if ( 'map' === $settings['popup_iframe_type'] )
				$content_link = $settings['popup_map_url'];
		}

		if ( 'image' === $settings['popup_type'] && ! empty( $settings['popup_image']['url'] ) ) {

			$image_url = Group_Control_Image_Size::get_attachment_image_src( $settings['popup_image']['id'], 'popup_image', $settings );

			$content_link =  $image_url ? $image_url : $settings['popup_image']['url'];

			$this->add_render_attribute( 'popup-trigger', [
				'data-elementor-open-lightbox' => 'no',
				'title' => ImageModule::get_image_caption( $settings['popup_image']['id'] ),
			] );
		}

		$this->add_render_attribute( [
			'popup' => [
				'class' 	=> [
					'ee-popup',
					'ee-popup--trigger-' . $settings['popup_click_target'],
				],
				'id'		=> 'popup-' . $this->get_id(),
			],
			'popup-trigger' => [
				'class' 	=> [
					'ee-popup__trigger',
					'ee-popup__trigger--' . $settings['popup_trigger'],
				],
				'href'		=> $content_link,
			],
			'popup-content' => [
				'class' 	=> [
					'ee-popup__content',
					'zoom-anim-dialog',
					'mfp-hide',
				],
				'id'		=> 'ee_popup__trigger-' . $this->get_id(),
			],
		] );

		if ( 'id' === $settings['popup_click_target'] ) {
			$this->add_render_attribute( 'popup-trigger', 'data-trigger-id', $settings['popup_click_element_id'] );
		} else if ( 'class' === $settings['popup_click_target'] ) {
			$this->add_render_attribute( 'popup-trigger', 'data-trigger-class', $settings['popup_click_element_class'] );
		}

		if ( '' !== $settings['popup_animation'] ) {
			$this->add_render_attribute( 'popup-content', 'class', 'mfp-with-anim' );
		}

		if ( 'url' === $settings['popup_type'] && '' !== $settings['popup_url']['url'] ) {
			$this->add_render_attribute( 'popup-content', 'data-ee-popup-url', $settings['popup_url']['url'] );
		}

		?><div <?php echo $this->get_render_attribute_string( 'popup' ) ; ?>><?php 
			
			if ( 'click' !== $settings['popup_trigger'] || 'text' !== $settings['popup_click_target'] )
				echo $this->render_placeholder( [
				'body' => __( 'This area will not appear on the front-end.', 'elementor-extras' ),
			] ); ?>

			<a <?php echo $this->get_render_attribute_string( 'popup-trigger' ) ; ?>><?php
				if ( $has_inline_trigger ) 
					echo $settings['popup_trigger_text'];
			?></a>

			<div <?php echo $this->get_render_attribute_string( 'popup-content' ) ; ?>>
				<?php $this->render_header(); ?>
				<?php $this->render_body(); ?>
				<?php $this->render_footer(); ?>
			</div>
		</div><?php

	}

	/**
	 * Render Header
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_header() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['popup_title'] ) )
			return;

		$title_tag = $settings['popup_title_tag'];

		$this->add_render_attribute( 'popup-header', 'class', 'ee-popup__header' );
		$this->add_render_attribute( 'popup-content-title', 'class', 'ee-popup__header__title' );

		?><div <?php echo $this->get_render_attribute_string( 'popup-header' ) ; ?>>
			<<?php echo $title_tag; ?> <?php echo $this->get_render_attribute_string( 'popup-content-title' ) ; ?>>
				<?php echo $settings['popup_title']; ?>
			</<?php echo $title_tag; ?>>
		</div><?php
	}

	/**
	 * Render Body
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_body() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'popup-content-body', 'class', 'ee-popup__content__body' );

		?><div <?php echo $this->get_render_attribute_string( 'popup-content-body' ) ; ?>><?php

			switch ( $settings['popup_type'] ) {
				case 'text':
					echo $this->parse_text_editor( $settings['popup_content'] );
					break;
				case 'template':
					TemplatesControl::render_template_content( $settings['popup_template'], $this );
					break;
				default:
					break;
			}

		?></div><?php
	}

	/**
	 * Render Footer
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_footer() {
		$settings = $this->get_settings_for_display();

		if ( 'default' !== $settings['popup_close_button_position'] )
			return;

		$this->add_render_attribute( 'popup-footer', 'class', 'ee-popup__footer' );
		$this->add_render_attribute( 'popup-button-wrapper', 'class', [
			'ee-popup__footer__button',
			'ee-button-wrapper',
		] );

		$this->add_render_attribute( 'popup-button', 'class', [
			'ee-button',
			'ee-button-link',
			'ee-size-sm',
		] );
		$this->add_render_attribute( 'popup-button-content', 'class', 'ee-button-content-wrapper' );

		?><div <?php echo $this->get_render_attribute_string( 'popup-footer' ) ; ?>>
			<a <?php echo $this->get_render_attribute_string( 'popup-button-wrapper' ) ; ?>>
				<span <?php echo $this->get_render_attribute_string( 'popup-button' ) ; ?>>
					<span <?php echo $this->get_render_attribute_string( 'popup-button-content' ) ; ?>>
						<?php echo $settings['popup_close_button_text']; ?>
					</span>
				</span>
			</a>
		</div><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function _content_template() {}
}