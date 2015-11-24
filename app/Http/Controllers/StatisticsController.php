<?php

namespace Bloglyzer\Http\Controllers;

use Bloglyzer\Http\Controllers\Controller;
use Bloglyzer\Models\Post;
use Bloglyzer\Services\StatisticsService;
use Carbon\Carbon;

class StatisticsController extends Controller {

	public function getStatistics()
	{
		$from = \Input::get('from');
		$to   = \Input::get('to');

		try
		{
			if($to) {
				$fromCarbon = new Carbon($from);
			}
		} catch (\Exception $e) {
			\Log::error($e);
		}

		try
		{
			if($from) {
				$toCarbon = new Carbon($to);
			}
		} catch (\Exception $e) {
			\Log::error($e);
		}

		$post = new Post();

		if(isset($fromCarbon)) {
			$post = $post->where('date', '>', $fromCarbon);
		}
		if(isset($toCarbon)) {
			$post = $post->where('date', '<', $toCarbon);
		}

		$posts = $post->select(['pictures', 'words', 'comments', 'ego', 'words', 'wordCount', 'site'])->get();

		$groups = $posts->groupBy(function($item, $key) {
			return $item->site;
		});

		$result = [];

		foreach ($groups as $group)
		{
			$data = StatisticsService::average($group);
			$data['site'] = $group[0]['site'];
			$result[] = $data;
		}

		$result = array_values(array_sort($result, function ($value) {
			return $value['site'];
		}));

		$total = StatisticsService::average($posts);
		$total['site'] = 'total';
		$result[] = $total;
		$result = collect($result);
		return \View::make('columns', ['statistics' => $result]);
	}

}
