@extends('home')

@section('statistics')

	<table class="table table-striped">
		<thead>
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
						<ol>
							@foreach($stat['words'] as $word => $count)
								<li><span class="word">{{$word}}</span> <span class="count">{{ $count }}</span></li>
							@endforeach
						</ol>
					</td>
				@endforeach
			</tr>

		</tbody>
	</table>

@endsection
