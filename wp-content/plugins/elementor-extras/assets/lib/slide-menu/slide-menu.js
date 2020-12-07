// -- slide Menu
// @license slideMenu v1.0.0 | MIT | Namogo 2018 | https://www.namogo.com
// --------------------------------
;(
    function( $, window, document, undefined ) {

		$.slideMenu = function(element, options) {

			var defaults = {
				scope 				: $(window),
				linkNavigation 		: false,
				backLabel 			: 'Back',
			};

			var plugin = this;

			plugin.opts = {};

			var $window			= null,
				$document		= null,
				$body 			= null,

				target			= element,
				$target			= $(element),

				$current 		= null,
				$previous		= null,

				$links 			= $target.find( 'li.ee-menu__item--has-children > a' ),
				$submenus 		= $target.find( 'ul.ee-menu__sub-menu' );

			plugin.init = function() {

				plugin.opts = $.extend({}, defaults, options);
				plugin._construct();
			};

			plugin._construct = function() {

				$window	= plugin.opts.scope;
				$body 	= $('body');

				plugin.setup();
			};

			plugin.setup = function() {

				$links.each( function() {
					var $link = $(this),
						$item = $link.parent(),
						$submenu = $link.next( '.ee-menu__sub-menu' ),
						$arrow = $link.prev( '.ee-menu__arrow' ),
						$trigger = ( true === plugin.opts.linkNavigation ) ? $link.add( $arrow ) : $arrow,
						$back = $( '<li class="ee-menu__item ee-menu__back"><span class="ee-menu__arrow"><i class="fa fa-angle-left"></i></span><a href="#" class="ee-menu__item__link ee-menu__sub-item__back">' + plugin.opts.backLabel + '</a></li>' );

					$submenu.prepend( $back );

					$trigger.on( 'click', function( e ) {
						e.preventDefault();
						e.stopPropagation();

						$submenu.addClass( 'ee--is-active' );
						$target.css( { height : $submenu.height() } );
						$submenu.parents('ul').first().addClass( 'ee--is-active-parent' );
						$current = $submenu;
					});

					$back.on( 'click', function( e ) {
						e.preventDefault();
						e.stopPropagation();

						var $parent = $(this).closest('ul'),
							$previous = $parent.parents('ul').first();

						$parent.removeClass( 'ee--is-active' );
						$previous.removeClass( 'ee--is-active-parent' );

						$target.css( { height: '' } );
						$target.css( { height : $previous.height() } );
					});
				});

			};

			plugin.onScroll = function() {
				currentScrollY = $window.scrollTop();
				plugin.requestTick();
			};

			plugin.requestTick = function() {
				
				if ( ! ticking ) {
					updateAF = requestAnimationFrame( plugin.update );
				}

				ticking = true;
			};

			plugin.update = function() {

			};

			plugin.destroy = function() {

			};

			plugin.open = function() {

			}

			plugin.init();

		};

		$.fn.slideMenu = function(options) {

			return this.each(function() {

				$.fn.slideMenu.destroy = function() {
					if( 'undefined' !== typeof( plugin ) ) {
						$(this).data('slideMenu').destroy();
						$(this).removeData('slideMenu');
					}
				}

				if (undefined === $(this).data('slideMenu')) {
					var plugin = new $.slideMenu(this, options);
					$(this).data('slideMenu', plugin);
				}
			});

		};

	}

)( jQuery, window, document );