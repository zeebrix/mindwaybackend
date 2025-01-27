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

                <h2>Update Sleep Audio</h2>

                <form action="{{ url('/manage-admin/update-sleep_audio',['id'=>$editSleepAudio->id] )}}" method="POST"  enctype="multipart/form-data">
                    @csrf

                    <select name="course_id">

                        <option value="" >Choose Course</option>

                        @foreach($courseList as $course)
                        <option  name="course_id" value="{{$course->id}}">{{$course->id}}</option>
                        @endforeach

                    </select>

                    <div class="form-group">
                        <label for="">Selected Course:</label>
                        <input type="text"  class="form-control"  value="{{ $editSleepAudio->course_id }}" disabled>
                      </div>


                    <div class="form-group">
                      <label for="question">Title:</label>
                      <input type="text"  class="form-control" placeholder="Enter title" value="{{ $editSleepAudio->title }}" name="title" required>
                    </div>
                    <div class="form-group">
                      <label for=""> Description:</label>
                      <input type="text"  class="form-control" placeholder="Enter description" value="{{ $editSleepAudio->description }}" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for=""> Duration:</label>
                        <input type="text"  class="form-control" placeholder="Enter  duration" value="{{ $editSleepAudio->duration }}" name="duration" required>
                      </div>

                      <div class="form-group">
                        <label for=""> Favorite Color:</label>
                        <input type="text"  class="form-control" placeholder="Enter color code" value="{{ $editSleepAudio->color }}" name="color" required>
                      </div>

                      <div class="form-group">
                        <label for="">Image:</label>
                        <input type="file"  class="form-control" placeholder="Enter image" name="image" required>
                      </div>

                      <div class="form-group">
                        <label for="">Old image:</label>
                        <img height="50px" width="50px" class="popup"
                        src="{{ asset('storage/') }}/{{ $editSleepAudio->image }}"
                        alt="">
                      </div>


                      <div class="form-group">
                        <label for="">Sleep Audio:</label>
                        <input type="file"  class="form-control" name="audio" >
                      </div>

                      <div class="form-group">
                        <label for="">Old Audio:</label>
                        <audio controls="" style="vertical-align: middle" src="{{ asset('storage/') }}/{{ $editSleepAudio->audio }}" type="audio/mp3" controlslist="nodownload"></audio>
                      </div>

                    <button type="submit" class="btn btn-primary">Update</button>
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

