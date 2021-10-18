<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{env('APP_NAME')}} | {{$data['title']}}</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />

</head>
<body>
	<p>Dears,</p>
	
	<p>
		{!! $data['message'] !!}
    </p>
    <p>
        {{--Full Name: <strong>{{$data['user']->first_name}} {{$data['user']->last_name}}</strong><br/>--}}
        Job Title: <strong>{{$data['job_name']}}</strong><br/>
        Job Type: <strong>{{$data['job_type']}}</strong><br/>
        First Name: <strong>{{$data['first_name']}}</strong><br/>
        Last Name: <strong>{{$data['last_name']}}</strong><br/>
        Phone Number: <strong>{{$data['phone_number']}}</strong><br/>
        Email: <strong>{{$data['email']}}</strong><br/>
        CV attached
	</p>
    
</body>
</html>