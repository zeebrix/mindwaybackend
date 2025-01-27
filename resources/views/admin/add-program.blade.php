<!DOCTYPE html>
<html lang="en">

{{-- head add --}}

@include('admin.head')

<body>

  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->

    {{-- admin header add --}}
    @include('admin.header') 

       <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      {{-- skins color add --}}
        @include('admin.skins-color')



      {{-- add side bar --}}

      @include('admin.sidebar')


      <div class="main-panel">
        <div class="content-wrapper">

            @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @endif
            @if ($errors->any())
              @foreach ($errors->all() as $error)
                  <div>{{$error}}</div>
              @endforeach
            @endif

          <div class="row">
            <div class="col-md-12">

                <h2>Add Program</h2>

                <form action="{{ url('/manage-admin/store-program' )}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                      <label for="company_name">Comapny Name</label>
                      <input type="text"  class="form-control" placeholder="Enter Comapny Name" name="company_name" required>
                    </div>

                    <div class="form-group">
                      <label for="email">Account Controller Email</label>
                      <input type="email"  class="form-control" placeholder="Enter Account Controller Email" name="email" required>
                    </div>

                    <div class="form-group">
                      <label for="max_lic">Max Licenses</label>
                      <input type="number"  class="form-control" placeholder="Enter Max Licenses" name="max_lic" required>
                    </div>

                    <div class="form-group">
                      <label for="code">Access Code</label>
                      <input type="text"  class="form-control" placeholder="Enter code" name="code" required>
                    </div>

                      <div class="form-group">
                        <label for="logo">Logo</label>
                        <input type="file"  class="form-control" placeholder="Upload Logo" name="logo" >
                      </div>
                      <div class="form-group">
                        <label for="link">Booking Link</label>
                        <input type="url"  class="form-control" placeholder="Enter Booking Link" name="link" required>
                      </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                  </form>
            </div>
          </div>

        </div>


        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        @include('admin.footer')
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
</body>

</html>

