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

			$data = [
				'count'     => 'Posts',
				'wordCount' => 'Words/Post',
				'comments'  => 'Comments/Post',
				'pictures'  => 'Pictures/Post',
				'ego'       => 'Ego/Post'
			];

		?>

		<tbody>
			@foreach($data as $key => $value)
				<tr>
					<td>{{ $value }}</td>
					@foreach($statistics as $stat)
						<td><a href="{{ \Url::route('posts.listing', [
							'from' => \Input::get('from'),
							'to'   => \Input::get('to'),
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
								<li>
									<span class="word tcell">{!! \Bloglyzer\Services\HighlighterService::wrapWord($word) !!}</span>
									<span class="count tcell">{{ $count }}</span>
									<span class="count-percentage tcell">({{ round(($count / $stat['totalWords']) * 100, 4) }} %)</span></li>
							@endforeach
						</ol>
					</td>
				@endforeach
			</tr>

		</tbody>
	</table>

@endsection
