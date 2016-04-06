@extends('home')

@section('statistics')

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
				$emotionalwords[$ew['word']] = $ew->toArray();
			}

			$emotionalwordKeys = array_keys($emotionalwords);

			$data = [
				'count'           => 'Posts',
				'wordCount'       => 'Words/Post',
				'emotionalScore'  => 'Em. Score/Post',
				'emotionalScoreX' => 'EmX. Score/Post',
				'emotionalScoreY' => 'EmY. Score/Post',
				'comments'        => 'Comments/Post',
				'pictures'        => 'Pictures/Post',
				'ego'             => 'Ego/Post'
			];

		?>

		<tbody>
			@foreach($data as $key => $value)
				<tr>
					<td style="white-space: nowrap">{{ $value }}</td>
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
						<table class="word-usage-list">
							<thead>
								<tr>
									<th>Word</th>
									<th>Count</th>
									<th>%</th>
									<th>score (1)</th>
									<th>scoreX (1)</th>
									<th>scoreY (1)</th>
								</tr>
							</thead>
							<tbody>

								<?php foreach ($stat['words'] as $word => $count): ?>

									<?php
										$superword = in_array($word, $emotionalwordKeys);

										if(\Input::get('onlysuperwords') == 1 && ! $superword) {
											continue;
										}

										if($superword) {
											$superwordScore = array_get($emotionalwords, $word . '.score', 0);
											$superwordScore = number_format($superwordScore, 10, '.', ' ');

											$superwordScoreX = array_get($emotionalwords, $word . '.scorex', 0);
											$superwordScoreX = number_format($superwordScoreX, 10, '.', ' ');

											$superwordScoreY = array_get($emotionalwords, $word . '.scorey', 0);
											$superwordScoreY = number_format($superwordScoreY, 10, '.', ' ');
										}
										else
										{
											$superwordScore = 0;
											$superwordScoreX = 0;
											$superwordScoreY = 0;
										}

									?>

									<tr class="<?php echo $superword ? 'superword' : '' ?>">
										<td class="word tcell">{!! $word !!}</td>
										<td class="count tcell">{{ $count }}</td>
										<td class="count-percentage tcell">({{ round(($count / $stat['totalWords']) * 100, 4) }} %)</td>
										<td class="tcell superscore">{{ $superwordScore }}</td>
										<td class="tcell superscore">{{ $superwordScoreX }}</td>
										<td class="tcell superscore">{{ $superwordScoreY }}</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</td>
				@endforeach
			</tr>

		</tbody>
	</table>

@endsection
