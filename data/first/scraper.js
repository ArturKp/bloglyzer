(function(){
	'use strict';

	var $q      = require('q');
	var cheerio = require("cheerio");
	var helpers = require('./helpers.js');
	var mongodb = require('mongodb').MongoClient;
	var settings = require('./settings.js').settings || {};

	var Scraper = function (options) {
		this.options = options;
		this.site = options.site;
		this.firstPost = options.firstPost;

		this.deferred = $q.defer();

		this.posts = [];
	};

	Scraper.prototype.scrape = function () {

		var _this = this;

		var postPromises = [];
		helpers.log(helpers.logType.DEBUG, 'Started to scrape: ' + this.site);

		/**
		 *
		 * Get array of all the sentences where an ego word is used
		 *
		 */
		var getEgoSentences = function (text) {
			var deferred = $q.defer();
			var ego = text.match(/[^.\?\!]*[^\da-zäöüõ](mina|ma|mul|minul|mulle|minule|minu)[^\da-zäöüõ][^.\?\!]*[.\?\!]/gi);
			deferred.resolve(ego);
			return deferred.promise;
		};

		/**
		 *
		 * Count all the pictures inside the post
		 *
		 */
		var getPictureCount = function (html) {
			var deferred = $q.defer();
			var images = cheerio.load('<div>' + html + '</div>')('img').not('.wp-smiley').not('.emoji') || [];
			deferred.resolve(images.length);
			return deferred.promise;
		};

		var getTextContent = function (html) {
			var deferred = $q.defer();
			var content = cheerio.load('<div>' + html + '</div>', {normalizeWhitespace: true})('div');
			var text = content.text();
			deferred.resolve(text);
			return deferred.promise;
		};

		/**
		 * Count total number of words
		 * http://stackoverflow.com/a/18679657/1016777
		 */
		var getWordCount = function (text) {
			var deferred = $q.defer();
			var wordCount = text.replace(/(^\s*)|(\s*$)/gi,'')//exclude  start and end white-space
			.replace(/[ ]{2,}/gi, ' ')//2 or more space to 1
			.replace(/\n /, '\n') // exclude newline with a start spacing
			.replace(/[\.,!?:;\(\)]+/ig, ' ')
			.split(' ')
			.length;
			deferred.resolve(wordCount);
			return deferred.promise;
		};

		var getWordOccurance = function (text) {
			var deferred = $q.defer();
			var words = text.replace(/(^\s*)|(\s*$)/gi, '')//exclude  start and end white-space
			.replace(/[ ]{2,}/gi, ' ')//2 or more space to 1
			.replace(/\n /, '\n') // exclude newline with a start spacing
			.replace(/[\.,!?:;\(\)\"\'\d-“”\\\/]+/ig, ' ')
			.toLowerCase()
			.split(' ').filter(function(w){
				return w.length > 1;
			});

			var result = {};
			for(var i = 0; i < words.length; ++i) {
			    if(!result[words[i]])
			        result[words[i]] = 0;
			    ++result[words[i]];
			}
			deferred.resolve(result);
			return deferred.promise;
		};

		var analyzePost = function (html, url) {

			helpers.log(helpers.logType.DEBUG, 'Starting to analyze post at: ' + url);

			var failedPromise = function (what) {
				return function () {
					helpers.log(helpers.logType.FAIL, 'Failed to load: ' + what);
				};
			};

			var successPromise = function (what) {
				return function (data) {
					helpers.log(helpers.logType.DEBUG, 'Successfully received: ' + what);
					post[what] = data;
					return data;
				};
			};

			// Collect all necessary data to this object
			var post = {
				html: html,
				url: url,
				site: _this.options.site
			};

			var deferred   = $q.defer();

			var title      = _this.options.getTitle(html).then(successPromise('title'), failedPromise('title'));
			var tags       = _this.options.getTags(html).then(successPromise('tags'), failedPromise('tags'));
			var date       = _this.options.getPostDate(html).then(successPromise('date'), failedPromise('date'));
			var content    = _this.options.getHtmlContent(html).then(successPromise('content'), failedPromise('content'));
			var categories = _this.options.getCategories(html).then(successPromise('categories'), failedPromise('categories'));
			var comments   = _this.options.getCommentsCount(html).then(successPromise('comments'), failedPromise('comments'));

			// Following we can do when we already have html content extracted
			var contentAnalysis = content.then(function success (html) {

				var stuff = getTextContent(html).then(function success (text) {
					var words = getWordCount(text).then(successPromise('wordCount'), failedPromise('wordCount'));
					var ego   = getEgoSentences(text).then(successPromise('ego'), failedPromise('ego'));
					var t  = $q(text).then(successPromise('text'), failedPromise('text'));
					var count = getWordOccurance(text).then(successPromise('words'), failedPromise('words'))
					return $q.all([words, ego, t, count]);
				});

				var pictures  = getPictureCount(html).then(successPromise('pictures'), failedPromise('pictures'));

				return $q.all([stuff, pictures]);
			});

			$q.all([tags, date, title, content, categories, comments, contentAnalysis]).then(function success (data) {
				helpers.log(helpers.logType.SUCCESS, 'Post analyze done for: ' + _this.options.site + " - " + title);
				helpers.log(helpers.logType.DEBUG, post);
				deferred.resolve(post);
			}, function failure (wut) {
				helpers.log(helpers.logType.FAIL, 'Something went wrong: ' + wut);
			});

			return deferred.promise;
		};

		var getPosts = function (url) {

			var deferred = $q.defer();

			helpers.log(helpers.logType.DEBUG, 'Trying to get post from: ' + url);

			helpers.getHtml(url).then(function success (html) {

				helpers.log(helpers.logType.DEBUG, 'Received html for: ' + url);

				nextHref = _this.options.getNextPost(html).then(
					function success (nextHref) {

						// Add new post request to the "queue"
						postPromises.push(getPosts(nextHref));

					}, function failure (err) {
						helpers.log(helpers.logType.SUCCESS, 'Reached the final post of a bolg: ' + url);
					}
				)['finally'](function always () {

					// We do this in finally because it is important that the nextHref promise is done and new post promise is in queue already

					if(_this.options.shouldSkip && _this.options.shouldSkip(html)) {
						helpers.log(helpers.logType.SUCCESS, 'Skipped a post: ' + url);
						deferred.resolve();
					} else {
						analyzePost(html, url).then(
							function success (data) {

								_this.posts.push(data);

								helpers.log(helpers.logType.SUCCESS, 'Successfully analyzed post at: ' + url);

								var mongoUrl = 'mongodb://localhost:27017/bloglyzer';
								if(settings.mongodb && settings.mongodb.user && settings.mongodb.password) {
									mongoUrl = 'mongodb://' + settings.mongodb.user + ':' + settings.mongodb.password + '@localhost:27017/bloglyzer';
								}

								mongodb.connect(mongoUrl, function(err, db) {
									var collection = db.collection('post');
									collection.insertMany([data], function (err, result) {
										if(err) console.log('Mongodb error: ' + err);
										else console.log('Mongodb success: ' + result);
										db.close();
									});
								});

								// When we have done analyzing we can say that this post promise is done
								deferred.resolve();

							}, function failure (err) {
								helpers.log(helpers.logType.FAIL, 'Failed to analyze post: ' + err);
							}
						);
					}
				});
			},
			function failure (err) {
				helpers.log(helpers.logType.FAIL, 'Failed to get post: ' + err);
			});

			return deferred.promise;
		};

		postPromises.push(getPosts(this.firstPost));

		$q.all(postPromises).then(function success () {
			this.deferred.resolve(this.posts);
		});

		return this.deferred.promise;
	};

	exports.Scraper = Scraper;

})();