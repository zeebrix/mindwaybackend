@include('admin.head')


<style>
.row {
  display: flex;
  flex-wrap: wrap; /* Allow columns to wrap on smaller screens */
  justify-content: center; /* Center the columns horizontally */
  gap: 20px; /* Add a gap between columns */
}

.column {
  flex: 1 1 calc(33% - 20px); /* Adjust width of each column with gap */
  height: 100px; /* Set desired height */
  padding: 20px;
  border-radius: 20px;
  display: flex; /* Use flexbox for icon and text placement */
  align-items: center; /* Align content vertically in the center */
  justify-content: center; /* Center content horizontally */
  /* Make columns rounded */
}

.column i {
  font-size: 36px;
  height: 30px; /* Adjust icon size as needed */
  color: black; /* Set icon color to black */
  margin-right: 20px; /* Add space between icon and text */
}

.column h1 {
  flex: 1;
  color: #688edc;
}

.column h3 {
  margin: 0;
  font-family: Inter;
  font-weight: 700;
  font-size: 20px;
}

/* Customize colors for each column */
.column:nth-child(1) {
  background-color: white; /* Light red */
}

.column:nth-child(2) {
  background-color: white; /* Light blue */
}

.column:nth-child(3) {
  background-color: white; /* Light green */
}

/* Add media query for smaller screens */
@media (max-width: 768px) {
  .column {
    flex: 1 0 100%; /* Make columns stack on top of each other for narrow screens */
    margin-bottom: 20px; /* Add space between stacked columns */
  }
}

.button {
      display: inline-block;
      padding: 10px 20px;
      background-color: #E8F1FF; /* Set background color to transparent */
        /* Set border to black */
      color: #000; /* Set text color to black */
      text-decoration: none; /* Remove default underline */
      font-size: 16px;
      font-weight: bold;
    }

    .button:hover {
      background-color: #000; /* Change background color on hover */
      color: #fff;
        /* Change text color on hover */
    }
    .custom-button {
    width: 280px; /* Adjust the width as needed */
    height: 70px;
    border-radius:20px;
    margin-left: -18px;
    margin-top:10px; /* Adjust the height as needed */
    /* Add any other styles as needed */
}
.rounded-pill {
    border-radius: 20px;
    
}
.modal-dialog {
    max-width: 850px; /* Adjust the max-width as needed */
  }

  .table thead th {
    background-color: gray; /* Setting background color of table header */
    color: white; /* Setting text color of table header */
  }
  .button-box {
    width: 600px; /* Initial width */
    margin: 35px auto;
    position: relative;
    border-radius: 30px;
    background: #fff;
    display: flex;
    justify-content: space-around;
}

.toggle-btn {
    padding: 0; /* Remove padding */
    cursor: pointer;
    background: transparent;
    border: 0;
    outline: none; /* Remove default focus outline */
    position: relative;
    text-align: center;
    flex: 1;
    transition: .3s;
    height: 50px; /* Fixed height for buttons */
    line-height: 50px; /* Adjust line height to center text vertically */
    color: #000; /* Set text color to black */
}

.toggle-btn h4 {
    margin-left: 1px; /* Add left margin to the text */
    margin: 0; /* Remove default margin for h4 */
}

.toggle-btn:focus {
    outline: none; /* Remove the outline */
}

#btn {
    left: 0;
    top: 0;
    position: absolute;
    width: 50%;
    height: 100%;
    background: #6C8EDB; /* Change button color to white */
    border-radius: 30px;
    transition: .5s;
}

@media screen and (max-width: 768px) {
    .button-box {
    width: 300px; /* Initial width */
        
    }
 
}

.clicked h4 {
    color: black;
}

.clicked h4 {
    color: white;
}


</style>
@php
use App\Models\CustomreBrevoData;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerRelatedProgram;
use App\Models\Program;
use App\Models\Session;
// Get the ID of the authenticated user
$userId = Auth::guard('programs')->id();
$Program = Auth::guard('programs')->user();
$programs = Program::where('id', $userId)->get();
$newUserCount = Session::where('new_user', 'Yes')
    ->where('program_id', $userId)
    ->count();
