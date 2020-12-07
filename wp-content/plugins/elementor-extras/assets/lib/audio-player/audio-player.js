// -- videPlayer
// @license videPlayer v1.0.0 | MIT | Namogo 2017 | https://www.namogo.com
// --------------------------------
(function($) {

	$.audioPlayer = function(element, options) {

		var defaults = {
			
			volume 				: 0.5,
			autoplay 			: false,
			controls			: '.ee-player__controls',
			playlist 			: '.ee-player__playlist',
			tracks				: '.ee-player__playlist__item',
			trackDuration 		: '.ee-player__playlist__item__duration',
			bar 				: '.ee-player__controls__bar-wrapper',
			controlPrevious		: '.ee-player__controls__previous',
			controlPlay			: '.ee-player__controls__play',
			controlNext			: '.ee-player__controls__next',
			controlRewind		: '.ee-player__controls__rewind',
			controlTime 		: '.ee-player__controls__time',
			controlDuration 	: '.ee-player__controls__duration',
			controlBrowse 		: '.ee-player__controls__browse',
			controlProgressBar 	: '.ee-player__controls__progress',
			controlProgress		: '.ee-player__controls__progress-time',
			controlVolumeBar 	: '.ee-player__controls__volume-bar',
			controlVolume 		: '.ee-player__controls__volume-bar__amount',
			controlVolumeIcon 	: '.ee-player__controls__volume-icon',

			playOnViewport		: false,
			stopOffViewport		: false,
			loopPlaylist 		: false,
			stopOthersOnPlay 	: true,
		};

		var plugin = this;

		plugin.opts = {};

		var $document 			= $( document ),
			$wrapper			= $( element ),
			$audios 			= $wrapper.find('audio'),
			
			$audio 				= null,
			$controls			= null,
			$playlist			= null,
			$tracks				= null,
			$bar 				= null,
			$controlPlay		= null,
			$controlRewind		= null,
			$controlTime 		= null,
			$controlDuration 	= null,
			$controlBrowse 		= null,
			$controlProgressBar = null,
			$controlProgress	= null,
			$controlVolumeBar 	= null,
			$controlVolume 		= null,
			$controlVolumeIcon 	= null,

			controlRewind 		= null,
			controlPlay 		= null,

			audio 				= null,
			volume 				= null,

			_current 			= 0,
			_next 				= 0,
			_previous 			= 0,
			_total				= 0,
			_is_dragging_time 	= false,
			_is_dragging_volume = false,
			_is_playing			= false,
			_is_loaded 			= false,
			_is_autoplay 		= false,
			_is_ios 			= /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream,
			_is_ios_played 		= false,

			_drag_time_amount 	= 0;

		plugin.init = function() {
			plugin.opts = $.extend({}, defaults, options);
			plugin._construct();
		};

		plugin._construct = function() {

			$controls			= $wrapper.find( plugin.opts.controls );
			$playlist			= $wrapper.find( plugin.opts.playlist );
			$tracks				= $wrapper.find( plugin.opts.tracks );
			$bar 				= $wrapper.find( plugin.opts.bar );

			$controlRewind		= $controls.find( plugin.opts.controlRewind );
			$controlPrevious	= $controls.find( plugin.opts.controlPrevious );
			$controlPlay		= $controls.find( plugin.opts.controlPlay );
			$controlNext		= $controls.find( plugin.opts.controlNext );
			$controlDuration 	= $controls.find( plugin.opts.controlDuration );
			$controlBrowse 		= $controls.find( plugin.opts.controlBrowse );
			$controlTime 		= $controls.find( plugin.opts.controlTime );
			$controlProgress	= $controls.find( plugin.opts.controlProgress);
			$controlProgressBar = $controls.find( plugin.opts.controlProgressBar);
			$controlVolume		= $controls.find( plugin.opts.controlVolume);
			$controlVolumeBar 	= $controls.find( plugin.opts.controlVolumeBar);
			$controlVolumeIcon 	= $controls.find( plugin.opts.controlVolumeIcon);

			volume 				= plugin.opts.volume;
			_total 				= $tracks.length;


			if ( $controls.length ) {
				controlPlay 	= $controlPlay.get(0);

				if ( $controlRewind.length )
				controlRewind 	= $controlRewind.get(0);
			}

			if ( _total === 0 ) {
				return;
			}

			// Bind events
			plugin.setup();

			// Set audio to first trac
			plugin.setTrack( _current );

			// Bind events
			plugin.events();

			// Set audio to first trac
			plugin.loadTrack();

			// Setup progress interactions
			plugin.initProgressBar();

			// Setup volume interactions
			plugin.initVolumeBar();

			if ( plugin.opts.autoplay ) {
				plugin.playTrack(0);
			}
		};

		plugin.events = function() {

			if ( $controlPlay.length )
				$controlPlay.on( 'click', plugin.maybePlay );


			if ( _total > 1 ) {
				if ( $controlPrevious.length )
					$controlPrevious.on( 'click', function() {
						plugin.playTrack( _previous );
					});

				if ( $controlNext.length )
					$controlNext.on( 'click', function() {
						plugin.playTrack( _next );
					});
			}

			if ( $controlRewind.length )
				$controlRewind.on( 'click', plugin.restart );

			if ( $controlBrowse.length )
				$controlBrowse.on( 'click', function() {
					$playlist.toggle();
				});

			if ( $playlist.length ) {
				$tracks.on( 'click', function() {
					plugin.playTrack( $(this).index() );
				});
			}
		};

		plugin.setup = function() {
			$tracks.each( function() {
				var $track = $(this),
					$_audio = $track.find( 'audio' );

				// Loop through the audios
				$_audio.each( function() {
					var _audio = this;

					// Load each audio file
					_audio.load();

					// When meta data available
					$(this).on( "loadedmetadata", function( e ) {

						// Set duration for each audio
						var $_duration = $track.find( plugin.opts.trackDuration );
							$_duration.html( plugin.formatTime( _audio.duration, true ) );
					});
				});
			});
		};

		plugin.setTrack = function( index ) {

			_current = index;
			_next = ( index === _total - 1 ) ? 0 : index + 1;
			_previous = ( index === 0 ) ? _total : index - 1;

			$audio = $audios.eq( _current );
			audio = $audio.get(0);

			// Remove and add active classes
			$tracks
				.removeClass( 'ee--is-active' ).eq( index )
				.addClass( 'ee--is-active' );
		};

		plugin.loadTrack = function() {

			// Make sure audio is loaded on iOS
			audio.load();

			audio.addEventListener( "loadedmetadata", plugin.initAudio );

			// Update time controls
			audio.addEventListener( 'timeupdate', plugin.updateTime );

			// Check if audio completed laoded
			audio.addEventListener( 'canplaythrough', plugin.canPlayThrough );

			// Stop if audio ended
			audio.addEventListener( 'ended', plugin.ended );
		};

		plugin.ended = function( event ) {
			plugin.reset();

			$wrapper.trigger( 'ee:audio-player:ended', [ plugin ] );

			if ( plugin.opts.loopPlaylist ) {
				plugin.playTrack( _next );
			}
		};

		plugin.canPlayThrough = function() {
			_is_loaded = true;
		};

		plugin.playTrack = function( index ) {
			plugin.reset();
			plugin.setTrack( index );
			plugin.loadTrack();
			plugin.maybePlay();
		};

		plugin.initAudio = function() {

			var initialVolume = 'true' === $audio.attr( 'muted' ) ? 0 : volume;

			plugin.updateVolume( 0, initialVolume );
			plugin.updateDuration();
		};

		plugin.initProgressBar = function() {
			if ( $controlProgressBar.length ) {

				$controlProgressBar.on( 'mousedown', function(e) {
					_is_dragging_time = true;
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

					if ( audio.volume == 0 ) { // Un-mute

						// Don't keep 0 volume
						if ( volume == 0 ) volume = plugin.opts.volume;

						// Make sure we unmute the audio element
						audio.muted = false;

						// Update volume with the last known value
						plugin.updateVolume( 0, volume );

					} else {  // Mute

						var _volume = audio.volume;

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
					audio.muted = false;

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

		plugin.maybePlay = function() {

			if ( ! _is_playing ) {
				plugin.play();
			} else {
				plugin.pause();
			}

			return false;
		};

		plugin.play = function() {

			if ( _is_playing )
				return;

			// Adjust button classes
			$controlPlay.removeClass('nicon-play').addClass('nicon-pause');

			$wrapper.trigger( 'ee:audio-player:beforePlay', [ plugin ] );

			// Play it
			audio.play().catch(function() {});

			// Everything else
			plugin.afterPlay();

			$wrapper.trigger( 'ee:audio-player:afterPlay', [ plugin ] );
		};

		plugin.afterPlay = function() {

			// Adjust classes
			$wrapper.removeClass('paused').addClass('playing');

			// Stop all other audios conditionally
			if ( plugin.opts.stopOthersOnPlay ) {
				var $players = $('.ee-audio-player').not( element );

				$players.each( function() {
					var instance = $(this).data('audioPlayer');

					if ( instance ) {
						instance.pause();
					}
				});
			}

			// Not playing anymore
			_is_playing = true;

			// Make sure overlays are turned off
			// TweenMax.set( plugin.opts.overlays, { opacity: 0 });
		};

		plugin.pause = function() {
			if ( ! _is_playing )
				return;

			// Adjust classes
			$wrapper.removeClass('playing');

			$controlPlay.removeClass('nicon-pause').addClass('nicon-play');

			// Add paused classes
			$wrapper.addClass('paused');

			// Pause the audio
			audio.pause();

			_is_playing = false;

			$wrapper.trigger( 'ee:audio-player:pause', [ plugin ] );
		};

		plugin.restart = function() {
			plugin.reset();
			plugin.play();

			$wrapper.trigger( 'ee:audio-player:restart', [ plugin ] );
		};

		plugin.reset = function() {

			// Pause the audio
			audio.pause();

			// Adjust classes
			$wrapper.removeClass('playing');

			$controlPlay.removeClass('nicon-pause').addClass('nicon-play');

			// Reset time
			audio.currentTime = 0;

			// Reset the progress
			plugin.updateTime();

			_is_playing = false;
		};

		plugin.maybeRewind = function() {
			audio.currentTime = 0;
			plugin.play();
		};

		plugin.updateTime = function() {

			var position 	= audio.currentTime,
				duration 	= audio.duration,
				percentage 	= 100 * position / duration;

			if ( $controlProgress)
				$controlProgress.css( 'width', percentage + '%' );

			// Update time text
			if ( $controlTime.length )
				$controlTime.html( plugin.formatTime( position ) );
		};

		plugin.updateDuration = function() {

			var duration 	= audio.duration;

			// Update duration text
			if ( $controlDuration.length )
				$controlDuration.html( plugin.formatTime( duration ) );
		};

		plugin.updateProgress = function( amount ) {

			var duration 	= audio.duration,
				position 	= amount - $controlProgressBar.offset().left,
				percentage 	= 100 * position / $controlProgressBar.width();

			if ( percentage > 100 ) percentage = 100;
				else if ( percentage < 0 )
					percentage = 0;

			$controlProgress.css( 'width', percentage + '%');

			audio.currentTime = duration * percentage / 100;
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

			// Update audio volume
			audio.volume = percentage / 100;

			plugin.updateVolumeIcon( audio.volume );
			
			// Update volume control position
			$controlVolume.css( 'width',percentage + '%' );

			// Keep value
			volume = audio.volume;
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
		plugin.formatTime = function( seconds, _hours ) {

			if ( _hours ) {
				hours = Math.floor(seconds / 3600);
			    hours = (hours >= 10) ? hours : "0" + hours;
			    hours += ":";
			} else { hours = ''; }
			minutes = Math.floor(seconds / 60);
		    minutes = (minutes >= 10) ? minutes : "0" + minutes;
		    seconds = Math.floor(seconds % 60);
		    seconds = (seconds >= 10) ? seconds : "0" + seconds;
		    return hours + minutes + ":" + seconds;
		};

		plugin.init();

	};

	$.fn.audioPlayer = function(options) {

		return this.each(function() {
			if (undefined == $(this).data('audioPlayer')) {
				var plugin = new $.audioPlayer(this, options);
				$(this).data('audioPlayer', plugin);
			}
		});

	};

})(jQuery);
