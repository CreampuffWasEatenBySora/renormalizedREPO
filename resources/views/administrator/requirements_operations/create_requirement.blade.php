@extends('administrator\layout')
 
@section('content')

@php
   
@endphp



<div class="resident-container">
   <div class="content-header">

      <h3>Create a new requirement</h3>
    
    </div>
<div class="resident-detail-container">
 <div class="document-detail-box">
    <form action="{{ route('admin.store_requirement') }}" method="get">
    
        
        <label for="name"> Requirement name </label>
        <input type="text" name="name" id="name">
    
        
        <label for="description"> Description </label>
        <input type="text" name="description" id="description">
    
        <button type="submit">Create Requirement</button>

    </form>
 </div>
</div>
 
</div>
@endsection
