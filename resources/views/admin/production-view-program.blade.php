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

</style>
<body>
@php
use Illuminate\Support\Facades\Auth;
use App\Models\CustomreBrevoData;
use App\Models\Session;
use App\Models\Program;
$userId = Auth::guard('programs')->id();
$Program = Program::with('customers')->get();
$customers = CustomreBrevoData::where('program_id', $userId)->get();
$sessions = Session::where('program_id', $userId)->get();
$isAdmin = Auth::guard('programs')->check() && Auth::guard('programs')->user()->email === 'test@gmail.com';
@endphp

@if ($isAdmin)
 <!-- users/index.blade.php -->



<div class="container">
<div class="container">
    <!-- Search Bar -->
    <div class="row mt-4 mb-2">
        <div class="col-md-6 offset-md-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email">
        </div>
    </div>
    <div class="row" id="staffprogram">
    <div class="col-md-12">
        <div class="card">
            <div class="table-responsive pt-3">
                <table class="table table-striped project-orders-table">
                    <thead>
                        <tr>
                            <th>Sr.no</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company Name</th>
                        </tr>
                    </thead>
                    <tbody id="userData">
                        @php($count=0)
                        {{-- Loop through each program and its customers --}}
                        @foreach($Program as $program)
                            @foreach($program->customers as $customer)
                                @php($count++)
                                <tr>
                                    <td>{{ $count }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                        {{ $customer->company_name}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>


@else
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
<div class="row" id="staffprogram">
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
                            
                        </tr>
                    </thead>
                    <tbody>
                        @php($count=0)
                        @foreach ($sessions as $session)
                        @php($count++)
                        <tr>
                            <td>{{ $count }}</td>
                            <td>{{ $session->session_date }}</td> <!-- Assuming date column name is 'date' -->
                            <td>{{ $session->session_type }}</td> <!-- Assuming session type column name is 'session_type' -->
                           
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
  @endif
  <!-- container-scroller -->

  <!-- base:js -->
  @include('admin.js')
  <!-- End custom js for this page-->
  <script>
    $(document).ready(function() {
        $('#searchInput').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            $('#userData tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1)
            });
        });
    });
</script>
</body>

</html>

