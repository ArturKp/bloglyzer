(function(){
	'use strict';

	exports.logType = {
		FAIL: 0,
		SUCCESS: 1,
		DEBUG: 3
	};

	var http     = require('http');
	var $q       = require('q');
	var urlParse = require('url-parse');
	var options = require('./options.js');

	exports.getHtml = function (url) {

		var deferred = $q.defer();

		var parsedUrl = urlParse(url, true);

		var options = {
			path: parsedUrl.pathname,
			host: parsedUrl.host,
			port: 80
		};

		var str = '';

		var request = http.request(options, function (response) {

			response.on('data', function (chunk) {
				str += chunk;
			});

			response.on('end', function () {
				deferred.resolve(str);
			});
		});

		request.on('error', function (err) {
			deferred.reject(err);
		});

		request.end();

		return deferred.promise;

	};


	exports.normalizeText = function (text) {
		return text.replace(/\n/g, "").replace(/\t/g, ' ').replace(/\s{2,}/g, ' ').trim();
	};


	exports.log = function (level) {
		if(options.debug.indexOf(level) > -1) {
			for (var i=1; i<arguments.length; i++) {
				console.log(arguments[i]);
			}
		}
	};

})();