@extends('administrator\layout')
 
@section('content')

@php

@endphp




<div class="resident-container">
   <div class="content-header">
      <h3>View requirement:{{$requirementData["name"];}}</h3>
    
    </div>
<div class="resident-detail-container">
 <div class="document-detail-box">
    <form action="{{ route('admin.modify_requirement') }}" method="POST">
@csrf
      
        <input type="text" name="reqID" id="reqID" value="{{$requirementData["id"];}}" style="display:none">
        <label for="name"> Requirement name </label>
        <input type="text" name="name"
        value="{{$requirementData["name"];}}"
        id="name">
    
        
        <label for="description"> Description </label>
        <input type="text" name="description"
        value="{{$requirementData["description"];}}"
        id="description">
    
        <button type="submit">Confirm Edit</button>

    </form>
 </div>
</div>
@endsection
