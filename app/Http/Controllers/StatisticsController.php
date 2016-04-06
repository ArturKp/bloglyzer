<?php

namespace Bloglyzer\Http\Controllers;

use Bloglyzer\Http\Controllers\Controller;
use Bloglyzer\Models\Post;
use Bloglyzer\Services\ExcelService;
use Bloglyzer\Services\StatisticsService;
use Carbon\Carbon;

class StatisticsController extends Controller {

	protected $fromCarbon;
	protected $toCarbon;
	protected $postQuery;
	protected $excel;

	const FILENAME_DATE_FORMAT = 'Y_m_d';

	public function __construct()
	{
		ini_set('memory_limit','256M');

		$this->postQuery = new Post();

		$from        = \Input::get('from');
		$to          = \Input::get('to');
		$site        = \Input::get('site');

		$this->excel = !! \Input::get('excel');

		$this->fromCarbon = $from ? @new Carbon($from) : Carbon::minValue();
		$this->toCarbon   = $to   ? @new Carbon($to)   : Carbon::now();


		$this->postQuery = $this->postQuery->where('date', '>', $this->fromCarbon);
		$this->postQuery = $this->postQuery->where('date', '<', $this->toCarbon);

		if(isset($site) && $site != 'total')
		{
			$this->postQuery = $this->postQuery->where('site', '=', $site);
		}
	}

	public function getStatistics()
	{
		$posts = $this->postQuery->select(['pictures', 'comments', 'ego', 'lemmas', 'wordCount', 'site', 'emotionalScore'])->get();

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
		$result        = $result;

		if($this->excel)
		{

			$filename = 'bloglyzer';
			$filename .= \Input::get('words') ? '_words' : 'general';
			if($this->fromCarbon && $this->toCarbon)
			{
				$filename .= '_' . $this->fromCarbon->format(self::FILENAME_DATE_FORMAT) . '-' . $this->toCarbon->format(self::FILENAME_DATE_FORMAT);
			}
			else
			{
				$filename .= time();
			}

			// Following is not working
			if(\Input::get('words')) {
				$words = array_pluck($result, 'lemmas', 'site');
				// array_unshift($words, null);
				// $words = call_user_func_array('array_map', $words);

				return ExcelService::getExcel($words, $filename);
			}
			return ExcelService::getExcel($result, $filename);
		}

		return \View::make('columns', ['statistics' => $result]);
	}

	public function getPosts()
	{

		$selects = ['_id', 'url', 'site', 'title', 'emotionalScore', 'tags', 'date', 'categories', 'comments', 'pictures', 'wordCount', 'ego'];

		$posts = $this->postQuery->select($selects)->get();

		$sort = \Input::get('sort', 'date');

		if(\Input::get('order', 'desc') === 'desc') {
			$posts = $posts->sortByDesc($sort);
		} else {
			$posts = $posts->sortBy($sort);
		}

		$header = array_diff($selects, ['_id', 'url']);

		if($this->excel)
		{
			return ExcelService::getExcel($posts);
		}

		return \View::make('posts', compact('posts', 'header'));
	}

}