//echo "New User Count: " . $newUserCount . "<br>";

$customerIds = CustomerRelatedProgram::where('program_id', $userId)->pluck('customer_id')->unique()->toArray();

// Retrieve all customers based on the obtained IDs using the relationship
$totalCustomers = CustomreBrevoData::whereIn('id', $customerIds)->get();

$count = count($Program->customers);
//echo "App User: " . $count . "<br>";
// echo $count;
$customerCount = $Program->customers->count();
$totalCount = $customerCount + $newUserCount; // Sum of customer count and new user count


$sum= $count+$newUserCount;
$totalCustomersCount = count($totalCustomers);
//echo "EAP USER: " . $totalCustomersCount . "<br>";
if ($totalCustomersCount != 0) {
    $customerPercentage = ($sum / $totalCustomersCount) * 100;
    $customerPercentage = number_format($customerPercentage, 1); // Format to one decimal place
    //echo "percentage: " .   $customerPercentage . "<br>";
} else {
    $customerPercentage = 0; // or any default value you prefer
}

//echo "Customer Percentage: " . $customerPercentage . "%";





@endphp
<h1 style="display: inline-block;  margin-top: 7px;
  font-family:Inter;
  font-weight:700; 
  font-size:30px;">EAP Statistics For  @foreach ($programs as $program)
    {{ $program->company_name }}
@endforeach
<span style="font-size: 16px; color: gray;">Updated Just Now</span></h1>
<div class="row" style="margin-top: 1opx">
  <div class="column">
    <i class="fas fa-key"></i>  
    <div>
      <h1>
        @foreach ($programs as $program)
    {{ $program->max_lic }}
@endforeach 
</h1>
      <h3>Eligible Licences</h3>
    </div>
  </div>
  <div class="column">
    <i class="fas fa-users"></i>  
    <div>
      <h1>{{ $totalCustomersCount }}</h1>
      <h3>Employee Added to EAP</h3>
    </div>
  </div>
  <div class="column">
  <i class="fas fa-solid fa-award"></i>

    <div>
      <h1>{{ $customerPercentage }}%</h1>
      <h3>Staff Utilise Program</h3>
    </div>
  </div>
</div>
<!-- Manage Staff Section -->

<h1 style="display: inline-block; margin-top: 10px; margin-bottom: 0; font-family: Inter; font-weight: 700; font-size: 30px;">Manage Staff</h1>
<div class="row" style="display: flex; justify-content: space-between;">
    <div class="form-box">
        <div class="button-box">
            <div id="btn"></div>
            <button type="button" class="toggle-btn" onclick="leftClick()" id="leftButton"><h4>Add & Manage All Staff</h4></button>
            <button type="button" class="toggle-btn" onclick="rightClick()" id="rightButton"><h4>Staff Utilised</h4></button>
        </div>
    </div>

    <div class="column" style="background-color: #E8F1FF; padding-left: 10px;">
    <div style="display: flex; align-items: center;">
        <a href="#" class="button" data-toggle="modal" data-target="#exampleModal" style="white-space: nowrap;">Add Individual</a>
        <span style="margin: 0 5px;">||</span>
        <a href="#" class="button" data-toggle="modal" data-target="#uploadModal" style="white-space: nowrap;">Add in Bulk</a>
    </div>
</div>

</div>

<div id="hiddenDiv" style="display: flex; align-items: center;">
  <span style="margin-right: 20px; cursor: pointer; font-size:20px; font-weight:700;">
    <a href="#" onclick="showAppUser()" id="showAppUserBtn" style="color: #6C8EDB; text-decoration: none;">App Users</a>
  </span> 
  <span style="cursor: pointer; font-size:20px; font-weight:700;">
    <a href="#" onclick="showTable()" id="showTableBtn" style="color: black; text-decoration: none;">Counselling Sessions</a>
  </span>
