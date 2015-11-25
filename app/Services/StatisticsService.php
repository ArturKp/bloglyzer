<?php

namespace Bloglyzer\Services;

class StatisticsService {

	public static function average(\Illuminate\Support\Collection $posts)
	{

		$count     = $posts->count();
		$comments  = $posts->pluck('comments')->avg();
		$pictures  = $posts->pluck('pictures')->avg();
		$wordCount = $posts->pluck('wordCount')->avg();
		$ego       = $posts->pluck('ego')->map(function($item, $key){
			return count($item);
		})->avg();

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
		$words = collect($words)->take(1000)->toArray();

		return compact('count', 'comments', 'pictures', 'wordCount', 'ego', 'words');

	}

}