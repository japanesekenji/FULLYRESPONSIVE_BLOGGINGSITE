<?php
namespace ElementorExtras\Modules\MediaPlayer\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\MediaPlayer\Skins;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * HTML5_Video
 *
 * @since 1.3.0
 */
class HTML5_Video extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  1.3.0
	 * @return string
	 */
	public function get_name() {
		return 'html5-video';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  1.3.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Video Player', 'elementor-extras' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  1.3.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-video';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  1.3.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'video-player',
			'jquery-appear',
			'iphone-inline-video',
		];
	}

	/**
	 * Register Skins
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function _register_skins() {
		// Comment until future development
		// $this->add_skin( new Skins\Skin_Classic( $this ) );
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_content',
			[
				'label' 	=> __( 'Content', 'elementor-extras' ),
			]
		);

			$this->start_controls_tabs( 'tabs_sources' );

			$this->start_controls_tab(
				'tab_source_mp4',
				[
					'label' => __( 'MP4', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'video_source',
					[
						'label' 		=> __( 'Source', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT,
						'default'		=> 'url',
						'options'		=> [
							'url'		=> __( 'URL', 'elementor-extras' ),
							'file'		=> __( 'File', 'elementor-extras' ),
						],
					]
				);

				$this->add_control(
					'video_url',
					[
						'label' 		=> __( 'URL', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'description' 	=> __( 'Insert URL to an .mp4 video file', 'elementor-extras' ),
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::URL_CATEGORY,
							],
						],
						'condition' 	=> [
							'video_source' => 'url',
						],
					]
				);

				$this->add_control(
					'video_file',
					[
						'label' 		=> __( 'File', 'elementor-extras' ),
						'type' 			=> Controls_Manager::MEDIA,
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::MEDIA_CATEGORY,
							],
						],
						'media_type' => 'video',
						'condition' 	=> [
							'video_source' => 'file',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_source_m4v',
				[
					'label' => __( 'M4V', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'video_source_m4v',
					[
						'label' 		=> __( 'Source', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT,
						'default'		=> 'url',
						'options'		=> [
							'url'		=> __( 'URL', 'elementor-extras' ),
							'file'		=> __( 'File', 'elementor-extras' ),
						],
					]
				);

				$this->add_control(
					'video_url_m4v',
					[
						'label' 		=> __( 'URL', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'description' 	=> __( 'Insert URL to a .m4v video file', 'elementor-extras' ),
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::URL_CATEGORY,
							],
						],
						'condition' 	=> [
							'video_source_m4v' => 'url',
						],
					]
				);

				$this->add_control(
					'video_file_m4v',
					[
						'label' 		=> __( 'File', 'elementor-extras' ),
						'type' 			=> Controls_Manager::MEDIA,
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::MEDIA_CATEGORY,
							],
						],
						'media_type' => 'video',
						'condition' 	=> [
							'video_source_m4v' => 'file',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_source_ogg',
				[
					'label' => __( 'OGG', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'video_source_ogg',
					[
						'label' 		=> __( 'Source', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT,
						'default'		=> 'url',
						'options'		=> [
							'url'		=> __( 'URL', 'elementor-extras' ),
							'file'		=> __( 'File', 'elementor-extras' ),
						],
					]
				);

				$this->add_control(
					'video_url_ogg',
					[
						'label' 		=> __( 'URL', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'description' 	=> __( 'Insert URL to an .ogg video file', 'elementor-extras' ),
						'dynamic' 		=> [
							'active' 	=> true,
						],
						'condition' 	=> [
							'video_source_ogg' => 'url',
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::URL_CATEGORY,
							],
						],
					]
				);

				$this->add_control(
					'video_file_ogg',
					[
						'label' 		=> __( 'File', 'elementor-extras' ),
						'type' 			=> Controls_Manager::MEDIA,
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::MEDIA_CATEGORY,
							],
						],
						'media_type' => 'video',
						'condition' 	=> [
							'video_source_ogg' => 'file',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_source_webm',
				[
					'label' => __( 'WEBM', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'video_source_webm',
					[
						'label' 		=> __( 'Source', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT,
						'default'		=> 'url',
						'options'		=> [
							'url'		=> __( 'URL', 'elementor-extras' ),
							'file'		=> __( 'File', 'elementor-extras' ),
						],
					]
				);

				$this->add_control(
					'video_url_webm',
					[
						'label' 		=> __( 'URL', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'description' 	=> __( 'Insert URL to a .webm video file', 'elementor-extras' ),
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::URL_CATEGORY,
							],
						],
						'condition' 	=> [
							'video_source_webm' => 'url',
						],
					]
				);

				$this->add_control(
					'video_file_webm',
					[
						'label' 		=> __( 'File', 'elementor-extras' ),
						'type' 			=> Controls_Manager::MEDIA,
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::MEDIA_CATEGORY,
							],
						],
						'media_type' => 'video',
						'condition' 	=> [
							'video_source_webm' => 'file',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'video_cover',
				[
					'label' 		=> __( 'Choose Cover', 'elementor-extras' ),
					'type' 			=> Controls_Manager::MEDIA,
					'dynamic' 		=> [ 'active' => true ],
					'separator'		=> 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'video_cover',
					'label' 	=> __( 'Cover Size', 'elementor-extras' ),
					'default' 	=> 'large',
					'condition'	=> [
						'video_cover[url]!'		=> '',
					]
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'video_behaviour_heading',
				[
					'label' 	=> __( 'Behaviour', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'video_autoplay',
				[
					'label' 		=> __( 'Auto Play', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'autoplay',
				]
			);

			$this->add_control(
				'video_autoplay_notice',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'Many browsers don\'t allow videos with sound to autoplay without user interaction. To avoid this, enable the "Start Muted" control to disable sound so that the video autoplays correctly.', 'elementor-extras' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
					'condition' 		=> [
						'video_autoplay!' => ''
					],
				]
			);		

			$this->add_control(
				'video_stop_others',
				[
					'label' 		=> __( 'Stop Others', 'elementor-extras' ),
					'description' 	=> __( 'Stop all other videos on page when this video is played.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'video_play_viewport',
				[
					'label' 		=> __( 'Play in Viewport', 'elementor-extras' ),
					'description' 	=> __( 'Autoplay video when the player is in viewport', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'condition'	=> [
						'video_autoplay'		=> 'autoplay',
					],
					'frontend_available'		=> true,
				]
			);

			$this->add_control(
				'video_stop_viewport',
				[
					'label' 		=> __( 'Stop on leave', 'elementor-extras' ),
					'description' 	=> __( 'Stop video when the player has left the viewport', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'condition'	=> [
						'video_autoplay'		=> 'autoplay',
						'video_play_viewport'	=> 'yes',
					],
					'frontend_available'		=> true,
				]
			);

			$this->add_control(
				'video_restart_on_pause',
				[
					'label' 		=> __( 'Restart on pause', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available'	=> true,
				]
			);

			$this->add_control(
				'video_loop',
				[
					'label' 		=> __( 'Loop', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'loop',
				]
			);

			$this->add_control(
				'video_end_at_last_frame',
				[
					'label' 		=> __( 'End at last frame', 'elementor-extras' ),
					'description' 	=> __( 'End the video at the last frame instead of showing the first one.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'condition'	=> [
						'video_loop'	=> '',
					],
					'frontend_available'		=> true,
				]
			);

			$this->add_responsive_control(
				'video_speed',
				[
					'label' 	=> __( 'Playback Speed', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 1,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 5,
							'min' 	=> 0.1,
							'step' 	=> 0.01,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'video_display_heading',
				[
					'label' 	=> __( 'Display', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'video_show_buttons',
				[
					'label' 		=> __( 'Show Buttons', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
				]
			);

			$this->add_control(
				'video_show_bar',
				[
					'label' 		=> __( 'Show Bar', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
				]
			);

			$this->add_control(
				'video_bar_hide',
				[
					'label' 		=> __( 'Hide Bar When Playing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'hide',
					'prefix_class' 	=> 'ee-video-player-bar--',
					'return_value'	=> 'hide',
					'condition' 	=> [
						'video_show_bar' => 'show',
					],
				]
			);

			$this->add_control(
				'video_show_rewind',
				[
					'label' 		=> __( 'Show Rewind', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition' 	=> [
						'video_restart_on_pause!' => 'yes',
					],
				]
			);

			$this->add_control(
				'video_show_time',
				[
					'label' 		=> __( 'Show Time', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'	=> [
						'video_show_bar!'		=> '',
					]
				]
			);

			$this->add_control(
				'video_show_progress',
				[
					'label' 		=> __( 'Show Progress', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'	=> [
						'video_show_bar!'		=> '',
					]
				]
			);

			$this->add_control(
				'video_show_duration',
				[
					'label' 		=> __( 'Show Duration', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'	=> [
						'video_show_bar!'		=> '',
					]
				]
			);

			$this->add_control(
				'video_show_fs',
				[
					'label' 		=> __( 'Show Fullscreen', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'	=> [
						'video_show_bar!'		=> '',
					]
				]
			);

			$this->add_control(
				'video_fs_hide_download',
				[
					'label' 		=> __( 'Hide Fullscreen Download', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'condition' 	=> [
						'video_show_fs!' => '',
					],
				]
			);

			$this->add_control(
				'video_fs_no_rightclick',
				[
					'label' 		=> __( 'Disable Fullscreen Right Click', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
					'condition' 	=> [
						'video_show_fs!' => '',
					],
				]
			);	

			$this->add_control(
				'video_volume_heading',
				[
					'label' 	=> __( 'Volume', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'video_show_volume',
				[
					'label' 		=> __( 'Show Volume', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'	=> [
						'video_show_bar!'		=> '',
					]
				]
			);

			$this->add_control(
				'video_show_volume_icon',
				[
					'label' 		=> __( 'Show Volume Icon', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'	=> [
						'video_show_bar!'		=> '',
						'video_show_volume!'	=> '',
					]
				]
			);

			$this->add_control(
				'video_show_volume_bar',
				[
					'label' 		=> __( 'Show Volume Bar', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'	=> [
						'video_show_bar!'		=> '',
						'video_show_volume!'	=> '',
					]
				]
			);

			$this->add_control(
				'video_start_muted',
				[
					'label' 		=> __( 'Start Muted', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'condition'	=> [
						'video_autoplay!'	=> '',
					]
				]
			);

			$this->add_responsive_control(
				'video_volume',
				[
					'label' 	=> __( 'Initial Volume', 'elementor-extras' ),
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
					'frontend_available' => true,
					'condition'	=> [
						'video_start_muted!' => 'yes',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_style',
			[
				'label' => __( 'Player', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'video_width',
				[
					'label' 		=> __( 'Width', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'%' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
						],
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 600,
						],
					],
					'size_units' => [ '%', 'px' ],
					'selectors'		=> [
						'{{WRAPPER}} .ee-video-player' => 'max-width: {{SIZE}}{{UNIT}};',
					]
				]
			);

			$this->add_responsive_control(
				'video_align',
				[
					'label' => __( 'Alignment', 'elementor-extras' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'elementor-extras' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'elementor-extras' ),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'elementor-extras' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default' => 'center',
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'video_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-video-player',
				]
			);

			$this->add_control(
				'video_border_radius',
				[
					'label' 			=> __( 'Border Radius', 'elementor-extras' ),
					'type' 					=> Controls_Manager::DIMENSIONS,
					'size_units' 			=> [ 'px', '%' ],
					'selectors' 			=> [
						'{{WRAPPER}} .ee-video-player' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'video_box_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-video-player',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_overlay',
			[
				'label' => __( 'Overlay', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'video_overlay_background',
				[
					'label' 	=> __( 'Background', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '#000000',
					'selectors' => [
						'{{WRAPPER}} .ee-video-player__cover::after' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'video_overlay_opacity',
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
						'{{WRAPPER}} .ee-video-player__cover::after' => 'opacity: {{SIZE}}',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Interface', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' 	=> 'or',
					'terms' 	=> [
						[
							'name' 		=> 'video_show_buttons',
							'operator' 	=> '==',
							'value' 	=> 'show',
						],
						[
							'name' 		=> 'video_show_bar',
							'operator' 	=> '==',
							'value' 	=> 'show',
						],
					],
				],
			]
		);

			$this->add_control(
				'video_controls_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'default' 		=> [
						'top' 		=> 100,
						'right' 	=> 100,
						'bottom' 	=> 100,
						'left' 		=> 100,
						'unit' 		=> 'px'
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control,
						{{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar .ee-player__control--progress' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar .ee-player__control--progress__inner' => 'border-radius: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0;'
					],
				]
			);

			$this->start_controls_tabs( 'tabs_controls_style' );

			$this->start_controls_tab(
				'video_controls',
				[
					'label' => __( 'Default', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'video_controls_foreground',
					[
						'label' 	=> __( 'Controls Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#ffffff',
						'selectors' => [
							'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control,
							 {{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar' => 'color: {{VALUE}}',

							'{{WRAPPER}} .ee-video-player__controls .ee-player__control--progress__inner' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'video_controls_background',
					[
						'label' 	=> __( 'Controls Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control,
							 {{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'video_controls_opacity',
					[
						'label' 	=> __( 'Controls Opacity (%)', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0.9,
						],
						'range' 	=> [
							'px' 	=> [
								'max' 	=> 1,
								'min' 	=> 0,
								'step' 	=> 0.01,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control,
							 {{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar' => 'opacity: {{SIZE}}',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' 		=> 'video_controls_border',
						'label' 	=> __( 'Border', 'elementor-extras' ),
						'selector' 	=>
							'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control,
							 {{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar',
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'video_controls_shadow',
						'selector' 	=>
							'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control,
							 {{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar',
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'video_controls_hover',
				[
					'label' => __( 'Hover', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'video_controls_foreground_hover',
					[
						'label' 	=> __( 'Controls Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'(desktop+){{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control:hover,
							 {{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar:hover' => 'color: {{VALUE}}',

							'(desktop+){{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar:hover .ee-player__control--progress__inner' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'video_controls_background_hover',
					[
						'label' 	=> __( 'Controls Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'(desktop+){{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control:hover,
							 {{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar:hover' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'video_controls_opacity_hover',
					[
						'label' 	=> __( 'Controls Opacity (%)', 'elementor-extras' ),
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
							'(desktop+){{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control:hover,
							 {{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar:hover' => 'opacity: {{SIZE}}',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' 		=> 'video_controls_border_hover',
						'label' 	=> __( 'Border', 'elementor-extras' ),
						'selector' 	=>
							'(desktop+){{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control:hover,
							{{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar:hover',
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'video_controls_shadow_hover',
						'selector' 	=>
							'(desktop+){{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control:hover,
							{{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar:hover',
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons_style',
			[
				'label' => __( 'Buttons', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'video_show_buttons!' => '',
				],
			]
		);

			$this->add_responsive_control(
				'video_buttons_size',
				[
					'label' => __( 'Size (%)', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 60,
					],
					'range' => [
						'px' => [
							'min' => 10,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__control' => 'font-size: {{SIZE}}px; width: {{SIZE}}px; height: {{SIZE}}px;',
					],
					'condition'		=> [
						'video_show_buttons!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'video_buttons_spacing',
				[
					'label' => __( 'Controls Spacing', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => '',
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__overlay .ee-player__controls__rewind' => 'margin-right: {{SIZE}}px;',
					],
					'condition'		=> [
						'video_show_buttons!' => '',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_bar_style',
			[
				'label' => __( 'Bar', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'video_show_bar!' => '',
				],
			]
		);

			$this->add_responsive_control(
				'video_bar_padding',
				[
					'label' 	=> __( 'Padding', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 72,
							'min' 	=> 0,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar' => 'padding: {{SIZE}}px',
					],
					'condition'		=> [
						'video_show_bar!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'video_bar_margin',
				[
					'label' 	=> __( 'Distance', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 72,
							'min' 	=> 0,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar-wrapper' => 'padding: 0 {{SIZE}}px {{SIZE}}px {{SIZE}}px',
					],
					'condition'		=> [
						'video_show_bar!' => '',
					],
				]
			);

			$this->add_control(
				'video_bar_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'selectors' 	=> [
						'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'		=> [
						'video_show_bar!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'bar',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-player__control--indicator',
				]
			);

			$this->add_control(
				'controls_heading',
				[
					'label' 	=> __( 'Controls', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
					'condition'		=> [
						'video_show_bar!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'video_bar_zoom',
				[
					'label' 	=> __( 'Zoom', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 36,
							'min' 	=> 6,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar' => 'font-size: {{SIZE}}px',
						'{{WRAPPER}} .ee-video-player__controls .ee-player__controls__bar .ee-player__control--progress' => 'height: {{SIZE}}px',
					],
					'condition'		=> [
						'video_show_bar!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'video_bar_spacing',
				[
					'label' 	=> __( 'Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 24,
							'min' 	=> 3,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-video-player__controls .ee-player__control--indicator,
						 {{WRAPPER}} .ee-video-player__controls .ee-player__control--icon' => 'padding: 0 {{SIZE}}px',
						'{{WRAPPER}} .ee-video-player__controls .ee-player__control--progress' => 'margin: 0 {{SIZE}}px',
					],
					'condition'		=> [
						'video_show_bar!' => '',
					],
				]
			);

			$this->add_control(
				'controls_progress_heading',
				[
					'label' 	=> __( 'Progress', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
					'condition'	=> [
						'video_show_bar!' => '',
						'video_show_progress!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'progress_width',
				[
					'label' 	=> __( 'Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 90,
							'min' 	=> 10,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-player__controls__progress' => 'flex-basis: {{SIZE}}%',
					],
					'condition'	=> [
						'video_show_bar!' => '',
						'video_show_progress!' => '',
					],
				]
			);

			$this->add_control(
				'controls_volume_heading',
				[
					'label' 	=> __( 'Volume', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
					'condition'	=> [
						'video_show_bar!' => '',
						'video_show_volume_bar!' => '',
						'video_show_volume!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'volume_width',
				[
					'label' 	=> __( 'Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 90,
							'min' 	=> 10,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-player__controls__volume' => 'flex-basis: {{SIZE}}%',
					],
					'separator' => 'after',
					'condition'	=> [
						'video_show_bar!' => '',
						'video_show_volume_bar!' => '',
						'video_show_volume!' => '',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  1.3.0
	 * @return void
	 */
	public function render() {
		$settings = $this->get_settings_for_display();

		if (
			empty( $settings['video_file']['url'] ) &&
			empty( $settings['video_file_ogg']['url'] ) &&
			empty( $settings['video_file_webm']['url'] ) &&
			empty( $settings['video_file_m4v']['url'] ) &&
			empty( $settings['video_url'] ) &&
			empty( $settings['video_url_ogg'] ) &&
			empty( $settings['video_url_webm'] ) &&
			empty( $settings['video_url_m4v'] )
		) return;

		$this->add_render_attribute( [
			'video-wrapper' => [
				'class' => [
					'ee-video-player',
					'ee-player'
				],
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( 'video-wrapper' ); ?>>
			<?php $this->render_video(); ?>
			<?php $this->render_cover(); ?>
			<?php $this->render_controls(); ?>
		</div><!-- .ee-video-player -->
		<?php
	}

	/**
	 * Render Video
	 * 
	 * Render html5 video markup
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function render_video() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'video', [
			'class' => [
				'ee-video-player__source',
				'ee-player__source'
			],
			'playsinline' => 'true',
			'width' => '100%',
			'height' => '100%',
		] );

		if ( 'yes' === $settings['video_fs_hide_download'] )  {
			$this->add_render_attribute( 'video', 'controlsList', 'nodownload' );
		}

		if ( 'autoplay' === $settings['video_autoplay'] && 'yes' !== $settings['video_play_viewport'] ) {
			$this->add_render_attribute( 'video', 'autoplay', 'true' );
		}

		if ( 'yes' === $settings['video_start_muted'] ) {
			$this->add_render_attribute( 'video', 'muted', 'true' );
		}

		if ( 'loop' === $settings['video_loop'] ) {
			$this->add_render_attribute( 'video', 'loop', 'true' );
		}

		if ( ! empty( $settings['video_cover']['url'] ) ) {
			$url = Group_Control_Image_Size::get_attachment_image_src( $settings['video_cover']['id'], 'video_cover', $settings );
			$this->add_render_attribute( 'video', 'poster', $url );
		}

		?><video <?php echo $this->get_render_attribute_string( 'video' ); ?>><?php

			$video_url = ( 'file' === $settings['video_source'] ) ? $settings['video_file']['url'] : $settings['video_url'];
			$video_url_m4v = ( 'file' === $settings['video_source_m4v'] ) ? $settings['video_file_m4v']['url'] : $settings['video_url_m4v'];
			$video_url_ogg = ( 'file' === $settings['video_source_ogg'] ) ? $settings['video_file_ogg']['url'] : $settings['video_url_ogg'];
			$video_url_webm = ( 'file' === $settings['video_source_webm'] ) ? $settings['video_file_webm']['url'] : $settings['video_url_webm'];

			if ( $video_url ) {
				$this->add_render_attribute( 'source-mp4', [
					'src' => $video_url,
					'type' => 'video/mp4',
				] );
			?><source <?php echo $this->get_render_attribute_string( 'source-mp4' ); ?>><?php } ?>

			<?php if ( $video_url_m4v ) {
				$this->add_render_attribute( 'source-m4v', [
					'src' => $video_url_m4v,
					'type' => 'video/m4v',
				] );
			?><source <?php echo $this->get_render_attribute_string( 'source-m4v' ); ?>><?php } ?>

			<?php if ( $video_url_ogg ) {
				$this->add_render_attribute( 'source-ogg', [
					'src' => $video_url_ogg,
					'type' => 'video/ogg',
				] );
			?><source <?php echo $this->get_render_attribute_string( 'source-wav' ); ?>><?php } ?>

			<?php if ( $video_url_webm ) {
				$this->add_render_attribute( 'source-webm', [
					'src' => $video_url_webm,
					'type' => 'video/webm',
				] );
			?><source <?php echo $this->get_render_attribute_string( 'source-webm' ); ?>><?php } ?>

		</video><?php
	}

	/**
	 * Render Cover
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function render_cover() {
		$this->add_render_attribute( 'video-cover', [
			'class' => [
				'ee-video-player__cover',
				'ee-player__cover',
			],
		] );
		?><div <?php echo $this->get_render_attribute_string( 'video-cover' ); ?>></div><?php
	}

	/**
	 * Render Controls
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function render_controls() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'controls' => [
				'class' => [
					'ee-video-player__controls',
					'ee-player__controls',
				],
			],
			'bar-wrapper' => [
				'class' => [
					'ee-player__controls__bar-wrapper',
					'ee-video-player__controls__bar-wrapper',
				],
			],
			'bar' => [
				'class' => [
					'ee-player__controls__bar',
				],
			],
			'control-play' => [
				'class' => [
					'ee-player__control',
					'ee-player__controls__play',
					'ee-player__control--icon',
					'nicon',
					'nicon-play',
				],
			],
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'controls' ); ?>><?php

			$this->render_overlay();

			if ( 'show' === $settings['video_show_bar'] ) {

				?><div <?php echo $this->get_render_attribute_string( 'bar-wrapper' ); ?>>
					<div <?php echo $this->get_render_attribute_string( 'bar' ); ?>>

						<?php if ( 'yes' !== $settings['video_restart_on_pause'] && 'show' === $settings['video_show_rewind'] ) {
							$this->add_render_attribute( 'control-rewind', [
								'class' => [
									'ee-player__control',
									'ee-player__controls__rewind',
									'ee-player__control--icon',
									'nicon',
									'nicon-rewind',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-rewind' ); ?>></div><?php } ?>
						
						<div <?php echo $this->get_render_attribute_string( 'control-play' ); ?>></div>

						<?php if ( $settings['video_show_time'] ) {
							$this->add_render_attribute( 'control-time', [
								'class' => [
									'ee-player__control',
									'ee-player__control--indicator',
									'ee-player__controls__time',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-time' ); ?>>00:00</div><?php } ?>

						<?php if ( $settings['video_show_progress'] ) {
							$this->add_render_attribute( [
								'control-progress' => [
									'class' => [
										'ee-player__control',
										'ee-player__controls__progress',
										'ee-player__control--progress',
									],
								],
								'control-progress-time' => [
									'class' => [
										'ee-player__controls__progress-time',
										'ee-player__control--progress__inner',
									],
								],
								'control-progress-track' => [
									'class' => [
										'ee-player__control--progress__inner',
										'ee-player__control--progress__track',
									],
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-progress' ); ?>>
							<div <?php echo $this->get_render_attribute_string( 'control-progress-time' ); ?>></div>
							<div <?php echo $this->get_render_attribute_string( 'control-progress-track' ); ?>></div>
						</div><?php } ?>

						<?php if ( $settings['video_show_duration'] ) {
							$this->add_render_attribute( 'control-duration', [
								'class' => [
									'ee-player__control',
									'ee-player__controls__duration',
									'ee-player__control--indicator',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-duration' ); ?>>00:00</div><?php } ?>

						<?php if ( $settings['video_show_volume'] ) {
							$this->add_render_attribute( 'control-volume', [
								'class' => [
									'ee-player__control',
									'ee-player__controls__volume',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-volume' ); ?>>

							<?php if ( $settings['video_show_volume_icon'] ) {
								$this->add_render_attribute( 'control-volume-icon', [
									'class' => [
										'ee-player__controls__volume-icon',
										'ee-player__control--icon',
										'nicon',
										'nicon-volume',
									],
								] );
							?><div <?php echo $this->get_render_attribute_string( 'control-volume-icon' ); ?>></div><?php } ?>

							<?php if ( $settings['video_show_volume_bar'] ) {
								$this->add_render_attribute( [
									'control-volume-bar' => [
										'class' => [
											'ee-player__control',
											'ee-player__controls__volume-bar',
											'ee-player__control--progress',
										],
									],
									'control-volume-bar-amount' => [
										'class' => [
											'ee-player__controls__volume-bar__amount',
											'ee-player__control--progress__inner',
										],
									],
									'control-volume-bar-track' => [
										'class' => [
											'ee-player__controls__volume-bar__track',
											'ee-player__control--progress__inner',
											'ee-player__control--progress__track',
										],
									],
								] );
							?><div <?php echo $this->get_render_attribute_string( 'control-volume-bar' ); ?>>
								<div <?php echo $this->get_render_attribute_string( 'control-volume-bar-amount' ); ?>></div>
								<div <?php echo $this->get_render_attribute_string( 'control-volume-bar-track' ); ?>></div>
							</div><?php } ?>

						</div><?php } ?>

						<?php if ( $settings['video_show_fs'] ) {
							$this->add_render_attribute( 'control-fullscreen', [
								'class' => [
									'ee-player__control',
									'ee-player__controls__fs',
									'ee-player__control--icon',
									'nicon',
									'nicon-expand'
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-fullscreen' ); ?>></div><?php } ?>

					</div><!-- .ee-player__controls__bar -->
				</div><!-- .ee-player__controls__bar-wrapper -->
			<?php } ?>
		</div><!-- .ee-video-player__controls -->
		<?php
	}

	/**
	 * Render Overlay
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function render_overlay() {
		$settings = $this->get_settings_for_display();

		if ( 'show' === $settings['video_show_buttons'] ) {
			$this->add_render_attribute( [
				'overlay' => [
					'class' => [
						'ee-player__controls__overlay',
						'ee-video-player__overlay',
					],
				],
				'overlay-play' => [
					'class' => [
						'ee-player__control',
						'ee-player__controls__play',
						'nicon',
						'nicon-play',
					],
				],
			] );

			?><ul <?php echo $this->get_render_attribute_string( 'overlay' ); ?>><?php

				if ( 'yes' !== $settings['video_restart_on_pause'] && 'show' === $settings['video_show_rewind'] ) {
					$this->add_render_attribute( 'overlay-rewind', [
						'class' => [
							'ee-player__control',
							'ee-player__controls__rewind',
							'nicon',
							'nicon-rewind',
						],
					] )
					?><li <?php echo $this->get_render_attribute_string( 'overlay-rewind' ); ?>></li><?php }

				?><li <?php echo $this->get_render_attribute_string( 'overlay-play' ); ?>></li>
			</ul>
		<?php }
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function _content_template() { ?><#

		if (
			'' !== settings.video_file.url ||
			'' !== settings.video_file_ogg.url ||
			'' !== settings.video_file_webm.url ||
			'' !== settings.video_file_m4v.url ||
			'' !== settings.video_url ||
			'' !== settings.video_url_ogg ||
			'' !== settings.video_url_webm ||
			'' !== settings.video_url_m4v
		) {
		
			view.addRenderAttribute( {
				'video-wrapper' : {
					'class' : [
						'ee-video-player',
						'ee-player',
					],
				},
			} );

			#><div {{{ view.getRenderAttributeString( 'video-wrapper' ) }}}>
				<?php echo $this->_video_template(); ?>
				<?php echo $this->_cover_template(); ?>
				<?php echo $this->_controls_template(); ?>
			</div><!-- .ee-player -->
		<# } #><?php
	}

	/**
	 * Video Template
	 * 
	 * Javascript video template
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function _video_template() { ?><#

		view.addRenderAttribute( 'video', {
			'class' : [
				'ee-video-player__source',
				'ee-player__source'
			],
			'playsinline' : 'true',
			'width' : '100%',
			'height' : '100%',
		} );

		if( 'autoplay' === settings.video_autoplay && 'yes' !== settings.video_play_viewport ) {
			view.addRenderAttribute( 'video', 'autoplay', 'true' );
		}

		if( 'yes' === settings.video_start_muted ) {
			view.addRenderAttribute( 'video', 'muted', 'true' );
		}

		if ( 'loop' === settings.video_loop ) {
			view.addRenderAttribute( 'video', 'loop', 'true' );
		}

		if ( settings.video_cover.id ) {

			var image = {
				id 			: settings.video_cover.id,
				url 		: settings.video_cover.url,
				size 		: settings.image_size,
				dimension 	: settings.image_custom_dimension,
				model 		: view.getEditModel(),
			};

			view.addRenderAttribute( 'video', 'poster', elementor.imagesManager.getImageUrl( image ) );
		}

		#><video {{{ view.getRenderAttributeString( 'video' ) }}}><#
			var video_url = ( 'file' === settings.video_source ) ? settings.video_file.url : settings.video_url,
				video_url_m4v = ( 'file' === settings.video_source_m4v ) ? settings.video_file_m4v.url : settings.video_url_m4v,
				video_url_ogg = ( 'file' === settings.video_source_ogg ) ? settings.video_file_ogg.url : settings.video_url_ogg,
				video_url_webm = ( 'file' === settings.video_source_webm ) ? settings.video_file_webm.url : settings.video_url_webm;

			if ( video_url ) {
				view.addRenderAttribute( 'source-mp4', {
					'src' : video_url,
					'type' : 'video/mp4',
				} );
			#><source {{{ view.getRenderAttributeString( 'source-mp4' ) }}}><# } #>

			<# if ( video_url_m4v ) {
				view.addRenderAttribute( 'source-m4v', {
					'src' : video_url_m4v,
					'type' : 'video/m4v',
				} );
			#><source {{{ view.getRenderAttributeString( 'source-m4v' ) }}}><# } #>

			<# if ( video_url_ogg ) {
				view.addRenderAttribute( 'source-ogg', {
					'src' : video_url_ogg,
					'type' : 'video/ogg',
				} );
			#><source {{{ view.getRenderAttributeString( 'source-ogg' ) }}}><# } #>

			<# if ( video_url_webm ) {
				view.addRenderAttribute( 'source-webm', {
					'src' : video_url_webm,
					'type' : 'video/webm',
				} );
			#><source {{{ view.getRenderAttributeString( 'source-webm' ) }}}><# } #>

		</video>
		<?php
	}

	/**
	 * Controls Template
	 * 
	 * Javascript controls template
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function _controls_template() { ?><#

		view.addRenderAttribute({
			'controls' : {
				'class' : [
					'ee-video-player__controls',
					'ee-player__controls',
				],
			},
			'bar-wrapper' : {
				'class' : [
					'ee-player__controls__bar-wrapper',
					'ee-video-player__controls__bar-wrapper',
				],
			},
			'bar' : {
				'class' : [
					'ee-player__controls__bar',
				],
			},
			'control-play' : {
				'class' : [
					'ee-player__control',
					'ee-player__controls__play',
					'ee-player__control--icon',
					'nicon',
					'nicon-play',
				],
			},
		});

		#><div {{{ view.getRenderAttributeString( 'controls' ) }}}>

			<?php $this->_overlay_template(); ?>

			<# if ( 'show' === settings.video_show_bar ) { #>

				<div {{{ view.getRenderAttributeString( 'bar-wrapper' ) }}}>
					<div {{{ view.getRenderAttributeString( 'bar' ) }}}>

						<# if ( 'yes' !== settings.video_restart_on_pause && 'show' === settings.video_show_rewind ) {
							view.addRenderAttribute( 'control-rewind', {
								'class' : [
									'ee-player__control',
									'ee-player__controls__rewind',
									'ee-player__control--icon',
									'nicon',
									'nicon-rewind',
								],
							} );
						#><div {{{ view.getRenderAttributeString( 'control-rewind' ) }}}></div><# } #>
						
						<div {{{ view.getRenderAttributeString( 'control-play' ) }}}></div>

						<# if ( settings.video_show_time ) {
							view.addRenderAttribute( 'control-time', {
								'class' : [
									'ee-player__control',
									'ee-player__control--indicator',
									'ee-player__controls__time',
								],
							} );
						#><div {{{ view.getRenderAttributeString( 'control-time' ) }}}>00:00</div><# } #>

						<# if ( settings.video_show_progress ) {
							view.addRenderAttribute( {
								'control-progress' : {
									'class' : [
										'ee-player__control',
										'ee-player__controls__progress',
										'ee-player__control--progress',
									],
								},
								'control-progress-time' : {
									'class' : [
										'ee-player__controls__progress-time',
										'ee-player__control--progress__inner',
									],
								},
								'control-progress-track' : {
									'class' : [
										'ee-player__control--progress__inner',
										'ee-player__control--progress__track',
									],
								},
							} );
						#><div {{{ view.getRenderAttributeString( 'control-progress' ) }}}>
							<div {{{ view.getRenderAttributeString( 'control-progress-time' ) }}}></div>
							<div {{{ view.getRenderAttributeString( 'control-progress-track' ) }}}></div>
						</div><# } #>

						<# if ( settings.video_show_duration ) {
							view.addRenderAttribute( 'control-duration', {
								'class' : [
									'ee-player__control',
									'ee-player__controls__duration',
									'ee-player__control--indicator',
								],
							} );
						#><div {{{ view.getRenderAttributeString( 'control-duration' ) }}}>00:00</div><# } #>

						<# if ( settings.video_show_volume ) {
							view.addRenderAttribute( 'control-volume', {
								'class' : [
									'ee-player__control',
									'ee-player__controls__volume',
								],
							} );
						#><div {{{ view.getRenderAttributeString( 'control-volume' ) }}}>

							<# if ( settings.video_show_volume_icon ) {
								view.addRenderAttribute( 'control-volume-icon', {
									'class' : [
										'ee-player__controls__volume-icon',
										'ee-player__control--icon',
										'nicon',
										'nicon-volume',
									],
								} );
							#><div {{{ view.getRenderAttributeString( 'control-volume-icon' ) }}}></div><# } #>

							<# if ( settings.video_show_volume_bar ) {
								view.addRenderAttribute( {
									'control-volume-bar' : {
										'class' : [
											'ee-player__control',
											'ee-player__controls__volume-bar',
											'ee-player__control--progress',
										],
									},
									'control-volume-bar-amount' : {
										'class' : [
											'ee-player__controls__volume-bar__amount',
											'ee-player__control--progress__inner',
										],
									},
									'control-volume-bar-track' : {
										'class' : [
											'ee-player__controls__volume-bar__track',
											'ee-player__control--progress__inner',
											'ee-player__control--progress__track',
										],
									},
								} );
							#><div {{{ view.getRenderAttributeString( 'control-volume-bar' ) }}}>
								<div {{{ view.getRenderAttributeString( 'control-volume-bar-amount' ) }}}></div>
								<div {{{ view.getRenderAttributeString( 'control-volume-bar-track' ) }}}></div>
							</div><# } #>

						</div><# } #>

					</div><!-- .ee-player__controls__bar -->
				</div><!-- .ee-player__controls__bar-wrapper -->
			<# } #>
		</div><!-- .ee-player__controls -->
		<?php
	}

	/**
	 * Cover Template
	 * 
	 * Javascript cover template
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function _cover_template() { ?><#
		view.addRenderAttribute( 'video-cover', {
			'class' : [
				'ee-video-player__cover',
				'ee-player__cover',
			],
		} );
		#><div {{{ view.getRenderAttributeString( 'video-cover' ) }}}></div><?php
	}

	/**
	 * Overlay Template
	 * 
	 * Javascript overlay template
	 *
	 * @since  1.3.0
	 * @return void
	 */
	protected function _overlay_template() { ?><#

		if ( 'show' === settings.video_show_buttons ) {
			view.addRenderAttribute( {
				'overlay' : {
					'class' : [
						'ee-player__controls__overlay',
						'ee-video-player__overlay',
					],
				},
				'overlay-play' : {
					'class' : [
						'ee-player__control',
						'ee-player__controls__play',
						'nicon',
						'nicon-play',
					],
				},
			} );

			#><ul {{{ view.getRenderAttributeString( 'overlay' ) }}}><#

				if ( 'yes' !== settings.video_restart_on_pause && 'show' === settings.video_show_rewind ) {
					view.addRenderAttribute( 'overlay-rewind', {
						'class' : [
							'ee-player__control',
							'ee-player__controls__rewind',
							'nicon',
							'nicon-rewind',
						],
					} )
					#><li {{{ view.getRenderAttributeString( 'overlay-rewind' ) }}}></li><# }

				#><li {{{ view.getRenderAttributeString( 'overlay-play' ) }}}></li>
			</ul>
		<# } #><?php
	}
}
