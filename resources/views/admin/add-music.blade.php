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

                <h2>Add Music</h2>

                <form action="{{ url('/manage-admin/music-add' )}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                      <label for="question">Title:</label>
                      <input type="text"  class="form-control" placeholder="Enter title" name="title" required>
                    </div>
                    <div class="form-group">
                      <label for=""> Subtitle:</label>
                      <input type="text"  class="form-control" placeholder="Enter subtitle" name="subtitle" required>
                    </div>
                    <div class="form-group">
                        <label for=""> duration:</label>
                        <input type="text"  class="form-control" placeholder="Enter  duration" name="duration" required>
                      </div>

                      <div class="form-group">
                        <label for="">Image:</label>
                        <input type="file"  class="form-control" placeholder="Enter image" name="image" required>
                      </div>

                      <div class="form-group">
                        <label for="">Music Audio:</label>
                        <input type="file"  class="form-control" name="music_audio" >
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

</body>

</html>

