@extends('home')

@section('statistics')

	<table class="table table-striped posts-table">
		<thead>
			<tr>
				@foreach($header as $s)
					<th>{{$s}}</th>
				@endforeach
			</tr>
		</thead>
		<tbody>
			@foreach($posts as $post)
				<tr>
					@foreach($header as $s)
						<td class="data-{{ $s }}">
							<?php $data = isset($post[$s]) ? $post[$s] : []; ?>

							@if(in_array($s, ['categories', 'tags']))
								{{-- For categories and tags --}}

								@if(count($data) > 3)
									<a data-expand="#summary-{{ $s }}-{{ $post->id }}" class="nowrap pointer">{{ implode(array_slice($data, 0, 3), ', ') }} [...]</a>
									<span id="summary-{{ $s }}-{{ $post->id }}" style="display:none">{{ implode($data, ', ') }}</span>
								@else
									{{ implode($data, ', ') }}
								@endif

							@elseif(is_array($data))

								@if(count($data) > 0)
									<a data-expand="#summary-{{ $s }}-{{ $post->id }}" class="nowrap pointer">{{ count($data) }} [...]</a>
									<ul id="summary-{{ $s }}-{{ $post->id }}" style="display:none">
										@foreach($data as $d)
											<li class="nowrap">{{ $d }}</li>
										@endforeach
									</ul>
								@else
									0
								@endif

							@elseif(in_array($s, ['title', 'site']))
								<a href="{{ $post['url'] }}">{{ $data }}</a>
							@else
								{{ $data }}
							@endif
						</td>
					@endforeach
				</tr>
			@endforeach
		</tbody>
	</table>

@endsection