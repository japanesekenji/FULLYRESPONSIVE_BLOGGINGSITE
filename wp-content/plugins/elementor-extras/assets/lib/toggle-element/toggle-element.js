// -- toggleElement
// @license toggle-element v1.2.0 | MIT | Namogo 2018 | https://www.namogo.com
// --------------------------------
;(
    function( $, window, document, undefined ) {

		$.toggleElement = function(element, options) {

			var defaults = {
				hashLoad 			: false,
				hashToggle 			: false,
				wrapper 			: null,
				watchControls 		: false,
				speed 				: 0.3,
				inactive 			: false,
				active 				: 1,
				indicatorColor 		: 'rgba(0, 0, 0, 1)',
				controlItemClass 	: '.ee-toggle-element__controls__item',
				indicatorClass 		: '.ee-toggle-element__indicator',
				elementClass 		: '.ee-toggle-element__element',
				onBeforeToggle 		: function(){},
				onAfterToggle 		: function( $currentElement ){},
			};

			var plugin = this;

			plugin.opts = {};

			var $window			= $(window),
				$document		= $(document),
				$element 		= $(element),
				$controls 		= null,
				$indicator 		= null,
				$elements 		= null,
				$current 		= null,
				$currentElement = null,

				_toggled 		= false,
				_current 		= 0,
				_next 			= null,
				_total 			= null;

			plugin.init = function() {
				plugin.opts = $.extend({}, defaults, options);
				plugin._construct();
			};

			plugin._construct = function() {

				$controls 		= $element.find( plugin.opts.controlItemClass );
				$indicator 		= $element.find( plugin.opts.indicatorClass );
				$elements 		= $element.find( plugin.opts.elementClass );

				_total = $controls.length;

				if ( plugin.opts.active > 0 && plugin.opts.active <= _total ) {
					plugin.setCurrent();
				}

				plugin.setup();
				plugin.events();

			};
			
			plugin.replaceHash = function( index ) {
				var $control = $controls.eq( index );

				if ( ! $control.length ) {
					return;
				}

				if ( history.pushState ) {
				    history.pushState( null, null, '#' + $control.data('hash') );
				}
			}

			plugin.setCurrent = function() {
				var locationHash = window.location.hash.substr( 1 );

				if ( plugin.opts.hashLoad && locationHash ) {
					var $currentControl = $controls.filter('[data-hash="' + locationHash + '"]');

					if ( $currentControl.length ) {
						_current = $currentControl.index();
						return;
					}
				}

				_current = plugin.opts.active - 1;
			};

			plugin.setup = function() {

				$current = $controls.eq( _current );

				if ( ! plugin.opts.inactive ) {
					if ( this.opts.watchControls ) {
						this.adjust();
					}

					this.goTo( _current );
				}

				$element.addClass( 'ee--is-ready' );
			};

			plugin.events = function() {
				
				$controls.on( 'click', plugin.onClick );
				$element._resize( plugin.adjust );

			};

			plugin.onClick = function( event ) {

				var $this 	= $( event.target ).closest( '.ee-toggle-element__controls__item' ),
					_index 	= $this.index(), 
					_next 	= _index < ( _total - 1 ) ? _index + 1 : 0;

				// Only go to next if already toggled
				if ( _toggled ) {
					if ( _current === _index ) {
						_current = _next;
					} else {
						_current = _index;
					}
				} else {
					_current = _index;
				}

				if ( plugin.opts.hashToggle ) {
					plugin.replaceHash( _current );
				}
				
				plugin.goTo( _current );
			}

			plugin.goTo = function( index ) {

				plugin.opts.onBeforeToggle();

				$current = $controls.eq( index );
				$currentElement = $elements.eq( index );

				$elements.hide();
				$currentElement.show();

				$controls.data( 'active', false );
				$controls.eq( index ).data( 'active', true );

				this.adjust();

				$controls.removeClass( 'ee--is-active' );
				$current.addClass( 'ee--is-active' );

				plugin.opts.onAfterToggle( $currentElement );

				$window.trigger('resize');

				_toggled = true;
			};

			plugin.adjust = function() {
				if ( ! _toggled ) {
					return;
				}

				var _left 	= $current.get(0).offsetLeft,
					_top 	= $current.get(0).offsetTop,
					_width 	= $current.outerWidth(),
					_height = $current.outerHeight(),
					_color 	= 'undefined' !== typeof $current.data('color') ? $current.data('color') : plugin.opts.indicatorColor,
					_args 	= {
						left 	: _left,
						top 	: _top,
						width 	: _width,
						height 	: _height,
					};

				if ( _color ) {
					_args.backgroundColor = _color;
				}

				TweenMax.to( $indicator, plugin.opts.speed, _args );
			};

			plugin.destroy = function() {
				$controls.off( 'click', plugin.onClick );
			};

			plugin.init();

		};

		$.fn.toggleElement = function(options) {

			return this.each(function() {

				$.fn.toggleElement.destroy = function() {
					if( 'undefined' !== typeof( plugin ) ) {
						$(this).data('toggle-element').destroy();
						$(this).removeData('toggle-element');
					}
				}

				if (undefined === $(this).data('toggle-element')) {
					var plugin = new $.toggleElement(this, options);
					$(this).data('toggle-element', plugin);
				}
			});

		};

	}

)( jQuery, window, document );