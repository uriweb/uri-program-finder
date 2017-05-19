(function($){

	'use strict';

	// @todo: this is probably the wrong scope for this variable
	var resultsDiv, statusDiv;
	var timers = [];
	var searchTimer; // used to put a delay on keyup to slow down search requests
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
		var form, els, selects, textSearch;
		
		form = document.createElement('form');
		form.className = 'has-js';
		el.appendChild(form);
		
		initResultsDiv(el);
		initStatusDiv(el);
		
		els = el.querySelectorAll('.program-finder-nojs');
		for(var i=0; i<els.length; i++) {
			els[i].style.display = 'none';
		}
		
		textSearch = el.querySelector('input[name="s"]');
		if(textSearch) {
			textSearch.addEventListener('keyup', function() {
				textSearchListener(el);
			}, false);
			form.appendChild(textSearch);
		}

		selects = el.querySelectorAll('select');
		for(var i=0; i<selects.length; i++) {
			form.appendChild(selects[i]);
			$(selects[i]).chosen().on( 'change', function() {
				changeListener(el);
			});
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
	 * Set the status div
     * @param str cl the class name(s) to set for the status div
     * @param str html the html for the status div
	 */
	function setStatus(cl,html) {
		//console.log(html);
        statusDiv.className = cl;
		statusDiv.innerHTML = html;
	}

	/**
	 * Show the loading DIV
	 */
	function showLoader() {
		setStatus('loading','<span>Loading...</span>');
	}
    
    /**
	 * Show the no results DIV
	 */
	function noResults() {
		setStatus('empty','No matches found.');
	}
	
	/**
	 * Clear the status div
	 */
	function clearStatus() {
		//console.log('clear status');
        statusDiv.className = '';
		statusDiv.innerHTML = '';
	}
    

	/**
	 * Create a result row's HTML
	 * @param obj data
	 * @return obj HTML element
	 */
	function createResultCard(data) {
		var result, i;
        
		result = document.createElement('a');
		result.setAttribute('class', 'card');
        result.setAttribute('href', data.link);
		//result.setAttribute('data-href', data.link);
		result.setAttribute('data-id', data.id);
		
        var badge, 
            badgeHtml = '';
        for (i=0; i<data.program_types.length; i++) {
            switch(data.program_types[i]['term_id']) {
                case 19:
                    badge = ['bs',"Bachelor's"];
                    break;
                case 30:
                    badge = ['phd','Ph.D.'];
                    break;
                case 34:
                    badge = ['cert','Certificate'];
                    break;
                case 38:
                    badge = ['ma',"Master's"];
                    break;
                case 39:
                    badge = ['ms',"Master's"];
                    break;
                case 40:
                    badge = ['pro','Professional'];
                    break;
                default:
                    badge = [];
            }
            if (badge.length) {
                badgeHtml += '<li class="' + badge[0] + '">' + badge[1] + '</li>';
            }
        }
        
        result.innerHTML = data.image;
        result.innerHTML += '<ul class="badges">' + badgeHtml + '</ul>';
		result.innerHTML += '<h1>' + data.title + '</h1>';
		result.innerHTML += '<span class="button">Explore</span>';
                                
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
		showLoader();
		loadPrograms(el);
	}

	/**
	 * Listen for change events on the select menus
	 * @param obj el the program finder parent element
	 */
	function textSearchListener(el) {
		window.clearTimeout(searchTimer);

		searchTimer = window.setTimeout(function() {
			console.log(this.value);
			showLoader();
			loadPrograms(el);
		}, 200);

	}

	
	/**
	 * Load programs from the REST API
	 * @param obj el the program finder parent element
	 */
	function loadPrograms(el) {
		var cats, selects, i, url, textSearch;
		
		cats = [];

		selects = el.querySelectorAll('select');
		for(i=0; i<selects.length; i++) {
			if(selects[i].value) {
				cats.push(selects[i].value);
			}
		}
		url = URIProgramFinder.base + '/wp-json/uri-programs/v1/category?ids=' + cats.join(',');
		
		textSearch = el.querySelectorAll('input[name="s"]');
		if(textSearch[0].value) {
			url += '&s=' + textSearch[0].value;
		}		

		fetch(url, handleResponse);
	}


	/**
	 * make the AJAX call
	 * @param url the URL to query
	 * @param callback function for a successful call
     * @todo: this doesn't seem to be catching 404 errors
	 */
	function fetch(url, success) {
		var xmlhttp;
		
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
				if (xmlhttp.status == 200) {
					success(xmlhttp.responseText);
				}
	 			else if (xmlhttp.status == 404) {
	 				//alert('There was an error 404');
					console.log('error 404 was returned');
					setStatus('error', 'There was an error retrieving results.');
					clearResults();
	 			}
				else {
					console.log('something else other than 200 or 404 was returned');
				}
			}
		};
		
		xmlhttp.open('GET', url, true);
		xmlhttp.send();
	}
	
	function clearTimeouts() {
		for(var i in timers) {
			window.clearTimeout(timers[i]);
		}
	}
    
    /**
     * Cache a list of post ids from cards currently on the page
     * @param obj cards the array of existing cards
     */
    function buildCache(cards) {
        if (cards.length) {
            var idarray = [];
            cards.each(function(){
                idarray.push($(this).data('id'));
            });
            return idarray;
        } else {
            return [];
        }
    }
    
	
	/**
	 * AJAX success callback
	 * parses the response, puts the data into the results div
	 * @param str raw the data from the URL (JSON as a string)
	 */
	function handleResponse(raw) {
		var data = JSON.parse(raw),
            i,s,t;
        
        console.log(data);
         
		clearTimeouts();
        
        if(data.length == 0) {
            clearResults();
			noResults();
		} else {
            
            // Set the status
            s = (data.length != 1) ? 'these ' + data.length : 'this';
            t = (data.length != 1) ? 'programs' : 'progam';
			setStatus('results', "Y'all's needs to check out " + s + ' exquisite ' + t + '!' );
            
            var existingCards = $('#program-results .card'),
                cache = buildCache(existingCards);
                    
            // If the cache has items, figure out what stays/goes
            if (cache.length) {
                
                var ids = [];
                for (i in data) {
                    ids.push(data[i]['id']);
                }
                
                // Remove existing cards that aren't in the new results
                existingCards.each(function(){
                    if ( ids.indexOf( $(this).data('id') ) == -1 ) {
                        $(this).remove();
                    }
                });
                
                // Loop through new result ids, check for dups, and add cards accordingly
                var refCard;
                for (i=0; i<data.length; i++) {
                    (function(arg) {
                        timers.push(window.setTimeout(function() {
                            refCard = $('#program-results .card').eq(arg.i);
                            if ( refCard.length ) {
                                if ( arg.data['id'] != refCard.data('id') ) {
                                    refCard.before( createResultCard(arg.data) );
                                }
                            } else {
                                resultsDiv.appendChild( createResultCard(arg.data) );
                            }
                        }, (delay*arg.i)));
                    }({'data': data[i], 'i': i}));
                }
            
            // Else there's nothing in the cache, add them all
            } else { 
                for(i=0; i<data.length; i++) {
                    (function(arg) {
                        timers.push(window.setTimeout(function() {
                            resultsDiv.appendChild( createResultCard(arg.data) );
                        }, (delay*arg.i)));
                    }({'data': data[i], 'i': i}));
                }
            }

        }
                
	}
	
})(jQuery);