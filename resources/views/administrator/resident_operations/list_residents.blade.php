@extends('administrator\layout')
 
@section('content')

<div class="content-container">

  <div class="content-header">

  <h3>Manage Residents</h3>

  <div class="searchbar">
    <form action="{{ route('admin.list_residents') }}" method="get">
    <button>search</button>
    <input type="text" name="resident_searchbox" id="resident_searchbox" :value="old('resident_searchbox')">
   
    <select name="resident_filter" id="resident_filter">

      <option value="any"> No filter </option>
      <option value="br.id"> ID </option>
      <option value="fullName"> Name</option>
      <option value="municipality"> Municipality </option>
      <option value="subdivision_district"> Subdivision </option>
      <option value="barangay"> Barangay </option>
      <option value="house_number"> House Number</option>
      <option value="ad.created_at"> Date Registered</option>

    </select>

    <select name="resident_sort" id="resident_sort">
      <option value="any"> Sort by </option>
      <option value="fullName"> Name</option>
      <option value="municipality"> Municipality </option>
      <option value="subdivision_district"> Subdivision </option>
      <option value="barangay"> Barangay </option>
      <option value="house_number"> House Number</option>
      <option value="Reg_date"> Date Registered</option>
    </select>
   
    </form>

    <button id="sort-button">⬆ Ascending</button>


  </div>

</div>

<div class="resident-container">

  <div class="resident-header">

    <div class="resident-detail">
       <p id="resident_regdate" class="detail-regdate">Registered on:</p>  
       <p id="resident_name" class="detail-name">Name</p>
       <p id="resident_address" class="detail-address">Address</p>
       
    </div>

    <div class="approval-container">
      <form action=""{{ route('admin.list_residents') }} method="get">
        <input  style="display: none" id="resident_uuid" name="resident_uuid" type="text">
        <button  type="submit">
          Proceed for Approval
        </button>
      </form>

    </div>

  </div>

  <div class="resident-table-container">
    <table class="resident-table" id="resident-table">
      <thead>
        <th>Registered on:</th>
        <th>Full Name</th>
        <th>Municipality</th>
        <th>Subdivision / District</th>
        <th>Barangay</th>
        <th>House Number</th>
      </thead>
       <tbody>

       </tbody>
    </table>
  </div>

</div>
</div>

<script>


  // Assume jsonData contains your JSON data
var jsonData = {!! $resident_jsonData !!};

// Get a reference to the table body
var tableBody = document.getElementById('resident-table').getElementsByTagName('tbody')[0];
let resident_id = document.getElementById('resident_uuid');
let resident_regdate = document.getElementById('resident_regdate');
let resident_name = document.getElementById('resident_name');
let resident_address = document.getElementById('resident_address');
let  reg = resident_regdate.textContent;
let  name = resident_name.textContent;
let  address = resident_address.textContent;

function fillTable(data) {
      
      
    // Iterate over each entry in the JSON data
    data.forEach(function(resident) {


    // Create a new table row
    var row = tableBody.insertRow();

          row.classList.add('resident-clickable-row');
          //assign an onclick function to the row:
          row.addEventListener('click', function() {
            
            // Handle row click event here
            resident_regdate.textContent = reg.concat(": ", resident.Reg_Date);
            resident_name.textContent = name.concat(": ",resident.fullName) ;
            resident_address.textContent  = address.concat(": ",resident.house_number, ", ", resident.barangay, ", ", resident.subdivision_district, ", ", resident.municipality);
            resident_id.value = resident.id;

          });

          // Insert cells into the row and populate them with data
          var cell1 = row.insertCell(0);
          cell1.innerHTML = resident.Reg_Date;

          var cell2 = row.insertCell(1);
          cell2.innerHTML = resident.fullName;

          var cell3 = row.insertCell(2);
          cell3.innerHTML = resident.municipality;

          var cell3 = row.insertCell(3);
          cell3.innerHTML = resident.subdivision_district;

          var cell3 = row.insertCell(4);
          cell3.innerHTML = resident.barangay;

          var cell3 = row.insertCell(5);
          cell3.innerHTML = resident.house_number;


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

let resident_sort = document.getElementById('resident_sort');
resident_sort.addEventListener("change", function () {
  
  tableBody.innerHTML = "";
  
  const resortedArray = jsonData;
  var filter = resident_sort.value;

  switch (filter) {
    case "fullName":
      resortedArray.sort((a, b) => 
        {
      if(a.fullName < b.fullName){
          return -1;
        }});
      break;

      case "municipality":
      resortedArray.sort((a, b) => 
        {
      if(a.municipality < b.municipality){
          return -1;
        }});
      break;

      case "subdivision_district":
      resortedArray.sort((a, b) => 
        {
      if(a.subdivision_district < b.subdivision_district){
          return -1;
        }});
      break;

      case "barangay":
      resortedArray.sort((a, b) => 
        {
      if(a.barangay < b.barangay){
          return -1;
        }});
      break;


      case "house_number":
      resortedArray.sort((a, b) => 
        {
      if(a.house_number < b.house_number){
          return -1;
        }});
      break;


      case "Reg_date":
      resortedArray.sort(function(a, b){return new Date(a.Reg_Date) - new Date(b.Reg_Date)});;
      break;
  
    default:
    resortedArray.sort((a, b) => 
        {
      if(a.fullName < b.fullName){
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
