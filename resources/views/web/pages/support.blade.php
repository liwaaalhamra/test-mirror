<style>
    body, html {
        height: 100%;
        width: 100%;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #FFFFFF; /* White background */
        font-family: 'Arial', sans-serif;
    }

    .content-wrapper {
        text-align: center;
        margin: auto;
        width: 100%; /* Full width */
    }

    .card {
        background-color: #FCFCFC;
        border-radius: 6px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        padding: 4rem; /* Adjusted padding */
        margin: 15px;
        width: 100%;
        max-width: 600px; /* Increased max card width */
        box-sizing: border-box;
    }

    @media (max-width: 768px) {
        .content-wrapper {
            padding: 1rem;
        }
        .card {
            padding: 2rem; /* Increased padding for larger screens */
            margin: 0;
        }
    }

    h1 {
        font-size: 18px;
        color: #333;
        margin: 2rem 0;
    }

    .info-text, .small-text {
        color: #666;
        font-size: 14px;
        line-height: 1.4;
        margin: 1.5rem 0;
    }

    .small-text {
        font-size: 14px;
    }

    .success-msj {
        color: green;
        font-size: 18px;
        font-weight: 800;
    }

    input[type="text"] {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 10px;
        width: 100%;
        margin-bottom: 10px;
    }

    input[type="email"] {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 10px;
        width: 100%;
        margin-bottom: 10px;
    }

    .action-btn {
        background-color: #cc2e25;
        border: none;
        border-radius: 4px;
        color: white;
        cursor: pointer;
        padding: 10px 20px;
        width: 100%;
        box-sizing: border-box; /* Ensures padding doesn't affect width */
        margin-bottom: 10px;
    }

    .action-btn:hover {
        background-color: #c9302c;
    }

    .app_icon {
        width:128px; 
        height:128px;
        margin-left: auto;
        margin-right: auto;
    }
</style>

@extends('web.layouts.main')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <img src="{{env('APP_ENV') == 'local' ? asset('resources/assets/images/logo_new.png') : secure_asset('resources/assets/images/logo_new.png')}}" class="app_icon">
            <h3>{{env('APP_NAME')}}</h3>
            <h3>Support Center</h3>

            @if(session('success'))
                <p class="success-msj">{{ session('success') }}</p>
            @else
                <p class="small-text">Please let us know how we can help you by leaving your message here.</p>
            @endif

            <form method="POST" action="{{ route('submit-support') }}">
                {{ csrf_field() }}
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="message" placeholder="Enter your message" required>
                <button type="submit" class="action-btn">Submit</button>
            </form>
        </div>
    </div>
@endsection
