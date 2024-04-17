@extends('signup.layout')

@section('content')

<h3>Sign up step 1!</h3>


<div class="SignInBox"
>
    <br>
    <form class="loginform" action="{{ route('signup.2_enterAddress') }}"   method="POST" >
     <p>Finished signup</p>
    </form> <br>
 
     </div>

@endsection