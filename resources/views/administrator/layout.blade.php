<!DOCTYPE html>
<html lang="en"> 



<head>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <title>Request</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>


function getApproxDate(date) {
    
    
	const curr = new Date();
    var eventDate = new Date(date);
	var nowMillis = curr.getTime();
    var eventMillis = eventDate.getTime();
	var approxDate;
  	var  diff = nowMillis - eventMillis;
            if ((diff/86400000) >= 365) {
                approxDate = Math.trunc((diff/86400000)) +" years(s) ago";
            } else if ((diff/86400000) >= 30) {
                approxDate =  Math.trunc((diff/86400000)) +" month(s) ago";
            } else if ((diff/86400000) >= 1) {
                approxDate =  Math.trunc((diff/86400000)) +" days(s) ago";
            }else if ((diff/3600000) >= 1) {
                approxDate =  Math.trunc((diff/3600000)) +" Hour(s) ago";
            } else if ((diff/60000) >= 1) {
                approxDate =  Math.trunc((diff/60000)) +" minute(s) ago";
            } else if ((diff/1000) >= 1){
                approxDate =  Math.trunc((diff/1000)) +" second(s) ago";
            }

    return approxDate;

};

    </script>

</head>
<body>

    <h1>Administrator panel</h1>
    <div class="main-container">
        <br>
        
        <div class="navigation-container"> 

    <ul>

    <li> <a href="{{ route('admin.home') }}"> Home </a></li> 
      <li>  <a href="{{ route('admin.list_residents') }}"> Residents</a></li> 
      <li>  <a href="{{ route('admin.list_documents') }}"> Documents</a></li> 
      <li>  <a href="{{ route('admin.list_requirements') }}"> Requirements</a></li> 
      <li>  <a href="{{ route('admin.list_requests') }}"> Requests</a></li> 
      <li>  <a href="{{ route('admin.list_collections') }}"> Collections</a></li> 
      <li >  <a id="notification" href="{{ route('admin.list_notifications') }}"> Notifications </a></li> 
      <li>  <a href=" #" Target="_blank"> About</a></li> 
     

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

<script>

let notiftext = document.getElementById('notification');

var json  = JSON.stringify({!! $notifications!!});
var baseNotiFJason= JSON.parse(json);
 console.log("From base notif jason here <3");
 console.log(baseNotiFJason);

var i = 0;
baseNotiFJason.forEach(function(notif) {
    if (!notif.read_status) {
        i++;
        console.log("unread notification");
    } else {
        console.log("read notification");
    }
});

if (i > 0 ) {
    notiftext.style.fontWeight = "bold";
    notiftext.textContent = "Notifications ("+i+")";    
}


</script>

</html>