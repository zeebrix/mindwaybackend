<!DOCTYPE html>
<html lang="en">

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

        @if(session()->has('message1'))
        <div class="alert alert-danger">
            {{ session()->get('message1') }}
        </div>
    @endif
            <h2>View Sleep Screen </h2>

          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="table-responsive pt-3">
                  <table class="table table-striped project-orders-table">
                    <thead>
                      <tr>

                        <th>Sr.no</th>
                        <th>Id</th>
                        <th>Audio title</th>
                        <th>Duration</th>
                        <th>Total Play</th>
                        <th>Sleep Audio</th>
                          <th>Sleep Image</th>
                        <th>Created at</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                        @php($count=0)
                        @foreach ($getSleepScreen as $data )
                        @php($count++)
                      <tr>
                        <td>{{ $count }}</td>
                        <td>{{ $data->id }}</td>
                        <td>{{ $data->audio_title }}</td>
                         <td>{{ $data->duration }}</td>
                         <td>{{ $data->total_play }}</td>
                       <td>  <audio controls="" style="vertical-align: middle" src="{{ asset('storage/') }}/{{ $data->sleep_audio }}" type="audio/mp3" controlslist="nodownload">
                        </audio></td>
                         <td><img height="50px" width="50px" class="popup"
                            src="{{ asset('storage/SleepScreen') }}/{{ $data->image }}"
                            alt="no image"></td>
                         <!--<td>{{$data->image}}</td>-->
                        <td>{{ $data->created_at }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                {{-- <a href="{{ url('/manage-admin/edit-faqs',['id' => $data->id]) }}" class="btn btn-success btn-sm btn-icon-text mr-3">
                                  Edit
                                  <i class="typcn typcn-edit btn-icon-append"></i>
                                </a> --}}
                                <a href="{{ url('/manage-admin/delete-sleep-screen',['id' => $data->id]) }}"  class="btn btn-danger btn-sm btn-icon-text">
                                  Delete
                                  <i class="typcn typcn-delete-outline btn-icon-append"></i>
                                </a>
                              </div>
                        </td>
                      </tr>

                        @endforeach

                    </tbody>
                  </table>
                  {{-- <div class="col-md-12 mb-3">
                    <nav class="pagination float-right">{!! $customer->appends(Request::query())->links() !!}</nav>
                </div> --}}
                </div>
              </div>
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

