@extends('administrator\layout')
 
@section('content')

<div class="content-container">

  <div class="content-header">

  <h3 id="page-header">Notifications</h3>

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
 

  <div class="request-status-container">
    
    <div class="request-status-switcher">
      <div class="request-status-item" onclick='filterStatus(0)'>
        New
      </div>
      <div class="request-status-item" onclick='filterStatus(1)'>
        Read
      </div>
      <div class="request-status-item" onclick='filterStatus(2)'>
        All
      </div>
    </div>

  </div>

  </div>

  <div class="resident-table-container">
    <table class="resident-table" id="resident-table">
      <thead>
        </thead>
       <tbody>

       </tbody>
    </table>
  </div>

</div>
</div>

<script>


  // Assume jsonData contains your JSON data
var json  = JSON.stringify({!! $notifs_jsonData!!});
var jsonData = JSON.parse(json);
var status = 0;
console.log(jsonData);
// Get a reference to the table body
var tableBody = document.getElementById('resident-table').getElementsByTagName('tbody')[0];
let request_id = document.getElementById('request_id');
let pageheader = document.getElementById('page-header');


function filterStatus(status) {
  
  switch (status) {
    case 0:
      Status = status;
      pageheader.textContent = "New notifications"
      break;
  
      case 1:
      Status = status;
      pageheader.textContent = "Read Notifications"
      break;

      case 2:
      Status = status;
      pageheader.textContent = "All Notifications "
      break;

    default:
      pageheader.textContent = "Notifications"
      break;
  }

  tableBody.innerHTML = "";
  fillTable(jsonData, status);

}

function fillTable(data, status) {


    console.log("status is: " + status );
    var i = 0;    
    // Iterate over each entry in the JSON data
    data.forEach(function(notification) {
      
      if (status == notification.readStatus || status == 2 ) {
         i++;
    // Create a new table row
    var row = tableBody.insertRow();

          row.classList.add('resident-clickable-row');
          //assign an onclick function to the row:
          row.addEventListener('click', function () {
            notifShortCut(notification.id, notification.eventType, notification.eventID, notification.readStatus)
          })

          // Insert cells into the row and populate them with data

          var cell1 = row.insertCell(0);
          cell1.innerHTML = notification.eventDesc + " " + notification.eventType + " From: " + notification.senderName; ;
  

          if (!notification.readStatus) {
      console.log("unread");
      row.classList.add('notifications-unread');            
          } else {
      console.log("read");
      row.classList.remove('notifications-unread');            
          }

      }
          });

          if (i==0) {
    var row = tableBody.insertRow();
    var cell1 = row.insertCell(0);

    var message; 

    switch (status) {
      case 0:
        message = "You have no new notifications."
        break;
      
      case 1:
        message = "You have no read notifications."
        break;

        default: 
        message = "You have no notifications"
        break;
    }

    cell1.innerHTML = message ;
        
      }

    }

    fillTable(jsonData, 0);


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

  function notifShortCut(notifId, eventType, eventID, readStatus) {
 
    $.ajax({
        url: "{{ route('admin.check_notification') }}",
        type: "GET",
        data: {
            id: notifId,
            eventId: eventID,
            eventType: eventType,
            readStatus: readStatus
        },
        success: function(response) {
            console.log(response); // Log the response to see what is being returned

            if (response.route) {
                window.location.replace(response.route);
            } else if (response.status) {
                console.log("Status: " + response.status);
                // Handle other statuses if needed
            } else {
                console.error("Unexpected response format");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error sending data to server:", error);
        }
    });
    }
 
             



</script>

@endsection
