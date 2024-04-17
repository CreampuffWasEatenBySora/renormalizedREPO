@extends('signup.layout')

@section('content')

<h3>Sign up step 4!</h3>

<div class="SignInBox">
    <br>
   
    <form class="loginform" action="{{ route('signup.4_finishSignUp') }}"   >
        @csrf
        <input type="text" name="email" id="email" placeholder="Enter email" value="asfagdsgasdfasfaf"><br> 
        @error('email')
        <div>{{ $message }}</div>
        @enderror
     
        <input type="password" name="password" id="password" placeholder="Enter password"><br> 
        @error('password')
        <div>{{ $message }}</div>
        @enderror

        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm password"><br> 
        @error('password_confirmation')
        <div>{{ $message }}</div>
        @enderror
     
        <button type="submit" name="login" class="btn btn-primary btn-lg">Create account!</button>
    </form> <br>
</div>  

@endsection