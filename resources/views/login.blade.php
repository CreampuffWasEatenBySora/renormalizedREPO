<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <title>Barangay-Econnect </title>
</head>
<body  >

    <h1>Welcome to Barangay-Econnect!</h1>
    <div class="container">
        <br>
        @yield('content')
    </div>

    <div class="loginBox"
    >
        <br>
        <form class="loginform" action="" method="POST">
         <input type="text" name="email" id="email" placeholder="Enter Email"><br> 
         <input type="password" name="password" id="password" placeholder="Enter Password"><br>
         <button type="submit" name="login" class="btn btn-primary btn-lg" >Login</button>
        </form> <br>
         Do not have an account?<a href="{{ route('signup.1_enterName') }}">Sign Up.</a>
     
         </div>
    
</body>
</html>