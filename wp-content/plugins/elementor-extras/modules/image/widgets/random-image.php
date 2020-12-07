<?php
namespace ElementorExtras\Modules\Image\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\Image\Module;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Random_Image
 *
 * @since 2.0.0
 */
class Random_Image extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-random-image';
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
		return __( 'Random Image', 'elementor-extras' );
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
		return 'nicon nicon-random';
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
	 * Register Widget Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_images',
			[
				'label' => __( 'Images', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'wp_gallery',
				[
					'label' 	=> __( 'Add Images', 'elementor-extras' ),
					'type' 		=> Controls_Manager::GALLERY,
					'frontend_available' => true,
					'dynamic'	=> [
						'active' => true,
					],
				]
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'image', // Actually its `image_size`.
					'label' 	=> __( 'Image Size', 'elementor-extras' ),
					'default' 	=> 'full',
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label' 	=> __( 'Alignment', 'elementor-extras' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' 	=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 	=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 	=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'caption',
				[
					'label' 		=> __( 'Caption', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Show', 'elementor-extras' ),
					'label_off' 	=> __( 'Hide', 'elementor-extras' ),
					'return_value' 	=> 'yes',
				]
			);

			$this->add_control(
				'link_to',
				[
					'label' => __( 'Link to', 'elementor-extras' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'none',
					'options' => [
						'none' => __( 'None', 'elementor-extras' ),
						'file' => __( 'Media File', 'elementor-extras' ),
						'custom' => __( 'Custom URL', 'elementor-extras' ),
					],
				]
			);

			$this->add_control(
				'link',
				[
					'label' => __( 'Link to', 'elementor-extras' ),
					'type' => Controls_Manager::URL,
					'dynamic' => [
						'active' => true,
					],
					'placeholder' => __( 'https://your-link.com', 'elementor-extras' ),
					'condition' => [
						'link_to' => 'custom',
					],
					'show_label' => false,
				]
			);

			$this->add_control(
				'open_lightbox',
				[
					'label' => __( 'Lightbox', 'elementor-extras' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default' => __( 'Default', 'elementor-extras' ),
						'yes' => __( 'Yes', 'elementor-extras' ),
						'no' => __( 'No', 'elementor-extras' ),
					],
					'condition' => [
						'link_to' => 'file',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __( 'Image', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'width',
				[
					'label' => __( 'Width', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'unit' => '%',
					],
					'tablet_default' => [
						'unit' => '%',
					],
					'mobile_default' => [
						'unit' => '%',
					],
					'size_units' => [ '%', 'px', 'vw' ],
					'range' => [
						'%' => [
							'min' => 1,
							'max' => 100,
						],
						'px' => [
							'min' => 1,
							'max' => 1000,
						],
						'vw' => [
							'min' => 1,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-random-image__image' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'space',
				[
					'label' => __( 'Max Width', 'elementor-extras' ) . ' (%)',
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'unit' => '%',
					],
					'tablet_default' => [
						'unit' => '%',
					],
					'mobile_default' => [
						'unit' => '%',
					],
					'size_units' => [ '%' ],
					'range' => [
						'%' => [
							'min' => 1,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-random-image__image' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'opacity',
				[
					'label' => __( 'Opacity', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 1,
							'min' => 0.10,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-random-image__image' => 'opacity: {{SIZE}};',
					],
				]
			);

			$this->add_control(
				'hover_animation',
				[
					'label' => __( 'Hover Animation', 'elementor-extras' ),
					'type' => Controls_Manager::HOVER_ANIMATION,
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'image_border',
					'selector' => '{{WRAPPER}} .ee-random-image__image',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'image_border_radius',
				[
					'label' => __( 'Border Radius', 'elementor-extras' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ee-random-image__image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'image_box_shadow',
					'exclude' => [
						'box_shadow_position',
					],
					'selector' => '{{WRAPPER}} .ee-random-image__image',
				]
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_style_caption',
				[
					'label' => __( 'Caption', 'elementor-extras' ),
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => [
						'caption!' => '',
					],
				]
			);

			$this->add_control(
				'caption_align',
				[
					'label' => __( 'Alignment', 'elementor-extras' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'elementor-extras' ),
							'icon' => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'elementor-extras' ),
							'icon' => 'fa fa-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'elementor-extras' ),
							'icon' => 'fa fa-align-right',
						],
						'justify' => [
							'title' => __( 'Justified', 'elementor-extras' ),
							'icon' => 'fa fa-align-justify',
						],
					],
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .widget-image-caption' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'text_color',
				[
					'label' => __( 'Text Color', 'elementor-extras' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'caption_typography',
					'selector' => '{{WRAPPER}} .widget-image-caption',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

			$this->add_responsive_control(
				'caption_space',
				[
					'label' => __( 'Spacing', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .widget-image-caption' => 'margin-top: {{SIZE}}{{UNIT}};',
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
	 * @since  2.0.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['wp_gallery'] ) {
			echo $this->render_placeholder( [
				'body' => __( 'No images selected.', 'elementor-extras' ),
			] );
			return;
		}

		$count 			= count( $settings['wp_gallery'] );
		$index 			= ( $count > 1 ) ? rand( 0, $count - 1 ) : 0;
		$id 			= $settings['wp_gallery'][ $index ]['id'];
		$has_caption 	= 'yes' === $settings['caption'];
		$link 			= $this->get_link_url( $settings, $index );
		$attachment 	= get_post( $id );

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => 'ee-random-image',
			],
			'figure' => [
				'class' => [
					'wp-caption',
					'ee-random-image__figure'
				],
			],
			'image' => [
				'class' => 'elementor-image ee-random-image__image',
				'src' => Group_Control_Image_Size::get_attachment_image_src( $id, 'image', $settings ),
				'alt' => esc_attr( Control_Media::get_image_alt( $id ) ),
			],
			'caption' => [
				'class' => [
					'widget-image-caption',
					'wp-caption-text',
					'ee-random-image__caption',
				],
			],
		] );

		if ( '' !== $settings['hover_animation'] ) {
			$this->add_render_attribute( 'image', 	'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		if ( $link ) {
			if ( ! empty( $link['url'] ) ) {
				$this->add_render_attribute( 'link', 'href', $link['url'] );
			}

			if ( ! empty( $link['is_external'] ) ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( ! empty( $link['nofollow'] ) ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}

			$this->add_lightbox_data_attributes( 'link', $id, $settings['open_lightbox'], $this->get_id_for_loop() );

			if ( $this->_is_edit_mode ) {
				$this->add_render_attribute( 'link', 'class', 'elementor-clickable' );
			}
		}

		?><div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $has_caption ) { ?>
			<figure <?php echo $this->get_render_attribute_string( 'figure' ); ?>>
			<?php } ?>

			<?php if ( $link ) { ?>
				<a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
			<?php } ?>
					<img <?php echo $this->get_render_attribute_string( 'image' ); ?> />
			<?php if ( $link ) { ?>
				</a>
			<?php } ?>

			<?php if ( $has_caption ) { ?>
				<figcaption <?php echo $this->get_render_attribute_string( 'caption' ); ?>>
					<?php echo Module::get_image_caption( $attachment ); ?>
				</figcaption>
			</figure>
			<?php } ?>
		</div><?php
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

		if ( ! settings.wp_gallery.length ) {

			#><div class="ee-editor-placeholder">
				<h4 class="ee-editor-placeholder__title">{{{ view.model.getTitle() }}}</h4>
				<div class="ee-editor-placeholder__body">No images selected</div>
			</div><#

		} else {

			var count 		= settings.wp_gallery.length,
				index 		= ( count > 1 ) ? Math.floor( ( Math.random() * count ) ) : 0,
				has_caption = 'yes' === settings.caption,
				link_url 	= false,
				image 		= settings.wp_gallery[index];

			var _image 	= {
					id 			: image.id,
					url 		: image.url,
					size 		: settings.image_size,
					dimension 	: settings.image_custom_dimension,
					model 		: view.getEditModel(),
				};

			var ensureAttachmentData = function( id ) {
					if ( 'undefined' === typeof wp.media.attachment( id ).get( 'caption' ) ) {
						wp.media.attachment( id ).fetch().then( function( data ) {
							view.render();
						} );
					}
				}

			var getAttachmentCaption = function( id ) {
					if ( ! id ) {
						return '';
					}
					ensureAttachmentData( id );
					return wp.media.attachment( id ).get( 'caption' );
				}

			var caption = getAttachmentCaption( image.id );

			if ( 'custom' === settings.link_to ) {
				link_url = settings.link.url;
			}

			if ( 'file' === settings.link_to ) {
				link_url = settings.wp_gallery[index].url;
			}

			view.addRenderAttribute( {
				'wrapper' : {
					'class' : 'ee-random-image',
				},
				'figure' : {
					'class' : 'wp-caption ee-random-image__figure',
				},
				'image' : {
					'src' : elementor.imagesManager.getImageUrl( _image ),
					'class' : 'ee-random-image__image',
				},
				'caption' : {
					'class' : [
						'widget-image-caption',
						'wp-caption-text',
						'ee-random-image__caption',
					],
				},
			} );

			if ( '' !== settings.hover_animation ) {
				view.addRenderAttribute( 'image', 	'src', 'elementor-animation-' + settings.hover_animation );
			}
		
			#><div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
				<# if ( has_caption ) { #>
				<figure {{{ view.getRenderAttributeString( 'figure' ) }}}>
				<# }

					if ( link_url ) { #><a class="elementor-clickable" data-elementor-open-lightbox="{{ settings.open_lightbox }}" href="{{ link_url }}"><# }
						#><img {{{ view.getRenderAttributeString( 'image' ) }}} /><#
					if ( link_url ) { #></a><# }

				if ( has_caption ) { #>
					<figcaption {{{ view.getRenderAttributeString( 'caption' ) }}}>
						{{{ caption }}}
					</figcaption>
				</figure>
				<# } #>
			</div><#

		} #><?php
	}

	/**
	 * Retrieve image widget link URL.
	 *
	 * @since 2.0.0
	 * @access private
	 * @param array $settings
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 */
	private function get_link_url( $settings, $index ) {
		if ( 'none' === $settings['link_to'] ) {
			return false;
		}

		if ( 'custom' === $settings['link_to'] ) {
			if ( empty( $settings['link']['url'] ) ) {
				return false;
			}
			return $settings['link'];
		}

		return [
			'url' => $settings['wp_gallery'][ $index ]['url'],
		];
	}
}
