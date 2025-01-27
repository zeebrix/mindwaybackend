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
            <h2>View Programs </h2>

          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="table-responsive pt-3">
                  <table class="table table-striped project-orders-table">
                    <thead>
                      <tr>

                        <th>Sr.no</th>

                        <th>Comapny Name</th>
                        <th>Account Controller Email</th>
                        <th>Max Licenses</th>
                        <th>Access Code</th>
                        <th>Logo</th>
                        <th>Booking Link</th>
                        <th>Created at</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                        @php($count=0)
                        @foreach ($Programs as $data )
                        @php($count++)
                        <tr>
                          <td>{{ $count }}</td>

                          <td>{{ $data->company_name }}</td>
                          <td>{{ $data->email }}</td>
                          <td>{{ $data->max_lic }}</td>
                          <td>{{ $data->code }}</td>
                          <td><img height="50px" width="50px" class="popup"
                            src="{{ asset('storage/logo') }}/{{ $data->logo }}"
                            alt=""></td>
                            <td>{{ $data->link }}</td>

                          <td>{{ $data->created_at }}</td>
                          <td>
                              <div class="d-flex align-items-center">
                                  <a href="{{ url('/manage-admin/program',['id' => $data->id]) }}" class="btn btn-success btn-sm btn-icon-text mr-3">
                                    View
                                    <i class="typcn typcn-view btn-icon-append"></i>
                                  </a>
                                  <!-- <a href="{{ url('/manage-admin/delete-quote',['id' => $data->id]) }}"  class="btn btn-danger btn-sm btn-icon-text">
                                    Delete
                                    <i class="typcn typcn-delete-outline btn-icon-append"></i>
                                  </a> -->
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

