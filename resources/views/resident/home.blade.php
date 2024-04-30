@extends('resident\layout')

@section('content')

<h3>Welcome, Admin!</h3>


<div class="navigation"
> 
    <ul>

      <li><a href="">Manage Request records</a></li> 
      <li><a href="">Manage Pick-up records</a></li> 
      <li><a href="">Manage Documents</a></li> 
      <li><a href="">Manage Requirements</a></li> 
      <li><a href="">View your stats</a></li> 
      <li><a href="">View your profile</a></li> 
      <form method="POST" action="{{ route('logout') }}">
        @csrf

        <x-dropdown-link :href="route('logout')"
                onclick="event.preventDefault();
                            this.closest('form').submit();">
            {{ __('Log Out') }}
        </x-dropdown-link>
    </form>
    </ul>

     </div>

@endsection