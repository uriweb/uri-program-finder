(function(){

	var resultsDiv;
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
	 * Create the results DIV
	 * @param obj el the program finder parent element
	 */
	function initResultsDiv(el) {
		resultsDiv = document.createElement('div');
		resultsDiv.id = 'program-results';
		resultsDiv.className = 'tiles fitted thirds';
		el.parentNode.insertBefore(resultsDiv, el.nextSibling);
	}

	
	/**
	 * Listen for change events on the select menus
	 * @param obj el the program finder parent element
	 */
	function changeListener(el) {
		clearResults();
		showLoader();
		loadPrograms(el);
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
		resultsDiv.innerHTML = '<div class="loading"><p>Loading...</p></div>';
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

		fetchPrograms(url, handleResponse);

	}


	/**
	 * make the AJAX call
	 * @param url the URL to query
	 * @param callback function for a successful call
	 */
	function fetchPrograms(url, success) {
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
	
	
	/**
	 * AJAX success callback
	 * parses the response, puts the data into the results div
	 * @param str raw the data from the URL (JSON as a string)
	 */
	function handleResponse(raw) {
		var data, i, result, entry;
		data = JSON.parse(raw);

		clearResults();
		
		for(i=0; i<data.length; i++) {
			result = document.createElement('div');
			result.className = 'card';
			entry = '<h1>' + data[i].title + '</h1>';
			entry += '<p>' + data[i].excerpt + '</p>';
			entry += '<a class="button" href="' + data[i].link + '">Explore</a>';

			result.innerHTML = entry
			resultsDiv.appendChild(result);
		}
				
		if(data.length == 0) {
			resultsDiv.innerHTML = '<p class="no-results">No matches found.</p>';
		}
		
	}
	
})();