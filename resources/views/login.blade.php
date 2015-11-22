@extends('base')

@section('content')

	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">

		<img src="{{ URL::to('/') }}/images/bloglyzer.png" alt="" id="login-logo">

		{!! Form::open(['url' => 'login']) !!}

			<div class="row">
				<div class="col-xs-6 col-xs-offset-3">{!! Form::password('password', ['class' => 'form-control']) !!}</div>
			</div>

		{!! Form::close() !!}
		</div>
	</div>

@endsection