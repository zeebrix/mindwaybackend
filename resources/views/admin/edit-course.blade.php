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

                <h2>Update course</h2>

               
                <form action="{{ url('/manage-admin/update-audio' )}}" method="POST"  enctype="multipart/form-data">
                    @csrf

                    <select name="session_id">

                        <option value="" >Choose Course</option>

                        @foreach($sessionList as $session1)

                        <option  name="session_id" value="{{$session1->id}}">{{$session1->id}}</option>
                        @endforeach


                    </select>
                    @foreach($getAudio as $session)

                     <div class="form-group">
                        <label for="">Duration:</label>
                        <input type="text"  class="form-control" value="{{$session->duration}}" placeholder="Enter audio duration" name="duration" required>
                        <input type="hidden" name="audio_id" value="{{ $session->id }}">
                     
                      </div>

                      <div class="form-group">
                        <label for="">Audio title:</label>
                        <input type="text"  class="form-control" value="{{$session->audio_title}}" placeholder="Enter audio title" name="audio_title" required>
                      </div>

                      <div class="form-group">
                      <label for="audio">Audio File:</label>
    <input type="file" class="form-control" name="audio" id="audio">

    <!-- Display current audio value if available -->
    @if(isset($session->audio))
        <p>Current Audio: {{ $session->audio }}</p>
    @endif

    <!-- Display errors if there are any -->
    @error('audio')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>


@endforeach

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

