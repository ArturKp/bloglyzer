@extends('base')

@section('content')

	<div class="row">
		<div class="col-xs-12">

			{!! Form::open(['url' => 'statistics', 'method'=> 'GET', 'class' => 'date-filters']) !!}

				<div class="row">
					<div class="col-xs-12">
						<div class="col-xs-5
									col-sm-4 col-sm-offset-2
									col-md-3 col-md-offset-3">{!! Form::text('from', isset($_GET['from'])?$_GET['from']:'', ['class' => 'form-control datepicker']) !!}</div>
						<div class="col-xs-5 col-sm-4 col-md-3">{!! Form::text('to', isset($_GET['to'])?$_GET['to']:'', ['class' => 'form-control datepicker']) !!}</div>
						<div class="col-xs-1">{!! Form::submit('GO', ['class' => 'btn btn-default']) !!}</div>
					</div>
				</div>

			{!! Form::close() !!}

			@yield('statistics')

		</div>
	</div>

@endsection