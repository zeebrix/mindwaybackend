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

                <h2>Add Home Screen</h2>

                <form action="{{ url('/manage-admin/update-home',['id'=>$editHome->id] )}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                      <label for="question">Title:</label>
                      <input type="text"  class="form-control" placeholder="Enter title" value="{{ $editHome->title }}" name="title" required>
                    </div>
                    <div class="form-group">
                      <label for=""> Subtitle:</label>
                      <input type="text"  class="form-control" placeholder="Enter subtitle" value="{{ $editHome->subtitle }}" name="subtitle" required>
                    </div>
                    <div class="form-group">
                        <label for=""> duration:</label>
                        <input type="text"  class="form-control" placeholder="Enter  duration" value="{{ $editHome->duration }}" name="duration" required>
                      </div>
                      
                       

                      <div class="form-group">
                        <label for="">Image:</label>
                        <input type="file"  class="form-control" placeholder="Enter image" name="image" required>
                      </div>

                      <div class="form-group">
                        <label for="">Old image:</label>
                        <img height="50px" width="50px" class="popup"
                        src="{{ asset('storage/homescreen') }}/{{ $editHome->image }}"
                        alt="">
                      </div>


                      <div class="form-group">
                        <label for="">Home Audio:</label>
                        <input type="file"  class="form-control" name="home_audio" >
                      </div>

                      <div class="form-group">
                        <label for="">Old Audio:</label>
                        <audio controls="" style="vertical-align: middle" src="{{ asset('storage/') }}/{{ $editHome->home_audio }}" type="audio/mp3" controlslist="nodownload"></audio>
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

