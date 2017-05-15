(function(){

	var resultsDiv;
	window.addEventListener('load', initFinder, false);
	
	function initFinder() {
		var els = document.querySelectorAll('.program-finder');
		for(var i=0; i<els.length; i++) {
			convertForm(els[i]);			
		}
	}
	
	function convertForm(el) {
		var form, els, selects
		
		form = document.createElement('form');
		form.className = 'has-js';
		el.appendChild(form);

		resultsDiv = document.createElement('div');
		resultsDiv.id = 'program-results';
		resultsDiv.className = 'tiles fitted thirds';
		el.parentNode.insertBefore(resultsDiv, el.nextSibling);

		
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
	
	function changeListener(el) {
		clearResults();
		showLoader();
		loadPrograms(el);
	}
	
	function clearResults() {
		resultsDiv.innerHTML = '';
	}

	function showLoader() {
		resultsDiv.innerHTML = '<div class="loading"><p>Loading...</p></div>';
	}

	
	function loadPrograms(el) {
		var cats, xmlhttp, url, selects;
		
		cats = [];

		selects = el.querySelectorAll('select');
		for(var i=0; i<selects.length; i++) {
			if(selects[i].value) {
				cats.push(selects[i].value);
			}
		}
		
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == XMLHttpRequest.DONE ) {
				if (xmlhttp.status == 200) {
					handleResponse(xmlhttp.responseText);
				}
	// 			else if (xmlhttp.status == 400) {
	// 				alert('There was an error 400');
	// 			}
				else {
					alert('something else other than 200 was returned');
				}
			}
		};
		
		//@todo: max is 100, what if there are more programs?
		url = URIProgramFinder.base + '/wp-json/wp/v2/posts?orderby=title&per_page=100&categories=' + cats.join(',');
		url = URIProgramFinder.base + '/wp-json/uri-programs/v1/category/' + cats.join(',') + '?orderby=title&per_page=100';

		xmlhttp.open("GET", url, true);
		xmlhttp.send();
	}

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
		
		console.log(data.length);
		
		if(data.length == 0) {
			resultsDiv.innerHTML = '<p class="no-results">No matches found.</p>';
		}
		
	}
	
})();