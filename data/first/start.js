(function(){
	'use strict';

	// Functions to get all posts from wordpress
	var scraper = require('./scraper.js');

	var getBlog = function (options) {
		var s = new scraper.Scraper(options);
		s.scrape(options);
	};

	// Load all required blogs
	var blogs = require('./options.js').blogs;

	for (var i = blogs.length - 1; i >= 0; i--) {
		getBlog(blogs[i]);
	}


})();