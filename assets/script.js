window.unserializer = window.unserializer || {};
( ( window, document, app, undefined ) => {
	'use strict';

	app.copy = txt => {
		if (window.copy) {
			// The `window.copy()` function is a non-standard function that is only
			// available in Chrome, and only for use in the console.
			return window.copy(txt);
		}

		const el = document.createElement('textarea');
		el.setAttribute('style', 'position:fixed;left:-100%;top:-100%');
		el.value = txt;
		document.body.appendChild(el);
		el.select();

		if (document.execCommand('copy')) {
			el.remove();
			return true;
		}

		el.remove();
		throw new Error();
	};

	app.getOutput = () => {
		return document.getElementById('output-formatted').innerText;
	};

	app.download = (txt = app.getOutput(), type = app.method) => {
		var dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(txt);
		const el = document.createElement('a');
		var ext = 'txt';
		switch( type ) {
			case 'var_export':
				ext = 'php';
				break;
			case 'json':
				ext = 'json';
				break;
			case 'csv':
				ext = 'csv';
				break;
		}

		var fileName = 'output-' + (new Date()).getTime() + '.' + ext;
		el.setAttribute('href', dataStr);
		el.setAttribute('download', fileName);
		document.body.appendChild(el);
		el.click();
		el.remove();

		return fileName;
	};

	window.onkeydown = function(e){
		if(e.metaKey && e.keyCode == 'S'.charCodeAt(0)){
			e.preventDefault();
			const code = app.getOutput();

			if ( ! code || ! code.length ) {
				return alert('Nothing to save!')
			}

			alert('Downloaded: ' + app.download( code ));
		}
	};

} )( window, document, window.unserializer );