</div>

 
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">

      <h1 style="display: inline-block;  margin: 0;
  font-family:Inter;
  font-weight:700; 
  font-size:30px;" class="modal-title" id="uploadModalLabel">Please upload a correctly formatted spreadsheet</h1>      
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <p><strong>*Ensure there are no other column with other information*</strong></p>
      <table style="background-color: #f2f2f2; border-radius: 10px; width: 100%; border-collapse: collapse;">
  <thead>
    <tr style="background-color: #333; color: white;">
      <th style="padding: 8px; font-weight: bold; border-bottom: 1px solid #ddd;">ID</th>
      <th style="padding: 8px; font-weight: bold; border-bottom: 1px solid #ddd;">Name</th>
      <th style="padding: 8px; font-weight: bold; border-bottom: 1px solid #ddd;">Email</th>
    </tr>
  </thead>
  <tbody>
    <tr style="border-bottom: 1px solid #ddd;">
      <td style="padding: 8px;">1</td>
      <td style="padding: 8px;">John Doe</td>
      <td style="padding: 8px;">john@example.com</td>
    </tr>
    <tr style="border-bottom: 1px solid #ddd;">
      <td style="padding: 8px;">2</td>
      <td style="padding: 8px;">Jane Smith</td>
      <td style="padding: 8px;">jane@example.com</td>
    </tr>
    <tr style="border-bottom: 1px solid #ddd;">
      <td style="padding: 8px;">3</td>
      <td style="padding: 8px;">Michael Johnson</td>
      <td style="padding: 8px;">michael@example.com</td>
    </tr>
  </tbody>
</table>
  <form id="uploadForm"  enctype="multipart/form-data">
    @csrf
    <div class="form-group">
      <label for="excelFile">Choose Excel File:</label>
      <input type="file" class="form-control-file" id="excelFile" name="excelFile" >
    </div>
    <button type="submit" class="btn btn-primary rounded-pill" id="uploadUsersBtn">Upload Spread Sheet</button>
  </form>
</div>

    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      <h1 style="display: inline-block;  margin: 0;
  font-family:Inter;
  font-weight:700; 
  font-size:30px;" class="modal-title" id="exampleModalLabel">Add Employee To EAP</h1>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="dataForm" method="POST" action="{{ route('saveData') }}">
          @csrf
          <div class="form-group">
            <label for="name">Enter Employee Name:</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Employee Name">
          </div>
          <div class="form-group">
            <label for="email">Enter Employee Email:</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Employee Email">
          </div>
          <button type="submit" class="btn btn-primary rounded-pill float-right">Add Employee</button>

        </form>
      </div>
    </div>
  </div>
</div>

 <!-- Modal to display response data -->
<div class="modal fade" id="uploadModal1" tabindex="-1" role="dialog" aria-labelledby="uploadModal1Label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      <h1 style="display: inline-block;  margin: 0;
  font-family:Inter;
  font-weight:700; 
  font-size:30px;" class="modal-title" id="exampleModalLabel">Confirm Bulk Upload</h1>
      
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form id="removeForm" action="{{ route('uploadUsers') }}" method="post">
    @csrf
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="uploadedDataTable">
            <!-- Dynamically generated table rows will go here -->
        </tbody>
    </table>
    <button type="submit" class="btn btn-primary rounded-pill ">Add Bulk List OF Employee</button>

</form>

</div>

    </div>
  </div>
</div>
 
    
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
 
 

 <script>
$(document).ready(function() {
    // Attach event listener to the delete buttons outside the AJAX success callback
    $(document).on('click', '.removeBtn', function() {
        var $closestTr = $(this).closest('tr');
        if ($closestTr.length) {
            $closestTr.remove();
        }
    });

    $('#uploadUsersBtn').click(function(e) {
        e.preventDefault(); // Prevent default form submission
        $('#uploadModal').modal('hide');

        var formData = new FormData($('#uploadForm')[0]);

        $.ajax({
            url: "{{ route('processExcel') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#uploadModal1').modal('show');
                    $('#uploadedDataTable').empty();

                    var newRows = '';
$.each(response.data, function(index, item) {
  // Build the row string
  newRows += '<tr>' +
              '<td>' + (index + 1) + '</td>' +
              '<td><input type="text" class="form-control" name="name[]" value="' + item.name + '" readonly style="background-color: white; border: none;"></td>' +
              '<td><input type="email" class="form-control" name="email[]" value="' + item.email + '" readonly style="background-color: white; border: none;"></td>' +
              '<td><button type="button" class="btn btn-danger removeBtn" data-id="' + item.id + '"><i class="fas fa-trash-alt"></i></button></td>' +
              '</tr>';
});
$('#uploadedDataTable').append(newRows);


                    $('#excelFile').val('');
                    console.log(response);
                } else {
                    alert(response.message);
                }
            },
            error: function(error) {
                console.error(error);
                alert("An error occurred while processing the file. Please try again.");
            }
        });
    });
});

