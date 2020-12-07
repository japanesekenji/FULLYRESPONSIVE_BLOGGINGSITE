;( function( elementor, $, window ) {
	////////////////////////////////////////////
	// Hooks.    							////
	////////////////////////////////////////////

	elementor.hooks.addAction( 'panel/open_editor/widget/ee-offcanvas', function( panel, model, view ) {

		if ( 'ee-offcanvas.classic' !== view.$el.data('widget_type') )
			return;

		elementor.channels.editor.off('ee:editor:offcanvas:open').on('ee:editor:offcanvas:open', function( event ) {

			var $element 		= view.$el,
				$element_id 	= $element.data('id'),
				$slidebar 		= window.elementor.$previewContents.find( '#ee-offcanvas-' + $element_id ),
				ocController 	= window.elementorFrontend.eeOffcanvas.controller;

			ocController.toggle( 'oc' + $element_id );
		});
	} );

	////////////////////////////////////////////
	// Controls Handlers.    				////
	////////////////////////////////////////////

	// Snazzy Control

	var ControlSnazzy = elementor.modules.controls.BaseData.extend( {

		getSnazzyDefaultOptions: function getSnazzyDefaultOptions() {
			var options = this.model.get( 'snazzy_options' );

			return {
				containerCssClass 	: 'ee-select2-container',
				dropdownCssClass 	: 'ee-select2-dropdown',
				allowClear			: true,
				placeholder 		: this.model.get( 'placeholder' ),
				dir 				: elementor.config.is_rtl ? 'rtl' : 'ltr',
				ajax 				: {
					url 			: 'https://snazzymaps.com/' + options.endpoint + '.json',
					dataType		: 'json',
					cache			: true,
					data 			: function ( params ) {
						return {
							key 			: options.key,
							page 			: params.page || 1,
						};
					},
					processResults: function ( data, params ) {

						var styles = $.map( data.styles, function ( obj ) {

								obj.id 		= JSON.stringify( obj );
								obj.text 	= obj.name;

								return obj;
							});
						
						params.page = params.page || 1;

						var response = {
							results 	: styles,
							pagination 	: {
								more: ( params.page * 12 ) < data.pagination.totalItems,
							},
						};

						return response;
					},
				},
				minimumInputLength 		: 0,
				minimumResultsForSearch : -1,
				escapeMarkup 			: function ( markup ) { return markup; },
				templateResult 			: this.formatResults,
  				templateSelection 		: this.formatResultsSelection,
			};
		},

		formatResults: function formatRepo( repo ) {
			if ( repo.loading ) {
				return repo.text;
			}

			var markup =
					"<div class='ee-select2-dropdown__option clearfix'>" +
					"<div class='ee-select2-dropdown__option__image'><img src='" + repo.imageUrl + "'/></div>" +
					"</div>";

			return markup;
		},

		formatResultsSelection: function formatRepoSelection( repo ) {
			return repo.text;
		},

		applySavedValue: function applySavedValue() {
			var controlValue = this.getControlValue();

			if ( controlValue && '' !== controlValue ) {
				controlValue = JSON.parse( this.getControlValue() );

				this.setInputValue('[data-setting="' + this.model.get('name') + '"]', controlValue.id );
			}
		},

		getSnazzyOptions: function getSnazzyOptions() {
			return jQuery.extend(this.getSnazzyDefaultOptions(), this.model.get('select2options'));
		},

		onReady: function onReady() {
			this.ui.select.select2(this.getSnazzyOptions());
		},

		onBeforeDestroy: function onBeforeDestroy() {
			if (this.ui.select.data('select2')) {
				this.ui.select.select2('destroy');
			}

			this.$el.remove();
		}
	} );

	// Query Control

	var ControlQuery = elementor.modules.controls.Select2.extend( {

		cache: null,
		isTitlesReceived: false,

		getSelect2Placeholder: function getSelect2Placeholder() {
			var self = this;
			
			return {
				id: '',
				text: self.model.get('placeholder') || 'All',
			};
		},

		getSelect2DefaultOptions: function getSelect2DefaultOptions() {
			var self = this;

			return jQuery.extend( elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply( this, arguments ), {
				ajax: {
					transport: function transport( params, success, failure ) {
						var data = {
							q 			: params.data.q,
							query_type 		: self.model.get('query_type'),
							query_options 	: self.model.get('query_options'),
							object_type 	: self.model.get('object_type'),
						};

						return elementorCommon.ajax.addRequest('ee_query_control_filter_autocomplete', {
							data 	: data,
							success : success,
							error 	: failure,
						});
					},
					data: function data( params ) {
						return {
							q 	 : params.term,
							page : params.page,
						};
					},
					cache: true
				},
				escapeMarkup: function escapeMarkup(markup) {
					return markup;
				},
				minimumInputLength: 1
			});
		},

		getValueTitles: function getValueTitles() {
			var self 			= this,
			    ids 			= this.getControlValue(),
			    queryType 		= this.model.get('query_type'),
			    queryOptions 	= this.model.get('query_options'),
			    objectType 		= this.model.get('object_type');

			if ( ! ids || ! queryType ) return;

			if ( ! _.isArray( ids ) ) {
				ids = [ ids ];
			}

			elementorCommon.ajax.loadObjects({
				action 	: 'ee_query_control_value_titles',
				ids 	: ids,
				data 	: {
					query_type 		: queryType,
					query_options 	: queryOptions,
					object_type 	: objectType,
					unique_id 		: '' + self.cid + queryType,
				},
				success: function success(data) {
					self.isTitlesReceived = true;
					self.model.set('options', data);
					self.render();
				},
				before: function before() {
					self.addSpinner();
				},
			});
		},

		addSpinner: function addSpinner() {
			this.ui.select.prop('disabled', true);
			this.$el.find('.elementor-control-title').after('<span class="elementor-control-spinner ee-control-spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
		},

		onReady: function onReady() {
			setTimeout( elementor.modules.controls.Select2.prototype.onReady.bind(this) );

			if ( ! this.isTitlesReceived ) {
				this.getValueTitles();
			}
		},

		onBeforeDestroy: function onBeforeDestroy() {
			if (this.ui.select.data('select2')) {
				this.ui.select.select2('destroy');
			}

			this.$el.remove();
		},

	} );

	// Add Control Handlers
	elementor.addControlView( 'ee-query', ControlQuery );
	elementor.addControlView( 'ee-snazzy', ControlSnazzy );
} )( elementor, jQuery, window );