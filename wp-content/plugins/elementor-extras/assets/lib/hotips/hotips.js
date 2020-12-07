// -- hotips
// @license hotips v1.1.0 | MIT | Namogo 2017 | https://www.namogo.com
// --------------------------------
;(
    function( $, window, document, undefined ) {

		$.hotips = function(element, options) {

			var defaults = {
				scope 			: null,
				id 				: null,
				position 		: 'top',
				arrowPositionH 	: 'center',
				arrowPositionV 	: 'center',
				fixed			: false,
				trigger 		: {
					desktop 	: 'mouseenter',
					tablet 		: 'click_target',
					mobile 		: 'click_target',
				},
				hide 			: {
					desktop 	: 'mouseleave',
					tablet 		: 'click_out',
					mobile 		: 'click_out',
				},
				delayIn			: 0,
				delayOut		: 0,
				speed 			: 0.2,
				content 		: false,
				source			: false,
				responsive 		: {
					disable 		: false,
					breakpoints		: {
						'mobile'	: 0,
						'tablet'	: 768,
						'desktop' 	: 1025,
					},
				},

				class 			: null,
			};

			var plugin = this;

			plugin.opts = {};

			var $window			= null,
				$tooltip		= null,
				$document		= null,

				target			= element,
				$target			= $(element),
				$content 		= null,

				is_open			= false,
				is_destroyed 	= false,

				_trigger 		= null,
				_hide 			= null,
				_tooltipWidth	= 0,
				_tooltipLeft	= 0,
				_tooltipTop		= 0,
				_tBottom		= 0,
				_tRight 		= 0,
				_offset 		= -20;


			plugin.init = function() {

				if ( options.delayIn === null ) {
					options.delayIn = defaults.delayIn;
				}

				if ( options.delayOut === null ) {
					options.delayOut = defaults.delayOut;
				}

				plugin.opts = $.extend({}, defaults, options);
				plugin._construct();
			};

			plugin._construct = function() {

				if ( ! plugin.opts.scope ) {
					$window		= $(window);
					$document 	= $(document);
				} else {
					$window		= plugin.opts.scope;
					$document 	= plugin.opts.scope;
				}

				if ( ! plugin.canShow() ) {
					return;
				}

				plugin.setTriggers();

				// Override position?
				if ( $target.data( 'hotips-position' ) ) {
					plugin.opts.position = $target.data( 'hotips-position' );
				}

				if ( $target.data( 'hotips-arrow-position-h' ) ) {
					plugin.opts.arrowPositionH = $target.data( 'hotips-arrow-position-h' );
				}

				if ( $target.data( 'hotips-arrow-position-v' ) ) {
					plugin.opts.arrowPositionV = $target.data( 'hotips-arrow-position-v' );
				}

				$tooltip = $( '<div class="hotip-tooltip"></div>' );

				if ( plugin.opts.id ) {
					$tooltip.attr( 'data-target-id', plugin.opts.id );
				}

				plugin.setup();
				plugin.events();

			};

			plugin.addClasses = function() {

				var classes = '',
					data_classes = $target.attr( 'data-hotips-class' ),
					opts_classes = plugin.opts.classes;

				if ( data_classes ) {
					classes += data_classes;
				} else {
					if ( opts_classes ) {
						data_classes += opts_classes;
					}
				}

				$tooltip.addClass( classes );
			}

			plugin.setup = function() {

				if ( ! plugin.setContent() )
					return;

				plugin.position();

			};

			plugin.setContent = function() {

				var $content_element = null;

				if ( $target.attr( 'data-hotips-content' ) ) {

					$content_element = $document.find( $target.attr( 'data-hotips-content' ) );

					if ( ! $content_element.length || $.trim( $content_element.html() ) === '' ) {
						return false;
					}

					var html = $content_element.html();

					$content = $( '<span>' + html + '</span>' ).addClass( 'ee-tooltip__content' );

				} else if ( plugin.opts.source ) {

					if ( typeof plugin.opts.source == 'string' ) {
						$content_element = $document.find( plugin.opts.source );
					} else if ( typeof plugin.opts.source == 'object' ) {
						$content_element = plugin.opts.source;
					}

					if ( ! $content_element.length || $.trim( $content_element.html() ) === '' ) {
						return false;
					}

					var html = $content_element.html();

					$content = $( '<span>' + html + '</span>' ).addClass( 'ee-tooltip__content' );

				} else if ( plugin.opts.content ) {

					$content = plugin.opts.content;

				} else {

					return false;

				}

				return true;
			};

			plugin.events = function() {
				if ( ! $content ) return;

				$(window).on( 'resize', plugin.onResize );

				$target.on( 'hotip.show', plugin.show );
				$target.on( 'hotip.hide', plugin.hide );

				plugin.setTriggerEvents();
			};

			plugin.setTriggers = function() {

				if ( plugin.isMobile() ) { // Mobile down
					_trigger = plugin.opts.trigger.mobile;
					_hide = plugin.opts.hide.mobile;
				} else if ( plugin.isTablet() ) { // Tablet down
					_trigger = plugin.opts.trigger.tablet;
					_hide = plugin.opts.hide.tablet;

				} else { // Desktop
					_trigger = plugin.opts.trigger.desktop;
					_hide = plugin.opts.hide.desktop;
				}
			};

			plugin.setTriggerEvents = function() {
				if ( plugin.isDesktop() ) { // Desktop
					plugin.setDesktopTriggerEvents();
				} else { // Tablet and down
					plugin.setMobileTriggerEvents();
				}
			};

			plugin.setDesktopTriggerEvents = function() {

				if ( _trigger === 'mouseenter' || _trigger === 'hover' ) {
					$target.on( 'mouseenter', plugin.show );
				} else if ( _trigger === 'click_target' ) {
					$target.on( 'click', plugin.show );
				} else if ( _trigger === 'load' ) {
					$document.on( 'ready', plugin.show );
				} else {
					$target.on( 'mouseenter', plugin.show );
				}

				if ( _hide === 'mouseleave' ) {
					$target.on( 'mouseleave', plugin.hide );
				} else if ( _hide === 'click_out' ) {
					$document.on( 'mouseup', plugin.clickOutside );
				} else if ( _hide === 'click_any' ) {
					$document.on( 'mouseup', plugin.hide );
					$target.off( 'click', plugin.show ).on( 'click', plugin.toggle );
				} else if ( _trigger !== 'click_target' && _hide === 'click_target' ) {
					$target.on( 'click', plugin.hide );
				} else if ( _trigger === 'click_target' && _hide === 'click_target' ) {
					$target.off( 'click', plugin.show ).on( 'click', plugin.toggle );
				} else {
					$target.on( 'mouseleave', plugin.hide );
				}
			};

			plugin.setMobileTriggerEvents = function() {

				if ( _trigger === 'mouseenter' || _trigger === 'hover' ) {
					$target.on( 'mouseenter', plugin.show );
				} else if ( _trigger === 'click_target' ) {
					$target.on( 'touchstart', plugin.show );
				} else if ( _trigger === 'load' ) {
					$document.on( 'ready', plugin.show );
				} else {
					$target.on( 'mouseenter', plugin.show );
				}

				if ( _hide === 'mouseleave' ) {
					$target.on( 'mouseleave', plugin.hide );
				} else if ( _hide === 'click_out' ) {
					$document.on( 'mouseup', plugin.clickOutside );
				} else if ( _hide === 'click_any' ) {
					$target.off( 'touchstart', plugin.show ).on( 'touchstart mouseleave', plugin.toggle );
				} else if ( _trigger !== 'click_target' && _hide === 'click_target' ) {
					$target.on( 'touchstart', plugin.hide );
				} else if ( _trigger === 'click_target' && _hide === 'click_target' ) {
					$target.off( 'touchstart', plugin.show ).on( 'touchstart', plugin.toggle );
				} else {
					$target.on( 'mouseleave', plugin.hide );
				}
			};

			plugin.removeTriggerEvents = function() {
				$target.off( 'mouseenter', plugin.show );
				$target.off( 'touchstart', plugin.show );
				$target.off( 'mouseleave', plugin.hide );
				$target.off( 'click', plugin.show );
				$target.off( 'touchstart', plugin.show );
				$target.off( 'click', plugin.hide );
				$target.off( 'touchstart', plugin.hide );
				$target.off( 'click', plugin.toggle );
				$target.off( 'touchstart', plugin.toggle );
				$document.off( 'ready', plugin.show );
				$document.off( 'mouseup', plugin.clickOutside );
				$document.off( 'touchstart', plugin.clickOutside );
			}

			plugin.clickOutside = function( event ) {
				// Check if clicked element is or is contained by our target
				var isNotClickedTarget = ! $target.is( event.target ) && $target.has( event.target ).length === 0,
					isNotClickedTooltip = ! $tooltip.is( event.target ) && $tooltip.has( event.target ).length === 0;

				if ( isNotClickedTarget && isNotClickedTooltip ) {
					plugin.hide();
				}
			};

			plugin.getPosition = function() {

				if ( $window.width() < $tooltip.outerWidth() )
					$tooltip.css( 'max-width', $window.width() );

				if ( ! plugin.opts.position ) {
					plugin.opts.position = 'bottom';
				}

				// Calculating position for direction

				var _to 			= plugin.opts.position,
					_at 			= ( 'left' === _to || 'right' === _to ) ? plugin.opts.arrowPositionV : plugin.opts.arrowPositionH,
					_scrollTop 		= plugin.opts.fixed ? 0 : $(window).scrollTop(),
					_tooltipRect 	= $tooltip.get(0).getBoundingClientRect(),
					_targetRect 	= $target.get(0).getBoundingClientRect(),

					_tooltipWidth	= plugin.opts.fixed ? _tooltipRect.width : $tooltip.outerWidth(),
					_tooltipHeight	= plugin.opts.fixed ? _tooltipRect.height : $tooltip.outerHeight(),
					_targetTop 		= plugin.opts.fixed ? _targetRect.top : $target.offset().top,
					_targetLeft 	= plugin.opts.fixed ? _targetRect.left : $target.offset().left,
					_targetWidth	= plugin.opts.fixed ? _targetRect.width : $target.outerWidth(),
					_targetHeight	= plugin.opts.fixed ? _targetRect.height : $target.outerHeight();

				if ( plugin.opts.position === 'bottom' ) { /* BOTTOM */

					_tooltipTop 	= _targetTop + _targetHeight + 10;
					_tooltipLeft 	= _targetLeft + ( _targetWidth / 2 ) - ( _tooltipWidth / 2 );
					_offset 		= 20;

				} else if ( plugin.opts.position === 'top' ) { /* TOP */

					_tooltipTop 	= _targetTop - _tooltipHeight - 10;
					_tooltipLeft 	= _targetLeft + ( _targetWidth / 2 ) - ( _tooltipWidth / 2 );
					_offset 		= -20;

				} else if ( plugin.opts.position === 'left' ) { /* LEFT */

					_tooltipTop 	= _targetTop + ( _targetHeight / 2 ) - ( _tooltipHeight / 2 );
					_tooltipLeft 	= _targetLeft - _tooltipWidth - 10;
					_offset 		= -20;

				} else if ( plugin.opts.position === 'right' ) { /* RIGHT */

					_tooltipTop 	= _targetTop + ( _targetHeight / 2 ) - ( _tooltipHeight / 2 );
					_tooltipLeft 	= _targetLeft + _targetWidth + 10;
					_offset 		= 20;
				}

				if ( 'top' === plugin.opts.position || 'bottom' === plugin.opts.position ) {
					if ( 'right' === plugin.opts.arrowPositionH ) {
						_tooltipLeft 	= _targetLeft + _targetWidth - _tooltipWidth;
					} else if ( 'left' === plugin.opts.arrowPositionH ) {
						_tooltipLeft 	= _targetLeft;
					}
				}

				if ( 'left' === plugin.opts.position || 'right' === plugin.opts.position ) {
					if ( 'bottom' === plugin.opts.arrowPositionV ) {
						_tooltipTop 	= _targetTop + _targetHeight - _tooltipHeight;
					} else if ( 'top' === plugin.opts.arrowPositionV ) {
						_tooltipTop 	= _targetTop;
					}
				}

				// Overrides for outside of viewport

				var _targetViewportTop 		= _tooltipTop - _scrollTop,
					_targetViewportLeft 	= _tooltipLeft,
					_targetViewportRight 	= $(window).width() - ( _tooltipLeft + _tooltipWidth ),
					_targetViewportBottom 	= $(window).height() + _scrollTop - ( _tooltipTop + _tooltipHeight );

				if ( _targetViewportTop < 0 ) {
					if ( plugin.opts.position === 'left' || plugin.opts.position === 'right' ) {

						_tooltipTop 	= _targetTop;
						_at 			= 'top';

					} else {

						_tooltipTop 	= _targetTop + _targetHeight + 10;
						_to 			= 'bottom';
						_offset 		= 20;
					}
				}

				if ( _targetViewportBottom < 0 ) {
					if ( plugin.opts.position === 'left' || plugin.opts.position === 'right' ) {

						_tooltipTop 	= _targetTop + _targetHeight - _tooltipHeight;
						_at 			= 'bottom';

					} else {

						_tooltipTop 	= _targetTop - _tooltipHeight - 10;
						_to 			= 'top';
						_offset 		= -20;

					}
				}

				if ( _targetViewportLeft < 0 ) {

					if ( plugin.opts.position === 'left' ) {

						_tooltipLeft 	= _targetLeft + _targetWidth + 10;
						_to 			= 'right';
						_offset 		= 20;

					} else {

						_tooltipLeft 	= _targetLeft;
						_at 			= 'left';

					}
				}

				if ( _targetViewportRight < 0 ) {

					if ( plugin.opts.position === 'right' ) {

						_tooltipLeft 	= _targetLeft - _tooltipWidth - 10;
						_to 			= 'left';
						_offset 		= -20;

					} else {

						_at 	= 'right';

						if ( plugin.opts.position === 'top' || plugin.opts.position === 'bottom' ) {
							_tooltipLeft = _targetLeft + _targetWidth - _tooltipWidth;
						} else {
							_tooltipLeft = _targetLeft + _targetWidth;
						}
					}
				}

				return {
					top : _tooltipTop,
					left : _tooltipLeft,
					to : _to,
					at : _at,
				}
			};

			plugin.onResize = function() {
				plugin.removeTriggerEvents();
				plugin.setTriggers();
				plugin.setTriggerEvents();
				plugin.position();
			};

			plugin.position = function() {
				var _position 	= plugin.getPosition();

				$tooltip.css({
					position : plugin.opts.fixed ? 'fixed' : 'absolute',
					top 	: _position.top,
					left 	: _position.left,
				});

				var _atClass = _position.at ? 'at--' + _position.at : '',
					_toClass = _position.to ? 'to--' + _position.to : '';

				$tooltip.removeClass( 'to--top to--bottom to--right to--left at--left at--right at--top at--bottom' );
				$tooltip.addClass( _toClass + ' ' + _atClass );
			};

			plugin.canShow = function() {
				if ( 'tablet' === plugin.opts.responsive.disable && ( plugin.isTablet() || plugin.isMobile() ) ) {
					return false;
				} else if ( 'mobile' === plugin.opts.responsive.disable && plugin.isMobile() ) {
					return false;
				}

				return true;
			};

			plugin.toggle = function( event ) {
				if ( is_open ) {
					plugin.hide( event );
				} else {
					plugin.show( event );
				}
			};

			plugin.show = function( event ) {

				// Exit to prevent opening when already open
				if ( is_open === true )
					return;

				if ( ! $content )
					return;

				is_open = true;

				plugin.addClasses();

				// Add html to tooltip making sure the html is not encoded
				$tooltip.empty().append( $content );

				// Append tooltip to body
				$document.find('body').append( $tooltip );

				// We need to "really" add it to get the dimmensions when the target is fixed
				$tooltip.css({
					position: 'fixed',
					left: -99999,
					top: -99999,
				});

				// Reposition if size changes
				if ( typeof $.fn._resize !== 'undefined' && plugin.opts.scope ) {
					$tooltip._resize( function() {
						plugin.onResize();
					});
				}

				// Update position
				plugin.position();

				// Animate it in
				TweenMax.killTweensOf( $tooltip );

				if ( plugin.opts.position === 'top' ) {
					
					TweenMax.fromTo( $tooltip, plugin.opts.speed,
						{ top : _tooltipTop + _offset, autoAlpha : 0 },
						{ delay	: plugin.opts.delayIn, top : _tooltipTop, autoAlpha : 1, onComplete : function() { is_open = true; }
					});

				} else if ( plugin.opts.position === 'right' ) {

					TweenMax.fromTo( $tooltip, plugin.opts.speed,
						{ left : _tooltipLeft + _offset, autoAlpha : 0 },
						{ delay	: plugin.opts.delayIn, left : _tooltipLeft, autoAlpha : 1, onComplete : function() { is_open = true; }
					});

				} else if ( plugin.opts.position === 'bottom' ) {
					
					TweenMax.fromTo( $tooltip, plugin.opts.speed,
						{ top : _tooltipTop + _offset, autoAlpha : 0 },
						{ delay	: plugin.opts.delayIn, top : _tooltipTop, autoAlpha : 1, onComplete : function() { is_open = true; }
					});

				} else if ( plugin.opts.position === 'left' ) {
					
					TweenMax.fromTo( $tooltip, plugin.opts.speed,
						{ left : _tooltipLeft + _offset, autoAlpha : 0 },
						{ delay	: plugin.opts.delayIn, left : _tooltipLeft, autoAlpha : 1, onComplete : function() { is_open = true; }
					});

				}

				if ( plugin.opts.hide === 'click_any' && plugin.opts.trigger === 'click_target' ) {
					$target.on( 'click', plugin.show );
				}
			};

			plugin.hide = function( event ) {

				// Animate it out
				TweenMax.killTweensOf( $tooltip );

				if ( plugin.opts.position === 'top' ) {
					
					TweenMax.to( $tooltip, plugin.opts.speed, { top : _tooltipTop + _offset, autoAlpha : 0, delay : plugin.opts.delayOut,
						onComplete : function() {
							$tooltip.remove();
							is_open = false;
						}
					});

				} else if ( plugin.opts.position === 'right' ) {

					TweenMax.to( $tooltip, plugin.opts.speed, { left : _tooltipLeft + _offset, autoAlpha : 0, delay : plugin.opts.delayOut,
						onComplete : function() {
							$tooltip.remove();
							is_open = false;
						}
					});

				} else if ( plugin.opts.position === 'bottom' ) {
					
					TweenMax.to( $tooltip, plugin.opts.speed, { top : _tooltipTop + _offset, autoAlpha : 0, delay : plugin.opts.delayOut,
						onComplete : function() {
							$tooltip.remove();
							is_open = false;
						}
					});

				} else if ( plugin.opts.position === 'left' ) {
					
					TweenMax.to( $tooltip, plugin.opts.speed, { left : _tooltipLeft + _offset, autoAlpha : 0, delay : plugin.opts.delayOut,
						onComplete : function() {
							$tooltip.remove();
							is_open = false;
						}
					});

				}

			};

			plugin.isMobile = function() {
				return $window.width() < plugin.opts.responsive.breakpoints['tablet'];
			};

			plugin.isTablet = function() {
				return $window.width() >= plugin.opts.responsive.breakpoints['tablet'] && $window.width() < plugin.opts.responsive.breakpoints['desktop'];
			};

			plugin.isDesktop = function() {
				return $window.width() >= plugin.opts.responsive.breakpoints['desktop'];
			};

			plugin.destroy = function() {

				// First remove the tooltip
				plugin.hide();

				// Unbinds
				$window.off( 'resize', plugin.onResize );
				
				plugin.removeTriggerEvents();

				// Rememvber the "soft" destroy
				is_destroyed = true;

			};

			plugin.init();

		};

		$.fn.hotips = function(options) {

			return this.each(function() {

				$.fn.hotips.destroy = function() {
					if( undefined !== typeof( plugin ) ) {
						$(this).data('hotips').destroy();
						$(this).removeData('hotips');
					}
				}

				if ( undefined !== typeof $(this).data('hotips') ) {
					var plugin = new $.hotips(this, options);
					$(this).data('hotips', plugin);
				}
			});

		};

	}

)( jQuery, window, document );