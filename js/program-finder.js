(function($){

	'use strict';

	// @todo: this is probably the wrong scope for this variable
	var resultsDiv, statusDiv;
	var timers = [];
    var cache = [];
	var delay = 50; // set the delay between cards appearing on the page (in milliseconds)
	window.addEventListener('load', initFinder, false);
	
	
	/**
	 * Find program finders and set them up to be awesome.
	 */
	function initFinder() {
		var els = document.querySelectorAll('.program-finder');
		for(var i=0; i<els.length; i++) {
			convertForm(els[i]);			
		}
	}


	/**
	 * Convert the exiting non-js form to something a little slicker
	 * @param obj el the program finder parent element
	 */
	function convertForm(el) {
		var form, els, selects
		
		form = document.createElement('form');
		form.className = 'has-js';
		el.appendChild(form);
		
		initResultsDiv(el);
		initStatusDiv(el);
		
		els = el.querySelectorAll('.program-finder-nojs');
		for(var i=0; i<els.length; i++) {
			els[i].style.display = 'none';
		}
		
		selects = el.querySelectorAll('select');
		for(var i=0; i<selects.length; i++) {
			form.appendChild(selects[i]);
			selects[i].addEventListener('change', function() {
				changeListener(el)
			}, false);
		}
	}


	/**
	 * Create the status DIV
	 * @param obj el the program finder parent element
	 */
	function initStatusDiv(el) {
		statusDiv = document.createElement('div');
		statusDiv.id = 'program-status';
		el.parentNode.insertBefore(statusDiv, el.nextSibling);
	}

	/**
	 * Create the results DIV
	 * @param obj el the program finder parent element
	 */
	function initResultsDiv(el) {
		resultsDiv = document.createElement('div');
		resultsDiv.id = 'program-results';
		resultsDiv.className = 'tiles thirds fitted reveal';
		el.parentNode.insertBefore(resultsDiv, el.nextSibling);
	}

	
	/**
	 * Empty the results div
	 */
	function clearResults() {
		resultsDiv.innerHTML = '';
	}

	/**
	 * Show the loading DIV
	 */
	function showLoader() {
		setStatus( '<div class="loading"><p>Loading...</p></div>' );
	}
	
	/**
	 * Set the status div
	 */
	function setStatus(html) {
		console.log(html);
		statusDiv.innerHTML = html;
	}
	
	/**
	 * Clear the status div
	 */
	function clearStatus() {
		console.log('clear status');
		statusDiv.innerHTML = '';
	}

	/**
	 * Show the no results DIV
	 */
	function noResults() {
		setStatus( '<p class="no-results">No matches found.</p>' );
	}
			

	/**
	 * Create a result row's HTML
	 * @param obj data
	 * @return obj HTML element
	 */
	function createResultCard(data) {
		var result;

		result = document.createElement('div');
		result.setAttribute('class', 'card');
		//result.setAttribute('data-href', data.link);
		result.setAttribute('data-id', data.id);
		
		result.innerHTML = '<h1>' + data.title + '</h1>';
		result.innerHTML += '<p>' + data.excerpt + '</p>';
		result.innerHTML += '<a class="button" href="' + data.link + '">Explore</a>';
		
		return result;
	}


	/**
	 * Remove a result row HTML (card)
	 * @param str url
	 */
	function removeResultCard(url) {
		var els, i;
		els = document.querySelectorAll('.card[data-href="' + url + '"]');
		
		for(i=0; i<els.length; i++) {
			els[i].parentNode.removeChild(els[i]);
		}
	}


	/**
	 * Listen for change events on the select menus
	 * @param obj el the program finder parent element
	 */
	function changeListener(el) {
		// clearResults();
		showLoader();
		loadPrograms(el);
	}

	
	/**
	 * Load programs from the REST API
	 * @param obj el the program finder parent element
	 */
	function loadPrograms(el) {
		var cats, selects, i, url;
		
		cats = [];

		selects = el.querySelectorAll('select');
		for(i=0; i<selects.length; i++) {
			if(selects[i].value) {
				cats.push(selects[i].value);
			}
		}
		url = URIProgramFinder.base + '/wp-json/uri-programs/v1/category/' + cats.join(',');

		fetch(url, handleResponse);
	}


	/**
	 * make the AJAX call
	 * @param url the URL to query
	 * @param callback function for a successful call
	 */
	function fetch(url, success) {
		var xmlhttp;
		
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
				if (xmlhttp.status == 200) {
					success(xmlhttp.responseText);
				}
	// 			else if (xmlhttp.status == 400) {
	// 				alert('There was an error 400');
	// 			}
				else {
					console.log('something else other than 200 was returned');
				}
			}
		};
		
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
	}
	
	function clearTimeouts() {
		for(var i in timers) {
			window.clearTimeout(timers[i]);
		}
	}
    
    

	
	/**
	 * AJAX success callback
	 * parses the response, puts the data into the results div
	 * @param str raw the data from the URL (JSON as a string)
	 */
	function handleResponse(raw) {
		var data, i, s;
		data = JSON.parse(raw);
                
        var ids = [];
        for (i in data) {
            ids.push(data[i]['id']);
        }
        
        //console.log(cache);
        //console.log(ids);
        
        var idsToRemove = [];
        for (i=0; i<cache.length; i++) {
            if ( ids.indexOf(cache[i]) == -1 ) idsToRemove.push(cache[i]);
        }
            
        var idsToAdd = [];
        for (i=0; i<ids.length; i++) {
            if ( cache.indexOf(ids[i]) == -1 ) idsToAdd.push(ids[i]);
        }
            
        //console.log(idsToRemove);
        //console.log(idsToAdd);
    
		clearStatus();
		clearTimeouts();
        
        if(data.length == 0) {
            clearResults();
			noResults();
		} else {
            s = (data.length != 1) ? 'programs' : 'progam';
			setStatus( '<p class="program-count">' + data.length + ' matching ' + s + '.</p>' );
            for(i=0; i<idsToRemove.length; i++) {
                $('#program-results').find('[data-id="'+idsToRemove[i]+'"]').remove();
            }
            for(i=0; i<data.length; i++) {
                if (idsToAdd.indexOf(data[i]['id']) != -1) {
                    (function(arg) {
                        timers.push(window.setTimeout(function() {
                            resultsDiv.appendChild( createResultCard(arg.data) );
                        }, (delay*arg.i)));
                    }({'el': resultsDiv, 'data': data[i], 'i': i}));
                }
            }
        } 
        
        cache = ids;
        
	}
	
})(jQuery);