<?php
namespace ElementorExtras\Modules\MediaPlayer\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\MediaPlayer\Skins;
use ElementorExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Audio_Player
 *
 * @since 2.0.0
 */
class Audio_Player extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-audio-player';
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
		return __( 'Audio Player', 'elementor-extras' );
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
		return 'nicon nicon-audio';
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
			'audio-player',
			'jquery-appear',
		];
	}

	/**
	 * Register Skins
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_skins() {
		// Comment until future development
		// $this->add_skin( new Skins\Skin_Classic( $this ) );
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_playlist',
			[
				'label' 	=> __( 'Playlist', 'elementor-extras' ),
			]
		);

			$repeater = new Repeater();

			$repeater->add_control(
				'title',
				[
					'label' 		=> __( 'Title', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic' 		=> [ 'active' => true ],
					'label_block' 	=> true,
					'default' 		=> __( 'No title', 'elementor-extras' ),
				]
			);

			$repeater->start_controls_tabs( 'tabs_sources' );

			$repeater->start_controls_tab(
				'tab_source_mpeg',
				[
					'label' => __( 'MP3', 'elementor-extras' ),
				]
			);

				$repeater->add_control(
					'audio_source',
					[
						'label' 		=> __( 'Source', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT,
						'default'		=> 'file',
						'options'		=> [
							'file'		=> __( 'File', 'elementor-extras' ),
							'url'		=> __( 'External', 'elementor-extras' ),
						],
					]
				);

				$repeater->add_control(
					'source_mpeg',
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
						'condition'		=> [
							'audio_source' => 'file',
						],
						'media_type' => 'audio',
					]
				);

				$repeater->add_control(
					'source_mpeg_url',
					[
						'label' 		=> __( 'URL', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'description' 	=> __( 'Insert URL to an .mp3 audio file', 'elementor-extras' ),
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::URL_CATEGORY,
							],
						],
						'condition' 	=> [
							'audio_source' => 'url',
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab(
				'tab_source_m4a',
				[
					'label' => __( 'M4A', 'elementor-extras' ),
				]
			);

				$repeater->add_control(
					'audio_source_m4a',
					[
						'label' 		=> __( 'Source', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT,
						'default'		=> 'file',
						'options'		=> [
							'file'		=> __( 'File', 'elementor-extras' ),
							'url'		=> __( 'External', 'elementor-extras' ),
						],
					]
				);

				$repeater->add_control(
					'source_m4a',
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
						'condition'		=> [
							'audio_source_m4a' => 'file',
						],
						'media_type' => 'audio',
					]
				);

				$repeater->add_control(
					'source_m4a_url',
					[
						'label' 		=> __( 'URL', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'description' 	=> __( 'Insert URL to an .m4a audio file', 'elementor-extras' ),
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::URL_CATEGORY,
							],
						],
						'condition' 	=> [
							'audio_source_m4a' => 'url',
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab(
				'tab_source_wav',
				[
					'label' => __( 'WAV', 'elementor-extras' ),
				]
			);

				$repeater->add_control(
					'audio_source_wav',
					[
						'label' 		=> __( 'Source', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT,
						'default'		=> 'file',
						'options'		=> [
							'file'		=> __( 'File', 'elementor-extras' ),
							'url'		=> __( 'External', 'elementor-extras' ),
						],
					]
				);

				$repeater->add_control(
					'source_wav',
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
						'condition'		=> [
							'audio_source_wav' => 'file',
						],
						'media_type' => 'audio',
					]
				);

				$repeater->add_control(
					'source_wav_url',
					[
						'label' 		=> __( 'URL', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'description' 	=> __( 'Insert URL to an .wav audio file', 'elementor-extras' ),
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::URL_CATEGORY,
							],
						],
						'condition' 	=> [
							'audio_source_wav' => 'url',
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab(
				'tab_source_ogg',
				[
					'label' => __( 'OGG', 'elementor-extras' ),
				]
			);

				$repeater->add_control(
					'audio_source_ogg',
					[
						'label' 		=> __( 'Source', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT,
						'default'		=> 'file',
						'options'		=> [
							'file'		=> __( 'File', 'elementor-extras' ),
							'url'		=> __( 'External', 'elementor-extras' ),
						],
					]
				);

				$repeater->add_control(
					'source_ogg',
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
						'condition'		=> [
							'audio_source_ogg' => 'file',
						],
						'media_type' => 'audio',
					]
				);

				$repeater->add_control(
					'source_ogg_url',
					[
						'label' 		=> __( 'URL', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'description' 	=> __( 'Insert URL to an .ogg audio file', 'elementor-extras' ),
						'dynamic' 		=> [
							'active' 	=> true,
							'categories' => [
								TagsModule::POST_META_CATEGORY,
								TagsModule::URL_CATEGORY,
							],
						],
						'condition' 	=> [
							'audio_source_ogg' => 'url',
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'playlist',
				[
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[
							
						]
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> '{{{ title }}}',
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
				'behaviour_heading',
				[
					'label' 	=> __( 'Behaviour', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'preload',
				[
					'label' 		=> __( 'Preload', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default'		=> 'auto',
					'options'		=> [
						'auto'		=> __( 'Auto', 'elementor-extras' ),
						'metadata'	=> __( 'Metadata', 'elementor-extras' ),
						'none'		=> __( 'None', 'elementor-extras' ),
					],
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label' 		=> __( 'Auto Play', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay_notice',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'Many browsers don\'t allow sound to autoplay without user interaction.', 'elementor-extras' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
					'condition' 		=> [
						'autoplay!' => ''
					],
				]
			);

			$this->add_control(
				'loop',
				[
					'label' 		=> __( 'Loop Track', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
				]
			);

			$this->add_control(
				'loop_playlist',
				[
					'label' 		=> __( 'Loop Playlist', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'		=> [
						'loop!'		=> 'yes',
					],
				]
			);

			$this->add_control(
				'display_heading',
				[
					'label' 	=> __( 'Display', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_control(
				'show_restart',
				[
					'label' 		=> __( 'Show Restart', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
				]
			);

			$this->add_control(
				'show_time',
				[
					'label' 		=> __( 'Show Time', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
				]
			);

			$this->add_control(
				'show_progress',
				[
					'label' 		=> __( 'Show Progress', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
				]
			);

			$this->add_control(
				'show_duration',
				[
					'label' 		=> __( 'Show Duration', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
				]
			);

			$this->add_control(
				'show_playlist',
				[
					'label' 		=> __( 'Show Playlist', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
				]
			);

			$this->add_control(
				'show_playlist_control',
				[
					'label' 		=> __( 'Show Playlist Button', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'		=> [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_control(
				'volume_heading',
				[
					'label' 	=> __( 'Volume', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_control(
				'show_volume',
				[
					'label' 		=> __( 'Show Volume', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
				]
			);

			$this->add_control(
				'show_volume_icon',
				[
					'label' 		=> __( 'Show Volume Icon', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'	=> [
						'show_volume!'	=> '',
					]
				]
			);

			$this->add_control(
				'show_volume_bar',
				[
					'label' 		=> __( 'Show Volume Bar', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'show',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'show',
					'condition'	=> [
						'show_volume!'	=> '',
					]
				]
			);

			$this->add_responsive_control(
				'volume',
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
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_player_style',
			[
				'label' => __( 'Player', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'width',
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
						'{{WRAPPER}} .ee-audio-player' => 'max-width: {{SIZE}}{{UNIT}};',
					]
				]
			);

			$this->add_responsive_control(
				'padding',
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
						'{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar' => 'padding: {{SIZE}}px',
					],
				]
			);

			$this->add_responsive_control(
				'align',
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
					'name' 		=> 'player_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-audio-player__controls',
				]
			);

			$this->add_control(
				'player_border_radius',
				[
					'label' 			=> __( 'Border Radius', 'elementor-extras' ),
					'type' 					=> Controls_Manager::DIMENSIONS,
					'size_units' 			=> [ 'px', '%' ],
					'selectors' 			=> [
						'{{WRAPPER}} .ee-audio-player__controls' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'player_box_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-audio-player__controls',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_controls_style',
			[
				'label' => __( 'Controls', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'zoom',
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
						'{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar' => 'font-size: {{SIZE}}px',
						'{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar .ee-player__control--progress' => 'height: {{SIZE}}px',
					],
				]
			);

			$this->add_responsive_control(
				'controls_spacing',
				[
					'label' 	=> __( 'Controls Spacing', 'elementor-extras' ),
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
						'{{WRAPPER}} .ee-audio-player__controls .ee-player__control--indicator,
						 {{WRAPPER}} .ee-audio-player__controls .ee-player__control--icon' => 'padding: 0 {{SIZE}}px',
						'{{WRAPPER}} .ee-audio-player__controls .ee-player__control--progress' => 'margin: 0 {{SIZE}}px',
					],
				]
			);

			$this->add_control(
				'controls_radius',
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
						'{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar .ee-player__control--progress' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar .ee-player__control--progress__inner' => 'border-radius: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0;'
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
						'show_progress!' => '',
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
					'condition'	=> [
						'show_progress!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-player__controls__progress' => 'flex-basis: {{SIZE}}%',
					],
				]
			);

			$this->add_control(
				'controls_play_heading',
				[
					'label' 	=> __( 'Play Icon', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_responsive_control(
				'play_icon_size',
				[
					'label' 	=> __( 'Size', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 10,
							'min' 	=> 0.1,
							'step' 	=> 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-player__controls__play' => 'font-size: {{SIZE}}em',
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
						'show_volume_bar!' => '',
						'show_volume!' => '',
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
					'condition'	=> [
						'show_volume_bar!' => '',
						'show_volume!' => '',
					],
				]
			);

			$this->add_control(
				'controls_style_heading',
				[
					'label' 	=> __( 'All Controls', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->start_controls_tabs( 'tabs_controls_style' );

			$this->start_controls_tab(
				'controls_default',
				[
					'label' => __( 'Default', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'controls_foreground',
					[
						'label' 	=> __( 'Foreground', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '#FFFFFF',
						'selectors' => [
							'{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar' => 'color: {{VALUE}}',
							'{{WRAPPER}} .ee-audio-player__controls .ee-player__control--progress__inner' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'controls_background',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							 '{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'controls_opacity',
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
							'{{WRAPPER}} .ee-audio-player__controls .ee-player__control' => 'opacity: {{SIZE}}',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'controls_hover',
				[
					'label' => __( 'Hover', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'controls_foreground_hover',
					[
						'label' 	=> __( 'Foreground', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar:hover .ee-player__control' => 'color: {{VALUE}}',
							'(desktop+){{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar:hover .ee-player__control--progress__inner' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'controls_background_hover',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar:hover' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'controls_opacity_hover',
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
							'{{WRAPPER}} .ee-audio-player__controls .ee-player__controls__bar:hover .ee-player__control' => 'opacity: {{SIZE}}',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'controls',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-player__control--indicator',
					'separator' => 'before',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_playlist_style',
			[
				'label' => __( 'Playlist', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_playlist!' => '',
				],
			]
		);

			$this->add_control(
				'playlist_height',
				[
					'label' 	=> __( 'Max. Height', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 500,
							'min' 	=> 0,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-audio-player__controls__playlist-wrapper' => 'max-height: {{SIZE}}px',
					],
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'playlist',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-player__playlist__item',
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_control(
				'playlist_background',
				[
					'label' 	=> __( 'Background', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-player__controls__playlist-wrapper' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_control(
				'heading_playlist_separator',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Separator', 'elementor-extras' ),
					'separator' => 'before',
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'playlist_links_separator_thickness',
				[
					'label' 		=> __( 'Thickness', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-player__playlist__item:not(:last-child)' => 'border-top: {{SIZE}}px solid;',
					],
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_control(
				'heading_playlist_links',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Links', 'elementor-extras' ),
					'separator' => 'before',
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'playlist_links_spacing',
				[
					'label' 		=> __( 'Spacing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default'		=> [
						'size'		=> 0,
					],
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-player__playlist__item:not(:last-child)' => 'margin-bottom: {{SIZE}}px;',
					],
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_control(
				'playlist_links_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'selectors' 	=> [
						'{{WRAPPER}} .ee-player__playlist__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'playlist_links_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-player__playlist__item',
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'image',
					'selector' 	=> '{{WRAPPER}} .ee-player__playlist__item',
					'separator'	=> '',
					'condition' => [
						'show_playlist!' => '',
					],
				]
			);

			$this->start_controls_tabs( 'playlist_tabs' );

			$this->start_controls_tab( 'playlist_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'playlist_links_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'show_playlist!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-player__playlist__item' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'playlist_links_separator_color',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'show_playlist!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-player__playlist__item:not(:last-child)' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'playlist_links_background',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-player__playlist__item' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'show_playlist!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'playlist_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'playlist_links_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'show_playlist!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-player__playlist__item:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'playlist_links_separator_color_hover',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'show_playlist!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-player__playlist__item:not(:last-child):hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'playlist_links_background_hover',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_SECONDARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-player__playlist__item:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'show_playlist!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'playlist_current', [ 'label' => __( 'Current', 'elementor-extras' ) ] );

				$this->add_control(
					'playlist_links_color_current',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'show_playlist!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-player__playlist__item.ee--is-active,
							 {{WRAPPER}} .ee-player__playlist__item.ee--is-active:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'playlist_links_separator_color_current',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'show_playlist!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-player__playlist__item.ee--is-active:not(:last-child),
							 {{WRAPPER}} .ee-player__playlist__item.ee--is-active:not(:last-child):hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'playlist_links_background_current',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_SECONDARY,
						],
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-player__playlist__item.ee--is-active,
							 {{WRAPPER}} .ee-player__playlist__item.ee--is-active:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'show_playlist!' => '',
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

		$this->add_render_attribute( [
			'audio-wrapper' => [
				'class' => [
					'ee-audio-player',
					'ee-player'
				],
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( 'audio-wrapper' ); ?>>
			<?php $this->render_controls(); ?>
		</div><!-- .ee-audio-player -->
		<?php
	}

	/**
	 * Render Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_controls() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'controls' => [
				'class' => [
					'ee-audio-player__controls',
					'ee-player__controls',
				],
			],
			'bar-wrapper' => [
				'class' => [
					'ee-player__controls__bar-wrapper',
					'ee-audio-player__controls__bar-wrapper',
				],
			],
			'bar' => [
				'class' => [
					'ee-player__controls__bar',
				],
			],
			'control-previous' => [
				'class' => [
					'ee-player__control',
					'ee-player__controls__previous',
					'ee-player__control--icon',
					'nicon',
					'nicon-play-previous',
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
			'control-next' => [
				'class' => [
					'ee-player__control',
					'ee-player__controls__next',
					'ee-player__control--icon',
					'nicon',
					'nicon-play-next',
				],
			],
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'controls' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'bar-wrapper' ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'bar' ); ?>>

					<?php if ( $settings['show_restart'] ) {
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

					<?php if ( count( $settings[ 'playlist' ] ) > 1 ) {
					?><div <?php echo $this->get_render_attribute_string( 'control-previous' ); ?>></div>
					<?php } ?>

					<div <?php echo $this->get_render_attribute_string( 'control-play' ); ?>></div>

					<?php if ( count( $settings[ 'playlist' ] ) > 1 ) {
					?><div <?php echo $this->get_render_attribute_string( 'control-next' ); ?>></div>
					<?php } ?>

					<?php if ( $settings['show_time'] ) {
						$this->add_render_attribute( 'control-time', [
							'class' => [
								'ee-player__control',
								'ee-player__control--indicator',
								'ee-player__controls__time',
							],
						] );
					?><div <?php echo $this->get_render_attribute_string( 'control-time' ); ?>>00:00</div><?php } ?>

					<?php if ( $settings['show_progress'] ) {
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

					<?php if ( $settings['show_duration'] ) {
						$this->add_render_attribute( 'control-duration', [
							'class' => [
								'ee-player__control',
								'ee-player__controls__duration',
								'ee-player__control--indicator',
							],
						] );
					?><div <?php echo $this->get_render_attribute_string( 'control-duration' ); ?>>00:00</div><?php } ?>

					<?php if ( $settings['show_volume'] ) {
						$this->add_render_attribute( 'control-volume', [
							'class' => [
								'ee-player__control',
								'ee-player__controls__volume',
							],
						] );
					?><div <?php echo $this->get_render_attribute_string( 'control-volume' ); ?>>

						<?php if ( $settings['show_volume_icon'] ) {
							$this->add_render_attribute( 'control-volume-icon', [
								'class' => [
									'ee-player__controls__volume-icon',
									'ee-player__control--icon',
									'nicon',
									'nicon-volume',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-volume-icon' ); ?>></div><?php } ?>

						<?php if ( $settings['show_volume_bar'] ) {
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

					<?php if ( $settings['show_playlist'] && $settings['show_playlist_control'] ) {
						$this->add_render_attribute( 'control-browse', [
							'class' => [
								'ee-player__control',
								'ee-player__control--icon',
								'ee-player__controls__browse',
								'fa',
								'fa-list-ul',
							],
						] );
					?><div <?php echo $this->get_render_attribute_string( 'control-browse' ); ?>></div><?php } ?>

				</div><!-- .ee-player__controls__bar -->
			</div><!-- .ee-player__controls__bar-wrapper -->
			<?php $this->render_playlist(); ?>
		</div><!-- .ee-player__controls -->
		<?php
	}

	/**
	 * Render Playlist
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_playlist() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'playlist-wrapper' => [
				'class' => [
					'ee-player__controls__playlist-wrapper',
					'ee-audio-player__controls__playlist-wrapper',
				],
			],
			'playlist' => [
				'class' => [
					'ee-player__controls__playlist',
					'ee-audio-player__playlist',
					'ee-player__playlist',
				],
			],
			'playlist-item-title' => [
				'class' => [
					'ee-player__playlist__item__title',
					'ee-audio-player__playlist__item__title',
				],
			],
			'playlist-item-duration' => [
				'class' => [
					'ee-player__playlist__item__duration',
					'ee-audio-player__playlist__item__duration',
				],
			],
		] );

		if ( '' === $settings['show_playlist'] ) {
			$this->add_render_attribute( 'playlist', 'class', 'ee-player__playlist--hidden' );
		}

		?>
		<div <?php echo $this->get_render_attribute_string( 'playlist-wrapper' ); ?>>
			<ul <?php echo $this->get_render_attribute_string( 'playlist' ); ?>>
				<?php foreach ( $settings['playlist'] as $index => $item ) {

					$sources = [];

					$sources['mpeg'] = $this->get_audio_source_url( $item, 'source_mpeg', 'audio_source' );
					$sources['m4a'] = $this->get_audio_source_url( $item, 'source_m4a', 'audio_source_m4a' );
					$sources['wav']  = $this->get_audio_source_url( $item, 'source_wav', 'audio_source_wav' );
					$sources['ogg']  = $this->get_audio_source_url( $item, 'source_ogg', 'audio_source_ogg' );
					
					$item['sources'] = $sources;

					if ( empty( $sources['mpeg'] ) && empty( $sources['m4a'] ) && empty( $sources['wav'] ) && empty( $sources['ogg'] ) )
						continue;

					$playlist_item_key = $this->get_repeater_setting_key( 'item', 'playlist', $index );

					$this->add_render_attribute(
						$playlist_item_key, [
							'class' => [
								'ee-player__playlist__item',
								'ee-audio-player__playlist__item',
							],
							'id' => 'elementor-repeater-item-' . $item['_id'],
						]
					);

					?><li <?php echo $this->get_render_attribute_string( $playlist_item_key ); ?>>
						<span  <?php echo $this->get_render_attribute_string( 'playlist-item-title' ); ?>>
							<?php echo $item['title']; ?>
						</span>
						<span  <?php echo $this->get_render_attribute_string( 'playlist-item-duration' ); ?>>00:00</span>
						<?php $this->render_audio( $item, $index ); ?>
					</li><!-- .ee-player__playlist__item -->
				<?php } ?>
			</ul><!-- .ee-player__controls__playlist -->
		</div><!-- .ee-player__controls__playlist-wrapper -->
		<?php
	}

	/*
	 * Get Audio Source URL
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_audio_source_url( $item, $format, $source_type ) {
		if ( 'file' === $item[ $source_type ] ) {
			if ( is_array( $item[ $format ] ) && array_key_exists( 'url', $item[ $format ] ) ) {
				return $item[ $format ]['url'];
			} else if ( filter_var( $item[ $format ], FILTER_VALIDATE_URL) ) {
				return $item[ $format ];
			}
		} else {
			return $item[ $format . '_url'];
		}

		return '';
	}

	/**
	 * Render Audio
	 * 
	 * Render html5 audio markup
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_audio( $item, $index ) {

		$settings = $this->get_settings_for_display();

		$audio_key = $this->get_repeater_setting_key( 'audio', 'playlist', $index );
		$mp3_key = $this->get_repeater_setting_key( 'mp3', 'playlist', $index );
		$m4a_key = $this->get_repeater_setting_key( 'm4a', 'playlist', $index );
		$wav_key = $this->get_repeater_setting_key( 'wav', 'playlist', $index );
		$ogg_key = $this->get_repeater_setting_key( 'ogg', 'playlist', $index );

		$this->add_render_attribute( [
			$audio_key => [
				'class' => [
					'ee-audio-player__source',
					'ee-player__source'
				],
				'playsinline' 	=> 'true',
				'preload'		=> $settings['preload'],
				'width' 		=> '100%',
				'height' 		=> '100%',
				'id' 			=> 'audio-' . $this->get_id() . '-' . $item['_id'],
			],
		] );

		if ( 'yes' === $settings['loop'] ) {
			$this->add_render_attribute( $audio_key, 'loop', 'true' );
		}

		?><audio <?php echo $this->get_render_attribute_string( $audio_key ); ?>>
			
			<?php if ( ! empty( $item['sources']['mpeg'] ) ) {
				$this->add_render_attribute( $mp3_key, [
					'src' => $item['sources']['mpeg'],
					'type' => 'audio/mp3',
				] );
			?><source <?php echo $this->get_render_attribute_string( $mp3_key ); ?>><?php } ?>

			<?php if ( ! empty( $item['sources']['m4a'] ) ) {
				$this->add_render_attribute( $m4a_key, [
					'src' => $item['sources']['m4a'],
					'type' => 'audio/x-m4a',
				] );
			?><source <?php echo $this->get_render_attribute_string( $m4a_key ); ?>><?php } ?>

			<?php if ( ! empty( $item['sources']['wav'] ) ) {
				$this->add_render_attribute( $wav_key, [
					'src' => $item['sources']['wav'],
					'type' => 'audio/wav',
				] );
			?><source <?php echo $this->get_render_attribute_string( $wav_key ); ?>><?php } ?>

			<?php if ( ! empty( $item['sources']['ogg'] ) ) {
				$this->add_render_attribute( $ogg_key, [
					'src' => $item['sources']['ogg'],
					'type' => 'audio/ogg',
				] );
			?><source <?php echo $this->get_render_attribute_string( $ogg_key ); ?>><?php } ?>

		</audio><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _content_template() { ?><#

		view.addRenderAttribute( {
			'audio-wrapper' : {
				'class' : [
					'ee-audio-player',
					'ee-player',
				],
			},
		} );

		#><div {{{ view.getRenderAttributeString( 'audio-wrapper' ) }}}>
			<?php echo $this->_controls_template(); ?>
		</div><!-- .ee-audio-player --><?php
	}

	/**
	 * Controls Template
	 * 
	 * Javascript controls template
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _controls_template() { ?><#

		view.addRenderAttribute({
			'controls' : {
				'class' : [
					'ee-audio-player__controls',
					'ee-player__controls',
				],
			},
			'bar-wrapper' : {
				'class' : [
					'ee-player__controls__bar-wrapper',
					'ee-audio-player__controls__bar-wrapper',
				],
			},
			'bar' : {
				'class' : [
					'ee-player__controls__bar',
				],
			},
			'control-previous' : {
				'class' : [
					'ee-player__control',
					'ee-player__controls__previous',
					'ee-player__control--icon',
					'nicon',
					'nicon-play-previous',
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
			'control-next' : {
				'class' : [
					'ee-player__control',
					'ee-player__controls__next',
					'ee-player__control--icon',
					'nicon',
					'nicon-play-next',
				],
			},
		});

		#><div {{{ view.getRenderAttributeString( 'controls' ) }}}>
			<div {{{ view.getRenderAttributeString( 'bar-wrapper' ) }}}>
				<div {{{ view.getRenderAttributeString( 'bar' ) }}}><#

					if ( settings.show_restart ) {
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
					
					<# if ( settings.playlist.length > 1 ) {
					#><div {{{ view.getRenderAttributeString( 'control-previous' ) }}}></div><# } #>

					<div {{{ view.getRenderAttributeString( 'control-play' ) }}}></div>

					<# if ( settings.playlist.length > 1 ) {
					#><div {{{ view.getRenderAttributeString( 'control-next' ) }}}></div><# } #>

					<# if ( settings.show_time ) {
						view.addRenderAttribute( 'control-time', {
							'class' : [
								'ee-player__control',
								'ee-player__control--indicator',
								'ee-player__controls__time',
							],
						} );
					#><div {{{ view.getRenderAttributeString( 'control-time' ) }}}>00:00</div><# } #>

					<# if ( settings.show_progress ) {
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

					<# if ( settings.show_duration ) {
						view.addRenderAttribute( 'control-duration', {
							'class' : [
								'ee-player__control',
								'ee-player__controls__duration',
								'ee-player__control--indicator',
							],
						} );
					#><div {{{ view.getRenderAttributeString( 'control-duration' ) }}}>00:00</div><# } #>

					<# if ( settings.show_volume ) {
						view.addRenderAttribute( 'control-volume', {
							'class' : [
								'ee-player__control',
								'ee-player__controls__volume',
							],
						} );
					#><div {{{ view.getRenderAttributeString( 'control-volume' ) }}}>

						<# if ( settings.show_volume_icon ) {
							view.addRenderAttribute( 'control-volume-icon', {
								'class' : [
									'ee-player__controls__volume-icon',
									'ee-player__control--icon',
									'nicon',
									'nicon-volume',
								],
							} );
						#><div {{{ view.getRenderAttributeString( 'control-volume-icon' ) }}}></div><# } #>

						<# if ( settings.show_volume_bar ) {
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

					<# if ( settings.show_playlist && settings.show_playlist_control ) {
						view.addRenderAttribute( 'control-browse', {
							'class' : [
								'ee-player__control',
								'ee-player__controls__browse',
								'ee-player__control--icon',
								'fa',
								'fa-list-ul',
							],
						} );
					#><div {{{ view.getRenderAttributeString( 'control-browse' ) }}}></div><# } #>

				</div><!-- .ee-player__controls__bar -->
			</div><!-- .ee-player__controls__bar-wrapper -->
			<?php $this->_playlist_template(); ?>
		</div><!-- .ee-player__controls -->
		<?php
	}

	/**
	 * Playlist Template
	 * 
	 * Javascript playlist template
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _playlist_template() { ?><#

		view.addRenderAttribute( {
			'playlist-wrapper' : {
				'class' : [
					'ee-player__controls__playlist-wrapper',
					'ee-audio-player__controls__playlist-wrapper',
				],
			},
			'playlist' : {
				'class' : [
					'ee-player__controls__playlist',
					'ee-audio-player__playlist',
					'ee-player__playlist',
				],
			},
			'playlist-item-title' : {
				'class' : [
					'ee-player__playlist__item__title',
					'ee-audio-player__playlist__item__title',
				],
			},
			'playlist-item-duration' : {
				'class' : [
					'ee-player__playlist__item__duration',
					'ee-audio-player__playlist__item__duration',
				],
			},
		} );

		if ( '' === settings.show_playlist ) {
			view.addRenderAttribute( 'playlist', 'class', 'ee-player__playlist--hidden' );
		}

		#><div {{{ view.getRenderAttributeString( 'playlist-wrapper' ) }}}>
			<ul {{{ view.getRenderAttributeString( 'playlist' ) }}}><#

				_.each( settings.playlist, function( item, index ) {

					var source_mpeg = ( 'file' === item.audio_source ) ? item.source_mpeg.url : item.source_mpeg_url,
						source_m4a = ( 'file' === item.audio_source_m4a ) ? item.source_m4a.url : item.source_m4a_url,
						source_wav = ( 'file' === item.audio_source_wav ) ? item.source_wav.url : item.source_wav_url,
						source_ogg = ( 'file' === item.audio_source_ogg ) ? item.source_ogg.url : item.source_ogg_url;

					if ( source_mpeg || source_m4a || source_wav || source_ogg ) {

						var playlistItemKey = view.getRepeaterSettingKey( 'item', 'playlist', index );

						view.addRenderAttribute(
							playlistItemKey, {
								'class' : [
									'ee-player__playlist__item',
									'ee-audio-player__playlist__item',
								],
								'id' : 'elementor-repeater-item-' + item._id,
							}
						);

						#><li {{{ view.getRenderAttributeString( playlistItemKey ) }}}>
							<span {{{ view.getRenderAttributeString( 'playlist-item-title' ) }}}>
								{{{ item.title }}}
							</span>
							<span {{{ view.getRenderAttributeString( 'playlist-item-duration' ) }}}>00:00</span><#

							var audioKey 	= view.getRepeaterSettingKey( 'audio', 'playlist', index ),
								mpegKey 	= view.getRepeaterSettingKey( 'mp3', 'playlist', index ),
								m4aKey 		= view.getRepeaterSettingKey( 'm4a', 'playlist', index ),
								wavKey 		= view.getRepeaterSettingKey( 'wav', 'playlist', index ),
								oggKey 		= view.getRepeaterSettingKey( 'ogg', 'playlist', index );

							view.addRenderAttribute( audioKey, {
								'class' : [
									'ee-audio-player__source',
									'ee-player__source'
								],
								'playsinline' 	: 'true',
								'width' 		: '100%',
								'height' 		: '100%',
								'id' 			: 'audio-' + view.$el.data('id') + '-' + item._id,
							} );

							if ( 'yes' === settings.loop ) {
								view.addRenderAttribute( audioKey, 'loop', 'true' );
							}

							#><audio {{{ view.getRenderAttributeString( audioKey ) }}}><#
								
								if ( source_mpeg ) {
									view.addRenderAttribute( mpegKey, {
										'src' : source_mpeg,
										'type' : 'audio/mp3',
									} );
								#><source {{{ view.getRenderAttributeString( mpegKey ) }}}><# } #>

								<# if ( source_m4a ) {
									view.addRenderAttribute( m4aKey, {
										'src' : source_m4a,
										'type' : 'audio/x-m4a',
									} );
								#><source {{{ view.getRenderAttributeString( wavKey ) }}}><# } #>

								<# if ( source_wav ) {
									view.addRenderAttribute( wavKey, {
										'src' : source_wav,
										'type' : 'audio/wav',
									} );
								#><source {{{ view.getRenderAttributeString( wavKey ) }}}><# } #>

								<# if ( source_ogg ) {
									view.addRenderAttribute( oggKey, {
										'src' : source_ogg,
										'type' : 'audio/ogg',
									} );
								#><source {{{ view.getRenderAttributeString( oggKey ) }}}><# } #>

							</audio>

						</li><!-- .ee-player__playlist__item -->
					<# }
				}); #>
			</ul><!-- .ee-player__controls__playlist -->
		</div><!-- .ee-player__controls__playlist-wrapper --><?php
	}
}
