<?php

namespace Bloglyzer\Http\Controllers;

use Bloglyzer\Http\Controllers\Controller;
use Bloglyzer\Models\Post;
use Bloglyzer\Services\StatisticsService;
use Carbon\Carbon;

class StatisticsController extends Controller {

	protected $fromCarbon;
	protected $toCarbon;
	protected $postQuery;
	public function __construct()
	{
		ini_set('memory_limit','256M');

		$this->postQuery = new Post();

		$from = \Input::get('from');
		$to   = \Input::get('to');
		$site = \Input::get('site');

		try
		{
			$this->fromCarbon = $to ? new Carbon($from) : null;
		}
		catch (\Exception $e)
		{
			\Log::error($e);
		}

		try
		{
			$this->toCarbon = $from ? new Carbon($to) : null;
		}
		catch (\Exception $e) {
			\Log::error($e);
		}

		if(isset($this->fromCarbon))
		{
			$this->postQuery = $this->postQuery->where('date', '>', $this->fromCarbon);
		}

		if(isset($this->toCarbon))
		{
			$this->postQuery = $this->postQuery->where('date', '<', $this->toCarbon);
		}

		if(isset($site) && $site != 'total')
		{
			$this->postQuery = $this->postQuery->where('site', '=', $site);
		}
	}

	public function getStatistics()
	{
		$posts = $this->postQuery->select(['pictures', 'comments', 'ego', 'words', 'wordCount', 'site'])->get();

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

		$total         = StatisticsService::average($posts);
		$total['site'] = 'total';
		$result[]      = $total;
		$result        = collect($result);

		return \View::make('columns', ['statistics' => $result]);
	}

	public function getPosts()
	{
		$selects = ['_id', 'url', 'site', 'title', 'tags', 'date', 'categories', 'comments', 'pictures', 'wordCount', 'ego'];

		$posts = $this->postQuery->select($selects)->get();

		$posts = $posts->sortByDesc('date');

		$header = array_diff($selects, ['_id', 'url']);

		return \View::make('posts', compact('posts', 'header'));
	}

}
