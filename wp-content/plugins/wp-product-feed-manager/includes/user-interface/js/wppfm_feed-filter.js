/*!
 * wppfm_feed-filter.js v1.0
 * Part of the WP Product Feed Manager
 * Copyright 2016, Michel Jongbloed
 *
 */

"use strict";

var $jq = jQuery.noConflict();
var _feedHolder;

function wppfm_makeFeedFilterWrapper( feedId, filter ) {
	
	var	htmlCode = 'All products from the selected Shop Categories will be included in the feed';
	var displayAcceptText = 'none';
	var displayEditText = 'none';
	
	if ( filter && filter.constructor === Array ) {

		var filterObject = JSON.parse(filter[0]['meta_value']);
		var nrFilters = wppfm_countObjectItems( filterObject );
		var i = 1;
		displayAcceptText = 'initial';
		displayEditText = 'none';
		
		for ( var key in filterObject ) {

			var filterArray = filterObject[key][i].split( '#' );
			var last = nrFilters === i ? true : false;
			htmlCode += '<span id="filter-accept-text"  style="display:' + displayAcceptText + ';"> except the ones where:</span>';
				
			htmlCode += wppfm_showEditFeedFilter( feedId, i, filterArray, last );
			i++;
		}
	} else {
		displayAcceptText = 'none';
		displayEditText = 'initial';
		htmlCode += '<span id="filter-accept-text"  style="display:' + displayAcceptText + ';"> except the ones where:</span>';
	}

	htmlCode += '<span id="filter-edit-text" style="display:' + displayEditText + ';"> (<a class="edit-feed-filter wppfm-btn wppfm-btn-small" href="javascript:void(0)" id="edit-feed-filters-' + feedId;
	htmlCode += '" onclick="wppfm_editFeedFilter(' + feedId + ')">edit</a>)</span>';
	
	$jq( '.product-filter-condition-wrapper' ).html( htmlCode );
	$jq( '.main-product-filter-wrapper' ).show();
}

function wppfm_showEditFeedFilter( feedId, filterLevel, filterArray, last ) {
	
	var htmlCode = '<div class="filter-wrapper" id="filter-wrapper-' + feedId + '-' + filterLevel + '">';
	
	htmlCode += wppfm_filterPreCntrl( feedId, filterLevel, filterArray[0] );
	htmlCode += wppfm_filterSourceCntrl( feedId, filterLevel, filterArray[1] );
	htmlCode += wppfm_filterOptionsCntrl( feedId, filterLevel, filterArray[2] );
	htmlCode += wppfm_filterInputCntrl( feedId, filterLevel, 1, filterArray[3] );
	htmlCode += wppfm_filterInputCntrl( feedId, filterLevel, 2, filterArray[3] );
	htmlCode += ' (<a class="feed-filter-remove wppfm-btn wppfm-btn-small" href="javascript:void(0)" id="filter-remove-';
	htmlCode += filterLevel + '" onclick="wppfm_removeFilter(' + feedId + ', ' + filterLevel + ')">remove</a>)';
	htmlCode += last ? '<span id="add-feed-filter-link"> (<a class="feed-filter-add wppfm-btn wppfm-btn-small" href="javascript:void(0)" id="filter-add-' 
		+ filterLevel + '" onclick="wppfm_addFilter(' + feedId + ', ' + filterLevel + ')">add</a>)</span>' : '';
	
	htmlCode += '</div>';

	return htmlCode;
}

function wppfm_filterChanged( feedId, filterLevel ) {
	
	var identString = feedId + '-' + filterLevel;
	var optionsValue = $jq( '#filter-options-control-' + identString ).val();
	var inputValue = '';

	// display the correct input fields
	if ( optionsValue === '4' || optionsValue === '5' || optionsValue === '14' ) {

		if ( optionsValue === '14' ) {
			
			$jq( '#filter-input-span-' + identString + '-2' ).show();
			$jq( '#filter-input-span-' + identString + '-1' ).show();
			inputValue = $jq( '#filter-input-control-' + identString + '-1' ).val() 
				+ '#0#' + $jq( '#filter-input-control-' + identString + '-2' ).val();
		} else {
			
			$jq( '#filter-input-span-' + identString + '-2' ).hide();
			$jq( '#filter-input-span-' + identString + '-1' ).hide();
		}
		
	} else {
		
		$jq( '#filter-input-span-' + identString + '-2' ).hide();
		$jq( '#filter-input-span-' + identString + '-1' ).show();
		inputValue = $jq( '#filter-input-control-' + identString + '-1' ).val();
	}

	if( wppfm_feedFilterIsFilled( feedId, filterLevel ) ) {
		
		var preValue = filterLevel > 1 ? $jq( '#filter-pre-control-' + identString ).val() : '0';
		var sourceValue = $jq( '#filter-source-control-' + identString ).val();

		var newValues = [ preValue, sourceValue, optionsValue, inputValue ];

		_feedHolder.setFeedFilter( changeFeedFilterValue( _feedHolder['feedFilter'], newValues, filterLevel ) );
	}
}

function wppfm_addFilter( feedId, filterLevel ) {
	
	if ( wppfm_feedFilterIsFilled( feedId, filterLevel ) || filterLevel === 0 ) {

		$jq( '#add-feed-filter-link' ).remove();
		$jq( '.product-filter-condition-wrapper' ).append( wppfm_showEditFeedFilter( feedId, filterLevel + 1, '', true ) );
		$jq( '#filter-edit-text' ).hide();
		$jq( '#filter-accept-text' ).show();
	} else {
		
		alert( 'Please fill in the filter values before adding a new one' );
	}
}

function wppfm_removeFilter( feedId, filterLevel ) {
	
	_feedHolder.removeFeedFilter( filterLevel );
	
	wppfm_makeFeedFilterWrapper( feedId, _feedHolder['feedFilter'] );
}

function wppfm_editFeedFilter( feedId ) {
	
	wppfm_addFilter( feedId, 0 );
}