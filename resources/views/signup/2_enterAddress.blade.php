@extends('signup.layout')

@section('content')

<h3>Sign up step 2!</h3>


<div class="SignInBox"
>
    <br>



    <script>
        function populateDropdown() {

            let municipalities = document.getElementById('municipality');
            let selectedIndex = municipalities.selectedIndex;
            let barangayDropdown = document.getElementById('barangays');

            if(selectedIndex > 0){

            // Array of sample barangay names
            let barangays = [
            ['Barangay01', 'Barangay02', 'Barangay03', 'Barangay04', 'Barangay05', 'Barangay06', 'Barangay07', 'Barangay08'],
            ['Barangay11', 'Barangay12', 'Barangay13', 'Barangay14', 'Barangay15', 'Barangay16', 'Barangay17', 'Barangay18'],
            ['Barangay21', 'Barangay22', 'Barangay23', 'Barangay24', 'Barangay25', 'Barangay26', 'Barangay27', 'Barangay28'],
            ['Barangay31', 'Barangay32', 'Barangay33', 'Barangay34', 'Barangay35', 'Barangay36', 'Barangay37', 'Barangay38']
        ];
            
            // Get the dropdown list
           

            // Clear existing options
            barangayDropdown.innerHTML = '';

            // Create and append new options
            let barangayTitle = "Barangays" + ""+ selectedIndex;
            barangays[selectedIndex-1].forEach(function(barangay) {
                let option = document.createElement('option');
                option.value = barangay.toLowerCase(); // Set the value
                option.text = barangay; // Set the text
                barangayDropdown.appendChild(option); // Append to the dropdown
            });}

            else {
             
                barangayDropdown.innerHTML = '';
                
            }

        }

        function test(){

        
        }

      
    </script>

    <form class="loginform" action="{{ route('signup.3_enterLogIn') }}"  >
        <label for="colors">Select a Municipality:</label> <br>
        <select id="municipality" name="municipality" onchange="populateDropdown()"  > 
            <option value="baseSelect">-Select a Municipality-</option>
        
            <option value="red">Red</option>
            <option value="green">Green</option>
            <option value="blue">Blue</option>
            <option value="yellow">Yellow</option>
        </select>
        <br><br>
    
     @error('municipality')
     <div>{{ $message }}</div>
    @enderror

    <label for="colors">Select a Barangay:</label> <br>
    <select id="barangays" name="barangays" onchange="test()" >
      
    </select>
    <br><br>

    @error('barangays')
    <div>{{ $message }}</div>
    @enderror
     
    <input type="text" name="subdivision" id="subdivision" placeholder="Enter your subdivision / District"><br> 
    @error('subdivision')
    <div>{{ $message }}</div>
    @enderror

     <input type="text" name="houseNumber" id="houseNumber" placeholder="Enter Street / House Number"><br> 
     @error('houseNumber')
     <div>{{ $message }}</div>
     @enderror
     

     <input type="number" name="phoneNumber" id="phoneNumber" placeholder="Enter your mobile number"><br> 
     @error('phoneNumber')
     <div>{{ $message }}</div>
     @enderror
     

     <button type="submit" name="login" class="btn btn-primary btn-lg" >To Next step!</button>
    </form> <br>
 
     </div>
<

@endsection