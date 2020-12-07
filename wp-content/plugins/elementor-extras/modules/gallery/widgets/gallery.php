<?php
namespace ElementorExtras\Modules\Gallery\Widgets;

// Extras for Elementor Classes
use ElementorExtras\ElementorExtrasPlugin as Plugin;
use ElementorExtras\Group_Control_Transition;
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\Gallery\Module;
use ElementorExtras\Modules\Image\Module as ImageModule;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Gallery
 *
 * @since 2.1.0
 */
class Gallery extends Extras_Widget {

	/**
	 * Instagram Access token.
	 *
	 * @since 2.1.0
	 * @var   string
	 */
	private $insta_access_token = null;

	/**
	 * Instagram API URL.
	 *
	 * @since 2.1.0
	 * @var   string
	 */
	private $insta_api_url = 'https://www.instagram.com/';

	/**
	 * Official Instagram API URL.
	 *
	 * @since 2.1.0
	 * @var   string
	 */
	private $insta_official_api_url = 'https://graph.instagram.com/';

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'gallery-extra';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Gallery', 'elementor-extras' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-image-gallery';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'tilt',
			'parallax-gallery',
			'jquery-resize-ee',
			'isotope',
			'packery-mode',
			'imagesloaded',
		];
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_gallery',
			[
				'label' => __( 'Gallery', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'gallery_type',
				[
					'label' 	=> __( 'Type', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'wordpress',
					'options' 	=> [
						'wordpress'	=> __( 'Wordpress', 'elementor-extras' ),
						'manual' 	=> __( 'Manual', 'elementor-extras' ),
						'instagram' => __( 'Instagram', 'elementor-extras' ),
					],
				]
			);

			$this->add_control(
				'insta_display',
				[
					'label' 	=> __( 'Display', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'feed',
					'options' 	=> [
						'feed'	=> __( 'My Photos', 'elementor-extras' ),
						'tags'	=> __( 'Tagged Photos', 'elementor-extras' ),
					],
					'condition' => [
						'gallery_type' => 'instagram',
					],
				]
			);

			if ( ! $this->get_insta_global_access_token() ) {
				$this->add_control(
					'access_token_missing',
					[
						'type' 				=> Controls_Manager::RAW_HTML,
						'raw'  				=> sprintf(
												__( 'The global Instagram access token is missing. You can use a custom one below or add it %1$shere%2$s. Find out %3$show to get your access token%4$s.', 'elementor-extras' ),
												'<a target="_blank" href="' . admin_url( 'admin.php?page=elementor-extras#elementor_extras_apis' ) . '">',
												'</a>',
												'<a target="_blank" href="' . Plugin::$instance->get_link('docs_ig_token') . '">',
												'</a>'
											),
						'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
						'condition' 		=> [
							'gallery_type' 	=> 'instagram',
							'insta_display' => 'feed',
						],
					]
				);
			}

			$this->add_control(
				'access_token',
				[
					'label' 		=> __( 'Override Global Access Token', 'elementor-extras' ),
					'description'	=> sprintf( __( 'Leave blank to use the global token set under Elementor > Extras > APIs. %1$sHow to get an access token%2$s', 'elementor-extras' ), '<a target="_blank" href="' . Plugin::$instance->get_link('docs_ig_token') . '">', '</a>' ),
					'label_block'	=> true,
					'default'		=> '',
					'type'			=> Controls_Manager::TEXT,
					'condition' 	=> [
						'gallery_type' 	=> 'instagram',
						'insta_display' => 'feed',
					],
				]
			);

			$this->add_control(
				'insta_hashtag',
				[
					'label' 			=> __( 'Hashtag', 'elementor-extras' ),
					'description' 		=> __( 'Enter without the # symbol', 'elementor-extras' ),
					'type'  			=> Controls_Manager::TEXT,
					'condition' 		=> [
						'gallery_type' 	=> 'instagram',
						'insta_display' => 'tags',
					],
					'dynamic' 			=> [
						'active' 		=> true,
						'categories' 	=> [
							TagsModule::POST_META_CATEGORY,
						],
					],
				]
			);

			$gallery_items = new Repeater();

			$gallery_items->add_control(
				'image',
				[
					'label' 	=> __( 'Choose Image', 'elementor-extras' ),
					'type' 		=> Controls_Manager::MEDIA,
					'default' 	=> [
						'url' 	=> Utils::get_placeholder_image_src(),
					],
				]
			);

			$gallery_items->add_control(
				'link',
				[
					'label' 	=> __( 'Link to', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'file',
					'options' 	=> [
						'file' 			=> __( 'Media File', 'elementor-extras' ),
						'attachment' 	=> __( 'Attachment Page', 'elementor-extras' ),
						'custom' 		=> __( 'Custom URL', 'elementor-extras' ),
						'' 				=> __( 'None', 'elementor-extras' ),
					],
				]
			);

			$gallery_items->add_control(
				'link_url',
				[
					'label' 		=> __( 'Link', 'elementor-extras' ),
					'type' 			=> Controls_Manager::URL,
					'placeholder' 	=> esc_url( home_url( '/' ) ),
					'default' 		=> [
						'url' 		=> esc_url( home_url( '/' ) ),
					],
					'condition'	=> [
						'link'	=> 'custom',
					]
				]
			);

			$gallery_items->start_controls_tabs('custom');

				$gallery_items->start_controls_tab(
					'custom_desktop',
					[
						'label' => __( 'Desktop', 'elementor-extras' ),
					]
				);

					$gallery_items->add_control(
						'custom_size',
						[
							'label'			=> __( 'Custom Size', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> '',
							'label_on' 		=> __( 'Yes', 'elementor-extras' ),
							'label_off' 	=> __( 'No', 'elementor-extras' ),
							'return_value' 	=> 'yes',
						]
					);

					$gallery_items->add_control(
						'width',
						[
							'label' 		=> __( 'Width', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SELECT,
							'label_block' 	=> true,
							'default' 		=> '',
							'options' 		=> [
								'' 			=> __( 'Default', 'elementor-extras' ),
								'100%' 		=> __( 'Full Width', 'elementor-extras' ),
								'50%' 		=> __( 'One Half', 'elementor-extras' ),
								'33.3333%' 	=> __( 'One Third', 'elementor-extras' ),
								'66.6666%' 	=> __( 'Two Thirds', 'elementor-extras' ),
								'25%' 		=> __( 'One Quarter', 'elementor-extras' ),
								'75%' 		=> __( 'Three Quarters', 'elementor-extras' ),
								'20%' 		=> __( 'One Fifth', 'elementor-extras' ),
								'40%' 		=> __( 'Two Fifths', 'elementor-extras' ),
								'60%' 		=> __( 'Three Fifths', 'elementor-extras' ),
								'80%' 		=> __( 'Four Fifths', 'elementor-extras' ),
								'16.6666%' 	=> __( 'One Sixth', 'elementor-extras' ),
								'83.3333%' 	=> __( 'Five Sixths', 'elementor-extras' ),
							],
							'selectors' => [
								'(desktop+){{WRAPPER}} {{CURRENT_ITEM}}.ee-grid__item--custom-size' => 'width: {{VALUE}};',
							],
							'condition' => [
								'custom_size!' => ''
							],
						]
					);

					$gallery_items->add_control(
						'height_ratio',
						[
							'label' 	=> __( 'Image Size Ratio', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default'	=> [
								'size'	=> '',
							],
							'range' 	=> [
								'px' 	=> [
									'min'	=> 10,
									'max' 	=> 200,
								],
							],
							'selectors' => [
								'(desktop+){{WRAPPER}} {{CURRENT_ITEM}} .ee-media--stretch:before' => 'padding-bottom: {{SIZE}}%;',
							],
							'condition' => [
								'custom_size!' => ''
							],
						]
					);

				$gallery_items->end_controls_tab();

				$gallery_items->start_controls_tab(
					'custom_tablet',
					[
						'label' => __( 'Tablet', 'elementor-extras' ),
					]
				);

					$gallery_items->add_control(
						'custom_size_tablet',
						[
							'label'			=> __( 'Custom Size', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> '',
							'label_on' 		=> __( 'Yes', 'elementor-extras' ),
							'label_off' 	=> __( 'No', 'elementor-extras' ),
							'return_value' 	=> 'yes',
						]
					);

					$gallery_items->add_control(
						'width_tablet',
						[
							'label' 		=> __( 'Width', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SELECT,
							'default' 		=> '',
							'label_block' 	=> true,
							'options' 		=> [
								'' 			=> __( 'Default', 'elementor-extras' ),
								'100%' 		=> __( 'Full Width', 'elementor-extras' ),
								'50%' 		=> __( 'One Half', 'elementor-extras' ),
								'33.3333%' 	=> __( 'One Third', 'elementor-extras' ),
								'66.6666%' 	=> __( 'Two Thirds', 'elementor-extras' ),
								'25%' 		=> __( 'One Quarter', 'elementor-extras' ),
								'75%' 		=> __( 'Three Quarters', 'elementor-extras' ),
								'20%' 		=> __( 'One Fifth', 'elementor-extras' ),
								'40%' 		=> __( 'Two Fifths', 'elementor-extras' ),
								'60%' 		=> __( 'Three Fifths', 'elementor-extras' ),
								'80%' 		=> __( 'Four Fifths', 'elementor-extras' ),
								'16.6666%' 	=> __( 'One Sixth', 'elementor-extras' ),
								'83.3333%' 	=> __( 'Five Sixths', 'elementor-extras' ),
							],
							'selectors' => [
								'(tablet+)(tablet-){{WRAPPER}} {{CURRENT_ITEM}}.ee-grid__item--custom-size' => 'width: {{VALUE}};',
							],
							'condition' => [
								'custom_size_tablet!' => ''
							],
						]
					);

					$gallery_items->add_control(
						'height_ratio_tablet',
						[
							'label' 	=> __( 'Image Size Ratio', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default'	=> [
								'size'	=> '',
							],
							'range' 	=> [
								'px' 	=> [
									'min'	=> 10,
									'max' 	=> 200,
								],
							],
							'selectors' => [
								'(tablet+)(tablet-){{WRAPPER}} {{CURRENT_ITEM}} .ee-media--stretch:before' => 'padding-bottom: {{SIZE}}%;',
							],
							'condition' => [
								'custom_size_tablet!' => ''
							],
						]
					);

				$gallery_items->end_controls_tab();

				$gallery_items->start_controls_tab(
					'custom_mobile',
					[
						'label' => __( 'Mobile', 'elementor-extras' ),
					]
				);

					$gallery_items->add_control(
						'custom_size_mobile',
						[
							'label'			=> __( 'Custom Size', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> '',
							'label_on' 		=> __( 'Yes', 'elementor-extras' ),
							'label_off' 	=> __( 'No', 'elementor-extras' ),
							'return_value' 	=> 'yes',
						]
					);

					$gallery_items->add_control(
						'width_mobile',
						[
							'label' 		=> __( 'Width', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SELECT,
							'default' 		=> '',
							'label_block' 	=> true,
							'options' 		=> [
								'' 			=> __( 'Default', 'elementor-extras' ),
								'100%' 		=> __( 'Full Width', 'elementor-extras' ),
								'50%' 		=> __( 'One Half', 'elementor-extras' ),
								'33.3333%' 	=> __( 'One Third', 'elementor-extras' ),
								'66.6666%' 	=> __( 'Two Thirds', 'elementor-extras' ),
								'25%' 		=> __( 'One Quarter', 'elementor-extras' ),
								'75%' 		=> __( 'Three Quarters', 'elementor-extras' ),
								'20%' 		=> __( 'One Fifth', 'elementor-extras' ),
								'40%' 		=> __( 'Two Fifths', 'elementor-extras' ),
								'60%' 		=> __( 'Three Fifths', 'elementor-extras' ),
								'80%' 		=> __( 'Four Fifths', 'elementor-extras' ),
								'16.6666%' 	=> __( 'One Sixth', 'elementor-extras' ),
								'83.3333%' 	=> __( 'Five Sixths', 'elementor-extras' ),
							],
							'selectors' => [
								'(mobile){{WRAPPER}} {{CURRENT_ITEM}}.ee-grid__item--custom-size' => 'width: {{VALUE}};',
							],
							'condition' => [
								'custom_size_mobile!' => ''
							],
						]
					);

					$gallery_items->add_control(
						'height_ratio_mobile',
						[
							'label' 	=> __( 'Image Size Ratio', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default'	=> [
								'size'	=> '',
							],
							'range' 	=> [
								'px' 	=> [
									'min'	=> 10,
									'max' 	=> 200,
								],
							],
							'selectors' => [
								'(mobile){{WRAPPER}} {{CURRENT_ITEM}} .ee-media--stretch:before' => 'padding-bottom: {{SIZE}}%;',
							],
							'condition' => [
								'custom_size_mobile!' => ''
							],
						]
					);

				$gallery_items->end_controls_tab();

			$gallery_items->end_controls_tab();

			$this->add_control(
				'gallery',
				[
					'label' 	=> __( 'Images', 'elementor-extras' ),
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[],
						[],
						[],
						[],
						[],
						[],
					],
					'fields' 		=> array_values( $gallery_items->get_controls() ),
					'condition'		=> [
						'gallery_type' => 'manual',
					]
				]
			);

			$this->add_control(
				'images_heading',
				[
					'label' 	=> __( 'Images', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'	=> [
						'gallery_type' => 'wordpress',
					]
				]
			);

			$this->add_control(
				'wp_gallery',
				[
					'label' 	=> __( 'Add Images', 'elementor-extras' ),
					'type' 		=> Controls_Manager::GALLERY,
					'dynamic'	=> [
						'active' => true,
					],
					'condition'	=> [
						'gallery_type' => 'wordpress',
					]
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_settings',
			[
				'label' => __( 'Settings', 'elementor-extras' ),
			]
		);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'thumbnail',
					'default'	=> 'full',
					'condition'	=> [
						'gallery_type!'	 => 'instagram',
					],
				]
			);

			$this->add_control(
				'insta_image_size',
				[
					'label'   => __( 'Image Size', 'elementor-extras' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'standard',
					'options' => [
						'thumbnail' => __( 'Thumbnail (150x150)', 'elementor-extras' ),
						'low'       => __( 'Low (320x320)', 'elementor-extras' ),
						'standard'  => __( 'Standard (640x640)', 'elementor-extras' ),
						'high'      => __( 'High (original)', 'elementor-extras' ),
					],
					'condition'	=> [
						'gallery_type'	 => 'instagram',
					],
				]
			);

			$this->add_responsive_control(
				'columns',
				[
					'label' 	=> __( 'Columns', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '3',
					'tablet_default' 	=> '2',
					'mobile_default' 	=> '1',
					'options' 			=> [
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					],
					'prefix_class'	=> 'ee-grid-columns%s-',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'columns_notice',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'If you are specifying the widths for each image individually, set this to correspond to the lowest width in your gallery.', 'elementor-extras' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
					'condition'			=> [
						'gallery_type'	 => 'manual',
					]
				]
			);

			$this->add_control(
				'gallery_link',
				[
					'label' 	=> __( 'Link to', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'file',
					'options' 	=> [
						'file' 			=> __( 'Media File', 'elementor-extras' ),
						'attachment' 	=> __( 'Attachment Page', 'elementor-extras' ),
						'' 				=> __( 'None', 'elementor-extras' ),
					],
					'condition'	=> [
						'gallery_type'	=> [ 'wordpress', 'instagram' ],
					]
				]
			);

			$this->add_control(
				'open_lightbox',
				[
					'label' 	=> __( 'Lightbox', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'default',
					'options' 	=> [
						'default' 	=> __( 'Default', 'elementor-extras' ),
						'yes' 		=> __( 'Yes', 'elementor-extras' ),
						'no' 		=> __( 'No', 'elementor-extras' ),
					],
					'condition' => [
						'gallery_link' => 'file',
					],
				]
			);

			$this->add_control(
				'lightbox_slideshow',
				[
					'label' 	=> __( 'Lightbox Slideshow', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'default' 	=> 'yes',
					'condition' => [
						'gallery_link' => 'file',
						'open_lightbox' => ['default', 'yes'],
					],
				]
			);

			$this->add_control(
				'gallery_rand',
				[
					'label' 	=> __( 'Ordering', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'' 		=> __( 'Default', 'elementor-extras' ),
						'rand' 	=> __( 'Random', 'elementor-extras' ),
					],
					'default' 	=> '',
				]
			);

			$this->add_control(
				'gallery_display_caption',
				[
					'label' 	=> __( 'Caption', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 		=> __( 'Show', 'elementor-extras' ),
						'none' 	=> __( 'Hide', 'elementor-extras' ),
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media__caption' => 'display: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'gallery_caption',
				[
					'label' 	=> __( 'Caption Type', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'caption',
					'options' 	=> [
						'title' 		=> __( 'Title', 'elementor-extras' ),
						'caption' 		=> __( 'Caption', 'elementor-extras' ),
						'description' 	=> __( 'Description', 'elementor-extras' ),
					],
					'condition' => [
						'gallery_display_caption' 	=> '',
						'gallery_type!' 			=> 'instagram',
					],
				]
			);

			$this->add_control(
				'view',
				[
					'label' 	=> __( 'View', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HIDDEN,
					'default' 	=> 'traditional',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_instagram',
			[
				'label' 	=> __( 'Instagram', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_CONTENT,
				'condition'	=> [
					'gallery_type' => 'instagram',
					'gallery_display_caption' => '',
				],
			]
		);

			$this->add_control(
				'insta_counter_comments',
				[
					'label'			=> __( 'Show Comments', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Show', 'elementor-extras' ),
					'label_off' 	=> __( 'Hide', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'		=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'insta_counter_likes',
				[
					'label'			=> __( 'Show Likes', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Show', 'elementor-extras' ),
					'label_off' 	=> __( 'Hide', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'		=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'insta_counter_caption',
				[
					'label'			=> __( 'Show Caption', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Show', 'elementor-extras' ),
					'label_off' 	=> __( 'Hide', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'		=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'insta_caption_length',
				[
					'label' 			=> __( 'Caption Length', 'elementor-extras' ),
					'type'  			=> Controls_Manager::NUMBER,
					'default'			=> 30,
					'condition' 		=> [
						'gallery_type' 	=> 'instagram',
					],
					'dynamic' 			=> [
						'active' 		=> true,
					],
				]
			);

			$this->add_control(
				'insta_posts_counter',
				[
					'label' 			=> __( 'Number of Posts', 'elementor-extras' ),
					'type'  			=> Controls_Manager::NUMBER,
					'default'			=> 10,
					'condition' 		=> [
						'gallery_type' 	=> 'instagram',
					],
					'dynamic' 			=> [
						'active' 		=> true,
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_parallax',
			[
				'label' 	=> __( 'Parallax', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'parallax_enable',
				[
					'label'			=> __( 'Parallax', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'separator'		=> 'before',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'parallax_disable_on',
				[
					'label' 	=> __( 'Disable for', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'mobile',
					'options' 			=> [
						'none' 		=> __( 'None', 'elementor-extras' ),
						'tablet' 	=> __( 'Mobile and tablet', 'elementor-extras' ),
						'mobile' 	=> __( 'Mobile only', 'elementor-extras' ),
					],
					'condition' => [
						'parallax_enable' => 'yes',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'parallax_speed',
				[
					'label' 	=> __( 'Parallax speed', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 0.5
					],
					'tablet_default' => [
						'size'	=> 0.5
					],
					'mobile_default' => [
						'size'	=> 0.5
					],
					'range' 	=> [
						'px' 	=> [
							'min'	=> 0.05,
							'max' 	=> 1,
							'step'	=> 0.01,
						],
					],
					'condition' => [
						'parallax_enable' => 'yes',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'image_distance',
				[
					'label' 	=> __( 'Parallax Distance (%)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' 	=> [
						'size' 	=> '10',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__item.is--3d .ee-gallery__media' => 'margin-left: calc({{SIZE}}%/2); margin-right: calc({{SIZE}}%/2);',
					],
					'condition' => [
						'parallax_enable' 		=> 'yes',
						'image_vertical_align!' => 'stretch',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_masonry',
			[
				'label' 	=> __( 'Masonry', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_CONTENT,
				'condition' 	=> [
					'parallax_enable!' 		=> 'yes',
				],
			]
		);

			$this->add_control(
				'masonry_enable',
				[
					'label'			=> __( 'Enable', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'separator'		=> 'before',
					'condition' 	=> [
						'parallax_enable!' 		=> 'yes',
					],
				]
			);

			$this->add_control(
				'masonry_layout',
				[
					'label' 		=> __( 'Layout', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'columns',
					'options' 		=> [
						'columns'    	=> [
							'title' 	=> __( 'Columns', 'elementor-extras' ),
							'icon' 		=> 'nicon nicon-masonry-columns',
						],
						'mixed' 		=> [
							'title' 	=> __( 'Mixed', 'elementor-extras' ),
							'icon' 		=> 'nicon nicon-masonry-mixed',
						],
					],
					'label_block'	=> false,
					'condition' 	=> [
						'masonry_enable!' 		=> '',
						'parallax_enable!' 		=> 'yes',
					],
					'prefix_class'		=> 'ee-grid-masonry-layout--',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_tilt',
			[
				'label' 	=> __( 'Tilt', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'tilt_enable',
				[
					'label'			=> __( 'Enable', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'tilt_depth',
				[
					'label'			=> __( 'Depth', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition'		=> [
						'tilt_enable!' => '',
					],
				]
			);

			$this->add_control(
				'tile_depth_warning',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw'  				=> __( 'Depth disables CSS overflow: hidden which disables border radius for thumbnails.', 'elementor-extras' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
					'condition' 		=> [
						'tilt_enable!' 	=> '',
						'tilt_depth!' 	=> '',
					],
				]
			);

			$this->add_control(
				'tilt_axis',
				[
					'label'			=> __( 'Axis', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'options' 			=> [
						'' 		=> __( 'Both', 'elementor-extras' ),
						'x' 	=> __( 'X Only', 'elementor-extras' ),
						'y' 	=> __( 'Y Only', 'elementor-extras' ),
					],
					'frontend_available' => true,
					'condition' => [
						'tilt_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'tilt_amount',
				[
					'label' 	=> __( 'Amount', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 10,
							'max' => 40,
						],
					],
					'default' 	=> [
						'size' 	=> 20,
					],
					'frontend_available' => true,
					'condition' => [
						'tilt_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'tilt_caption_depth',
				[
					'label' 	=> __( 'Depth', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' 	=> [
						'size' 	=> 20,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__tilt .ee-gallery__media__content' => 'transform: translateZ({{SIZE}}px);',
					],
					'condition' => [
						'tilt_enable!' => '',
						'tilt_depth!' => '',
					],
				]
			);

			$this->add_control(
				'tilt_scale',
				[
					'label' 	=> __( 'Scale', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 1,
							'max' 	=> 1.5,
							'step'	=> 0.01,
						],
					],
					'default' 		=> [
						'size' 		=> 1.05,
					],
					'frontend_available' => true,
					'condition' => [
						'tilt_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'tilt_speed',
				[
					'label' 	=> __( 'Speed', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 100,
							'max' 	=> 1000,
							'step'	=> 50,
						],
					],
					'default' 		=> [
						'size' 		=> 800,
					],
					'frontend_available' => true,
					'condition' => [
						'tilt_enable' => 'yes',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_layout',
			[
				'label' 	=> __( 'Layout', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'image_align',
				[
					'label' 		=> __( 'Horizontal Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'left',
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
					'prefix_class'		=> 'ee-grid-halign%s--',
					'condition'			=> [
						'masonry_enable' => '',
					],
				]
			);

			$this->add_responsive_control(
				'image_vertical_align',
				[
					'label' 		=> __( 'Vertical Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'top',
					'options' 		=> [
						'top'    			=> [
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
						'stretch' 		=> [
							'title' 	=> __( 'Stretch', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-stretch',
						],
					],
					'prefix_class'		=> 'ee-grid-align%s--',
					'condition'			=> [
						'masonry_enable' => '',
					],
				]
			);

			$this->add_responsive_control(
				'image_stretch_ratio',
				[
					'label' 	=> __( 'Image Size Ratio', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> '100'
						],
					'range' 	=> [
						'px' 	=> [
							'min'	=> 10,
							'max' 	=> 200,
						],
					],
					'condition' => [
						'image_vertical_align' 	=> 'stretch',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media:before' => 'padding-bottom: {{SIZE}}%;',
					],
				]
			);

			$columns_horizontal_margin = is_rtl() ? 'margin-left' : 'margin-right';
			$columns_horizontal_padding = is_rtl() ? 'padding-left' : 'padding-right';

			$this->add_control(
				'image_horizontal_space',
				[
					'label' 	=> __( 'Horizontal Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'custom',
					'options' 	=> [
						'none' 		=> __( 'None', 'elementor-extras' ),
						'custom' 	=> __( 'Custom', 'elementor-extras' ),
						'overlap' 	=> __( 'Overlap', 'elementor-extras' ),
					],
					'condition'		=> [
						'masonry_layout!' => 'mixed',
					],
				]
			);

			$this->add_responsive_control(
				'image_horizontal_spacing',
				[
					'label' 	=> __( 'Horizontal Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'default' 	=> [
						'size' 	=> 24,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery' 		=> $columns_horizontal_margin . ': -{{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-gallery__item' => $columns_horizontal_padding . ': {{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'image_horizontal_space' => 'custom',
						'masonry_layout!' => 'mixed',
					],
				]
			);

			$this->add_responsive_control(
				'image_overlap',
				[
					'label' 	=> __( 'Horizontal Overlap', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'default' 	=> [
						'size' 	=> 0,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-gallery__item .ee-gallery__media' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'image_horizontal_space' => 'overlap',
						'masonry_layout!' => 'mixed',
					],
				]
			);

			$this->add_responsive_control(
				'image_vertical_spacing',
				[
					'label' 	=> __( 'Vertical Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'default' 	=> [
						'size' 	=> 24,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__item .ee-gallery__media' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'masonry_layout!' => 'mixed',
					],
				]
			);

			$this->add_responsive_control(
				'image_mixed_masonry_spacing',
				[
					'label' 	=> __( 'Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'default' 	=> [
						'size' 	=> 24,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media-wrapper' => 'margin: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-gallery' => 'margin: -{{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'masonry_enable!' => '',
						'masonry_layout' => 'mixed',
					],
				]
			);

			$this->add_responsive_control(
				'overflow',
				[
					'label'			=> __( 'Overflow', 'elementor-extras' ),
					'description'	=> __( 'Hiding overflow solves the horizontal scroll issue on mobile devices, but affects shadows and tilt effects which will be hidden outside the grid area.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'separator'		=> 'before',
					'default' 		=> '',
					'tablet_default'=> 'yes',
					'mobile_default'=> 'yes',
					'label_on' 		=> __( 'Hidden', 'elementor-extras' ),
					'label_off' 	=> __( 'Visible', 'elementor-extras' ),
					'prefix_class'	=> 'ee-gallery-overflow%s--',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_images',
			[
				'label' 	=> __( 'Thumbnails', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'image_border',
					'label' 	=> __( 'Image Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media-wrapper',
					'separator' => '',
				]
			);

			$this->add_control(
				'image_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery__media-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'image_background_color',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media__thumbnail' => 'background-color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' 	=> __( 'Captions', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'gallery_display_caption' => '',
				],
			]
		);

			$this->add_control(
				'align',
				[
					'label' 	=> __( 'Text Align', 'elementor-extras' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' 	=> [
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
					'default' 	=> 'center',
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media__caption' => 'text-align: {{VALUE}};',
					],
					'condition' => [
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'vertical_align',
				[
					'label' 	=> __( 'Vertical Align', 'elementor-extras' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'top' 	=> [
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
					'default' 		=> 'bottom',
					'prefix_class'	=> 'ee-media-align--',
					'condition' 	=> [
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'horizontal_align',
				[
					'label' 	=> __( 'Horizontal Align', 'elementor-extras' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' 	=> [
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
						'justify' 		=> [
							'title' 	=> __( 'Justify', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'default' 		=> 'justify',
					'prefix_class'	=> 'ee-media-align--',
					'condition' 	=> [
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media__caption',
					'condition' => [
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'text_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery__media__caption' 	=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'text_margin',
				[
					'label' 		=> __( 'Margin', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery__media__caption' 	=> 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'gallery_display_caption' => '',
					],
					'separator'		=> 'after',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'text_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media__caption',
					'condition' => [
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'text_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery__media__caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'gallery_display_caption' => '',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_instagram_style',
			[
				'label' 	=> __( 'Instagram', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'gallery_type' => 'instagram',
					'gallery_display_caption' => '',
				],
			]
		);

			$this->add_control(
				'insta_counters_heading',
				[
					'label' 	=> __( 'Counters', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_counters_align',
				[
					'label' 		=> __( 'Horizontal Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
					'label_block'	=> false,
					'options' 		=> [
						'flex-start'    => [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-center',
						],
						'flex-end' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-right',
						],
					],
					'selectors'			=> [
						'{{WRAPPER}} .ee-caption__insta' => 'justify-content: {{VALIE}}',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_counters_distance',
				[
					'label' 		=> __( 'Distance', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-caption__insta:not(:first-child)' => 'padding-top: {{SIZE}}px;',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_counters_spacing',
				[
					'label' 	=> __( 'Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-caption__insta__counter:not(:first-child)' => 'margin-left: {{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'insta_icons_heading',
				[
					'label' 	=> __( 'Icons', 'elementor-extras' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_responsive_control(
				'insta_icons_style',
				[
					'label' 		=> __( 'Style', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
					'label_block'	=> false,
					'options' 		=> [
						'solid'    		=> [
							'title' 	=> __( 'Solid', 'elementor-extras' ),
							'icon' 		=> 'fa fa-comment',
						],
						'outline' 		=> [
							'title' 	=> __( 'Outline', 'elementor-extras' ),
							'icon' 		=> 'fa fa-comment-o',
						],
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_icons_spacing',
				[
					'label' 	=> __( 'Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-caption__insta__icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_icons_size',
				[
					'label' 	=> __( 'Size', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 2,
							'step' => 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-caption__insta__icon' => 'font-size: {{SIZE}}em;',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hover_effects',
			[
				'label' 	=> __( 'Hover Effects', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'hover_images_heading',
				[
					'label' 	=> __( 'Images', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'image_transition',
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media-wrapper,
									{{WRAPPER}} .ee-gallery__media__thumbnail img',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'image_style' );

				$this->start_controls_tab(
					'image_style_default',
					[
						'label' => __( 'Default', 'elementor-extras' ),
					]
				);

					$this->add_responsive_control(
						'image_opacity',
						[
							'label' 	=> __( 'Opacity (%)', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__thumbnail img' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_responsive_control(
						'image_scale',
						[
							'label' 		=> __( 'Scale', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SLIDER,
							'range' 		=> [
								'px' 		=> [
									'min' => 1,
									'max' => 2,
									'step'=> 0.01,
								],
							],
							'condition' 	=> [
								'tilt_enable!' => 'yes',
							],
							'selectors' 	=> [
								'{{WRAPPER}} .ee-gallery__media__thumbnail img' => 'transform: scale({{SIZE}});',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' 		=> 'image_box_shadow',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media-wrapper',
							'separator'	=> '',
						]
					);

					$this->add_group_control(
						Group_Control_Css_Filter::get_type(),
						[
							'name' => 'image_css_filters',
							'selector' => '{{WRAPPER}} .ee-gallery__media__thumbnail img',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'image_style_hover',
					[
						'label' 	=> __( 'Hover', 'elementor-extras' ),
					]
				);

					$this->add_responsive_control(
						'image_opacity_hover',
						[
							'label' 	=> __( 'Opacity (%)', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__thumbnail img' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_responsive_control(
						'image_scale_hover',
						[
							'label' 		=> __( 'Scale', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SLIDER,
							'range' 		=> [
								'px' 		=> [
									'min' => 1,
									'max' => 2,
									'step'=> 0.01,
								],
							],
							'condition' 	=> [
								'tilt_enable!' => 'yes',
							],
							'selectors' 	=> [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__thumbnail img' => 'transform: scale({{SIZE}});',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' 		=> 'image_box_shadow_hover',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media-wrapper',
							'separator'	=> '',
						]
					);

					$this->add_control(
						'image_border_color_hover',
						[
							'label' 	=> __( 'Border Color', 'elementor-extras' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media-wrapper' => 'border-color: {{VALUE}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Css_Filter::get_type(),
						[
							'name' => 'image_css_filters_hover',
							'selector' => '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__thumbnail img',
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'hover_overlay_heading',
				[
					'label' 	=> __( 'Overlay', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'overlay_transition',
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media__overlay',
				]
			);

			$this->start_controls_tabs( 'overlay_style' );

				$this->start_controls_tab( 'overlay_style_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

					$this->add_control(
						'overlay_background_color',
						[
							'label' 	=> __( 'Background Color', 'elementor-extras' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__overlay' => 'background-color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'overlay_blend',
						[
							'label' 		=> __( 'Blend mode', 'elementor-extras' ),
							'description'	=> __( 'Using blend mode removes the impact of depth properties from the tilt effect.', 'elementor-extras' ),
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
								'{{WRAPPER}} .ee-gallery__media__overlay' => 'mix-blend-mode: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'overlay_blend_notice',
						[
							'type' 				=> Controls_Manager::RAW_HTML,
							'raw' 				=> sprintf( __( 'Please check blend mode support for your browser %1$s here %2$s', 'elementor-extras' ), '<a href="https://caniuse.com/#search=mix-blend-mode" target="_blank">', '</a>' ),
							'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
							'condition' 		=> [
								'overlay_blend!' => 'normal'
							],
						]
					);

					$this->add_responsive_control(
						'overlay_margin',
						[
							'label' 	=> __( 'Margin', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 48,
									'min' 	=> 0,
									'step' 	=> 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__overlay' => 'top: {{SIZE}}px; right: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px',
							],
						]
					);

					$this->add_responsive_control(
						'overlay_opacity',
						[
							'label' 	=> __( 'Opacity (%)', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__overlay' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name' 		=> 'overlay_border',
							'label' 	=> __( 'Border', 'elementor-extras' ),
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media__overlay',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'overlay_style_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

					$this->add_control(
						'overlay_background_color_hover',
						[
							'label' 	=> __( 'Background Color', 'elementor-extras' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__overlay' => 'background-color: {{VALUE}};',
							],
						]
					);

					$this->add_responsive_control(
						'overlay_margin_hover',
						[
							'label' 	=> __( 'Margin', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 48,
									'min' 	=> 0,
									'step' 	=> 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__overlay' => 'top: {{SIZE}}px; right: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px',
							],
						]
					);

					$this->add_responsive_control(
						'overlay_opacity_hover',
						[
							'label' 	=> __( 'Opacity (%)', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__overlay' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name' 		=> 'overlay_border_hover',
							'label' 	=> __( 'Border', 'elementor-extras' ),
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__overlay',
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'hover_captions_heading',
				[
					'label' 	=> __( 'Captions', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'content',
					'selector' 		=> '{{WRAPPER}} .ee-gallery__media__content,
										{{WRAPPER}} .ee-gallery__media__caption',
					'condition' 	=> [
						'gallery_display_caption' => '',
					],
				]
			);

			$this->update_control( 'content_transition', array(
				'default' => 'custom',
			));

			$this->add_control(
				'content_effect',
				[
					'label' 	=> __( 'Effect', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' => [
						''					=> __( 'None', 'elementor-extras' ),
						'fade-in'			=> __( 'Fade In', 'elementor-extras' ),
						'fade-out'			=> __( 'Fade Out', 'elementor-extras' ),
						'from-top'			=> __( 'From Top', 'elementor-extras' ),
						'from-right'		=> __( 'From Right', 'elementor-extras' ),
						'from-bottom'		=> __( 'From Bottom', 'elementor-extras' ),
						'from-left'			=> __( 'From Left', 'elementor-extras' ),
						'fade-from-top'		=> __( 'Fade From Top', 'elementor-extras' ),
						'fade-from-right'	=> __( 'Fade From Right', 'elementor-extras' ),
						'fade-from-bottom'	=> __( 'Fade From Bottom', 'elementor-extras' ),
						'fade-from-left'	=> __( 'Fade From Left', 'elementor-extras' ),
						'to-top'			=> __( 'To Top', 'elementor-extras' ),
						'to-right'			=> __( 'To Right', 'elementor-extras' ),
						'to-bottom'			=> __( 'To Bottom', 'elementor-extras' ),
						'to-left'			=> __( 'To Left', 'elementor-extras' ),
						'fade-to-top'		=> __( 'Fade To Top', 'elementor-extras' ),
						'fade-to-right'		=> __( 'Fade To Right', 'elementor-extras' ),
						'fade-to-bottom'	=> __( 'Fade To Bottom', 'elementor-extras' ),
						'fade-to-left'		=> __( 'Fade To Left', 'elementor-extras' ),
					],
					'prefix_class'	=> 'ee-media-effect__content--',
					'condition' 	=> [
						'gallery_display_caption' 	=> '',
						'tilt_enable!' 				=> 'yes',
						'content_transition!' 		=> '',
					],
				]
			);

			$this->start_controls_tabs( 'caption_style' );

				$this->start_controls_tab( 'caption_style_default', [
					'label' 	=> __( 'Default', 'elementor-extras' ),
					'condition' => [
						'gallery_display_caption' => '',
					],
				] );

					$this->add_control(
						'text_color',
						[
							'label' 	=> __( 'Color', 'elementor-extras' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__caption' => 'color: {{VALUE}};',
							],
							'condition' => [
								'gallery_display_caption' => '',
							],
						]
					);

					$this->add_control(
						'text_background_color',
						[
							'label' 	=> __( 'Background', 'elementor-extras' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__caption' => 'background-color: {{VALUE}};',
							],
							'condition' => [
								'gallery_display_caption' => '',
							],
						]
					);

					$this->add_control(
						'text_opacity',
						[
							'label' 	=> __( 'Opacity (%)', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__caption' => 'opacity: {{SIZE}}',
							],
							'condition'	=> [
								'tilt_enable' => 'yes',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						[
							'name' 		=> 'text_box_shadow',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media__caption',
							'separator'	=> '',
							'condition' => [
								'gallery_display_caption' => '',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'caption_style_hover', [
					'label' 	=> __( 'Hover', 'elementor-extras' ),
					'condition' => [
						'gallery_display_caption' => '',
					],
				] );

					$this->add_control(
						'text_color_hover',
						[
							'label' 	=> __( 'Color', 'elementor-extras' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption' => 'color: {{VALUE}};',
							],
							'condition' => [
								'gallery_display_caption' => '',
							],
						]
					);

					$this->add_control(
						'text_background_color_hover',
						[
							'label' 	=> __( 'Background', 'elementor-extras' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption' => 'background-color: {{VALUE}};',
							],
							'condition' => [
								'gallery_display_caption' => '',
							],
						]
					);

					$this->add_control(
						'text_opacity_hover',
						[
							'label' 	=> __( 'Opacity (%)', 'elementor-extras' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption' => 'opacity: {{SIZE}}',
							],
							'condition'	=> [
								'tilt_enable' => 'yes',
							],
						]
					);

					$this->add_control(
						'text_border_color_hover',
						[
							'label' 	=> __( 'Border Color', 'elementor-extras' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption' => 'border-color: {{VALUE}};',
							],
							'condition' => [
								'gallery_display_caption' => '',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						[
							'name' 		=> 'text_box_shadow_hover',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption',
							'separator'	=> '',
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
	 * @since  2.1.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings();

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => 'ee-gallery-wrapper',
			],
			'gallery' => [
				'class' => [
					'ee-gallery',
					'ee-grid',
					'ee-grid--gallery',
					'ee-gallery__gallery',
				],
			],
			'gallery-thumbnail' => [
				'class' => [
					'ee-media__thumbnail',
					'ee-gallery__media__thumbnail',
				],
			],
			'gallery-overlay' => [
				'class' => [
					'ee-media__overlay',
					'ee-gallery__media__overlay',
				],
			],
			'gallery-content' => [
				'class' => [
					'ee-media__content',
					'ee-gallery__media__content',
				],
			],
			'gallery-caption' => [
				'class' => [
					'wp-caption-text',
					'ee-media__content__caption',
					'ee-gallery__media__caption',
					'ee-caption',
					'ee-caption--' . $settings['gallery_type'],
				],
			],
		] );

		if ( 'manual' === $settings['gallery_type'] ) {
			$this->render_gallery();
		} elseif ( 'wordpress' === $settings['gallery_type'] ) {
			$this->render_wp_gallery();
		} elseif ( 'instagram' === $settings['gallery_type'] ) {
			$this->render_instagram_gallery();
		}
	}

	/**
	 * Render Gallery Start
	 * 
	 * Render start tags for gallery wrappers
	 *
	 * @since  2.2.23
	 * @return void
	 */
	protected function render_gallery_start() {
		?><div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'gallery' ); ?>><?php
				$this->render_grid_sizer();
	}

	/**
	 * Render Gallery End
	 * 
	 * Render end tags for gallery wrappers
	 *
	 * @since  2.2.23
	 * @return void
	 */
	protected function render_gallery_end() {
			?></div>
		</div><?php

		$this->render_masonry_script();
	}

	/**
	 * Render Gallery
	 * 
	 * Render custom gallery
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_gallery() {

		$settings 			= $this->get_settings();
		$gallery 			= $settings['gallery'];

		if ( ! empty( $settings['gallery_rand'] ) ) {
			shuffle( $gallery );
		}

		$this->render_gallery_start();

		foreach ( $gallery as $index => $item ) {

			$media_tag 			= 'figure';
			$item_key 			= $this->get_repeater_setting_key( 'item', 'gallery', $index );
			$media_key 			= $this->get_repeater_setting_key( 'media', 'gallery', $index );
			$media_wrapper_key 	= $this->get_repeater_setting_key( 'media-wrapper', 'gallery', $index );

			$this->add_render_attribute( [
				$item_key => [
					'class' => [
						'ee-gallery__item',
						'ee-grid__item',
						'elementor-repeater-item-' . $item['_id'],
					],
				],
				$media_key => [
					'class' => [
						'ee-media',
						'ee-gallery__media',
					],
				],
				$media_wrapper_key => [
					'class' => [
						'ee-media__wrapper',
						'ee-gallery__media-wrapper',
					],
				],
			] );

			if ( 'yes' === $item['custom_size'] || 'yes' === $item['custom_size_tablet'] || 'yes' === $item['custom_size_mobile'] ) {
				$this->add_render_attribute( [
					$media_key => [
						'class' => 'ee-media--stretch',
					],
					$item_key => [
						'class' => 'ee-grid__item--custom-size',
					],
				] );
			}

			if ( '' !== $item['link'] ) {
				$media_tag = 'a';

				if ( 'file' === $item['link'] ) {
					$item_link 	= $item['image']['url'];

					if ( $item['image']['id'] ) {
						$item_link 	= wp_get_attachment_image_src( $item['image']['id'], 'full' );
						$item_link	= $item_link[0];
					}

					$slideshow = $settings['lightbox_slideshow'] ? $this->get_id_for_loop() : false;

					$this->add_lightbox_data_attributes( $media_key, $item['image']['id'], $settings['open_lightbox'], $slideshow );

					if ( $this->_is_edit_mode ) {
						$this->add_render_attribute( $media_key, 'class', 'elementor-clickable' );
					}

				} else if ( 'attachment' === $item['link'] ) {

					$item_link 	= get_attachment_link( $item['image']['id'] );

				} else if ( 'custom' === $item['link'] ) {

					if ( ! empty( $item['link_url']['url'] ) ) {

						$item_link = $item['link_url']['url'];

						if ( ! empty( $item['link_url']['is_external'] ) ) {
							$this->add_render_attribute( $media_key, 'target', '_blank' );
						}

						if ( ! empty( $item['link_url']['nofollow'] ) ) {
							$this->add_render_attribute( $media_key, 'rel', 'nofollow' );
						}
					}

				}

				$this->add_render_attribute( $media_key, 'href', $item_link );
			}

			if ( 'yes' === $settings['tilt_enable'] ) {
				$this->add_render_attribute( $media_wrapper_key, 'class', 'ee-gallery__tilt' );

				if ( 'yes' === $settings['tilt_depth'] ) {
					$this->add_render_attribute( $media_wrapper_key, 'class', 'ee-gallery__tilt--depth' );
				}
			}

			?>

			<div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
				<<?php echo $media_tag; ?> <?php echo $this->get_render_attribute_string( $media_key ); ?>>
					<div <?php echo $this->get_render_attribute_string( $media_wrapper_key ); ?>><?php
						$this->render_image_thumbnail( $item, $index );
						$this->render_image_overlay();
						$this->render_image_caption( $item, $index );
					?></div>
				</<?php echo $media_tag; ?>>
			</div>

		<?php }

		$this->render_gallery_end();
	}

	/**
	 * Render WP Gallery
	 * 
	 * Render wordpress gallery
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_wp_gallery() {

		$settings 	= $this->get_settings();

		$gallery 	= $this->get_settings_for_display( 'wp_gallery' );
		$media_tag 	= 'figure';

		if ( ! empty( $settings['gallery_rand'] ) ) {
			shuffle( $gallery );
		}

		if ( '' !== $settings['gallery_link'] ) {
			$media_tag = 'a';
		}

		$this->render_gallery_start();

		foreach ( $gallery as $index => $item ) {

			$gallery_media_key 			= 'gallery-media' . $index;
			$gallery_media_wrapper_key 	= 'gallery-media-wrapper' . $index;
			$gallery_item_key 			= 'gallery-item' . $index;
			$item_url 					= ( in_array( 'url', $item ) ) ? $item['url'] : '';

			$item['image'] = Module::get_image_info( $item['id'], $item_url, $settings['thumbnail_size'] );

			$this->add_render_attribute( [
				$gallery_media_key => [
					'class' => [
						'ee-media',
						'ee-gallery__media',
					],
				],
				$gallery_media_wrapper_key => [
					'class' => [
						'ee-media__wrapper',
						'ee-gallery__media-wrapper',
					],
				],
				$gallery_item_key => [
					'class' => [
						'ee-gallery__item',
						'ee-gallery__item--' . ( $index + 1 ),
						'ee-grid__item',
					],
				],
			] );

			if ( '' !== $settings['gallery_link'] ) {

				if ( 'file' === $settings['gallery_link'] ) {

					$item_link 	= wp_get_attachment_image_src( $item['id'], 'full' );
					$item_link	= $item_link[0];

					$slideshow = $settings['lightbox_slideshow'] ? $this->get_id_for_loop() : false;

					$this->add_lightbox_data_attributes( $gallery_media_key, $item['id'], $settings['open_lightbox'], $slideshow );

					if ( $this->_is_edit_mode ) {
						$this->add_render_attribute( $gallery_media_key, 'class', 'elementor-clickable' );
					}

				} else if ( 'attachment' === $settings['gallery_link'] ) {

					$item_link 	= get_attachment_link( $item['id'] );

				}

				$this->add_render_attribute( $gallery_media_key, 'href', $item_link );
			}

			if ( 'yes' === $settings['tilt_enable'] ) {
				$this->add_render_attribute( $gallery_media_wrapper_key, 'class', 'ee-gallery__tilt' );

				if ( 'yes' === $settings['tilt_depth'] ) {
					$this->add_render_attribute( $gallery_media_wrapper_key, 'class', 'ee-gallery__tilt--depth' );
				}
			}

			?><div <?php echo $this->get_render_attribute_string( $gallery_item_key ); ?>>
				<<?php echo $media_tag; ?> <?php echo $this->get_render_attribute_string( $gallery_media_key ); ?>>
					<div <?php echo $this->get_render_attribute_string( $gallery_media_wrapper_key ); ?>><?php
						$this->render_image_thumbnail( $item, $index );
						$this->render_image_overlay();
						$this->render_image_caption( $item, $index );
					?></div>
				</<?php echo $media_tag; ?>>
			</div><?php
		}

		$this->render_gallery_end();
	}

	/**
	 * Render the instagram gallery 
	 *
	 * @since  2.1.0
	 * @return empty
	 */
	protected function render_instagram_gallery() {

		$settings = $this->get_settings();

		if ( 'tags' === $settings['insta_display'] && empty( $settings['insta_hashtag'] ) ) {
			return _e( 'Please enter a hashtag.', 'elementor-extras' );
		}

		if ( 'feed' === $settings['insta_display'] && ! $this->get_insta_access_token() ) {
			return _e( 'Please enter your Instagram access token.', 'elementor-extras' );
		}

		$media_tag 	= 'figure';
		$icon_style = 'outline' === $this->get_settings( 'insta_icons_style' ) ? '-o' : '' ;

		$this->add_render_attribute([
			'caption-text' => [
				'class' => 'ee-caption__text',
			],
			'caption-insta' => [
				'class' => 'ee-caption__insta',
			],
			'insta-counter-comments' => [
				'class' => [
					'ee-caption__insta__counter',
					'ee-caption__insta__counter--comments',
				]
			],
			'insta-counter-comments-icon' => [
				'class' => [
					'fa',
					'fa-comment' . $icon_style,
					'ee-caption__insta__icon',
				]
			],
			'insta-counter-likes' => [
				'class' => [
					'ee-caption__insta__counter',
					'ee-caption__insta__counter--likes',
				]
			],
			'insta-counter-likes-icon' => [
				'class' => [
					'fa',
					'fa-heart' . $icon_style,
					'ee-caption__insta__icon',
				]
			],
		]);

		if ( '' !== $settings['gallery_link'] ) {
			$media_tag = 'a';
		}

		$gallery = $this->get_insta_posts( $settings );

		if ( empty( $gallery ) || is_wp_error( $gallery ) ) {
			$message = is_wp_error( $gallery ) ? $gallery->get_error_message() : esc_html__( 'No Posts Found', 'elementor-extras' );

			echo $message;
			return;
		}

		if ( ! empty( $settings['gallery_rand'] ) ) {
			shuffle( $gallery );
		}

		$this->render_gallery_start();

		foreach ( $gallery as $index => $item ) {

			$item_key 			= $this->get_repeater_setting_key( 'item', 'gallery', $index );
			$media_key 			= $this->get_repeater_setting_key( 'media', 'gallery', $index );
			$image_key 			= $this->get_repeater_setting_key( 'image', 'gallery', $index );
			$media_wrapper_key 	= $this->get_repeater_setting_key( 'wrapper', 'gallery', $index );

			$this->add_render_attribute( [
				$item_key => [
					'class' => [
						'ee-gallery__item',
						'ee-grid__item',
						'elementor-repeater-item-' . $index,
					],
				],
				$media_key => [
					'class' => [
						'ee-media',
						'ee-gallery__media',
					],
				],
				$media_wrapper_key => [
					'class' => [
						'ee-media__wrapper',
						'ee-gallery__media-wrapper',
					],
				],
			] );

			if ( '' !== $settings['gallery_link'] ) {

				if ( 'file' === $settings['gallery_link'] ) {

					$item_link = $this->get_insta_image_url( $item, 'high' );

					$this->add_render_attribute( $media_key, [
						'data-elementor-open-lightbox' 		=> $settings['open_lightbox'],
						'data-elementor-lightbox-title' 	=> $item['caption'],
					] );

					if ( $settings['lightbox_slideshow'] ) {
						$this->add_render_attribute( $media_key, 'data-elementor-lightbox-slideshow', $this->get_id() );
					}

					if ( $this->_is_edit_mode ) {
						$this->add_render_attribute( $media_key, 'class', 'elementor-clickable' );
					}

				} else if ( 'attachment' === $settings['gallery_link'] ) {

					$item_link 	= $item['link'];

					$this->add_render_attribute( $media_key, 'target', '_blank' );
				}

				$this->add_render_attribute( $media_key, 'href', $item_link );
			}

			if ( 'yes' === $settings['tilt_enable'] ) {
				$this->add_render_attribute( $media_wrapper_key, 'class', 'ee-gallery__tilt' );

				if ( 'yes' === $settings['tilt_depth'] ) {
					$this->add_render_attribute( $media_wrapper_key, 'class', 'ee-gallery__tilt--depth' );
				}
			}

			?><div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
				<<?php echo $media_tag; ?> <?php echo $this->get_render_attribute_string( $media_key ); ?>>
					<div <?php echo $this->get_render_attribute_string( $media_wrapper_key ); ?>>
						<?php $this->render_image_thumbnail( $item, $index ); ?>
						<?php $this->render_image_overlay(); ?>
						<?php $this->render_image_caption( $item, $index ); ?>
					</div>
				</<?php echo $media_tag; ?>>				
			</div><?php
		}

		$this->render_gallery_end();

	}

	/**
	 * Render Grid Sizer
	 * 
	 * The sizer for masonry layout mode
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_grid_sizer() {
		$settings = $this->get_settings();

		if ( 'yes' === $settings['masonry_enable'] && 'yes' !== $settings['parallax_enable'] ) {
			?><div class="ee-grid__item ee-grid__item--sizer"></div><?php
		}
	}

	/**
	 * Render Image Thumbnail
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_image_thumbnail( $item, $index ) {

		$settings 			= $this->get_settings();
		$thumbnail_url 		= $this->get_thumbnail_image_url( $item, $settings );
		$thumbnail_alt 		= $this->get_thumbnail_image_alt( $item );
		$thumbnail_title 	= $this->get_thumbnail_image_title( $item );
		$image_key 			= $this->get_repeater_setting_key( 'image', 'gallery', $index );

		$this->add_render_attribute( $image_key, 'src', $thumbnail_url );

		if ( '' !== $thumbnail_alt ) {
			$this->add_render_attribute( $image_key, 'alt', $thumbnail_alt );
		}

		if ( '' !== $thumbnail_title ) {
			$this->add_render_attribute( $image_key, 'title', $thumbnail_title );
		}

		?><div <?php echo $this->get_render_attribute_string( 'gallery-thumbnail' ); ?>>
			<img <?php echo $this->get_render_attribute_string( $image_key ); ?> />
		</div><?php
	}

	/**
	 * Render Image Caption
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_image_caption( $item, $index ) {
		$settings = $this->get_settings();
		$caption = $this->get_item_caption( $item );

		if ( ! $caption )
			return;

		?><figcaption <?php echo $this->get_render_attribute_string( 'gallery-content' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'gallery-caption' ); ?>>
				<?php echo $caption; ?>
			</div>
		</figcaption><?php
	}

	/**
	 * Render Image Overlay
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_image_overlay() {
		?><div <?php echo $this->get_render_attribute_string( 'gallery-overlay' ); ?>></div><?php
	}

	/**
	 * Get Thumbnail Image URL
	 *
	 * @since  2.1.0
	 * @return string 	The url of the attachment
	 */
	protected function get_thumbnail_image_url( $item, array $settings ) {

		if ( $this->is_instagram_gallery() ) {
			$image_url = $this->get_insta_image_url( $item, $this->get_settings('insta_image_size') );
		} else {
			$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'thumbnail', $settings );
		}

		if ( ! $image_url ) {
			$image_url = $item['image']['url'];
		}

		return $image_url;
	}

	/**
	 * Get Insta Thumbnail Image URL
	 *
	 * @since  2.2.23
	 * @return string 	The url of the instagram post image
	 */
	protected function get_insta_image_url( $item, $size = 'high' ) {
		$thumbnail  = $item['thumbnail'];

		if ( ! empty( $thumbnail[ $size ] ) ) {
			$image_url = $thumbnail[ $size ]['src'];
		} else {
			$image_url = isset( $item['image'] ) ? $item['image'] : '';
		}

		return $image_url;
	}

	/**
	 * Get Thumbnail Image Alt Text
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_thumbnail_image_alt( $item ) {
		if ( $this->is_instagram_gallery() )
			return $item['caption'];

		return trim( strip_tags( get_post_meta( $item['image']['id'], '_wp_attachment_image_alt', true) ) );
	}

	/**
	 * Get Thumbnail Image Title
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_thumbnail_image_title( $item ) {
		if ( $this->is_instagram_gallery() )
			return $item['caption'];

		return trim( strip_tags( get_the_title( $item['image']['id'] ) ) );
	}

	/**
	 * Get Item Caption
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_item_caption( $item ) {
		if ( $this->is_instagram_gallery() ) {
			return $this->get_insta_caption( $item );
		}

		$attachment = get_post( $item['image']['id'] );

		return ImageModule::get_image_caption( $attachment, $this->get_settings( 'gallery_caption' ) );
	}

	/**
	 * Get Insta Caption
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_insta_caption( $item ) {

		$settings = $this->get_settings();

		ob_start();

		if ( '' !== $settings['insta_counter_caption'] ) {
			?><div <?php echo $this->get_render_attribute_string( 'caption-text' ); ?>><?php echo $item['caption']; ?></div><?php
		}

		if ( '' !== $settings['insta_counter_comments'] || '' !== $settings['insta_counter_likes'] ) {
			?><div <?php echo $this->get_render_attribute_string( 'caption-insta' ); ?>><?php

			if ( '' !== $settings['insta_counter_comments'] ) {
				?><span <?php echo $this->get_render_attribute_string( 'insta-counter-comments' ); ?>>
					<i <?php echo $this->get_render_attribute_string( 'insta-counter-comments-icon' ); ?>></i><?php echo $item['comments']; ?>
				</span><?php
			}

			if ( '' !== $settings['insta_counter_likes'] ) {
				?><span <?php echo $this->get_render_attribute_string( 'insta-counter-likes' ); ?>>
					<i <?php echo $this->get_render_attribute_string( 'insta-counter-likes-icon' ); ?>></i><?php echo $item['likes']; ?>
				</span><?php
			}
			
			?></div><?php	
		}

		return ob_get_clean();
	}

	/**
	 * Get Instagram Comments
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_insta_comments( $item ) {
		if ( $this->is_instagram_gallery() )
			return $item['comments'];
	}

	/**
	 * Render Masonry script
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_masonry_script() {

		if ( ! $this->_is_edit_mode )
			return;

		if ( 'yes' !== $this->get_settings( 'masonry_enable' ) || 'yes' === $this->get_settings( 'parallax_enable' ) )
			return;

		?><script type="text/javascript">
        	jQuery( document ).ready( function( $ ) {

				$( '.ee-gallery' ).each( function() {

					var $scope_id = '<?php echo $this->get_id(); ?>',
        				$scope = $( '[data-id="' + $scope_id + '"]' );

        			// Don't move forward if this is not our widget
        			if ( $(this).closest( $scope ).length < 1 ) {
        				return;
        			}

					var $gallery 	= $(this),
						isotopeArgs = {
							itemSelector	: '.ee-gallery__item',
			  				percentPosition : true,
			  				hiddenStyle 	: {
			  					opacity 	: 0,
			  				},
						};

					var $isotope = $gallery.isotope( isotopeArgs );

					$isotope.masonry();

					$gallery.find('.ee-gallery__item')._resize( function() {
						$isotope.masonry();
					});

					$(window).resize( function() {
						$isotope.masonry();
					});

				} );
				
        	} );
		</script><?php
	}

	/**
	 * Check if gallery source is Instagram
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function is_instagram_gallery() {

		$settings = $this->get_settings();

		if ( 'instagram' === $settings['gallery_type'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieve Instagram posts.
	 *
	 * @since  2.1.0
	 * @param  array $settings
	 * @return array
	 */
	public function get_insta_posts( $settings ) {

		// $user = $this->get_insta_user_id();
		// $user_media = $this->get_insta_user_media( $user['id'] );

		// foreach( $user_media['data'] as $media ) {
		// 	$media_object = $this->get_insta_media( $media['id'] );
		// }

		$response = $this->get_insta_remote( $this->get_fetch_url() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$data = ( 'tags' === $settings['insta_display'] ) ? $this->get_insta_tags_response_data( $response ) : $this->get_insta_feed_response_data( $response );

		if ( empty( $data ) ) {
			return array();
		}

		return $data;
	}

	/**
	 * Retrieve response from API
	 *
	 * @since  2.1.0
	 * @return array|WP_Error
	 */
	public function get_insta_remote( $url ) {
		$response 		= wp_remote_get( $url, array(
			'timeout'   => 60,
			'sslverify' => false
		) );

		$response_code 	= wp_remote_retrieve_response_code( $response );
		$result 		= json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $response_code ) {
			$message = is_array( $result ) && isset( $result['error']['message'] ) ? $result['error']['message'] : __( 'No posts found', 'elementor-extras' );

			return new \WP_Error( $response_code, $message );
		}

		if ( ! is_array( $result ) ) {
			return new \WP_Error( 'error', __( 'Data Error', 'elementor-extras' ) );
		}

		return $result;
	}

	public function get_insta_user_id() {
		$result = $this->get_insta_remote( $this->get_user_url() );
		return $result;
	}

	public function get_insta_user_media( $user_id ) {
		$result = $this->get_insta_remote( $this->get_user_media_url( $user_id ) );

		return $result;
	}

	public function get_insta_media( $media_id ) {
		$result = $this->get_insta_remote( $this->get_media_url( $media_id ) );

		return $result;
	}

	/**
	 * Retrieve a grab URL.
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_fetch_url() {

		$settings = $this->get_settings();

		if ( 'tags' == $settings['insta_display'] ) {
			$url = sprintf( $this->get_tags_endpoint(), $settings['insta_hashtag'] );
			$url = add_query_arg( array( '__a' => 1 ), $url );

		} else if ( 'feed' == $settings['insta_display'] ) {
			$url = $this->get_feed_endpoint();
			$url = add_query_arg( [
				'fields'       => 'id,media_type,media_url,thumbnail_url,permalink,caption,likes_count,likes',
				'access_token' => $this->get_insta_access_token(),
			], $url );
		}

		return $url;
	}

	public function get_user_url() {
		$url = $this->get_user_endpoint();
		$url = add_query_arg( [
			'access_token' => $this->get_insta_access_token(),
			// 'fields' => 'media.limit(10){comments_count,like_count,likes,likes_count,media_url,permalink,caption}',
		], $url );

		return $url;
	}

	public function get_user_media_url( $user_id ) {
		$url = sprintf( $this->get_user_media_endpoint(), $user_id );
		$url = add_query_arg( [
			'access_token' => $this->get_insta_access_token(),
			'fields' => 'id,like_count',
		], $url );

		return $url;
	}

	public function get_media_url( $media_id ) {
		$url = sprintf( $this->get_media_endpoint(), $media_id );
		$url = add_query_arg( [
			'access_token' => $this->get_insta_access_token(),
			'fields' => 'id,media_type,media_url,timestamp,like_count',
		], $url );

		return $url;
	}

	/**
	 * Retrieve a URL for own photos.
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_feed_endpoint() {
		return $this->insta_official_api_url . 'me/media/';
	}

	/**
	 * Retrieve a URL for photos by hashtag.
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_tags_endpoint() {
		return $this->insta_api_url . 'explore/tags/%s/';
	}

	public function get_user_endpoint() {
		return $this->insta_official_api_url . 'me/';
	}

	public function get_user_media_endpoint() {
		return $this->insta_official_api_url . '%s/media/';
	}

	public function get_media_endpoint() {
		return $this->insta_official_api_url . '%s/';
	}

	/**
	 * Get data from response
	 *
	 * @param $response
	 * @since  2.1.0
	 *
	 * @return array
	 */
	public function get_insta_feed_response_data( $response ) {

		if ( ! array_key_exists( 'data', $response ) ) { // Avoid PHP notices
			return;
		}

		$response_posts = $response['data'];

		if ( empty( $response_posts ) ) {
			return array();
		}

		$return_data  = array();
		$posts = array_slice( $response_posts, 0, $this->get_settings('insta_posts_counter'), true );

		foreach ( $posts as $post ) {
			$_post				= array();

			$_post['id'] 		= $post['id'];
			$_post['link']		= $post['permalink'];
			$_post['caption']	= '';
			$_post['image'] 	= 'VIDEO' === $post['media_type'] ? $post['thumbnail_url'] : $post['media_url'];
			$_post['comments']	= ! empty( $post['comments_count'] ) ? $post['comments_count'] : 0;
			$_post['likes']		= ! empty( $post['likes_count'] ) ? $post['likes_count'] : 0;;
			$_post['thumbnail'] = $this->get_insta_feed_thumbnail_data( $post );

			if ( ! empty( $post['caption'] ) ) {
				$_post['caption'] = wp_html_excerpt( $post['caption'], $this->get_settings('insta_caption_length'), '&hellip;' );
			}

			$return_data[] = $_post;
		}

		return $return_data;
	}

	/**
	 * Get thumbnail data from response data
	 *
	 * @param $post
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function get_insta_feed_thumbnail_data( $post ) {
		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'		=> false,
		);

		if ( ! empty( $post['images'] ) && is_array( $post['images'] ) ) {

			$data = $post['images'];

			$thumbnail['thumbnail'] = [
				'src'           => $data['thumbnail']['url'],
				'config_width'  => $data['thumbnail']['width'],
				'config_height' => $data['thumbnail']['height'],
			];

			$thumbnail['low'] = [
				'src'           => $data['low_resolution']['url'],
				'config_width'  => $data['low_resolution']['width'],
				'config_height' => $data['low_resolution']['height'],
			];

			$thumbnail['standard'] = [
				'src'           => $data['standard_resolution']['url'],
				'config_width'  => $data['standard_resolution']['width'],
				'config_height' => $data['standard_resolution']['height'],
			];

			$thumbnail['high'] = $thumbnail['standard'];
		}

		return $thumbnail;
	}

	/**
	 * Get data from response
	 *
	 * @param  $response
	 * @since  2.1.0
	 *
	 * @return array
	 */
	public function get_insta_tags_response_data( $response ) {

		$settings = $this->get_settings();
		$response_posts = $response['graphql']['hashtag']['edge_hashtag_to_media']['edges'];

		if ( empty( $response_posts ) ) {
			$response_posts = $response['graphql']['hashtag']['edge_hashtag_to_top_posts']['edges'];
		}

		$return_data  = array();
		$posts = array_slice( $response_posts, 0, $settings['insta_posts_counter'], true );

		foreach ( $posts as $post ) {
			$_post				= array();

			$_post['link']		= sprintf( $this->insta_api_url . 'p/%s/', $post['node']['shortcode'] );
			$_post['caption']	= '';
			$_post['comments']	= $post['node']['edge_media_to_comment']['count'];
			$_post['likes']		= $post['node']['edge_liked_by']['count'];
			$_post['thumbnail'] = $this->get_insta_tags_thumbnail_data( $post );

			if ( isset( $post['node']['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
				$_post['caption'] = wp_html_excerpt( $post['node']['edge_media_to_caption']['edges'][0]['node']['text'], $settings['insta_caption_length'], '&hellip;' );
			}

			$return_data[] = $_post;
		}

		return $return_data;
	}

	/**
	 * Generate thumbnail resources.
	 *
	 * @since 2.1.0
	 * @param $post_data
	 *
	 * @return array
	 */
	public function get_insta_tags_thumbnail_data( $post ) {
		$post = $post['node'];

		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'		=> false,
		);

		if ( is_array( $post['thumbnail_resources'] ) && ! empty( $post['thumbnail_resources'] ) ) {
			foreach ( $post['thumbnail_resources'] as $key => $resources_data ) {

				if ( 150 === $resources_data['config_width'] ) {
					$thumbnail['thumbnail'] = $resources_data;
					continue;
				}

				if ( 320 === $resources_data['config_width'] ) {
					$thumbnail['low'] = $resources_data;
					continue;
				}

				if ( 640 === $resources_data['config_width'] ) {
					$thumbnail['standard'] = $resources_data;
					continue;
				}
			}
		}

		if ( ! empty( $post['display_url'] ) ) {
			$thumbnail['high'] = array(
				'src'           => $post['display_url'],
				'config_width'  => $post['dimensions']['width'],
				'config_height' => $post['dimensions']['height'],
			) ;
		}

		return $thumbnail;
	}

	/**
	 * Get Instagram access token.
	 *
	 * @since 2.1.0
	 * @return string
	 */
	public function get_insta_access_token() {
		$settings = $this->get_settings_for_display();

		if ( ! $this->insta_access_token ) {
			$custom_access_token = $settings['access_token'];

			if ( '' !== trim( $custom_access_token ) ) {
				$this->insta_access_token = $custom_access_token;
			} else {
				$this->insta_access_token = $this->get_insta_global_access_token();
			}
		}

		return $this->insta_access_token;
	}

	/**
	 * Get Instagram access token from wp options.
	 *
	 * @since 2.2.23
	 * @return string
	 */
	public function get_insta_global_access_token() {
		return \ElementorExtras\ElementorExtrasPlugin::$instance->settings->get_option( 'instagram_access_token', 'elementor_extras_apis', false );
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _content_template() {}
}
