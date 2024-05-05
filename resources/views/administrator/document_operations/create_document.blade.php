@extends('administrator\layout')
 
@section('content')

@php
   
@endphp



<div class="resident-container">
   <div class="content-header">

      <h3>Create a new document</h3>
    
    </div>
<div class="resident-detail-container">
 <div class="document-detail-box">
    <form action="{{ route('admin.create_document') }}" method="get">
    
        
        <label for="document_name"> Document Title: </label>
        <input type="text" name="document_name" id="document_name">
    
        
        <label for="document_name"> Description </label>
        <input type="text" name="document_name" id="document_desc">
    
        <label for="requirement_menu"> Add Requirements: </label>
        <select name="requirement_menu" id="requirement_menu">
        </select>
        <a href="">View Requirements List</a>
        <button type="submit">Create document</button>
    </form>
 </div>
</div>

<script>

var requirements_list = {!! $requirement_jsonData !!};





</script>
</div>
@endsection