</script>
<script>
    var btn = document.getElementById('btn');
    var asif = document.getElementById('utilisedprogram');
    window.onload = function() {
        leftClick();
    };
    // function leftClick() {
    //     btn.style.left = '0';
    //     staffprogram.style.display = 'block';
    //     utilisedprogram.style.display = 'none';
    //     var leftButton = document.getElementById('leftButton');
    // var rightButton = document.getElementById('rightButton');
    
    // leftButton.classList.add('clicked');
    // rightButton.classList.remove('clicked');
    // }

    // function rightClick() {
    //     btn.style.left = '50%';
    //     staffprogram.style.display = 'none';
    //     utilisedprogram.style.display = 'block';
    //     var leftButton = document.getElementById('leftButton');
    // var rightButton = document.getElementById('rightButton');
    
    // leftButton.classList.remove('clicked');
    // rightButton.classList.add('clicked');
    // }
      function leftClick() {
        // Access the hidden div
  var hiddenDiv = document.getElementById("hiddenDiv");

// Show the div on right-click
hiddenDiv.style.display = 'none';
        btn.style.left = '0';
        staffprogram.style.display = 'block';
        utilisedprogram.style.display = 'none';  
        var leftButton = document.getElementById('leftButton');
    var rightButton = document.getElementById('rightButton');
    var Staff = document.getElementById('staffprogram');
Staff.style.display = 'none';
var utilisedprogramDiv = document.getElementById("staffprogram");
    utilisedprogramDiv.style.display = 'block'; // Show the table
    // showAppUser();
    leftButton.classList.add('clicked');
    rightButton.classList.remove('clicked');
    var tableDiv = document.getElementById("session");
  tableDiv.style.display = 'none';
  document.getElementById("showAppUserBtn").style.color = "#688EDE"; // Set color to bold
    document.getElementById("showTableBtn").style.color = "black"; // Set color to black
 
    }

    function rightClick() {
  btn.style.left = '50%';
  staffprogram.style.display = 'none';
  utilisedprogram.style.display = 'block';

  var leftButton = document.getElementById('leftButton');
  var rightButton = document.getElementById('rightButton');

  leftButton.classList.remove('clicked');
  rightButton.classList.add('clicked');

  // Access the hidden div
  var hiddenDiv = document.getElementById("hiddenDiv");

  // Show the div on right-click
  hiddenDiv.style.display = 'block';
   // Hide the session table
   var tableDiv = document.getElementById("session");
  tableDiv.style.display = 'none';
}

function showTable() {
    var tableDiv = document.getElementById("session");
    tableDiv.style.display = 'block'; // Show the table
    // Hide the element with the class 'utilisedprogram'
    var utilisedprogramDiv = document.getElementById("utilisedprogram");
    utilisedprogramDiv.style.display = 'none'; // Hide the table

    // Update button styles
    document.getElementById("showTableBtn").style.color = "#688EDE"; // Set color to bold
    document.getElementById("showAppUserBtn").style.color = "black"; // Set color to black
  }

  function showAppUser() {
    var utilisedprogramDiv = document.getElementById("utilisedprogram");
    utilisedprogramDiv.style.display = 'block'; // Show the table

    // Hide the element with the class 'session'
    var sessionDiv = document.getElementById("session");
    sessionDiv.style.display = 'none'; // Hide the table

    // Update button styles
    document.getElementById("showAppUserBtn").style.color = "#688EDE"; // Set color to bold
    document.getElementById("showTableBtn").style.color = "black"; // Set color to black
  }
    
</script>

