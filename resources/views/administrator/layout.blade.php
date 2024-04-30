<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <title>Request</title>
</head>
<body>

    <h1>Administrator panel</h1>
    <div class="main-container">
        <br>
        
        <div class="navigation-container"> 

    <ul>

    <li> <a href="{{ route('admin.home') }}"> Home </a></li> 
      <li>  <a href="{{ route('admin.list_residents') }}"> Residents</a></li> 
      <li>  <a href="{{ route('admin.list_residents') }}"> Documents</a></li> 
      <li>  <a href="{{ route('admin.list_residents') }}"> Collections</a></li> 
      <li>  <a href="{{ route('admin.list_residents') }}"> About</a></li> 
      

    <li><div class="profile-container">
    <p><a href="">profile</a></p>
    <p><a href="">  {{ Auth::user()->name }} </a></p>     
    </div>
  
    <form method="POST" action="{{ route('logout') }}">
      @csrf

      <x-dropdown-link :href="route('logout')"
              onclick="event.preventDefault();
                          this.closest('form').submit();">
          {{ __('Log Out') }}
      </x-dropdown-link>
  </form>
  
  
  </li>

    </ul>
     </div>

        @yield('content')
    </div>


    
</body>
</html>