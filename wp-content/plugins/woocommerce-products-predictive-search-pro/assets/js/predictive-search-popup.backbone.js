jQuery( document ).ready( function( $ ) {
	var wc_ps_legacy_api_url = wc_ps_vars.legacy_api_url;
	var wc_ps_permalink_structure = wc_ps_vars.permalink_structure;
	var wc_ps_search_page_url = wc_ps_vars.search_page_url;
	var wc_ps_minChars = wc_ps_vars.minChars;
	var wc_ps_delay = wc_ps_vars.delay;
	var wc_ps_cache_timeout = 24;
	var wc_ps_is_debug = true;
	var wc_ps_allow_result_effect = true;
	var wc_ps_show_effect = 'fadeInUpBig';

	if ( typeof wc_ps_vars.cache_timeout !== 'undefined' ) {
		wc_ps_cache_timeout = wc_ps_vars.cache_timeout;
	}
	if ( typeof wc_ps_vars.is_debug !== 'undefined' && wc_ps_vars.is_debug != 'yes' ) {
		wc_ps_is_debug = false;
	}
	if ( typeof wc_ps_vars.allow_result_effect !== 'undefined' && wc_ps_vars.allow_result_effect != 'yes' ) {
		wc_ps_allow_result_effect = false;
	}
	if ( typeof wc_ps_vars.show_effect !== 'undefined' ) {
		wc_ps_show_effect = wc_ps_vars.show_effect;
	}

	if ( wc_ps_is_debug ) {
		console.log( 'Predictive Search -- DEBUG' );
	}
	
	var wc_psearch_popup = { apps:{}, models:{}, collections:{}, views:{} };
	
	_.templateSettings = {
  		evaluate: /[<{][%{](.+?)[%}][}>]/g,
    	interpolate: /[<{][%{]=(.+?)[%}][}>]/g,
    	escape: /[<{][%{]-(.+?)[%}][}>]/g
	}
	
	wc_psearch_popup.models.Item = Backbone.Model.extend({
		defaults: {
			title: 'Empty Product',
			keyword: '',
			url: null,
			image_url: null,
			sku: null,
			price: null,
			description: null,
			stock: null,
			categories: [],
			type: 'product',
			status: true
		}
	});
	
	wc_psearch_popup.collections.Items = Backbone.Collection.extend({
		model: 	wc_psearch_popup.models.Item,
		
		totalItems: function() {
			return this.where({ status: true }).length;
		},
		
		haveItems: function( item_type ) {
			return this.where({ type: item_type }).length;
		}
	});
	
	wc_psearch_popup.views.Item = Backbone.View.extend({
		tagName: 'li',
		className: function( model ) {
			switch( this.model.get('type') ) {
				case 'nothing':
					return 'ac_odd nothing';
				break;
				default:
					return 'ac_odd';
				break;
			}
		},
		
		itemTpl: 			_.template( $('#wc_psearch_itemTpl').html().replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' ) ),
		footerSidebarTpl: 	_.template( $('#wc_psearch_footerSidebarTpl').html().replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' ) ),
		footerHeaderTpl:	_.template( $('#wc_psearch_footerHeaderTpl').html().replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' ) ),
		
		initialize: function() {
			this.listenTo( this.model, 'destroy', this.remove );
		},
		
		render: function() {
			switch( this.model.get('type') ) {
				case 'header':
					//console.log('Predictive Search Popup - Rendering ' + this.model.get('title') + ' header');
					this.$el.html( '<div class="ajax_search_content_title">' + this.model.get('title') + '</div>' );
				break;
				
				case 'footerSidebar':
					//console.log('Predictive Search Popup - Rendering footer Sidebar Template');
					this.$el.html( this.footerSidebarTpl( this.model.toJSON() ) );
				break;
				case 'footerHeader':
					//console.log('Predictive Search Popup - Rendering footer Header Template');
					this.$el.html( this.footerHeaderTpl( this.model.toJSON() ) );
				break;
				
				case 'nothing':
					//console.log('Predictive Search Popup - Rendering nothing');
					this.$el.html( '<div class="ajax_no_result">' + this.model.get('title') + '</div>' );
				break;
				
				default:
					//console.log('Predictive Search Popup - Rendering item ' + this.model.get('title') );
					if ( wc_ps_allow_result_effect ) {
						this.$el.html( this.itemTpl( this.model.toJSON() ) ).addClass('animated ' + wc_ps_show_effect).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
							$(this).removeClass('animated ' + wc_ps_show_effect);
						});
					} else {
						this.$el.html( this.itemTpl( this.model.toJSON() ) );
					}
					
				break;
			}
			
			return this;
		}
		
	});
	
	wc_psearch_popup.views.PopupResult = Backbone.View.extend({
	
		initialize: function() {
			//console.log('Predictive Search Popup - init');
			this.predictive_search_input = null;
			this.original_ps_search_other = '';
			this.delaytime = 0;
			this.prepend = false;
			
			this.listenTo( this.collection, 'add', this.addItem );
			
			this.list_items_container = this.$('.predictive_search_results');
			
			//this.collection.fetch();
		},
		
		createItems: function( itemsData, prepend ) {
			this.prepend = prepend;
			$.each( itemsData, function ( index, data ) {
				this.collection.add( data );
			}.bind( this ));
			
			if ( this.original_ps_search_other == '' ) {
				this.original_ps_search_other = $( this.predictive_search_input ).data('ps-search_other');
			}
			if ( this.original_ps_search_in == '' ) {
				this.original_ps_search_in = $( this.predictive_search_input ).data('ps-search_in');
			}
			ps_search_other = this.original_ps_search_other.split(',');
			ps_id = $( this.predictive_search_input ).data('ps-id');
			
			new_ps_search_other = [];
			new_ps_search_in = '';
			$.each( ps_search_other, function( index, search_item ) {
				if ( this.collection.haveItems( search_item ) > 0 ) {
					new_ps_search_other.push( search_item );
					if ( new_ps_search_in == '' ) new_ps_search_in = search_item;
				}
			}.bind( this ));
			
			if ( new_ps_search_in != '' ) { 
				$( this.predictive_search_input ).data('ps-search_in', new_ps_search_in );
				$('#wc_ps_form_' + ps_id ).find('input[name=search_in]').val( new_ps_search_in );
			}
			if ( new_ps_search_other.length == 0 ) {
				new_ps_search_other = [ $( this.predictive_search_input ).data('ps-search_in') ];
			}
			$( this.predictive_search_input ).data('ps-search_other', new_ps_search_other.join(',') );
			$('#wc_ps_form_' + ps_id ).find('input[name=search_other]').val( new_ps_search_other.join(',') );
		},
		
		addItem: function ( itemModel ) {
			//console.log('Predictive Search Popup - Added item ' + itemModel.get('title') );
			var itemView = new wc_psearch_popup.views.Item({ model: itemModel });
			var itemHtml = itemView.render().el;
			if ( this.prepend ) {
				this.list_items_container.prepend( itemHtml );
			} else {
				this.list_items_container.append( itemHtml );
			}
			
			$.data( itemHtml, "ac_data", itemModel.attributes );
		},
		
		clearAll: function() {
			_.invoke( this.collection.where({status: true}), 'destroy');
			return false;	
		}
		
	});
	
	wc_psearch_popup.apps.App = {
		initialize: function() {
			
			$(document).on( 'click', '.wc_ps_nav_submit_bt', this.goToSearchResultPage );
			$('.wc_ps_form' ).bind( 'keypress', function( e ){
				if ( e.keyCode == 13 ) {
					this.goToSearchResultPage( e );
					return false;
				}
			}.bind( this ));

			$('.wc_ps_category_selector').change( function() {
				$(this).parents('.wc_ps_container').find('.wc_ps_nav_facade_label').html( $(this).find('option:selected').text().trim() );
			}).focus( function() {
				$(this).parents('.wc_ps_container').addClass('wc_ps_container_active');
			}).blur(function() {
				$(this).parents('.wc_ps_container').removeClass('wc_ps_container_active');
			});

			this.initPredictSearch();
		},
		
		initPredictSearch: function() {
			$(".wc_ps_search_keyword").each( function() {
				$(this).ps_autocomplete( wc_ps_legacy_api_url , {
					minChars: wc_ps_minChars,
					delay: wc_ps_delay,
					cacheTimeout: wc_ps_cache_timeout,
					isDebug: wc_ps_is_debug,
					scrollHeight: 2000,
					loadingClass: "predictive_loading",
					highlight : false
				}, wc_psearch_popup );
				
				$(this).result( function( event, keyword, url ) {
					if ( keyword != '' ) {
						$( this ).val( keyword );
					}
					if ( url != '' && url != null && url != '#' ) window.location = url;
				});
			}).focus( function() {
				$(this).parents('.wc_ps_container').addClass('wc_ps_container_active');
			}).blur(function() {
				$(this).parents('.wc_ps_container').removeClass('wc_ps_container_active');
			});
			
		},
		
		goToSearchResultPage: function( event ) {
			var target = $(event.target);
			ps_id = target.data('ps-id');
			predictive_search_input = $('#wc_ps_search_keyword_' + ps_id);
			cat_selected = predictive_search_input.parents('.wc_ps_container').find('.wc_ps_category_selector option:selected');
			if ( predictive_search_input.val() != '' && predictive_search_input.val() != predictive_search_input.data('ps-default_text') ) {
				$('#wc_ps_form_' + ps_id ).submit();
			} else if ( '' !== cat_selected.val() ) {
				cat_href = cat_selected.data('href');
				window.location = cat_href;
			}
		}
	};
	
	var wc_psearch_popup_app = wc_psearch_popup.apps.App;
	wc_psearch_popup_app.initialize();
	
});