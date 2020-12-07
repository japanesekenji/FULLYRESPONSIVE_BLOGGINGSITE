/**
 * Parallax Background version 1.2
 * https://github.com/erensuleymanoglu/parallax-background
 *
 * by Eren Suleymanoglu
 */
;(
	function( $, window, document, undefined ) {

		if ( ! window.requestAnimationFrame ) {
			return;
		}

		$.parallaxBackground = function( element, options ) {

			var defaults = {
				parallaxResizeWatch : null,
				parallaxBgImage 	: '',
				parallaxBgPosition	: 'center center',
				parallaxBgRepeat	: 'no-repeat',
				parallaxBgSize		: 'cover',
				parallaxSpeed		: 0.5,
				parallaxSpeedTablet : 0.5,
				parallaxSpeedMobile : 0.5,
				parallaxDirection	: 'down',
				breakpoints		: {
					'mobile'	: 768,
					'tablet' 	: 1024,
				}
			};

			var plugin = this;

			plugin.opts = {};

			var $element 	= $(element),
				$parallax 	= null,
				$window 	= $(window),
				$parallax_inner, d, e, h, p, s, t, w,
				pt, pb, pl, pr, hd, wd, wh, ww,
				x = 0,
				y = 0,
				z = 0,
				i = 0,
				f = 1,
				lastScrollY = ( $window.get(0).pageYOffset || document.documentElement.scrollTop )  - ( document.documentElement.clientTop || 0 ),
				frameRendered = true,
				scroll_top = 0;

			plugin.init = function() {
				plugin.opts = $.extend({}, defaults, options);
				plugin._construct();
			};

			plugin._construct = function() {

				plugin.setup();
				plugin.events();

			};

			plugin.render = function() {
				if ( frameRendered !== true ) {
					plugin.move();
				}
				window.requestAnimationFrame( plugin.render );
				frameRendered = true;
			}

			plugin.setup = function() {

				// Remove background image on parent element
				$element.css( 'background-image', 'none' );

				if ($element.find('.ee-parallax').length < 1) {
					$element.prepend('<div class="ee-parallax"></div>');
				}

				$parallax = $element.find('.ee-parallax');

				if ($parallax.find('.ee-parallax__inner').length < 1) {
					$parallax.prepend('<div class="ee-parallax__inner"></div>');
				}

				$parallax_inner = $parallax.find('.ee-parallax__inner');

				d = plugin.getElementSize($parallax);
				e = plugin.repositionBackground($parallax, d);

				$element.css({
					'z-index': 0,
				});

				$parallax_inner.css({
					'position'	: 'absolute',
					'width'		: d[0],
					'height'	: d[1],
					'transform'	: 'translate3d(' + e[0] + 'px, ' + e[1] + 'px, ' + e[2] + 'px)',
					'z-index'	: '-1'
				});

				if (plugin.opts.parallaxDirection === 'left' || plugin.opts.parallaxDirection === 'right') {
					p = 0;
					s = e[0];
				}

				if (plugin.opts.parallaxDirection === 'up' || plugin.opts.parallaxDirection === 'down') {
					p = 0;
					s = e[1];
				}

				if ( $element.visible(true) ) {
					scroll_top = $window.scrollTop();
				} else {
					scroll_top = $parallax.offset().top;
				}

			};

			plugin.refresh = function() {

				// Wait for Elementor's stretch function to execute
				setTimeout( function() { plugin.adjust(); }, 100);
				plugin.move();
			};

			plugin.events = function() {

				$(document).ready(function() {
					plugin.render();
				});
				
				// Bind to window resize
				$window.on( 'resize', plugin.refresh );

				// Bind to resize of custom element
				if ( plugin.opts.parallaxResizeWatch ) {
					plugin.opts.parallaxResizeWatch._resize( plugin.refresh );
				}

				$window.on( 'scroll', function() {
					if ( frameRendered === true ) {
						lastScrollY = (window.pageYOffset || document.documentElement.scrollTop)  - (document.documentElement.clientTop || 0);
					}
					frameRendered = false;
				});

				// $window.on( 'scroll', function(){
				// 	plugin.move();
				// });
			};

			plugin.getElementSize = function( parent ) {
				w = parent.width();
				h = parent.height();

				wh = $window.height();
				ww = $window.width();

				if ( plugin.isMobile() ) {
					f = 2;
				} else { f = 1; }

				if (plugin.opts.parallaxDirection === 'left' || plugin.opts.parallaxDirection === 'right') {
					w += f * Math.ceil( ww * plugin.getSpeed() );
				}

				if (plugin.opts.parallaxDirection === 'up' || plugin.opts.parallaxDirection === 'down') {
					h += f * Math.ceil( wh * plugin.getSpeed() );
				}

				return [w, h];
			};

			plugin._getProgress = function() {
				return ( ( lastScrollY - $parallax_inner.offset().top + wh ) / ( wh + h ) );
			};

			plugin.repositionBackground = function( el, d ) {
				pl = parseInt( el.css('padding-left').replace('px', '') );
				pr = parseInt( el.css('padding-right').replace('px', ''));
				pt = parseInt( el.css('padding-top').replace('px', ''));
				pb = parseInt( el.css('padding-bottom').replace('px', ''));

				hd = (d[1] - el.outerHeight()) / 2;
				wd = (d[0] - el.outerWidth()) / 2;

				switch (plugin.opts.parallaxDirection) {
					case 'up':
						x = -pl;
						y = -(hd + pt);
						z = 0;
						break;
					case 'down':
						x = -pl;
						y = -(hd + pt);
						z = 0;
						break;
					case 'left':
						x = -(wd + pl);
						y = -pt;
						z = 0;
						break;
					case 'right':
						x = -(wd + pl);
						y = -pt;
						z = 0;
						break;
				}

				return [x, y, z];
			};

			plugin.adjust = function() {

				d = plugin.getElementSize( $parallax );
				e = plugin.repositionBackground( $parallax, d );

				if (plugin.opts.parallaxDirection === 'left' || plugin.opts.parallaxDirection === 'right') {
					p = 0;
					s = e[0];
				}

				if (plugin.opts.parallaxDirection === 'up' || plugin.opts.parallaxDirection === 'down') {
					p = 0;
					s = e[1];
				}

				$parallax_inner.css({
					'width' 	: d[0],
					'height'	: d[1],
					'transform'	: 'translate3d(' + e[0] + 'px, ' + e[1] + 'px, ' + e[2] + 'px)'
				});

			};

			plugin.move = function() {

				if ( ! $element.visible( true ) )
					return;

				i = $window.scrollTop() - scroll_top;

				p = i * ( plugin.getSpeed() / 4 );

				if (plugin.opts.parallaxDirection === 'up') {
					s += -p;
					t = 'translate3d(' + e[0] + 'px, ' + s + 'px, ' + e[2] + 'px)';
				}

				if (plugin.opts.parallaxDirection === 'down') {
					s += p;
					t = 'translate3d(' + e[0] + 'px, ' + s + 'px, ' + e[2] + 'px)';
				}

				if (plugin.opts.parallaxDirection === 'left') {
					s += p;
					t = 'translate3d(' + s + 'px, ' + e[1] + 'px, ' + e[2] + 'px)';
				}

				if (plugin.opts.parallaxDirection === 'right') {
					s += -p;
					t = 'translate3d(' + s + 'px, ' + e[1] + 'px, ' + e[2] + 'px)';
				}

				$parallax_inner.css({
					'width'		: d[0],
					'height'	: d[1],
					'transform'	: t
				});

				scroll_top = $window.scrollTop();
			};

			plugin.getSpeed = function() {
				var speed = plugin.opts.parallaxSpeed;

				if ( plugin.isTablet() ) {
					speed = plugin.opts.parallaxSpeedTablet;
				} else if ( plugin.isMobile() ) {
					speed = plugin.opts.parallaxSpeedMobile;
				}

				return parseFloat( speed );
			};

			plugin.isTablet = function() {
				return $window.width() < plugin.opts.breakpoints['tablet'] && $window.width() >= plugin.opts.breakpoints['mobile'];
			};

			plugin.isMobile = function() {
				return $window.width() < plugin.opts.breakpoints['tablet'] && $window.width() < plugin.opts.breakpoints['mobile'];
			};

			plugin.isDesktop = function() {
				return $window.width() > plugin.opts.breakpoints['tablet'];
			};

			plugin.destroy = function() {
				$parallax.remove();
				$parallax_inner.remove();
				$element.css( 'background-image', "" )
				$element.removeData( 'parallaxBackground' );
			};

			plugin.init();

		};

		$.fn.parallaxBackground = function(options) {
			return this.each(function() {

				$.fn.parallaxBackground.destroy = function() {
					if( 'undefined' !== typeof( plugin ) ) {
						$(this).data( 'parallaxBackground' ).destroy();
						$(this).removeData( 'parallaxBackground' );
					}
				}

				if (undefined === $(this).data('parallaxBackground')) {
					var plugin = new $.parallaxBackground(this, options);
					$(this).data('parallaxBackground', plugin);
				}
			});
		};

	}

)( jQuery, window, document );