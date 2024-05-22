@extends('administrator\layout')
 
@section('content')

<div class="content-container">

  <div class="content-header">

  <h3 id="page-header">Manage Residents</h3>

  <div class="searchbar">
    <form action="{{ route('admin.list_requests') }}" method="get">
    <button>search</button>
    <input type="text" name="searchbox" id="searchbox" :value="old('resident_searchbox')">
   
    <select name="filter" id="filter">

      <option value="any"> No filter </option>
      <option value="reqs.id"> Request ID </option>
      <option value="resident.id"> Requestee ID </option>
      <option value="brgy_res.fullName"> Requestee Name</option>
      <option value="brgy_off.id"> Barangay Officer ID  </option>
      <option value="brgy_off.fullName"> Barangay Officer Name </option>
      <option value="document"> Requested Document </option>

    </select>

    <select name="resident_sort" id="resident_sort">
      <option value="req_code"> Request Code </option>
      <option value="res_fullName"> Requestee Name</option>
      <option value="off_fullName"> Barangay Officer Name  </option>
      <option value="resp_date"> Date Requested </option>
      <option value="req_date"> Date Responded </option>

    </select>
   
    </form>

    <button id="sort-button">⬆ Ascending</button>


  </div>

</div>

<div class="resident-container">

  <div class="request-header">
    <div class="request-details-box">
    <div class="request-detail">
       <p id="regdate" class="detail-regdate">Requested on:</p>  
       <p id="name" class="detail-name">Requested by:</p>
       <p id="documents" class="detail-address">Requested Documents:</p>
       
    </div>

    <div class="approval-container">
      <form action= "{{ route('admin.view_request') }}" method="get">
        <input  style="display: none" id="request_id" name="request_id" type="text">
        <button  type="submit">
          Proceed for Approval
        </button>
      </form>

    </div>
  </div>

  <div class="request-status-container">
    
    <div class="request-status-switcher">
      <div class="request-status-item" onclick='filterStatus("PEN")'>
        Pending
      </div>
      <div class="request-status-item" onclick='filterStatus("APR")'>
        Approved
      </div>
      <div class="request-status-item" onclick='filterStatus("REJ")'>
        Rejected
      </div>
    </div>

  </div>

  </div>

  <div class="resident-table-container">
    <table class="resident-table" id="resident-table">
      <thead>
        <th>Request code</th>
        <th>Requested on</th>
        <th>Requested by</th>
        <th>Requested documents</th>
        <th class="barangay-officer">Responded by</th>
        <th class="barangay-officer">Responded on</th>
      </thead>
       <tbody>

       </tbody>
    </table>
  </div>

</div>
</div>

<script>


  // Assume jsonData contains your JSON data
var json  = JSON.stringify({!! $requests_jsonData!!});
var jsonData = JSON.parse(json);
var Status = "PEN";
console.log(jsonData);
// Get a reference to the table body
var tableBody = document.getElementById('resident-table').getElementsByTagName('tbody')[0];
let request_id = document.getElementById('request_id');
let pageheader = document.getElementById('page-header');
let request_regdate = document.getElementById('regdate');
let requestee_name = document.getElementById('name');
let summmarized_reqDocument_list = document.getElementById('documents');
let  reg = request_regdate.textContent;
let  name = requestee_name.textContent;
let  document_list = summmarized_reqDocument_list.textContent;


function filterStatus(status) {
  
  switch (status) {
    case "PEN":
      Status = status;
      pageheader.textContent = "Pending requests"
      break;
  
      case "APR":
      Status = status;
      pageheader.textContent = "Approved requests"
      break;

      case "REJ":
      Status = status;
      pageheader.textContent = "Rejected requests"
      break;

    default:
      pageheader.textContent = "Manage requests"
      break;
  }

  tableBody.innerHTML = "";
  fillTable(jsonData, status);

}

function fillTable(data, status) {
 // console.log(data);
      
      
    // Iterate over each entry in the JSON data
    data.forEach(function(request) {

      if (status == request.Status) {
        

        requestedDocuments =  request.requested_doc;
      var document_items = "";      
      requestedDocuments.forEach(element => {
      document_items =  document_items.concat(" | ", element.docName," | ");

      });


    // Create a new table row
    var row = tableBody.insertRow();

          row.classList.add('resident-clickable-row');
          //assign an onclick function to the row:
          row.addEventListener('click', function() {
            
            // Handle row click event here
            request_regdate.textContent = reg.concat(": ", request.Requested_on);
            requestee_name.textContent = name.concat(": ",request.Requestee) ;
            summmarized_reqDocument_list.textContent = document_list.concat(":", document_items );
            request_id.value = request.id;
          });

          // Insert cells into the row and populate them with data


          var cell1 = row.insertCell(0);
          cell1.innerHTML = request.Request_code;
          
          var cell2 = row.insertCell(1);
          cell2.innerHTML = request.Requested_on;

          var cell3 = row.insertCell(2);
          cell3.innerHTML = request.Requestee;

          var cell4 = row.insertCell(3);
          cell4.innerHTML = document_items;

          var cell5 = row.insertCell(4);
          cell5.innerHTML = request.Responded_by;
 
          var cell6 = row.insertCell(5);
          cell6.innerHTML = request.Responded_on;
 

      }
     
          });

    }

    fillTable(jsonData, "PEN");


let sort_order_button = document.getElementById('sort-button');

sort_order_button.addEventListener('click', function sorted() {
  
  tableBody.innerHTML = "";
  
  const reversedArray = jsonData.reverse();
  
    fillTable(reversedArray, Status);
  
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
    case "req_code":
      resortedArray.sort((a, b) => 
        {
      if(a.Request_code < b.Request_code){
          return -1;
        }});
      break;

      case "res_fullName":
      resortedArray.sort((a, b) => 
        {
      if(a.Requestee < b.Requestee){
          return -1;
        }});
      break;

      case "off_fullName":
      resortedArray.sort((a, b) => 
        {
      if(a.Responded_by < b.Responded_by){
          return -1;
        }});
      break;

      case "req_date":
      resortedArray.sort(function(a, b){return new Date(a.Requested_on) - new Date(b.Requested_on)});;
      break;

      case "resp_date":
      resortedArray.sort(function(a, b){return new Date(a.Responded_on) - new Date(b.Responded_on)});;
      break;

  
    default:
    resortedArray.sort((a, b) => 
        {
      if(a.fullName < b.fullName){
          return -1;
        }});
      break;
  }

 

    // console.log(Status);
    fillTable(resortedArray, Status);

 
    sort_order_button.textContent = "⬆ Ascending";
  

});




</script>

@endsection
