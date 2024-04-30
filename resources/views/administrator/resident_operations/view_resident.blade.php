@extends('administrator\layout')
 
@section('content')


<div class="content-header">

  <h3>{{ request('resident_name') }}'s Details</h3>

</div>

<div class="resident-detail-container">
   <div class="resident-detail-box">
      <p class="name"> Name: {{ request('resident_name') }}</p>
      <p class="birthday"></p>
   </div>
</div>


@endsection
