<!DOCTYPE html>
<html lang="en">

@include('admin.head')

<body>

  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    {{-- Admin header --}}
    @include('admin.header')

    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      {{-- Skins color --}}
      @include('admin.skins-color')

      {{-- Sidebar --}}
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
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
            Add counselor
          </button>
          <h2>View Sessions</h2>
          @php
            use App\Models\CounsellingSession;
            $counsellingSessions = CounsellingSession::all();
          @endphp
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="table-responsive pt-3">
                  <table class="table table-striped project-orders-table">
                    <thead>
                      <tr>
                         
                        <th>Name</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Counselor Name</th>
                        <th>Session Date</th>
                        <th>Session Type</th>
                        <th>Session Reason</th>
                        <th>New User</th>
                        <th>Max Session</th>
                        <th>Created at</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php($count = 0)
                      @foreach ($counsellingSessions as $session)
                        @php($count++)
                        <tr>
                           
                          <td>{{ $session->name }}</td>
                          <td>{{ $session->company_name }}</td>
                          <td>{{ $session->email }}</td>
                          <td>{{ isset($session->counselor) ? $session->counselor->name : '' }}</td>
                          <td>{{ $session->session_date }}</td>
                          <td>{{ $session->session_type }}</td>
                          <td>{{ $session->reason }}</td>
                          <td>{{ $session->new_user }}</td>
                          <td>{{ $session->max_session }}</td>
                          <td>{{ $session->created_at }}</td>
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
 <!-- The Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="myModalLabel">Add counseller</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="inputForm">
            @csrf
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- base:js -->
  @include('admin.js')

  <!-- Include DataTables CSS and JS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

  <!-- Initialize DataTable -->
  <script type="text/javascript">
    $(document).ready(function() {
      $('.project-orders-table').DataTable({
        "pageLength": -1
      });
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#inputForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        var csrfToken = $('input[name="_token"]').val();
        // Get form data
        var formData = {
          name: $('#name').val(),
          email: $('#email').val(),
          password: $('#password').val(),
          _token: csrfToken
        };

        // Send AJAX request
        $.ajax({
          url: "{{url('/manage-admin/add-counselor')}}", // Replace with your endpoint URL
          method: 'POST',
          data: formData,
          success: function(response) {
            // Handle success
            alert('Form submitted successfully!');
            $('#myModal').modal('hide'); // Hide the modal
            $('.modal-backdrop').remove();
            $('#inputForm')[0].reset(); // Reset the form
          },
          error: function(xhr) {
            // Handle validation errors
            if (xhr.status === 422) {
              var errors = xhr.responseJSON.errors;
              var errorMessage = '';
              $.each(errors, function(key, value) {
                errorMessage += value.join(' ') + '\n'; // Combine all error messages
              });
              alert('Validation failed:\n' + errorMessage);
            } else {
              alert('An error occurred. Please try again.');
            }
          }
        });
      });
    });
  </script>
  <!-- End custom js for this page-->
</body>

</html>
