@extends('signup.layout')

@section('content')

<h3>Confirm your information!</h3>

 
<div class="SignInBox"
>
    <br>
    <form class="loginform" action="{{ route('signup.store') }}" >
        @csrf <!-- CSRF token -->

        <p>Your name: {{ $name }} </p>
       
    </form>
     </div>


@endsection