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

                <h2>Add Sleep Audio</h2>

                <form action="{{ url('/manage-admin/sleep-audio-add' )}}" method="POST"  enctype="multipart/form-data">
                    @csrf

                    <select name="course_id">

                        <option value="" >Choose Course</option>

                        @foreach($courseList as $course)
                        <option  name="course_id" value="{{$course->id}}">{{$course->id}}</option>
                        @endforeach

                    </select>

                         <div class="form-group">
                        <label for="">Duration:</label>
                        <input type="text"  class="form-control" placeholder="Write duration here" name="duration" >
                      </div>

                      <div class="form-group">
                        <label for="">Audio:</label>
                        <input type="file"  class="form-control" name="audio" >
                      </div>



                       <div class="form-group">
                        <label for="">Title:</label>
                        <input type="text"  class="form-control" placeholder="Write title here" name="title" >
                      </div>


                       <div class="form-group">
                        <label for="">Image:</label>
                        <input type="file"  class="form-control" placeholder="Select image" name="image" >
                      </div>

                       <div class="form-group">
                        <label for="">Description:</label>
                        <input type="text"  class="form-control" placeholder="Write description here" name="description" >
                      </div>


                        <div class="form-group">
                        <label for="">Course Favorite Color:</label>
                        <input type="text"  class="form-control" placeholder="Enter Color Code Ex:#000000" name="favorite_color" required>
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

