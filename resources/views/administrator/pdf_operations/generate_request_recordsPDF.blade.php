<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
</head>
<body>
    

    <p>{{ $title }}</p>
    <p>{{ $date }}</p>

    <div class="resident-table-container">
    <table class="resident-table">
        <thead>
            <th>Date Requested</th>
            <th>Requester Name </th>
            <th>Approved by: </th>
            <th>Date Approved</th>
        </thead>
        <tbody>
             @foreach ( $requests as $request)

             <tr>
                <td>{{ $request['Requested_on'] }}</td>
                <td>{{ $request['Requestee'] }}</td>
                <td>{{ $request['Responded_by'] }}</td>
                <td>{{ $request['Responded_on'] }}</td>
             </tr>



            @endforeach
    
        </tbody>
        
    </table>
</div>


</body>
</html>