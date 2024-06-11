@extends('administrator\layout')
 
@section('content')

<div class="content-container">

  <div class="content-header">

  <h3 id="page-header">Manage Collections</h3>

  <div class="searchbar">
    <form action="{{ route('admin.list_collections') }}" method="get">
    <button>search</button>
    <input type="text" name="searchbox" id="searchbox" :value="old('resident_searchbox')">
   
    <select name="filter" id="filter">

      <option value="any"> No filter </option>
      <option value="reqs.id"> Request ID </option>
      <option value="resident.id"> Requestee ID </option>
      <option value="brgy_res.name"> Requestee Name</option>
      <option value="brgy_off.id"> Barangay Officer ID  </option>
      <option value="brgy_off.name"> Barangay Officer Name </option>
      <option value="document"> Requested Document </option>

    </select>

    <select name="resident_sort" id="resident_sort">
      <option value="req_code"> Request Code </option>
      <option value="res_name"> Requestee Name</option>
      <option value="off_name"> Barangay Officer Name  </option>
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
       <p id="conDate" class="detail-regdate">Confirmed on:</p>  
       <p id="schedDate" class="detail-regdate">Collection Scheduled on:</p>  
       <p id="colDate" class="detail-regdate">Collected on:</p>  
       <p id="name" class="detail-name">Requested by:</p>
       <p id="documents" class="detail-address">Requested Documents:</p>
       
    </div>

    <div class="approval-container">
      <form action= "{{ route('admin.view_collection') }}" method="get">
        <input  style="display: none" id="collection_id" name="collection_id" type="text">
        <button  type="submit">
          Proceed for Approval
        </button>
      </form>

    </div>
  </div>

  <div class="request-status-container">
    
    <div class="request-status-switcher">
      <div class="request-status-item" onclick='filterStatus("TBC")'>
        Pending
      </div>
      <div class="request-status-item" onclick='filterStatus("COL")'>
        Collected
      </div>
      <div class="request-status-item" onclick='filterStatus("CAN")'>
        Cancelled
      </div>
    </div>

  </div>

  </div>

  <div class="resident-table-container">
    <table class="resident-table" id="resident-table">
      <thead>
        <th>Request code</th>
        <th>Requested by</th>
        <th>Requested documents</th>
        <th>Confirmed On</th>
        <th>Scheduled On</th>
        <th class="barangay-officer">Issued by</th>
        <th class="barangay-officer">Collected on</th>
        
      </thead>
       <tbody>

       </tbody>
    </table>
  </div>

</div>
</div>

<script>


  // Assume jsonData contains your JSON data
var json  = JSON.stringify({!! $collections_jsonData!!});
var jsonData = JSON.parse(json);
var Status = "TBC";
console.log(jsonData);
// Get a reference to the table body
var tableBody = document.getElementById('resident-table').getElementsByTagName('tbody')[0];
let collection_id = document.getElementById('collection_id');
let pageheader = document.getElementById('page-header');
let collection_confirmDate= document.getElementById('conDate');
let collection_scheduleDate = document.getElementById('schedDate');
let collection_collectDate = document.getElementById('colDate');
let requestee_name = document.getElementById('name');
let summmarized_reqDocument_list = document.getElementById('documents');
let  con = collection_confirmDate.textContent;
let  sched = collection_scheduleDate.textContent;
let  col = collection_collectDate.textContent;
let  name = requestee_name.textContent;
let  document_list = summmarized_reqDocument_list.textContent;


function filterStatus(status) {
  
  switch (status) {
    case "TBC":
      Status = status;
      pageheader.textContent = "Pending collections"
      break;
  
      case "COL":
      Status = status;
      pageheader.textContent = "Completed collections"
      break;

      case "CAN":
      Status = status;
      pageheader.textContent = "Cancelled collections"
      break;

    default:
      pageheader.textContent = "Pending Collections"
      break;
  }

  tableBody.innerHTML = "";
  fillTable(jsonData, status);

}

function fillTable(data, status) {
      
      
    // Iterate over each entry in the JSON data
    data.forEach(function(collection) {

      console.log("statuses:");
      console.log(collection.Status + " " + status);

      if (status == collection.Status) {
       

        requestedDocuments =  collection.requested_doc;
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
            collection_confirmDate.textContent = con.concat(": ", collection.confirmed_on);
            collection_scheduleDate .textContent = sched.concat(": ", collection.scheduled_on);
            collection_collectDate.textContent = col.concat(": ", collection.collected_on); 
            requestee_name.textContent = name.concat(": ",collection.Requestee) ;
            summmarized_reqDocument_list.textContent = document_list.concat(":", document_items );
            collection_id.value = collection.id;
          });

          // Insert cells into the row and populate them with data


          var cell1 = row.insertCell(0);
          cell1.innerHTML = collection.Request_code;
          
          var cell2 = row.insertCell(1);
          cell2.innerHTML = collection.Requestee;

          var cell3 = row.insertCell(2);
          cell3.innerHTML = document_items;

          var cell4 = row.insertCell(3);
          cell4.innerHTML = collection.confirmed_on;

          var cell5 = row.insertCell(4);
          cell5.innerHTML = collection.scheduled_on;
 
          var cell6 = row.insertCell(5);
          cell6.innerHTML = collection.Issued_by;
 
          var cell7 = row.insertCell(5);
          cell6.innerHTML = collection.collected_on;
 

      }
     
          });

    }

    fillTable(jsonData, "TBC");


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

      case "res_name":
      resortedArray.sort((a, b) => 
        {
      if(a.Requestee < b.Requestee){
          return -1;
        }});
      break;

      case "off_name":
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
      if(a.name < b.name){
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
