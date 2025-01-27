<!DOCTYPE html>
<html lang="en">

{{-- head add --}}

@include('admin.head')
<style>
  .main-panel {
    transition: width 0.25s ease, margin 0.25s ease;
    width: calc(100% - 236px);
    min-height: calc(100vh - 4.625rem);
    display: -webkit-flex;
    display: flex;
    -webkit-flex-direction: column;
    flex-direction: column;
    align-items: center;
    margin: 0 auto;
    
}
.pro-details {
    margin: auto;
    width: 50%;
    padding: 30px 10px;}
    h5.lic_code {
      margin: 15px;
}
    h5.lic_code span {
    background-color: #007bff;
    padding: 5px;
    border-radius: 9px;
    color: #fff;
}
h2.lic_precent {
    color: #844fc1;
    margin: 0;
}
 /* Container for the search bar */
.search-container {
    display: flex;
    justify-content: flex-start; /* Align input to the left */
    align-items: center;
    width: 100%;
    padding: 10px;
    position: relative; /* Necessary for positioning the icon */
}

/* Styling the search input */
.search-container input[type="search"] {
    width: 300px; /* Adjust the width as needed */
    padding: 10px 20px 10px 50px; /* Add padding to the left for the icon */
    border: 2px solid #ccc; /* Border color */
    border-radius: 25px; /* Rounded corners */
    outline: none; /* Remove default outline */
    font-size: 16px; /* Font size */
    transition: all 0.3s ease; /* Smooth transition for focus effect */
}

/* Focus state for the search input */
.search-container input[type="search"]:focus {
    border-color: #007BFF; /* Border color on focus */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Shadow effect on focus */
}

/* Optional: Adding a placeholder styling */
.search-container input[type="search"]::placeholder {
    color: #aaa; /* Placeholder text color */
    font-style: italic; /* Italic placeholder text */
}

/* Search icon inside the input field */
.search-container .search-icon {
    position: absolute;
    left: 10px; /* Adjust the position as needed */
    font-size: 20px; /* Increased font size */
    color: #aaa;
    margin-left: 10px; /* Added more margin to the left */
}

/* Example style for the search icon (font awesome) */
.search-container .search-icon::before {
    content: '\f002'; /* Font Awesome search icon code */
    font-family: 'FontAwesome';
}

input[type="date"], input[type="text"] {
    border-radius: 10px; /* Slightly rounded corners */
    width: 100%; /* Full width within the column */
    padding: 10px; /* Add padding for better appearance */
    border: 2px solid #ccc;
    margin: 10px 0;
}

input[type="date"]::placeholder, input[type="text"]::placeholder {
    text-align: center;
}

.form-check-input {
    width: 30px;
    height: 20px;
    margin-left: 0.5rem;
}

.form-check-label {
    margin-left: 1rem;
}

.additional-reasons {
    margin-left: 2rem;
}

.additional-reasons input {
    margin-bottom: 0.5rem;
}

</style>
<body>

  <div class="container-scroller" style="background-color: #E8F1FF;">
    <!-- partial:partials/_navbar.html -->

    {{-- admin header add --}}
    

       <div class="container-fluid page-body-wrapper" style="background-color: #E8F1FF;">
      <!-- partial:partials/_settings-panel.html -->
      {{-- skins color add --}}
        @include('admin.skins-color')
 

<div class="container-fluid page-body-wrapper" style="background-color: #E8F1FF;">
<!-- partial:partials/_settings-panel.html -->
{{-- skins color add --}}
 @include('admin.skins-color')



{{-- add side bar --}}

@include('program.sidebar')


      

      


      <div class="main-panel" style="background-color: #E8F1FF;">
    

<!-- Your upload form goes here -->


        <div class="content-wrapper">
        @include('program.headerbody')

            @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
                @endif
                @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">{{$error}}</div>
            @endforeach
        @endif

     <!-- Assuming this is your Blade view file -->
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif



<!-- Your upload form goes here -->

          

        <!-- <div class="pro-details" style="text-align: CENTER;">
            <div class="pro-logo"><img height="100px"
                            src="{{ asset('storage/logo') }}/{{ $Program->logo }}"
                            alt=""></div>
                          
            <h4 >{{ $Program->company_name }} Program</h4>
            <h5 class="lic_code"><span>{{$Program->code}}</span></h5>
            <h2 class="lic_precent">{{number_format(round(($Program->customers()->count() / $Program->max_lic) * 100),2); }} %</h2>
            <h6>Licenses Claimed</h6>
          </div> -->
          @php
use App\Models\CustomreBrevoData;
use App\Models\Session;
$userId = Auth::guard('programs')->id();
$customers = CustomreBrevoData::where('program_id', $userId)->get();
$sessions = Session::where('program_id', $userId)->get();
@endphp
           
     
 

 
<div class="row" id="staffprogram">
    <div class="search-container">
        <span class="search-icon"></span>
        <input id="searchInput" type="search" placeholder="Search Employee">
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive pt-3">
                <table class="table table-striped project-orders-table" id="myTable">
                    <thead>
                        <tr>
                            <th>Sr.no</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($count=0)
                        {{-- Loop through each customer --}}
                        @foreach($customers as $customer)
                        @php($count++)
                        <tr>
                            <td>{{ $count }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <form action="{{ route('remove-customer') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="customerId" value="{{ $customer->id }}">
                                        <input type="hidden" name="email" value="{{ $customer->email }}">
                                        <button type="submit" class="btn btn-success btn-sm btn-icon-text mr-3">
                                            Remove
                                            <i class="typcn typcn-view btn-icon-append"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



      <div class="row" id="utilisedprogram">
            <div class="col-md-12">
              <div class="card">
                <div class="table-responsive pt-3">
                  <table class="table table-striped project-orders-table">
                    <thead>
                      <tr>

                        <th>Sr.no</th>
                       
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
    @php($count=0)
    @foreach ($Program->customers as $customer)
    @php($count++)
    <tr>
        <td>{{ $count }}</td>
        <td>{{ $customer->name }}</td>
        <td>{{ $customer->email}}</td>
        <td>
            <div class="d-flex align-items-center">
            <a href="{{ isset($customer) ? route('remove-cusomer-program', ['customerId' => $customer->id, 'programId' => $Program->id]) : '#' }}" class="btn btn-success btn-sm btn-icon-text mr-3">
    Remove
    <i class="typcn typcn-view btn-icon-append"></i>
</a>

            </div>
        </td>
    </tr>
    @endforeach
</tbody>
</table>
                  
                </div>
              </div>
            </div>
          </div>
          
          
        
<!-- New table -->
<div class="row" id="session">
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive pt-3">
                <table class="table table-striped project-orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Session Type</th>
                             <th>Reason</th>
                            
                        </tr>
                    </thead>
               <tbody>
    @php($count=0)
    @foreach ($sessions as $session)
    @php($count++)
    <tr>
        <td>{{ $count }}</td>
        <td>{{ $session->session_date }}</td>
        <td>{{ $session->session_type }}</td>
        <td>{!! str_replace(',', ' <i class="fa-solid fa-arrow-right"></i> ', rtrim($session->reason, ',')) !!}</td>
    </tr>
    @endforeach
</tbody>


                </table>
            </div>
        </div>
    </div>
</div>
<!-- New table end -->  
          
      </div>
     


        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <!-- //@include('admin.footer') -->
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- base:js -->
  @include('admin.js')
  <!-- End custom js for this page-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var searchTerm = this.value.toLowerCase();
            var rows = document.querySelectorAll('#myTable tbody tr');

            rows.forEach(function(row) {
                var text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    });
</script>
</html>

