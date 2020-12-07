/*!
 * HC-Sticky
 * =========
 * Version: 2.2.3
 * Author: Some Web Media
 * Author URL: http://somewebmedia.com
 * Plugin URL: https://github.com/somewebmedia/hc-sticky
 * Description: Cross-browser plugin that makes any element on your page visible while you scroll
 * License: MIT
 */

'use strict';

(function (global, factory) {
	'use strict';

	if (typeof module === 'object' && typeof module.exports === 'object') {
		if (global.document) {
			module.exports = factory(global);
		} else {
			throw new Error('HC-Sticky requires a browser to run.');
		}
	} else if (typeof define === 'function' && define.amd) {
		define('hcSticky', [], factory(global));
	} else {
		factory(global);
	}
})(typeof window !== 'undefined' ? window : undefined, function (window) {
	'use strict';

	var DEFAULT_OPTIONS = {
		top: 0,
		bottom: 0,
		bottomEnd: 0,
		innerTop: 0,
		innerSticker: null,
		stickyClass: 'sticky',
		stickTo: null,
		followScroll: true,
		responsive: null,
		mobileFirst: false,
		onStart: null,
		onStop: null,
		onBeforeResize: null,
		onResize: null,
		resizeDebounce: 100,
		disable: false,

		// deprecated
		queries: null,
		queryFlow: 'down'
	};

	var deprecated = (function () {
		var pluginName = 'HC Sticky';

		return function (what, instead, type) {
			console.warn('%c' + pluginName + ':' + '%c ' + type + "%c '" + what + "'" + '%c is now deprecated and will be removed. Use' + "%c '" + instead + "'" + '%c instead.', 'color: #fa253b', 'color: default', 'color: #5595c6', 'color: default', 'color: #5595c6', 'color: default');
		};
	})();

	var document = window.document;

	var _hcSticky = function _hcSticky(elem) {
		var _this = this;

		var userSettings = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];

		// use querySeletor if string is passed
		if (typeof elem === 'string') {
			elem = document.querySelector(elem);
		}

		// check if element exist
		if (!elem) {
			return false;
		}

		if (userSettings.queries) {
			deprecated('queries', 'responsive', 'option');
		}

		if (userSettings.queryFlow) {
			deprecated('queryFlow', 'mobileFirst', 'option');
		}

		var STICKY_OPTIONS = {};
		var Helpers = _hcSticky.Helpers;
		var elemParent = elem.parentNode;

		// parent can't be static
		if (Helpers.getStyle(elemParent, 'position') === 'static') {
			elemParent.style.position = 'relative';
		}

		var setOptions = function setOptions() {
			var options = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];

			if (Helpers.isEmptyObject(options) && !Helpers.isEmptyObject(STICKY_OPTIONS)) {
				// nothing to set
				return;
			}

			// extend options
			STICKY_OPTIONS = Object.assign({}, DEFAULT_OPTIONS, STICKY_OPTIONS, options);
		};

		var resetOptions = function resetOptions(options) {
			STICKY_OPTIONS = Object.assign({}, DEFAULT_OPTIONS, options || {});
		};

		var getOptions = function getOptions(option) {
			return option ? STICKY_OPTIONS[option] : Object.assign({}, STICKY_OPTIONS);
		};

		var isDisabled = function isDisabled() {
			return STICKY_OPTIONS.disable;
		};

		var applyQueries = function applyQueries() {
			var mediaQueries = STICKY_OPTIONS.responsive || STICKY_OPTIONS.queries;

			if (mediaQueries) {
				var window_width = window.innerWidth;

				// reset settings
				resetOptions(userSettings);

				if (STICKY_OPTIONS.mobileFirst) {
					for (var width in mediaQueries) {
						if (window_width >= width && !Helpers.isEmptyObject(mediaQueries[width])) {
							setOptions(mediaQueries[width]);
						}
					}
				} else {
					var queriesArr = [];

					// convert to array so we can reverse loop it
					for (var b in mediaQueries) {
						var q = {};

						q[b] = mediaQueries[b];
						queriesArr.push(q);
					}

					for (var i = queriesArr.length - 1; i >= 0; i--) {
						var query = queriesArr[i];
						var breakpoint = Object.keys(query)[0];

						if (window_width <= breakpoint && !Helpers.isEmptyObject(query[breakpoint])) {
							setOptions(query[breakpoint]);
						}
					}
				}
			}
		};

		// our helper function for getting necessary styles
		var getStickyCss = function getStickyCss(el) {
			var cascadedStyle = Helpers.getCascadedStyle(el);
			var computedStyle = Helpers.getStyle(el);

			var css = {
				height: el.offsetHeight + 'px',
				left: cascadedStyle.left,
				right: cascadedStyle.right,
				top: cascadedStyle.top,
				bottom: cascadedStyle.bottom,
				position: computedStyle.position,
				display: computedStyle.display,
				verticalAlign: computedStyle.verticalAlign,
				boxSizing: computedStyle.boxSizing,
				marginLeft: cascadedStyle.marginLeft,
				marginRight: cascadedStyle.marginRight,
				marginTop: cascadedStyle.marginTop,
				marginBottom: cascadedStyle.marginBottom,
				paddingLeft: cascadedStyle.paddingLeft,
				paddingRight: cascadedStyle.paddingRight
			};

			if (cascadedStyle['float']) {
				css['float'] = cascadedStyle['float'] || 'none';
			}

			if (cascadedStyle.cssFloat) {
				css['cssFloat'] = cascadedStyle.cssFloat || 'none';
			}

			// old firefox box-sizing
			if (computedStyle.MozBoxSizing) {
				css['MozBoxSizing'] = computedStyle.MozBoxSizing;
			}

			css['width'] = cascadedStyle.width !== 'auto' ? cascadedStyle.width : css.boxSizing === 'border-box' || css.MozBoxSizing === 'border-box' ? el.offsetWidth + 'px' : computedStyle.width;

			return css;
		};

		var Sticky = {
			css: {},
			position: null, // so we don't need to check css all the time
			stick: function stick() {
				var args = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];

				// check if element is already sticky
				if (Helpers.hasClass(elem, STICKY_OPTIONS.stickyClass)) {
					return;
				}

				if (Spacer.isAttached === false) {
					Spacer.attach();
				}

				Sticky.position = 'fixed';

				// apply styles
				elem.style.position = 'fixed';
				elem.style.left = Spacer.offsetLeft + 'px';
				elem.style.width = Spacer.width;

				if (typeof args.bottom === 'undefined') {
					elem.style.bottom = 'auto';
				} else {
					elem.style.bottom = args.bottom + 'px';
				}

				if (typeof args.top === 'undefined') {
					elem.style.top = 'auto';
				} else {
					elem.style.top = args.top + 'px';
				}

				// add sticky class
				if (elem.classList) {
					elem.classList.add(STICKY_OPTIONS.stickyClass);
				} else {
					elem.className += ' ' + STICKY_OPTIONS.stickyClass;
				}

				// fire 'start' event
				if (STICKY_OPTIONS.onStart) {
					STICKY_OPTIONS.onStart.call(elem, Object.assign({}, STICKY_OPTIONS));
				}
			},
			release: function release() {
				var args = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];

				args.stop = args.stop || false;

				// check if we've already done this
				if (args.stop !== true && Sticky.position !== 'fixed' && Sticky.position !== null && (typeof args.top === 'undefined' && typeof args.bottom === 'undefined' || typeof args.top !== 'undefined' && (parseInt(Helpers.getStyle(elem, 'top')) || 0) === args.top || typeof args.bottom !== 'undefined' && (parseInt(Helpers.getStyle(elem, 'bottom')) || 0) === args.bottom)) {
					return;
				}

				if (args.stop === true) {
					// remove spacer
					if (Spacer.isAttached === true) {
						Spacer.detach();
					}
				} else {
					// check spacer
					if (Spacer.isAttached === false) {
						Spacer.attach();
					}
				}

				var position = args.position || Sticky.css.position;

				// remember position
				Sticky.position = position;

				// apply styles
				elem.style.position = position;
				elem.style.left = args.stop === true ? Sticky.css.left : Spacer.positionLeft + 'px';
				elem.style.width = position !== 'absolute' ? Sticky.css.width : Spacer.width;

				if (typeof args.bottom === 'undefined') {
					elem.style.bottom = args.stop === true ? '' : 'auto';
				} else {
					elem.style.bottom = args.bottom + 'px';
				}

				if (typeof args.top === 'undefined') {
					elem.style.top = args.stop === true ? '' : 'auto';
				} else {
					elem.style.top = args.top + 'px';
				}

				// remove sticky class
				if (elem.classList) {
					elem.classList.remove(STICKY_OPTIONS.stickyClass);
				} else {
					elem.className = elem.className.replace(new RegExp('(^|\\b)' + STICKY_OPTIONS.stickyClass.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
				}

				// fire 'stop' event
				if (STICKY_OPTIONS.onStop) {
					STICKY_OPTIONS.onStop.call(elem, Object.assign({}, STICKY_OPTIONS));
				}
			}
		};

		var Spacer = {
			el: document.createElement('div'),
			offsetLeft: null,
			positionLeft: null,
			width: null,
			isAttached: false,
			init: function init() {
				Spacer.el.className = 'sticky-spacer';

				// copy styles from sticky element
				for (var prop in Sticky.css) {
					Spacer.el.style[prop] = Sticky.css[prop];
				}

				// just to be sure the spacer is behind everything
				Spacer.el.style['z-index'] = '-1';

				var elemStyle = Helpers.getStyle(elem);

				// get spacer offset and position
				Spacer.offsetLeft = Helpers.offset(elem).left - (parseInt(elemStyle.marginLeft) || 0);
				Spacer.positionLeft = Helpers.position(elem).left;

				// get spacer width
				Spacer.width = Helpers.getStyle(elem, 'width');
			},
			attach: function attach() {
				// insert spacer to DOM
				elemParent.insertBefore(Spacer.el, elem);
				Spacer.isAttached = true;
			},
			detach: function detach() {
				// remove spacer from DOM
				Spacer.el = elemParent.removeChild(Spacer.el);
				Spacer.isAttached = false;
			}
		};

		// define our private variables
		var stickTo_document = undefined;
		var container = undefined;
		var inner_sticker = undefined;

		var container_height = undefined;
		var container_offsetTop = undefined;

		var elemParent_offsetTop = undefined;

		var window_height = undefined;

		var options_top = undefined;
		var options_bottom = undefined;

		var stick_top = undefined;
		var stick_bottom = undefined;

		var top_limit = undefined;
		var bottom_limit = undefined;

		var largerSticky = undefined;
		var sticky_height = undefined;
		var sticky_offsetTop = undefined;

		var calcContainerHeight = undefined;
		var calcStickyHeight = undefined;

		var calcSticky = function calcSticky() {
			// get/set element styles
			Sticky.css = getStickyCss(elem);

			// init or reinit spacer
			Spacer.init();

			// check if referring element is document
			stickTo_document = STICKY_OPTIONS.stickTo && (STICKY_OPTIONS.stickTo === 'document' || STICKY_OPTIONS.stickTo.nodeType && STICKY_OPTIONS.stickTo.nodeType === 9 || typeof STICKY_OPTIONS.stickTo === 'object' && STICKY_OPTIONS.stickTo instanceof (typeof HTMLDocument !== 'undefined' ? HTMLDocument : Document)) ? true : false;

			// select referred container
			container = STICKY_OPTIONS.stickTo ? stickTo_document ? document : typeof STICKY_OPTIONS.stickTo === 'string' ? document.querySelector(STICKY_OPTIONS.stickTo) : STICKY_OPTIONS.stickTo : elemParent;

			// get sticky height
			calcStickyHeight = function () {
				var height = elem.offsetHeight + (parseInt(Sticky.css.marginTop) || 0) + (parseInt(Sticky.css.marginBottom) || 0);
				var h_diff = (sticky_height || 0) - height;

				if (h_diff >= -1 && h_diff <= 1) {
					// sometimes element height changes by 1px when it get fixed position, so don't return new value
					return sticky_height;
				} else {
					return height;
				}
			};

			sticky_height = calcStickyHeight();

			// get container height
			calcContainerHeight = function () {
				return !stickTo_document ? container.offsetHeight : Math.max(document.documentElement.clientHeight, document.body.scrollHeight, document.documentElement.scrollHeight, document.body.offsetHeight, document.documentElement.offsetHeight);
			};

			container_height = calcContainerHeight();

			container_offsetTop = !stickTo_document ? Helpers.offset(container).top : 0;
			elemParent_offsetTop = !STICKY_OPTIONS.stickTo ? container_offsetTop // parent is container
			: !stickTo_document ? Helpers.offset(elemParent).top : 0;
			window_height = window.innerHeight;
			sticky_offsetTop = elem.offsetTop - (parseInt(Sticky.css.marginTop) || 0);

			// get inner sticker element
			inner_sticker = STICKY_OPTIONS.innerSticker ? typeof STICKY_OPTIONS.innerSticker === 'string' ? document.querySelector(STICKY_OPTIONS.innerSticker) : STICKY_OPTIONS.innerSticker : null;

			// top
			options_top = isNaN(STICKY_OPTIONS.top) && STICKY_OPTIONS.top.indexOf('%') > -1 ? parseFloat(STICKY_OPTIONS.top) / 100 * window_height : STICKY_OPTIONS.top;

			// bottom
			options_bottom = isNaN(STICKY_OPTIONS.bottom) && STICKY_OPTIONS.bottom.indexOf('%') > -1 ? parseFloat(STICKY_OPTIONS.bottom) / 100 * window_height : STICKY_OPTIONS.bottom;

			// calculate sticky breakpoints
			stick_top = inner_sticker ? inner_sticker.offsetTop : STICKY_OPTIONS.innerTop ? STICKY_OPTIONS.innerTop : 0;

			stick_bottom = isNaN(STICKY_OPTIONS.bottomEnd) && STICKY_OPTIONS.bottomEnd.indexOf('%') > -1 ? parseFloat(STICKY_OPTIONS.bottomEnd) / 100 * window_height : STICKY_OPTIONS.bottomEnd;

			top_limit = container_offsetTop - options_top + stick_top + sticky_offsetTop;
		};

		// store scroll position so we can determine scroll direction
		var last_pos = window.pageYOffset || document.documentElement.scrollTop;
		var diff_y = 0;
		var scroll_dir = undefined;

		var runSticky = function runSticky() {
			// always calculate sticky and container height in case of DOM change
			sticky_height = calcStickyHeight();
			container_height = calcContainerHeight();

			bottom_limit = container_offsetTop + container_height - options_top - stick_bottom;

			// check if sticky is bigger than container
			largerSticky = sticky_height > window_height;

			var offset_top = window.pageYOffset || document.documentElement.scrollTop;
			var sticky_top = Helpers.offset(elem).top;
			var sticky_window_top = sticky_top - offset_top;
			var bottom_distance = undefined;

			// get scroll direction
			scroll_dir = offset_top < last_pos ? 'up' : 'down';
			diff_y = offset_top - last_pos;
			last_pos = offset_top;

			if (offset_top > top_limit) {
				// http://geek-and-poke.com/geekandpoke/2012/7/27/simply-explained.html
				if (bottom_limit + options_top + (largerSticky ? options_bottom : 0) - (STICKY_OPTIONS.followScroll && largerSticky ? 0 : options_top) <= offset_top + sticky_height - stick_top - (sticky_height - stick_top > window_height - (top_limit - stick_top) && STICKY_OPTIONS.followScroll ? (bottom_distance = sticky_height - window_height - stick_top) > 0 ? bottom_distance : 0 : 0)) {
					// bottom reached end
					Sticky.release({
						position: 'absolute',
						//top: bottom_limit - sticky_height - top_limit + stick_top + sticky_offsetTop
						bottom: elemParent_offsetTop + elemParent.offsetHeight - bottom_limit - options_top
					});
				} else if (largerSticky && STICKY_OPTIONS.followScroll) {
					// sticky is bigger than container and follows scroll
					if (scroll_dir === 'down') {
						// scroll down
						if (sticky_window_top + sticky_height + options_bottom <= window_height + .9) {
							// stick on bottom
							// fix subpixel precision with adding .9 pixels
							Sticky.stick({
								//top: window_height - sticky_height - options_bottom
								bottom: options_bottom
							});
						} else if (Sticky.position === 'fixed') {
							// bottom reached window bottom
							Sticky.release({
								position: 'absolute',
								top: sticky_top - options_top - top_limit - diff_y + stick_top
							});
						}
					} else {
						// scroll up
						if (Math.ceil(sticky_window_top + stick_top) < 0 && Sticky.position === 'fixed') {
							// top reached window top
							Sticky.release({
								position: 'absolute',
								top: sticky_top - options_top - top_limit + stick_top - diff_y
							});
						} else if (sticky_top >= offset_top + options_top - stick_top) {
							// stick on top
							Sticky.stick({
								top: options_top - stick_top
							});
						}
					}
				} else {
					// stick on top
					Sticky.stick({
						top: options_top - stick_top
					});
				}
			} else {
				// starting point
				Sticky.release({
					stop: true
				});
			}
		};

		var scrollAttached = false;
		var resizeAttached = false;

		var disableSticky = function disableSticky() {
			if (scrollAttached) {
				// detach sticky from scroll
				Helpers.event.unbind(window, 'scroll', runSticky);

				// sticky is not attached to scroll anymore
				scrollAttached = false;
			}
		};

		var initSticky = function initSticky() {
			// check if element or it's parents are visible
			if (elem.offsetParent === null || Helpers.getStyle(elem, 'display') === 'none') {
				disableSticky();
				return;
			}

			// calculate stuff
			calcSticky();

			// check if sticky is bigger than reffering container
			if (sticky_height > container_height) {
				disableSticky();
				return;
			}

			// run
			runSticky();

			if (!scrollAttached) {
				// attach sticky to scroll
				Helpers.event.bind(window, 'scroll', runSticky);

				// sticky is attached to scroll
				scrollAttached = true;
			}
		};

		var resetSticky = function resetSticky() {
			// remove inline styles
			elem.style.position = '';
			elem.style.left = '';
			elem.style.top = '';
			elem.style.bottom = '';
			elem.style.width = '';

			// remove sticky class
			if (elem.classList) {
				elem.classList.remove(STICKY_OPTIONS.stickyClass);
			} else {
				elem.className = elem.className.replace(new RegExp('(^|\\b)' + STICKY_OPTIONS.stickyClass.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
			}

			// reset sticky object data
			Sticky.css = {};
			Sticky.position = null;

			// remove spacer
			if (Spacer.isAttached === true) {
				Spacer.detach();
			}
		};

		var reinitSticky = function reinitSticky() {
			resetSticky();
			applyQueries();

			if (isDisabled()) {
				disableSticky();
				return;
			}

			// restart sticky
			initSticky();
		};

		var resizeSticky = function resizeSticky() {
			// fire 'beforeResize' event
			if (STICKY_OPTIONS.onBeforeResize) {
				STICKY_OPTIONS.onBeforeResize.call(elem, Object.assign({}, STICKY_OPTIONS));
			}

			// reinit sticky
			reinitSticky();

			// fire 'resize' event
			if (STICKY_OPTIONS.onResize) {
				STICKY_OPTIONS.onResize.call(elem, Object.assign({}, STICKY_OPTIONS));
			}
		};

		var resize_cb = !STICKY_OPTIONS.resizeDebounce ? resizeSticky : Helpers.debounce(resizeSticky, STICKY_OPTIONS.resizeDebounce);

		// Method for updating options
		var Update = function Update(options) {
			setOptions(options);

			// also update user settings
			userSettings = Object.assign({}, userSettings, options || {});

			reinitSticky();
		};

		var Detach = function Detach() {
			// detach resize reinit
			if (resizeAttached) {
				Helpers.event.unbind(window, 'resize', resize_cb);
				resizeAttached = false;
			}

			disableSticky();
		};

		var Destroy = function Destroy() {
			Detach();
			resetSticky();
		};

		var Attach = function Attach() {
			// attach resize reinit
			if (!resizeAttached) {
				Helpers.event.bind(window, 'resize', resize_cb);
				resizeAttached = true;
			}

			applyQueries();

			if (isDisabled()) {
				disableSticky();
				return;
			}

			initSticky();
		};

		this.options = getOptions;
		this.refresh = reinitSticky;
		this.update = Update;
		this.attach = Attach;
		this.detach = Detach;
		this.destroy = Destroy;

		// jQuery methods
		this.triggerMethod = function (method, options) {
			if (typeof _this[method] === 'function') {
				_this[method](options);
			}
		};

		this.reinit = function () {
			deprecated('reinit', 'refresh', 'method');
			reinitSticky();
		};

		// init settings
		setOptions(userSettings);

		// start sticky
		Attach();

		// reinit on complete page load
		Helpers.event.bind(window, 'load', reinitSticky);
	};

	// jQuery Plugin
	if (typeof window.jQuery !== 'undefined') {
		(function () {
			var $ = window.jQuery;
			var namespace = 'hcSticky';

			$.fn.extend({
				hcSticky: function hcSticky(args, update) {
					// check if selected element exist
					if (!this.length) return this;

					// we need to return options
					if (args === 'options') {
						return $.data(this.get(0), namespace).options();
					}

					return this.each(function () {
						var instance = $.data(this, namespace);

						if (instance) {
							// already created, trigger method
							instance.triggerMethod(args, update);
						} else {
							// create new instance
							instance = new _hcSticky(this, args);
							$.data(this, namespace, instance);
						}
					});
				}
			});
		})();
	}

	// browser global
	window.hcSticky = window.hcSticky || _hcSticky;

	return _hcSticky;
});

