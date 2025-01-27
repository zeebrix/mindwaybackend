 <!-- partial:partials/_sidebar.html -->
 <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/manage-admin/view-dashboard') }}">
          <i class="typcn typcn-user-add-outline menu-icon"></i>
          <span class="menu-title">Users</span>
          {{-- <div class="badge badge-danger">new</div> --}}
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#form2" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Home screen</span>
          <i class="menu-arrow"></i>
        </a> 
        <div class="collapse" id="form2">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-home') }}">Add Home Screen</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-home') }}">View Home Screen</a></li>

          </ul>
        </div>

      </li>

      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Courses</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="form-elements">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-course') }}">Add Course</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-course') }}">View Course</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-audio') }}">Add Audio</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-audio') }}">View Audio</a></li>

             <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-sos-audio') }}">Add SOS Audio</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-sos-audio') }}">View SOS Audio</a></li>
          </ul>
        </div>

      </li>

      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#category" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Course Category</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="category">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-category') }}">Add Category</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-category') }}">View Category</a></li>

          </ul>
        </div>

      </li>

       <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#sleep" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Sleep Course</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="sleep">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-sleep-course') }}">Add sleep course</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-sleep-course') }}">View sleep course</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-sleep-audio') }}">Add Sleep Audio</a></li>

            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-sleep-audio') }}">View sleep audio</a></li>
          </ul>
        </div>

      </li>

      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#links" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Account links</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="links">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-links') }}">Add Links</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-links') }}">View Links</a></li>

          </ul>
        </div>

      </li>

       <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#links1" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Emoji</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="links1">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-emoji') }}">Add Emoji</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-emoji') }}">View Emoji</a></li>

          </ul>
        </div>

      </li>

      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#music" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Music</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="music">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-music') }}">Add Music</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-music') }}">View Music</a></li>

          </ul>
        </div>

      </li>


      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#sleep-screen" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Sleep Screen</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="sleep-screen">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-sleep-screen') }}">Add Sleep Screen</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-sleep-screen') }}">View Sleep Screen</a></li>

          </ul>
        </div>

      </li>


      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#home-emoji" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Home Emoji</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="home-emoji">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-home-emoji') }}">Add Home Emoji</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-home-emoji') }}">View Home Emoji</a></li>

          </ul>
        </div>

      </li>

      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#single" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Single Course</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="single">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-single-course') }}">Add Single Course</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-single-course') }}">View Single Course</a></li>

          </ul>
        </div>

      </li>

    <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#quote" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Quotes</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="quote">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-quote') }}">Add Quote</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-quote') }}">View Quote </a></li>

          </ul>
        </div>

      </li>

      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#programs" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Programs</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="programs">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/add-program') }}">Add New Program</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-programs') }}">View all  Programs</a></li>

          </ul>
        </div>

      </li>
  <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#sessions" aria-expanded="false" aria-controls="form-elements">
          <i class="typcn typcn-film menu-icon"></i>
          <span class="menu-title">Sessions</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="sessions">
          <ul class="nav flex-column sub-menu">
             <li class="nav-item"><a class="nav-link" href="{{ url('/manage-admin/view-session') }}">View all  Sessions</a></li>

          </ul>
        </div>

      </li>
    </ul>
  </nav>
