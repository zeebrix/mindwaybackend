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

                <h2>Update Sleep course</h2>

                <form action="{{ url('/manage-admin/update-sleep-course',['id'=>$getSleepCourse->id] )}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">Category id:</label>
                        <input type="text"  class="form-control" placeholder="Enter category id" name="category_id" value="{{ $getSleepCourse->category_id }}" required>
                      </div>
                    <div class="form-group">
                      <label for="question">title:</label>
                      <input type="text"  class="form-control" placeholder="Enter title" value="{{ $getSleepCourse->title }}"  name="title" required>
                    </div>
                    <div class="form-group">
                      <label for="">description:</label>
                      <input type="text"  class="form-control" placeholder="Enter description" value="{{ $getSleepCourse->description }}" name="description" required>
                    </div>



                      <div class="form-group">
                        <label for="">Thumbnail:</label>
                        <input type="file"  class="form-control" placeholder="Enter thumbnail" name="thumbnail" required>
                      </div>

                      <div class="form-group">
                        <label for="">Old thumbnail</label>
                        <img height="50px" width="50px" class="popup"
                            src="{{ asset('storage/course') }}/{{ $getSleepCourse->thumbnail }}"
                            alt=""></td>

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
  <!-- End custom js for this page-->
</body>

</html>

