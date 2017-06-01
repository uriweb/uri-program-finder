(function($){

	'use strict';

	// @todo: this is probably the wrong scope for this variable
	var resultsDiv, statusDiv, xmlhttp, lasturl;
	var timers = [];
	var searchTimer; // used to put a delay on keyup to slow down search requests
	var delay = 20; // set the delay between cards appearing on the page (in milliseconds)
	window.addEventListener('load', initFinder, false);
	
	
	/**
	 * Find program finder and set it up to be awesome.
	 */
	function initFinder() {
		var el = document.getElementById('program-finder');
        convertForm(el);
        
        // Only load programs on startup if a URL query string exists
        var querystring = getQueryString();
        if (querystring !== undefined) {
            loadPrograms();
        }
	}


	/**
	 * Convert the exiting non-js form to something a little slicker
	 * @param obj el the program finder parent element
	 */
	function convertForm(el) {
		var form, els, selects, textSearch, firstopt;
		
		initResultsDiv(el);
		initStatusDiv(el);
		
		els = el.querySelectorAll('.program-finder-nojs');
		for(var i=0; i<els.length; i++) {
			els[i].style.display = 'none';
		}
        
        form = el.querySelector('.has-js');
        $(form).css('display','block');
        
		
		textSearch = form.querySelector('input[name="s"]');
		if(textSearch) {
			textSearch.addEventListener('keyup', function() {
				textSearchListener(this);
			}, false);
            textSearch.addEventListener('blur', function() {
				textSearchListener(this);
			}, false);
            textSearch.focus();
		}

		selects = form.querySelectorAll('select');

		for(var i=0; i<selects.length; i++) {
            firstopt = $(selects[i]).find('option:eq(0)');
            $(firstopt).html('').removeAttr('selected');
		}	
        
        initChosen(form, selects);
        
        form.querySelector('#js-form-reset').addEventListener('click', function () {
            resetForm(form,textSearch,selects);
        });

	}
    
    
    /**
	 * Initiate Chosen on all selects and binds listener
     * @param obj form the js form parent element
	 * @param obj selects the select menus
	 */
    function initChosen(form, selects) {
        for(var i=0; i<selects.length; i++) {
            $(selects[i]).chosen().on( 'change', function() {
				changeListener(form, this);
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
            switch(data.program_types[i]['slug']) {
                case 'bachelors':
                    badge = ['ba',"Bachelor's"];
                    break;
                case 'ph-d':
                    badge = ['phd','Ph.D.'];
                    break;
                case 'graduate-certificate':
                    badge = ['cert','Certificate'];
                    break;
                case 'masters':
                    badge = ['ma',"Master's"];
                    break;
                case 'professional-degree':
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
	 * Listen for change events on the select menus
	 * @param obj form the js form parent element
	 * @param obj select the select element (what you'd expect to be "this")
	 */
	function changeListener(form, select) {
        var selected, x;
        
        selected = getSelectedCategoryIds(form);
        for (x in selected) { 
          updateQueryString(x,selected[x]);
        }
        loadPrograms();
	}

	/**
	 * Listen for change events on the select menus
	 * @param obj input the input text element (what you'd expect to be "this")
	 */
	function textSearchListener(input) {
		window.clearTimeout(searchTimer);
        
        if ($(input).is(':focus') == false || input.value != '') {
		
            searchTimer = window.setTimeout(function() {
                updateQueryString('q', input.value);
                loadPrograms();
            }, 300);
            
        }

	}
        

    /**
     * Clear the search form and load all programs
     * @param obj form the js form parent element
     * @param obj input the text search input
     * @param obj selects the select menus
     */
    function resetForm(form, input, selects) {
        input.value = '';        
        updateQueryString('q', input.value);
        
        $(selects).each(function() {
            this.selectedIndex = -1;
            $(this).trigger('chosen:updated');
            updateQueryString($(this).attr('name'),'any');
        });
        
        $('#js-form-reset').html('Clear');
        
        loadPrograms();
        
    }

	/**
	 * Update the browser URL, and add the selection to the browser's history
	 * @param str key is the querystring key
	 * @param str value is the querystring value
	 */
	function updateQueryString(key, value) {
		var url, regex, separator, newURL;
		url = window.location.toString();
		regex = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
		separator = url.indexOf('?') !== -1 ? "&" : "?";
		        
		if (url.match(regex)) {
            newURL = url.replace(regex, '$1' + key + "=" + value + '$2');
		}
		else {
			newURL = url + separator + key + "=" + value;
		} 
        
		if (history.pushState) {
			window.history.pushState({path:newURL}, '', newURL);
		}
        
		
	}

	/**
	 * get the category ids from the select menus
	 * @param obj form the js form parent element
	 * @return arr
	 */
	function getSelectedCategoryIds(form) {
		var cats, selects, vals, i;
		cats = {};

		selects = form.querySelectorAll('select');
        
		for(i=0; i<selects.length; i++) {
            vals = $(selects[i]).val();
            
			if( vals != null ) {
				cats[$(selects[i]).attr('name')] = vals;
			} else {
                cats[$(selects[i]).attr('name')] = 'all';
            }
		}
        		
		return cats;
	}
	

	/**
	 * Parses the current query string and returns it as an object
	 * @return obj
	 */
	function getQueryString() {
		var qs, obj, p;
		qs = location.search.substring(1);
        if (qs != '') {
            obj = qs.split("&").reduce(function(prev, curr, i, arr) {
                p = curr.split("=");
                prev[decodeURIComponent(p[0])] = decodeURIComponent(p[1]);
                return prev;
            }, {});
        }
		return obj;
	}


	/**
	 * Load programs from the REST API
	 */
	function loadPrograms() {
		var queryString, url, text, s, x;

		queryString = getQueryString();
        		
		url = URIProgramFinder.base + '/wp-json/uri-programs/v1/category';	
        
        for(x in queryString) {
            s = url.indexOf('?') !== -1 ? "&" : "?";
            url += s + x + '=' + queryString[x];
        }
		
		if(queryString.q) {
			url += '&s=' + queryString.q;
		}		

        if(url !== lasturl) {
            showLoader();
            lasturl = url;
            fetch(url, handleResponse);
        } else {
            console.log('same request!');
        }
	}


	/**
	 * make the AJAX call
	 * @param url the URL to query
	 * @param callback function for a successful call
	 */
	function fetch(url, success) {
		console.log('start ' + url);
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
				if (xmlhttp.status == 200) {
                    console.log('done ' + url);
					success(xmlhttp.responseText);
				}
	 			else if (xmlhttp.status == 404) {
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
	 * AJAX success callback
	 * parses the response, puts the data into the results div
	 * @param str raw the data from the URL (JSON as a string)
	 */
	function handleResponse(raw) {
		var data = JSON.parse(raw),
            dataL = data.length,
            i,s,t;
                 
		clearTimeouts();
                
        if(dataL == 0) {
            clearResults();
			noResults();
		} else {
            
            // Set the status
            t = (dataL != 1) ? 'programs match' : 'program matches';
			setStatus('results', dataL + ' ' + t + ' your search.' );
            
            var existingCards = $('#program-results .card');
                        
            // If there are existing cards, figure out what stays/goes
            if (existingCards.length) {
                                
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
                for (i=0; i<dataL; i++) {
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
            
            // Else there's nothing in the program results, add them all
            } else { 
                for(i=0; i<dataL; i++) {
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