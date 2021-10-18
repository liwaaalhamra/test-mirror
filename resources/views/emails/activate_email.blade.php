<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{$data['title']}}</title>
</head>
<body>	
	<p>Hello <strong>{!! $data['full_name'] !!}</strong>,</p>
	
	<p>
		{{$data['message1']}}
		<p>{{$data['message2']}} : <h3><strong>{{ $data['code'] }}</strong></h3></p>
    </p>

    <p>
		{{$data['message3']}},<br/>
		{{$data['message4']}}
	</p>
    
</body>
</html>