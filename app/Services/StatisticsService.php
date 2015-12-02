<?php

namespace Bloglyzer\Services;

class StatisticsService {

	public static function average(\Illuminate\Support\Collection $posts)
	{

		$count     = $posts->count();
		$comments  = $posts->pluck('comments')->avg();
		$pictures  = $posts->pluck('pictures')->avg();
		$wordCount = $posts->pluck('wordCount')->avg();
		$totalWords = $posts->pluck('wordCount')->sum();
		$ego       = $posts->pluck('ego')->map(function($item, $key){
			return count($item);
		})->avg();

		$comments = round($comments, 2);
		$pictures = round($pictures, 2);
		$wordCount = round($wordCount, 2);
		$ego = round($ego, 2);

		$wordCounts = $posts->pluck('words');
		$words = [];

		foreach ($wordCounts as $k => $subArray) {
			foreach ($subArray as $key => $value) {
				if( ! isset($words[$key])) {
					$words[$key] = 0;
				}
				$words[$key] += $value;
			}
		}
		arsort($words);
		$words = array_slice($words, 0, 5000);
		$words = array_filter($words, function($value) {
			return $value > 2;
		});
		// collect($words)->filter(function ($key, $value) {
		// 	return $value > 2;
		// })->toArray();

		return compact('count', 'comments', 'pictures', 'wordCount', 'ego', 'words', 'totalWords');

	}

}