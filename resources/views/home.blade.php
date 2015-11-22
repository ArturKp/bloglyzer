@extends('base')

@section('content')

	<div class="row">
		<div class="col-xs-12">

			{!! Form::open(['url' => 'statistics', 'class' => 'date-filters']) !!}

				<div class="row">
					<div class="col-xs-12">
						<div class="col-xs-5 col-xs-offset-1 col-sm-4 col-sm-offset-2 col-md-3 col-md-offset-3">{!! Form::text('to', '', ['class' => 'form-control datepicker']) !!}</div>
						<div class="col-xs-5 col-sm-4 col-md-3">{!! Form::text('from', '', ['class' => 'form-control datepicker']) !!}</div>
					</div>
				</div>

			{!! Form::close() !!}

		</div>
	</div>

@endsection