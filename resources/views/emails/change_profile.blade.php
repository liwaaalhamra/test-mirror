<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{env('APP_NAME')}} | {{$data['title']}}</title>
</head>
<body>

	<p>Dears,</p>
	
	<p>
		{{$data['message1']}}
    </p>
    <ul>


    @if($data['email'] != null)
        <li>
            <p>Email: <strong>{{ $data['email'] }}</strong></p>
        </li>
    @endif
    @if($data['first_name'] != null)
        <li>
            <p>First Name: <strong>{{ $data['first_name'] }}</strong></p>
        </li>
    @endif
    @if($data['last_name'] != null)
        <li>
            <p>Last Name: <strong>{{ $data['last_name'] }}</strong></p>
        </li>
    @endif
    @if($data['department'] != null)
        <li>
            <p>Department: <strong>{{ $data['department'] }}</strong></p>
        </li>
    @endif
    @if($data['blood_type'] != null)
        <li>
            <p>Blood Type: <strong>{{ $data['blood_type'] }}</strong></p>
        </li>
    @endif
    @if($data['primary_phone'] != null)
        <li>
            <p>Primary Phone: <strong>{{ $data['primary_phone'] }}</strong></p>
        </li>
    @endif
    @if($data['secondary_phone'] != null)
        <li>
            <p>Secondary Phone: <strong>{{ $data['secondary_phone'] }}</strong></p>
        </li>
    @endif
    @if($data['street'] != null)
        <li>
            <p>Street: <strong>{{ $data['street'] }}</strong></p>
        </li>
    @endif
    @if($data['building'] != null)
        <li>
            <p>Building: <strong>{{ $data['building'] }}</strong></p>
        </li>
    @endif
    @if($data['floor'] != null)
        <li>
            <p>Floor: <strong>{{ $data['floor'] }}</strong></p>
        </li>
    @endif
    @if($data['dob'] != null)
        <li>
            <p>DOB: <strong>{{ $data['dob'] }}</strong></p>
        </li>
    @endif
    @if($data['gender'] != null)
        <li>
            <p>Gender: <strong>{{ $data['gender'] }}</strong></p>
        </li>
    @endif
    @if($data['address'] != null)
        <li>
            <p>Address: <strong>{{ $data['address'] }}</strong></p>
        </li>
    @endif

    </ul>
    
</body>
</html>