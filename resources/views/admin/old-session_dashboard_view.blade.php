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

  <div class="container-scroller" style="background-color: #E8F1FF;">
@php
use Illuminate\Support\Facades\Auth;
use App\Models\CustomreBrevoData;
use App\Models\Session;
use App\Models\Program;
$userId = Auth::guard('programs')->id();
$Program = Program::with('customers')->get();
$sessions = Session::where('program_id', $userId)->get();
$customers = CustomreBrevoData::where('program_id', $userId)->get();
$isAdmin = Auth::guard('programs')->check() && Auth::guard('programs')->user()->email === 'test@gmail.com';
@endphp

 
 <!-- users/index.blade.php -->




<div class="container">
<div class="container">
    <!-- Mindway -->
    <h1 style="margin-left: 20px;">Mindway</h1>
<h4 style="margin-left: 20px;">Search Available Staff For Counselling</h4>

<!-- Search Bar -->
<div class="row mt-4 mb-4">
    <div class="col-md-6 offset-md-3" style="margin-left: 20px;">
        <input type="text" id="searchInput" class="form-control rounded-pill" style="width: 800px; height: 60px;" placeholder="Type employee name, email address, or company">
    </div>
</div>

    <br>
    <div class="row" id="staffprogram">
        <div class="col-md-12">
            <div class="card">
                <div class="table-responsive pt-3">
                    <table class="table table-striped project-orders-table">
                        <thead>
                            <tr>
                                <th><strong>Sr.no</strong></th>
                                <th><strong>Name</strong></th>
                                <th><strong>Email</strong></th>
                                <th><strong>Company Name</strong></th>
                            </tr>
                        </thead>
                        <tbody id="userData">
                            @php($count=0)
                            {{-- Loop through each program and its customers --}}
                            @foreach($Program as $program)
                                @foreach($program->customers as $customer)
                                    @php($count++)
                                    <tr>
                                        <td><strong>{{ $count }}</strong></td>
                                        <td><strong>{{ $customer->name }}</strong></td>
                                        <td><strong>{{ $customer->email }}</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <strong>{{ $customer->company_name }}</strong>
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



 
  </div>
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