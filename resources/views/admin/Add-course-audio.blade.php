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

                <h2>Add course audio</h2>

                <form action="{{ url('/manage-admin/audio-add' )}}" method="POST"  enctype="multipart/form-data">
                    @csrf

                    <select name="session_id">

                        <option value="" >Choose Course</option>

                        @foreach($sessionList as $session)
                        <option  name="session_id" value="{{$session->id}}">{{$session->id}}</option>
                        @endforeach

                    </select>
                     <div class="form-group">
                        <label for="">Duration:</label>
                        <input type="text"  class="form-control" placeholder="Enter audio duration" name="duration" required>
                      </div>

                      <div class="form-group">
                        <label for="">Audio title:</label>
                        <input type="text"  class="form-control" placeholder="Enter audio title" name="audio_title" required>
                      </div>


                      <div class="form-group">
                        <label for="">Audio:</label>
                        <input type="file"  class="form-control" name="audio" >
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

