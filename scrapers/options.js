(function(){
	'use strict';

	var $q      = require('q');
	var cheerio = require("cheerio");
	var helpers = require('./helpers.js');

	exports.debug = [helpers.logType.SUCCESS, helpers.logType.FAIL];

	var cheerioOptions = {
		normalizeWhitespace: true
	};

	var wordpressDefaults = {

		// Test defaults
		// console.log('next', jQuery('link[rel="next"]').attr('href'));
		// console.log('comments', jQuery('.comment').length);
		// console.log('title', jQuery('.entry-title').text());
		// console.log('content', jQuery('.entry-content').text());
		// console.log('date', jQuery('.entry-date').attr('datetime'));
		// console.log('tags', jQuery('[rel="tag"]').length);
		// console.log('categories', jQuery('[rel^="category"]').length);

		getNextPost: function (html) {
			var deferred = $q.defer();
			var link     = cheerio.load(html, cheerioOptions)('link[rel="next"]');
			if(link && link.attr) {
				deferred.resolve(link.attr('href'));
			} else {
				deferred.reject('No link found');
			}
			return deferred.promise;
		},
		getCommentsCount: function (html) {
			var deferred = $q.defer();
			var comments = cheerio.load(html, cheerioOptions)('.comment') || [];
			deferred.resolve(comments.length);
			return deferred.promise;
		},
		getTitle: function (html) {
			var deferred = $q.defer();
			var title = cheerio.load(html, cheerioOptions)('.entry-title');
			deferred.resolve(title.text());
			return deferred.promise;
		},
		getHtmlContent: function (html) {
			var deferred = $q.defer();
			var content = cheerio.load(html, cheerioOptions)('.entry-content');
			deferred.resolve(content.html());
			return deferred.promise;
		},
		getPostDate: function (html) {
			var deferred = $q.defer();
			var date = cheerio.load(html, cheerioOptions)('.entry-date');
			deferred.resolve(new Date(date.attr('datetime')));
			return deferred.promise;
		},
		getTags: function (html) {
			var deferred = $q.defer();
			var tags = cheerio.load(html, cheerioOptions)('[rel="tag"]');
			tags = tags.map(function () {
				var tag = cheerio(this).text();
				return tag;
			}).get();
			deferred.resolve(tags);
			return deferred.promise;
		},
		getCategories: function (html) {
			var deferred = $q.defer();
			var categories = cheerio.load(html, cheerioOptions)('[rel^="category"]');
			categories = categories.map(function () {
				var cat = cheerio(this).text();
				return cat;
			}).get();
			deferred.resolve(categories);
			return deferred.promise;
		},
		shouldSkip: function (html) {
			return cheerio.load(html, cheerioOptions)('article').hasClass('post-password-required');
		}

	};

	exports.blogs = [

		{
			site: 'mallukas',
			firstPost: 'www.mallukas.com/2012/03/23/taimetoitlane-aga-makiburksi-ikka-vitsutaks/',
			type: 'wordpress',
			getNextPost: wordpressDefaults.getNextPost,
			getCommentsCount: wordpressDefaults.getCommentsCount,
			getTitle: wordpressDefaults.getTitle,
			getHtmlContent: wordpressDefaults.getHtmlContent,
			getPostDate: wordpressDefaults.getPostDate,
			getTags: wordpressDefaults.getTags,
			getCategories: wordpressDefaults.getCategories,
			shouldSkip: wordpressDefaults.shouldSkip
		},

		{
			site: 'marimell',
			firstPost: 'marimell.eu/kohe-nuud-ja-praegu/',
			type: 'wordpress',
			getNextPost: wordpressDefaults.getNextPost,
			getCommentsCount: wordpressDefaults.getCommentsCount,
			getTitle: wordpressDefaults.getTitle,
			getHtmlContent: function (html) {
				var deferred = $q.defer();
				var content = cheerio.load(html, cheerioOptions);
				content('[action=like]').remove();
				content('.addtoany_content_bottom').remove();
				deferred.resolve(content('.entry-content').html());
				return deferred.promise;
			},
			getPostDate: wordpressDefaults.getPostDate,
			getTags: wordpressDefaults.getTags,
			getCategories: wordpressDefaults.getCategories,
			shouldSkip: wordpressDefaults.shouldSkip
		},

		{
			site: 'lifeaccordingtob',
			firstPost: 'lifeaccordingtob.com/4/',
			type: 'wordpress',
			getNextPost: wordpressDefaults.getNextPost,
			getCommentsCount: wordpressDefaults.getCommentsCount,
			getTitle: wordpressDefaults.getTitle,
			getHtmlContent: function (html) {
				var deferred = $q.defer();
				var content = cheerio.load(html, cheerioOptions);
				content('.wp_rp_wrap').remove();
				deferred.resolve(content('.entry-content').html());
				return deferred.promise;
			},
			getPostDate: wordpressDefaults.getPostDate,
			getTags: wordpressDefaults.getTags,
			getCategories: wordpressDefaults.getCategories,
			shouldSkip: wordpressDefaults.shouldSkip
		},

		{
			site: 'ailialber',
			firstPost: 'http://ailialber.blogspot.com.ee/2013/06/uuutissss.html',
			type: 'midagimuud',
			getNextPost: function (html) {
				var deferred = $q.defer();
				var link     = cheerio.load(html, cheerioOptions)('.blog-pager-newer-link');
				if(link && link.attr) {
					deferred.resolve(link.attr('href'));
				} else {
					deferred.reject('No link found');
				}
				return deferred.promise;
			},
			getCommentsCount: function (html) {
				var deferred = $q.defer();
				var comments = cheerio.load(html, cheerioOptions)('.comment') || [];
				deferred.resolve(comments.length);
				return deferred.promise;
			},
			getTitle: function (html) {
				var deferred = $q.defer();
				var title = cheerio.load(html, cheerioOptions)('.post-title');
				deferred.resolve(title.text());
				return deferred.promise;
			},
			getHtmlContent: function (html) {
				var deferred = $q.defer();
				var content = cheerio.load(html, cheerioOptions)('.post-body');
				deferred.resolve(content.html());
				return deferred.promise;
			},
			getPostDate: function (html) {
				var deferred = $q.defer();
				var date = cheerio.load(html, cheerioOptions);
				date = (date('#post-header-top').text().split(";")[0]).split(",")[1];

				var dateParts = date.trim().replace(".", "").replace("\"", "").split(" ");
				var months = ["jaanuar", "veebruar", "märts", "aprill", "mai", "juuni", "juuli", "august", "september", "oktoober", "november", "detsember"];
				var monthNumber = months.indexOf(dateParts[1]);
				var dateObj = new Date(dateParts[2], monthNumber, dateParts[0]);
				deferred.resolve(dateObj);
				return deferred.promise;
			},
			getTags: function (html) {
				var deferred = $q.defer();
				var tags = cheerio.load(html, cheerioOptions)('[rel="tag"]');
				tags = tags.map(function () {
					var tag = cheerio(this).text();
					return tag;
				}).get();
				deferred.resolve(tags);
				return deferred.promise;
			},
			getCategories: function (html) {
				return $q([]);
			}
		},

		{
			site: 'amidahenryteeb',
			firstPost: 'http://amidahenryteeb.eu/2012/12/26/kiusatused/',
			type: 'wordpress',
			getNextPost: wordpressDefaults.getNextPost,
			getCommentsCount: wordpressDefaults.getCommentsCount,
			getTitle: function (html) {
				var deferred = $q.defer();
				var title = cheerio.load(html)('.post-title');
				deferred.resolve(title.text());
				return deferred.promise;
			},
			getHtmlContent: function (html) {
				var deferred = $q.defer();
				var content = cheerio.load(html);
				content('.entry .widget').nextAll().remove();
				content('.entry .widget').remove();
				deferred.resolve(content('.entry').html());
				return deferred.promise;
			},
			getPostDate: function (html) {
				var deferred = $q.defer();
				var content = cheerio.load(html);
				content('.post-datecomment a').remove();
				content('.post-datecomment span').remove();
				var date = content('.post-datecomment').text();
				date = date.trim().replace(' by', '');
				if(date) {
					date = deferred.resolve(new Date(date));
				}
				deferred.resolve(date);
				return deferred.promise;
			},
			getTags: wordpressDefaults.getTags,
			getCategories: wordpressDefaults.getCategories
		}

	];

})();