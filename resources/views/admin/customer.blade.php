<!DOCTYPE html>
<html lang="en">

{{-- head add --}}

@include('admin.head')

<body>

  < class="container-scroller">
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
            <div class="alert alert-danger">
                {{ session()->get('message') }}
            </div>
        @endif
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <form action="" class="pull-right">
                <div class="input-group">
                    <div class="form-outline">
                        <input type="search" placeholder="Search by First name" name="search" class="form-control" />
                    </div>
                    <div>
                        <button style="margin-top:3px" class="btn btn-primary">Search</button>
                        <a href="{{ url('/manage-admin/view-dashboard') }}">
                            <button  style="margin-top:3px"class="btn btn-primary">Reset</button>
                        </a>
                    </div>
                </div>
            </form>
          </div>

          <div class="row">
            <div class="col-md-12">
                <h2>Users</h2>
              <div class="card">
                <div class="table-responsive pt-3">
                  <table class="table table-striped project-orders-table">
                    <thead>
                      <tr>

                        <th>Sr.no</th>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Email</th>
                         <th>Goal Id</th>
                        <th>Improve</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                        @php($count=0)
                        @foreach ($getCustomer as $data )
                        @php($count++)
                      <tr>
                        <td>{{ $count }}</td>
                        <td>{{ $data->id }}</td>
                        <td>{{ $data->name }} </td>
                        
                        <td>{{ $data->email }}</td>
                         <td>{{ $data->goal_id }}</td>
                        @if ( $data->improve == null)

                            <td>Not selected</td>
                            @else
                            {{ $data->improve  }}
                        @endif


                        <td>{{ $data->status }}</td>
                        <td>{{ $data->created_at }}</td>
                        <td>
                          <div class="d-flex align-items-center">
                            {{-- <button type="button" class="btn btn-success btn-sm btn-icon-text mr-3">
                              Edit
                              <i class="typcn typcn-edit btn-icon-append"></i>
                            </button> --}}
                            <a  class="btn btn-danger btn-sm btn-icon-text" href="{{ url('/manage-admin/delete-customer',['id'=>$data->id]) }}">
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
      
        <!-- partial -->

      
          


@include('admin.footer')
      </div>
      <!-- main-panel ends -->
    
    </div>
    <!-- page-body-wrapper ends -->
      
  

  </div>
 

  <!-- container-scroller -->

  {{-- Js include --}}
 
  


  @include('admin.js')

</body>

</html>

