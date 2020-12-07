// -- videoPlayer
// @license videoPlayer v1.0.0 | MIT | Namogo 2018 | https://www.namogo.com
// --------------------------------
(function($) {

	$.videoPlayer = function(element, options) {

		var defaults = {
			cover				: '.ee-player__cover',
			
			volume 				: 0.5,
			speed 				: 1,
			controls			: '.ee-player__controls',
			bar 				: '.ee-player__controls__bar-wrapper',
			controlPlay			: '.ee-player__controls__play',
			controlRewind		: '.ee-player__controls__rewind',
			controlFullScreen 	: '.ee-player__controls__fs',
			controlTime 		: '.ee-player__controls__time',
			controlDuration 	: '.ee-player__controls__duration',
			controlProgressBar 	: '.ee-player__controls__progress',
			controlProgress		: '.ee-player__controls__progress-time',
			controlVolumeBar 	: '.ee-player__controls__volume-bar',
			controlVolume 		: '.ee-player__controls__volume-bar__amount',
			controlVolumeIcon 	: '.ee-player__controls__volume-icon',

			overlays 			: '.ee-player__controls__overlay',

			restartOnPause		: false,
			stopOthersOnPlay 	: false,
			playOnViewport		: false,
			stopOffViewport		: false,
			endAtLastFrame 		: false,
			disableFsRightClick : false,
		};

		var plugin = this;

		plugin.opts = {};

		var $document 			= $(document),
			$wrapper			= $(element),
			$video 				= $wrapper.find('> video'),
			video 				= $video.get(0),
			cover 				= null,
			$cover				= null,

			$controls			= null,
			$bar 				= null,
			$controlPlay		= null,
			$controlRewind		= null,
			$controlFullScreen 	= null,
			$controlTime 		= null,
			$controlDuration 	= null,
			$controlProgressBar = null,
			$controlProgress	= null,
			$controlVolumeBar 	= null,
			$controlVolume 		= null,
			$controlVolumeIcon 	= null,

			controlRewind 		= null,
			controlPlay 		= null,

			volume 				= null,

			source				= $video.attr('src'),

			_is_autoplay 		= $video.is("[autoplay]"),
			_is_dragging_time 	= false,
			_is_dragging_volume = false,
			_is_playing			= false,
			_is_loaded 			= false,
			_is_ios 			= /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream,
			_is_ios_played 		= false,

			_drag_time_amount 	= 0;


		plugin.init = function() {
			plugin.opts = $.extend({}, defaults, options);
			plugin._construct();
		};

		plugin._construct = function() {

			$cover				= $wrapper.find( plugin.opts.cover );
			$controls			= $wrapper.find( plugin.opts.controls );
			$bar 				= $wrapper.find( plugin.opts.bar );

			$controlRewind		= $controls.find( plugin.opts.controlRewind );
			$controlPlay		= $controls.find( plugin.opts.controlPlay );
			$controlFullScreen 	= $controls.find( plugin.opts.controlFullScreen );
			$controlDuration 	= $controls.find( plugin.opts.controlDuration );
			$controlTime 		= $controls.find( plugin.opts.controlTime );
			$controlProgress	= $controls.find( plugin.opts.controlProgress);
			$controlProgressBar = $controls.find( plugin.opts.controlProgressBar);
			$controlVolume		= $controls.find( plugin.opts.controlVolume);
			$controlVolumeBar 	= $controls.find( plugin.opts.controlVolumeBar);
			$controlVolumeIcon 	= $controls.find( plugin.opts.controlVolumeIcon);

			volume 				= plugin.opts.volume;
			playbackRate 		= plugin.opts.speed

			cover 				= $cover.get(0);

			if ( $controls.length ) {
				controlPlay 	= $controlPlay.get(0);

				if ( $controlRewind.length )
				controlRewind 	= $controlRewind.get(0);
			}

			// Make sure video is loaded on iOS
			video.load();

			// Bind events
			plugin.events();

			// Setup viewport play
			plugin.initViewportPlay();

			// Setup progress interactions
			plugin.initProgressBar();

			// Setup volume interactions
			plugin.initVolumeBar();
		};

		plugin.events = function() {

			if ( $controlPlay.length ) {
				$controlPlay.on( 'click', plugin.maybePlay );
			}

			if ( $cover.length ) {
				$cover.on( "click", plugin.maybePlay );
			}

			if ( $controlRewind.length ) {
				$controlRewind.on( "click", plugin.maybeRewind );
			}

			if ( $controlFullScreen.length ) {
				$controlFullScreen.on( "click", function(e) {
					e.preventDefault();
					plugin.fullscreen();
				});
			}

			if ( plugin.opts.disableFsRightClick ) {
				$video.on( 'contextmenu', plugin.onContextMenu );
			}

			video.addEventListener( "loadedmetadata", plugin.initVideo );

			// Update time controls
			video.addEventListener( 'timeupdate', plugin.updateTime );

			// Check if video completed laoded
			video.addEventListener( 'canplaythrough', plugin.canPlayThrough );

			// Stop if video ended
			video.addEventListener( 'ended', plugin.ended );

			// Set default playback rate
			video.defaultPlaybackRate = playbackRate;

			if( _is_ios ) {
				$video.on( 'webkitExitFullscreen', plugin.stop );
			}

		};

		plugin.onContextMenu = function( event ) {
			event.preventDefault();
		};

		plugin.canPlayThrough = function() {
			_is_loaded = true;
		};

		plugin.ended = function() {
			plugin.stop( false );

			$wrapper.trigger( 'ee:video-player:ended', [ plugin ] );
		};

		plugin.initVideo = function() {
			var initialVolume = 'true' === $video.attr( 'muted' ) ? 0 : volume;

			video.playbackRate = playbackRate;

			plugin.updateVolume( 0, initialVolume );
			plugin.updateDuration();

			if ( _is_autoplay ) {
				plugin.beforePlay();
				plugin.afterPlay();
			}

			// Trigger window resize due to changing of player height
			$( window ).trigger('resize');
		};

		plugin.initProgressBar = function() {
			if ( $controlProgressBar.length ) {

				$controlProgressBar.on( 'mousedown', function(e) {
					_is_dragging_time = true;
					// plugin.stop( true );
					plugin.updateProgress( e.pageX );
				});

				$document.on( 'mouseup', function(e) {
					if( _is_dragging_time ) {
						_is_dragging_time = false;
						plugin.updateProgress( e.pageX );
					}
				});

				$document.on('mousemove', function(e) {
					if( _is_dragging_time ) {
						plugin.updateProgress( e.pageX );
					}
				});
			}
		};

		plugin.initVolumeBar = function() {

			if ( $controlVolumeIcon.length ) {

				$controlVolumeIcon.click( function(e) {
					e.preventDefault();

					if ( video.volume == 0 ) { // Un-mute

						// Don't keep 0 volume
						if ( volume == 0 ) volume = plugin.opts.volume;

						// Make sure we unmute the video element
						video.muted = false;

						// Update volume with the last known value
						plugin.updateVolume( 0, volume );

					} else {  // Mute

						var _volume = video.volume;

						// Turn volume off
						plugin.updateVolume( 0, 0 );

						// Update volume with last known value to be > 0
						volume = _volume;
					}
				});

			}

			if ( $controlVolumeBar.length ) {

				$controlVolumeBar.on( 'mousedown', function(e) {
					_is_dragging_volume = true;

					// Make sure it's no longer muted
					video.muted = false;

					plugin.updateVolumeIcon( 1 );

					// Update volume
					plugin.updateVolume( e.pageX );
				});

				$document.on( 'mouseup', function(e) {
					if( _is_dragging_volume ) {
						_is_dragging_volume = false;

						// Update volume
						plugin.updateVolume( e.pageX );
					}
				});

				$document.on( 'mousemove', function(e) {
					if( _is_dragging_volume ) {

						// Update volume
						plugin.updateVolume( e.pageX );
					}
				});

			}
		};

		plugin.initViewportPlay = function() {
			$wrapper._appear({ force_process: true });

			// Handles autplay when in viewport
			if (  plugin.opts.playOnViewport ) {
				$wrapper.on( '_appear', function(){ plugin.play(); } );

				if ( plugin.opts.stopOffViewport )
				$wrapper.on( '_disappear', function(){ plugin.stop( true ); });
			}
		};

		plugin.beforePlay = function() {
			// Adjust button classes
			$controlPlay.removeClass('nicon-play').addClass('nicon-pause');
		};

		plugin.play = function() {

			if ( _is_playing )
				return;

			plugin.beforePlay();

			$wrapper.trigger( 'ee:video-player:beforePlay', [ plugin ] );

			// Play it
			video.play();

			// Everything else
			plugin.afterPlay();

			$wrapper.trigger( 'ee:video-player:afterPlay', [ plugin ] );
		};

		plugin.afterPlay = function() {

			// Adjust classes
			$wrapper.removeClass('paused').addClass('playing');

			// Not playing anymore
			_is_playing = true;

			// Stop all other videos conditionally
			if ( plugin.opts.stopOthersOnPlay ) {
				var $players = $('.ee-video-player').not(element);

				$players.each( function() {
					var instance = $(this).data('videoPlayer');

					instance.stop( true );
				});
			}

			// Make sure overlays are turned off
			// TweenMax.set( plugin.opts.overlays, { opacity: 0 });
		};

		plugin.stop = function( pausing ) {

			if ( ! _is_playing )
				return;

			// Adjust classes
			$wrapper.removeClass('playing');

			$controlPlay.removeClass('nicon-pause').addClass('nicon-play');

			if ( ! _is_playing )
				return;

			if ( pausing ) { // Pausing

				// Add paused classes
				$wrapper.addClass('paused');

				// Pause the video
				video.pause();

				if ( plugin.opts.restartOnPause )
					video.currentTime = 0; // Go to first frame

			} else { // Ending

				if ( ! plugin.opts.endAtLastFrame ) {
					video.currentTime = 0;
				}
			}

			_is_playing = false;

			$wrapper.trigger( 'ee:video-player:stop', [ plugin ] );
		};

		plugin.maybeRewind = function() {
			video.currentTime = 0;
			plugin.play();

			$wrapper.trigger( 'ee:video-player:rewind', [ plugin ] );
		};

		plugin.maybePlay = function( event ) {

			if ( ! _is_playing ) {
				plugin.play();
			} else {
				plugin.stop( true );
			}

			return false;
		};

		plugin.fullscreen = function() {

			if ( video.requestFullscreen ) {
			    video.requestFullscreen();
			} else if ( video.webkitRequestFullscreen ) {
			    video.webkitRequestFullscreen();
			} else if ( video.webkitEnterFullscreen ) {
				video.webkitEnterFullscreen();
			} else if ( video.mozRequestFullScreen ) {
			    video.mozRequestFullScreen();
			} else if ( video.msRequestFullscreen ) {
			    video.msRequestFullscreen();
			} else {
				alert('Your browser doesn\'t support fullscreen');
			}
		};

		plugin.updateTime = function() {

			var position 	= video.currentTime,
				duration 	= video.duration,
				percentage 	= 100 * position / duration;

			if ( $controlProgress)
				$controlProgress.css( 'width', percentage + '%' );

			// Update time text
			if ( $controlTime.length )
				$controlTime.html( plugin.formatTime( position ) );
		};

		plugin.updateDuration = function() {

			var duration 	= video.duration;

			// Update duration text
			if ( $controlDuration.length )
				$controlDuration.html( plugin.formatTime( duration ) );
		};

		plugin.updateProgress = function( amount ) {

			var duration 	= video.duration,
				position 	= amount - $controlProgressBar.offset().left,
				percentage 	= 100 * position / $controlProgressBar.width();

			if ( percentage > 100 ) percentage = 100;
				else if ( percentage < 0 )
					percentage = 0;

			$controlProgress.css( 'width', percentage + '%');

			video.currentTime = duration * percentage / 100;
		};

		plugin.updateVolume = function( amount, volume ) {
			var percentage;

			if( volume ) {
				percentage = volume * 100;
			} else {

				var offsetLeft = ( $controlVolumeBar.length ) ? $controlVolumeBar.offset().left : 1,
					position = amount - offsetLeft,
					percentage = 100 * position / $controlVolumeBar.width();
			}
			
			if ( percentage > 100 ) percentage = 100;
				else if ( percentage < 0 )
					percentage = 0;

			// Update video volume
			video.volume = percentage / 100;

			plugin.updateVolumeIcon( video.volume );
			
			// Update volume control position
			$controlVolume.css( 'width',percentage + '%' );

			// Keep value
			volume = video.volume;
		}

		plugin.updateVolumeIcon = function( vol ) {
			if ( vol == 0 )
				$controlVolumeIcon.addClass('nicon-volume-off').removeClass('nicon-volume');
			else
				$controlVolumeIcon.addClass('nicon-volume').removeClass('nicon-volume-off');
		}

		// ————
		// https://stackoverflow.com/a/4605470
		// ————
		plugin.formatTime = function( seconds ) {
			minutes = Math.floor(seconds / 60);
		    minutes = (minutes >= 10) ? minutes : "0" + minutes;
		    seconds = Math.floor(seconds % 60);
		    seconds = (seconds >= 10) ? seconds : "0" + seconds;
		    return minutes + ":" + seconds;
		};

		plugin.init();

	};

	$.fn.videoPlayer = function(options) {

		return this.each(function() {
			if (undefined == $(this).data('videoPlayer')) {
				var plugin = new $.videoPlayer(this, options);
				$(this).data('videoPlayer', plugin);
			}
		});

	};

})(jQuery);
