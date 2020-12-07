// -- Reading Progress
// @license scrollIndicator v1.0.0 | MIT | Namogo 2019 | https://www.namogo.com
// --------------------------------
;(
    function( $, window, document, undefined ) {

		$.scrollIndicator = function(element, options) {

			var defaults = {
				scope 			: $(window),
				progress 		: 'circle',
				autoHover 		: true,
				click 			: true,
				property 		: 'width',
				scrollOffset 	: 0,
			};

			var plugin = this;

			plugin.opts = {};

			var $window			= null,
				$document		= null,
				$body 			= null,

				target			= element,
				$target			= $(element),

				$sections		= null,
				$links			= null,

				scrollTop 		= null,
				circleLength 	= null,
				windowHeight	= null,
				docHeight 		= null,
				scrolling 		= false,
				resizing 		= false,

				scrollTop 				= null,

				startTopToTop 			= null,
				startBottomToTop 		= null,

				endBottomtoBottom 		= null,
				endTopToBottom 			= null,

				progressHeight 			= null,
				progressStart 			= null,
				progressEnd 			= null,
				$sectionLinks 			= null,
				currentSectionTop 		= null,
				currentSectionHeight 	= null,
				currentSectionLink 		= null;

			plugin.init = function() {

				plugin.opts = $.extend({}, defaults, options);
				plugin._construct();
			};

			plugin._construct = function() {

				plugin.setup();

				if ( ! $sections.length )
					return;

				plugin.events();
				plugin.update();

			};

			plugin.setup = function() {
				
				$window				= plugin.opts.scope;
				$body 				= $('body');
				$sections 			= $('');
				$sectionLinks 		=  $target.find( '.ee-scroll-indicator__element__link' )
				$elements 			= $target.find('.ee-scroll-indicator__element');
				circleLength 		= parseInt(Math.PI*($elements.eq(0).find('circle').attr('r')*2));
				windowHeight 		= $window.height();
				docHeight 			= $(document).height();

				$target.addClass( 'is--active' );

				$elements.each( function() {
					var sectionId = $(this).data('selector'),
						$_section = $( "#" + sectionId );
					
					if ( $_section.length )
						$sections = $sections.add( $_section );
				});

				if ( ! $sections.length )
					$sections = $sections.add( $('body') );
			};

			plugin.events = function() {
				$window.on('scroll', plugin.checkRead );
				$window.on('resize', plugin.resetScroll);

				if ( plugin.opts.click ) {
					$target.on('click', 'a', plugin.onClick );
				}
			};

			plugin.onClick = function( event ) {
				event.preventDefault();

				var $selectedSection = $sections.eq($(this).parent('li').index());

				if ( ! $selectedSection.length ) {
					return;
				}

				var selectedSectionTop = $selectedSection.offset().top;

				$window.off('scroll', plugin.checkRead );

				$('body,html').animate(
					{ 'scrollTop': selectedSectionTop + 2 + plugin.opts.scrollOffset }, 
					300, function(){
						plugin.checkRead();
						$window.on('scroll', plugin.checkRead );
					}
				); 
			}

			plugin.getChapters = function() {
				return $target.find('.ee-scroll-indicator__element');
			};

			plugin.getDefaultSectionLink = function() {
				return plugin.getChapters().eq(0).children( '.ee-scroll-indicator__element__link' );
			};

			plugin.getSectionLink = function( $section ) {

				var _id = $section.attr( 'id' ),
					$element = plugin.getDefaultSectionLink();

				if ( _id ) {
					$_element = plugin.getChapters().filter( '[data-selector=' + _id + ']' ).children( '.ee-scroll-indicator__element__link' );
					
					if ( $_element.length )
						$element = $_element;
				}

				return $element;
			};

			plugin.isLastSection = function( index ) {
				if ( index + 1 === $sections.length )
					return true;

				return false;
			};

			plugin.isVisibleAtEnd = function( index ) {
				if ( windowHeight > docHeight - ( $sections.eq( index ).offset().top + $sections.eq( index ).outerHeight() ) )
					return true;

				return false;
			}

			plugin.isWindowTaller = function( index ) {
				if ( windowHeight >= $sections.eq( index ).outerHeight() )
					return true;

				return false;
			};

			plugin.update = function() {
				scrollTop = $window.scrollTop();

				$sections.each( function( index, value ) {
					var $section = $(this),
						otherSectionLinks = $sectionLinks.not(':eq(' + index + ')');

					plugin.updateSectionVars( index, $section );

					if ( ! currentSectionLink.length )
						return;

					if ( ! progressStart ) {
						currentSectionLink.removeClass('is--read is--reading');

						if ( plugin.opts.autoHover )
							currentSectionLink.trigger('hotip.hide');

					} else if ( progressStart && progressEnd ) {
						currentSectionLink.addClass('is--reading').removeClass('is--read');

						if ( plugin.opts.autoHover )
							currentSectionLink.trigger('hotip.show');

						plugin.progress( index, $section );
					} else {
						if ( plugin.opts.autoHover )
							currentSectionLink.trigger('hotip.hide');

						currentSectionLink.removeClass('is--reading').addClass('is--read');
					}
				});

				scrolling = false;
			};

			plugin.progress = function( index, $section ) {
				if ( 'circle' === plugin.opts.progress ) {
					plugin.updateCircle( index, $section );
				} else if ( 'background' === plugin.opts.progress ) {
					plugin.updateBackground( index, $section );
				}
			};

			plugin.updateCircle = function( index, $section ) {
				plugin.updateSectionVars( index, $section );
				
				var sectionCircle	= currentSectionLink.find('circle'),
					dashoffsetValue = circleLength * ( ( currentSectionTop + progressHeight - scrollTop ) / currentSectionHeight );
				
				sectionCircle.attr({ 'stroke-dashoffset': dashoffsetValue });
			};

			plugin.updateBackground = function( index, $section ) {
				plugin.updateSectionVars( index, $section );

				var sectionProgress 	= currentSectionLink.find('.ee-scroll-indicator__element__progress'),
					progressValue 		= 100 - ( currentSectionTop + progressHeight - scrollTop ) / currentSectionHeight * 100,
					props 				= {};

				props[ plugin.opts.property ] = progressValue + '%';
				sectionProgress.css( props );
			};

			plugin.checkRead = function() {
				if( ! scrolling ) {
					scrolling = true;
					if ( ! window.requestAnimationFrame ) {
						setTimeout( plugin.update, 300 );
					} else {
						window.requestAnimationFrame( plugin.update );
					}
				}
			};

			plugin.resetScroll = function() {
				if( ! resizing ) {
					resizing = true;
					if ( ! window.requestAnimationFrame ) {
						setTimeout( plugin.updateParams, 300 );
					} else {
						window.requestAnimationFrame( plugin.updateParams );
					}
					plugin.checkRead();
				}
			};

			plugin.updateSectionVars = function( index, $section ) {

				var start 		= plugin.getSectionLink( $section ).parent().data('start'),
					startOffset = plugin.getSectionLink( $section ).parent().data('start-offset'),
					end 		= plugin.getSectionLink( $section ).parent().data('end'),
					endOffset 	= plugin.getSectionLink( $section ).parent().data('end-offset');

				scrollTop 				= $window.scrollTop();
				currentSectionTop 		= $section.offset().top + startOffset;
				currentSectionHeight 	= $section.outerHeight() - startOffset - endOffset;
				currentSectionLink 		= plugin.getSectionLink( $section );

				startTopToTop 			= scrollTop >= currentSectionTop;
				startBottomToTop 		= scrollTop + windowHeight >= currentSectionTop;
				endBottomtoBottom 		= scrollTop + windowHeight <= currentSectionTop + currentSectionHeight;
				endTopToBottom 			= scrollTop <= currentSectionTop + currentSectionHeight;

				progressStart 			= startTopToTop;
				progressEnd 			= endTopToBottom;
				progressHeight 			= currentSectionHeight;

				if ( 'bottom-top' === start && 'bottom-bottom' === end ) {
					progressStart 		= startBottomToTop;
					progressEnd 		= endBottomtoBottom;
					circleHeight 		= currentSectionHeight + windowHeight;
					progressHeight 		= currentSectionHeight - windowHeight;
				} else if ( 'top-top' === start && 'bottom-bottom' === end ) {
					progressStart 			= startTopToTop;
					progressEnd 			= endBottomtoBottom;
					currentSectionHeight 	= currentSectionHeight - windowHeight;
					circleHeight 			= currentSectionHeight + windowHeight;
					progressHeight 			= currentSectionHeight;
				} else if ( 'bottom-top' === start && 'top-bottom' === end ) {
					progressStart 			= startBottomToTop;
					progressEnd 			= endTopToBottom;
					currentSectionHeight 	= currentSectionHeight + windowHeight;
				}

				// if ( plugin.isVisibleAtEnd( index ) && ! plugin.isLastSection( index ) ) {
				// 	progressStart 			= startTopToTop;
				// 	progressEnd 			= endBottomtoBottom;
				// 	currentSectionHeight 	= currentSectionHeight - windowHeight;
				// 	progressHeight 			= currentSectionHeight;
				// } else if ( plugin.isVisibleAtEnd( index ) && plugin.isLastSection( index ) && plugin.isWindowTaller( index ) ) {
				// 	progressStart 		= startBottomToTop;
				// 	progressEnd 		= endBottomtoBottom;
				// 	circleHeight 		= currentSectionHeight + windowHeight;
				// 	progressHeight 		= currentSectionHeight - windowHeight;
				// } else if ( plugin.isVisibleAtEnd( index ) && plugin.isLastSection( index ) && ! plugin.isWindowTaller( index ) ) {
				// 	progressStart 			= startTopToTop;
				// 	progressEnd 			= endBottomtoBottom;
				// 	currentSectionHeight 	= currentSectionHeight - windowHeight;
				// 	circleHeight 			= currentSectionHeight + windowHeight;
				// 	progressHeight 			= currentSectionHeight;
				// } else {
				// 	progressStart 		= startTopToTop;
				// 	progressEnd 		= endTopToBottom;
				// 	progressHeight 		= currentSectionHeight;
				// }
			};

			plugin.updateParams = function() {
				docHeight 		= $(document).height();
				windowHeight 	= $(window).height();
				
				$window.off('scroll', plugin.checkRead );
				$window.on('scroll', plugin.checkRead );

				resizing = false;
			};

			plugin.destroy = function() {
				$window.off('scroll', plugin.checkRead );
				$window.off('resize', plugin.resetScroll);
				$target.off('click', 'a', plugin.onClick );

				$target.removeClass( 'is--active' );
			};

			plugin.init();

		};

		$.fn.scrollIndicator = function(options) {

			return this.each(function() {

				$.fn.scrollIndicator.destroy = function() {
					if( 'undefined' !== typeof( plugin ) ) {
						$(this).data('scrollIndicator').destroy();
						$(this).removeData('scrollIndicator');
					}
				}

				if (undefined === $(this).data('scrollIndicator')) {
					var plugin = new $.scrollIndicator(this, options);
					$(this).data('scrollIndicator', plugin);
				}
			});

		};

	}

)( jQuery, window, document );