<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Bloglyzer</title>

		<link rel="stylesheet" href="{{ elixir('css/app.css') }}" />

	</head>

	<body>

		<div id="navbar">
			<img src="{{ URL::to('/') }}/images/bloglyzer.png" alt="" id="navbar-logo">
		</div>

		<div class="container">
			@yield('content')
		</div>

		<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>

		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>

		<script>
			jQuery( ".datepicker" ).datepicker({
				dateFormat: 'dd.mm.yy'
			});
		</script>

	</body>
</html>