'use strict';

(function (window) {
	'use strict';

	var hcSticky = window.hcSticky;
	var document = window.document;

	/*
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign
	 */
	if (typeof Object.assign !== 'function') {
		Object.defineProperty(Object, 'assign', {
			value: function assign(target, varArgs) {
				'use strict';
				if (target == null) {
					throw new TypeError('Cannot convert undefined or null to object');
				}

				var to = Object(target);

				for (var index = 1; index < arguments.length; index++) {
					var nextSource = arguments[index];

					if (nextSource != null) {
						for (var nextKey in nextSource) {
							if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
								to[nextKey] = nextSource[nextKey];
							}
						}
					}
				}
				return to;
			},
			writable: true,
			configurable: true
		});
	}

	/*
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/forEach
	 */
	if (!Array.prototype.forEach) {
		Array.prototype.forEach = function (callback) {
			var T, k;

			if (this == null) {
				throw new TypeError('this is null or not defined');
			}

			var O = Object(this);
			var len = O.length >>> 0;

			if (typeof callback !== 'function') {
				throw new TypeError(callback + ' is not a function');
			}

			if (arguments.length > 1) {
				T = arguments[1];
			}

			k = 0;

			while (k < len) {
				var kValue;

				if (k in O) {
					kValue = O[k];
					callback.call(T, kValue, k, O);
				}

				k++;
			}
		};
	}

	/*
	 * https://github.com/desandro/eventie
	 */
	var event = (function () {
		var docElem = document.documentElement;

		var bind = function bind() {};

		function getIEEvent(obj) {
			var event = window.event;
			// add event.target
			event.target = event.target || event.srcElement || obj;
			return event;
		}

		if (docElem.addEventListener) {
			bind = function (obj, type, fn) {
				obj.addEventListener(type, fn, false);
			};
		} else if (docElem.attachEvent) {
			bind = function (obj, type, fn) {
				obj[type + fn] = fn.handleEvent ? function () {
					var event = getIEEvent(obj);
					fn.handleEvent.call(fn, event);
				} : function () {
					var event = getIEEvent(obj);
					fn.call(obj, event);
				};
				obj.attachEvent("on" + type, obj[type + fn]);
			};
		}

		var unbind = function unbind() {};

		if (docElem.removeEventListener) {
			unbind = function (obj, type, fn) {
				obj.removeEventListener(type, fn, false);
			};
		} else if (docElem.detachEvent) {
			unbind = function (obj, type, fn) {
				obj.detachEvent("on" + type, obj[type + fn]);
				try {
					delete obj[type + fn];
				} catch (err) {
					// can't delete window object properties
					obj[type + fn] = undefined;
				}
			};
		}

		return {
			bind: bind,
			unbind: unbind
		};
	})();

	// debounce taken from underscore
	var debounce = function debounce(func, wait, immediate) {
		var timeout = undefined;

		return function () {
			var context = this;
			var args = arguments;
			var later = function later() {
				timeout = null;
				if (!immediate) {
					func.apply(context, args);
				}
			};
			var callNow = immediate && !timeout;

			clearTimeout(timeout);
			timeout = setTimeout(later, wait);

			if (callNow) {
				func.apply(context, args);
			}
		};
	};

	// cross-browser get style
	var getStyle = function getStyle(el, style) {
		if (window.getComputedStyle) {
			return style ? document.defaultView.getComputedStyle(el, null).getPropertyValue(style) : document.defaultView.getComputedStyle(el, null);
		} else if (el.currentStyle) {
			return style ? el.currentStyle[style.replace(/-\w/g, function (s) {
				return s.toUpperCase().replace('-', '');
			})] : el.currentStyle;
		}
	};

	// check if object is empty
	var isEmptyObject = function isEmptyObject(obj) {
		for (var _name in obj) {
			return false;
		}

		return true;
	};

	// check if element has class
	var hasClass = function hasClass(el, className) {
		if (el.classList) {
			return el.classList.contains(className);
		} else {
			return new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
		}
	};

	// like jQuery .offset()
	var offset = function offset(el) {
		var rect = el.getBoundingClientRect();
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

		return {
			top: rect.top + scrollTop,
			left: rect.left + scrollLeft
		};
	};

	// like jQuery .position()
	var position = function position(el) {
		var offsetParent = el.offsetParent;
		var parentOffset = offset(offsetParent);
		var elemOffset = offset(el);
		var prentStyle = getStyle(offsetParent);
		var elemStyle = getStyle(el);

		parentOffset.top += parseInt(prentStyle.borderTopWidth) || 0;
		parentOffset.left += parseInt(prentStyle.borderLeftWidth) || 0;

		return {
			top: elemOffset.top - parentOffset.top - (parseInt(elemStyle.marginTop) || 0),
			left: elemOffset.left - parentOffset.left - (parseInt(elemStyle.marginLeft) || 0)
		};
	};

	// get cascaded instead of computed styles
	var getCascadedStyle = function getCascadedStyle(el) {
		// clone element
		var clone = el.cloneNode(true);

		clone.style.display = 'none';

		// remove name attr from cloned radio buttons to prevent their clearing
		Array.prototype.slice.call(clone.querySelectorAll('input[type="radio"]')).forEach(function (el) {
			el.removeAttribute('name');
		});

		// insert clone to DOM
		el.parentNode.insertBefore(clone, el.nextSibling);

		// get styles
		var currentStyle = undefined;

		if (clone.currentStyle) {
			currentStyle = clone.currentStyle;
		} else if (window.getComputedStyle) {
			currentStyle = document.defaultView.getComputedStyle(clone, null);
		}

		// new style oject
		var style = {};

		for (var prop in currentStyle) {
			if (isNaN(prop) && (typeof currentStyle[prop] === 'string' || typeof currentStyle[prop] === 'number')) {
				style[prop] = currentStyle[prop];
			}
		}

		// safari copy
		if (Object.keys(style).length < 3) {
			style = {}; // clear
			for (var prop in currentStyle) {
				if (!isNaN(prop)) {
					style[currentStyle[prop].replace(/-\w/g, function (s) {
						return s.toUpperCase().replace('-', '');
					})] = currentStyle.getPropertyValue(currentStyle[prop]);
				}
			}
		}

		// check for margin:auto
		if (!style.margin && style.marginLeft === 'auto') {
			style.margin = 'auto';
		} else if (!style.margin && style.marginLeft === style.marginRight && style.marginLeft === style.marginTop && style.marginLeft === style.marginBottom) {
			style.margin = style.marginLeft;
		}

		// safari margin:auto hack
		if (!style.margin && style.marginLeft === '0px' && style.marginRight === '0px') {
			var posLeft = el.offsetLeft - el.parentNode.offsetLeft;
			var marginLeft = posLeft - (parseInt(style.left) || 0) - (parseInt(style.right) || 0);
			var marginRight = el.parentNode.offsetWidth - el.offsetWidth - posLeft - (parseInt(style.right) || 0) + (parseInt(style.left) || 0);
			var diff = marginRight - marginLeft;

			if (diff === 0 || diff === 1) {
				style.margin = 'auto';
			}
		}

		// destroy clone
		clone.parentNode.removeChild(clone);
		clone = null;

		return style;
	};

	hcSticky.Helpers = {
		isEmptyObject: isEmptyObject,
		debounce: debounce,
		hasClass: hasClass,
		offset: offset,
		position: position,
		getStyle: getStyle,
		getCascadedStyle: getCascadedStyle,
		event: event
	};
})(window);