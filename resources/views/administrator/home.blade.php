@extends('administrator\layout')
 
@section('content')

<div class="content-container">
  <div class="content-header">
  <h3>Welcome, Admin!</h3>
</div>
<div class="content-quickstats">

    <div class="stat-item">
       <p class="stat-label">Requests for Approval</p>  
       <p class="stat-number">##</p>
    </div>

    <div class="stat-item">
       <p class="stat-label">Requests for Collection</p> 
       <p class="stat-number">##</p>

    </div>
    
</div>

<div class="shortcut-menu">
  
  <div class="menu-row">
    <div class="menu-item">Residents</div>
    <div class="menu-item">Documents & Requirements</div>
  </div>

  <div class="menu-row">
    <div class="menu-item">Requests</div>
    <div class="menu-item">Collections</div>
  </div>

</div>

       </div>

@endsection
