<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{$data['title']}}</title>
</head>
<body>	
	<p>Hello <strong>{!! $data['to'] !!}</strong>,</p>
	
		<h3>{{$data['title']}}</h3>
		<p>{{$data['message']}}</p>    
</body>
</html>