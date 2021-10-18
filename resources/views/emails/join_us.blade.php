<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Syndicate | {{$data['title']}}</title>
</head>
<body>
	@include("emails.email_header")
	
	<p>Hello <strong>{!! $data['name'] !!} </strong>,</p>
	
	<p>
		{{$data['message']}}
    </p>

    <p>
		Sincerely,<br/>
		Syndicate Team
	</p>
    
</body>
</html>