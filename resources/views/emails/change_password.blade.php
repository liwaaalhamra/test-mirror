<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{env('APP_NAME')}}</title>
</head>
<body>

	<p>Hello <strong>{!! $data['user']->full_name !!}</strong>,</p>
	
	<p>
		{{$data['message1']}}: <strong>{{ $data['new_password'] }}</strong>
    </p>

    <p>
		{{$data['message3']}},<br/>
		{{$data['message4']}}
	</p>
    
</body>
</html>