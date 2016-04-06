(function(){
	'use strict';

	var settings = require('./settings.js').settings || {};
	var mongodb = require('mongodb').MongoClient;

	var mongoUrl = 'mongodb://localhost:27017/bloglyzer';
	if(settings.mongodb && settings.mongodb.user && settings.mongodb.password) {
		mongoUrl = 'mongodb://' + settings.mongodb.user + ':' + settings.mongodb.password + '@localhost:27017/bloglyzer';
	}

	mongodb.connect(mongoUrl, function(err, db) {
		var collection = db.collection('post');
		collection.remove({}, function (err, result) {
			if(err) console.log('Mongodb failed to delete collection: ' + err);
			else console.log('Mongodb deleted collection!');
			db.close();
		});
	});

})();