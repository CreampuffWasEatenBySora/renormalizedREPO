@extends('administrator\layout')
 
@section('content')

<div class="content-container">

  <div class="content-header">

  <h3>Manage Requirements</h3>

  <div class="searchbar">
    <form action="{{ route('admin.list_residents') }}" method="get">
    <button>search</button>
    <input type="text" name="searchbox" id="searchbox" :value="old('searchbox')">
   
    <select name="filter" id="filter">

      <option value="any"> No filter </option>
      <option value="id"> ID </option>
      <option value="name"> Name</option>
      <option value="description"> Description </option>
      <option value="created_at"> Created on </option>
      <option value="updated_at"> Updated on </option>

    </select>

    <select name="sort" id="sort">
      <option value="any"> No filter </option>
      <option value="id"> ID </option>
      <option value="name"> Name</option>
      <option value="description"> Description </option>
      <option value="created_at"> Created on </option>
      <option value="updated_at"> Updated on </option>
    </select>
   
    </form>

    <button id="sort-button">⬆ Ascending</button>


  </div>

</div>

<div class="resident-container">

  <div class="resident-header">

    <div class="document-detail">
      <div class="details-box">
       <p id="regdate" class="detail-regdate">Created on:</p>  
       <p id="name" class="detail-name">Name</p>
       <p id="description" class="detail-address">Description</p>
      </div>
      <div id="close_button" style="display: none">
        <p>X</p>
      </div>
    </div>

    <div class="approval-container">
      <a href="{{ route('admin.create_requirement') }}">
        <button  id="add_button" type="submit">
         Add a new requirement
        </button>
      </a>  
      </form>

      <form action= "{{ route('admin.view_requirement') }}" method="get"  style="display: none">>
        <input  style="display: none" id="id" name="id" type="text">
        <button id="edit_button" type="submit">
          Edit This Requirement
        </button>
      </form>

    </div>

  </div>

  <div class="resident-table-container">
    <table class="document-table" id="table">
      <thead>
        <th>Created on:</th>
        <th>Name</th>
        <th>Description</th>
        <th>Updated on:</th>
      </thead>
       <tbody>

       </tbody>
    </table>
  </div>

</div>
</div>

<script>


  // Assume jsonData contains your JSON data
var jsonData = {!! $requirement_jsonData !!};

// Get a reference to the table body
var tableBody = document.getElementById('table').getElementsByTagName('tbody')[0];
let requirement_id = document.getElementById('id');
let requirement_regdate = document.getElementById('regdate');
let requirement_name = document.getElementById('name');
let requirement_description = document.getElementById('description');

let  close_button = document.getElementById('close_button');
let  add_button = document.getElementById('add_button');
let  edit_button =document.getElementById('edit_button');

let  reg =  requirement_regdate.textContent;
let  name = requirement_name.textContent;
let  description = requirement_description.textContent;


close_button.addEventListener('click', function name() {
  
  close_button.style.display = 'none';
  add_button.style.display = 'block';
  edit_button.style.display = 'none';

})


function fillTable(data) {
      
      
    // Iterate over each entry in the JSON data
    data.forEach(function(requirement) {


    // Create a new table row
    var row = tableBody.insertRow();

          row.classList.add('resident-clickable-row');
          //assign an onclick function to the row:
          row.addEventListener('click', function() {
            
            // Handle row click event here
            requirement_regdate.textContent = reg.concat(": ", requirement.created_at);
            requirement_name.textContent = name.concat(": ",requirement.name) ;
            requirement_description.textContent  = description.concat(": ", requirement.description);
            requirement_id.value = requirement.id;
            close_button.style.display = 'block';
            add_button.style.display = 'none';
            edit_button.style.display = 'block';


          });

          // Insert cells into the row and populate them with data
          var cell1 = row.insertCell(0);
          cell1.innerHTML = requirement.created_at;

          var cell2 = row.insertCell(1);
          cell2.innerHTML =  requirement.name;

          var cell3 = row.insertCell(2);
          cell3.innerHTML =  requirement.description;

          var cell4 = row.insertCell(3);
          cell4.innerHTML =  requirement.updated_at;

          });
          console.log(jsonData);

    }

    fillTable(jsonData);


let sort_order_button = document.getElementById('sort-button');

sort_order_button.addEventListener('click', function sorted() {
  
  tableBody.innerHTML = "";
  
  const reversedArray = jsonData.reverse();
  
    fillTable(reversedArray);
  
  if (sort_order_button.textContent == "⬆ Ascending") {
    sort_order_button.textContent = "⬇ Descending";
  } else {
    sort_order_button.textContent = "⬆ Ascending";
  }

});

let resident_sort = document.getElementById('sort');
resident_sort.addEventListener("change", function () {
  
  tableBody.innerHTML = "";
  
  const resortedArray = jsonData;
  var filter = resident_sort.value;

  switch (filter) {
    case "name":
      resortedArray.sort((a, b) => 
        {
      if(a.name < b.name){
          return -1;
        }});
      break;

      case "description":
      resortedArray.sort((a, b) => 
        {
      if(a.description < b.description){
          return -1;
        }});
      break;


      case "created_at":
      resortedArray.sort(function(a, b){return new Date(a.created_at) - new Date(b.created_at)});;
      break;
  
      
      case "updated_at":
      resortedArray.sort(function(a, b){return new Date(a.updated_at) - new Date(b.updated_at)});;
      break;

    default:
    resortedArray.sort((a, b) => 
        {
      if(a.name < b.name ){
          return -1;
        }});
      break;
  }

 

    console.log("Filter :" + filter +" " +resortedArray);
    fillTable(resortedArray);

 
    sort_order_button.textContent = "⬆ Ascending";
  

});




</script>

@endsection
