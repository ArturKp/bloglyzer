@extends('home')

@section('statistics')

	<label for="superwords-cb"><input id="superwords-cb" type="checkbox">Show only emotionalwords</label>

	<table class="table table-striped statistics-overview-table">
		<thead>
			<tr>
				<th colspan="43">
					<a href="{{ \URL::full() . '&excel=true'  }}">Download general</a>
					{{-- <a href="{{ \URL::full() . '&words=true&excel=true'  }}">Download words</a> --}}
				</th>
			</tr>
			<tr>
				<th></th>
				@foreach($statistics as $stat)
					<th>{{ $stat['site'] }}</th>
				@endforeach
			</tr>
		</thead>

		<?php

			function getListingUrl($params = []) {
				$params = $params + [
					'from' => \Input::get('from'),
					'to'   => \Input::get('to'),
				];
				return \Url::route('posts.listing', $params);
			}

			// $searchString = '(' . implode('|', \Bloglyzer\Models\Emotionalword::all()->pluck('word')->toArray()) . ')';
			// function wrapWord($in, $searchString) {
			// 	return preg_replace("/(\s|^)" . $searchString . "(\s|$)/", "<span class='superword'>$2</span>", $in);
			// }

			$emotionalwords = [];

			foreach (\Bloglyzer\Models\Emotionalword::all() as $ew) {
				$emotionalwords[$ew['word']] = $ew['score'];
			}

			$emotionalwordKeys = array_keys($emotionalwords);

			$data = [
				'count'          => 'Posts',
				'wordCount'      => 'Words/Post',
				'emotionalScore' => 'Em. Score/Post',
				'comments'       => 'Comments/Post',
				'pictures'       => 'Pictures/Post',
				'ego'            => 'Ego/Post'
			];

		?>

		<tbody>
			@foreach($data as $key => $value)
				<tr>
					<td>{{ $value }}</td>
					@foreach($statistics as $stat)
						<td><a href="{{ getListingUrl([
							'site' => $stat['site']
						]) }}">{{ $stat[$key] }}</a></td>
					@endforeach
				</tr>
			@endforeach
			<tr>
				<td>Words</td>
				@foreach($statistics as $stat)
					<td>
						<ol class="word-usage-list">
							@foreach($stat['words'] as $word => $count)
								<?php
									$superword = in_array($word, $emotionalwordKeys);
									$superwordScore = $superword ? array_get($emotionalwords, $word, 0) : 0;
									$superwordScore = number_format($superwordScore, 10, '.', ' ');
								?>
								<li class="<?php echo $superword ? 'superword' : '' ?>">
									<span class="word tcell">{!! $word !!}</span>
									<span class="count tcell">{{ $count }}</span>
									<span class="count-percentage tcell">({{ round(($count / $stat['totalWords']) * 100, 4) }} %)</span>
									<span class="tcell superscore">{{ $superword ? $superwordScore : '' }}</span>
								</li>
							@endforeach
						</ol>
					</td>
				@endforeach
			</tr>

		</tbody>
	</table>

@endsection
