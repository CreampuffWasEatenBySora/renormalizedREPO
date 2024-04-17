@extends('signup.layout')

@section('content')

<h3>Sign up step 1!</h3>


<div class="SignInBox"
>
    <br>
    <form class="loginform" action="{{ route('signup.2_enterAddress') }}"   >
        @csrf
     <input type="text" name="firstName" id="firstName" placeholder="Enter Firstname"><br> 
     @error('firstName')
     <div>{{ $message }}</div>
    @enderror
     
    <input type="text" name="middleName" id="middleName" placeholder="Enter Middlename"><br> 
    @error('middleName')
    <div>{{ $message }}</div>
    @enderror

     <input type="text" name="lastName" id="lastName" placeholder="Enter Surname"><br> 
     @error('lastName')
     <div>{{ $message }}</div>
     @enderror
     
     <button type="submit" name="login" class="btn btn-primary btn-lg" >To Next step!</button>
    </form> <br>
 
     </div>

@endsection