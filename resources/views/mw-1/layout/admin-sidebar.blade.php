 <aside class="left-sidebar">
     <!-- Sidebar scroll-->
     <div>
         <div class="brand-logo d-flex align-items-center justify-content-between">
             <a href="{{ url('/manage-admin/view-dashboard') }}" class="text-nowrap logo-img">
                 <img src="{{ asset('/logo/logo.png') }}" width="180" alt="" />
             </a>
             <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                 <i class="ti ti-x fs-8"></i>
             </div>
         </div>
         <!-- Sidebar navigation-->
         <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
             <ul id="sidebarnav">

                 <li class="nav-small-cap">
                     <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                     <span class="hide-menu">Home</span>
                 </li>
                 <li class="sidebar-item">
                     {{-- @yield('selected_menu') --}}
                     <a class="sidebar-link " href="{{ url('/manage-admin/view-dashboard') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-user-plus"></i>
                         </span>
                         <span class="hide-menu">Users</span>
                     </a>
                 </li>
                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-programs?status=1') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Program</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/counsellor') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Sessions</span>
                     </a>
                 </li>
                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-home') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Home Screen</span>
                     </a>
                 </li>

                 <li class="nav-small-cap">
                     <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                     <span class="hide-menu">Courses</span>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-course') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">All Courses</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-audio') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Course Audio</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-sos-audio') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">SoS Audio</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-category') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Course Category</span>
                     </a>
                 </li>

                 <li class="nav-small-cap">
                     <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                     <span class="hide-menu">Sleep Courses</span>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-sleep-course') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Sleep Course</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-sleep-audio') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Sleep Audio</span>
                     </a>
                 </li>


                 <li class="nav-small-cap">
                     <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                     <span class="hide-menu"></span>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-links') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Account Links</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-emoji') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Emojis</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-music') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Music</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-sleep-screen') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Sleep Screen</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-home-emoji') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Home Emoji</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-single-course') }}"
                         aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Single Course</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/view-quote') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Quote</span>
                     </a>
                 </li>
                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-admin/setting') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Setting</span>
                     </a>
                 </li>

             </ul>

         </nav>
         <!-- End Sidebar navigation -->
     </div>
     <!-- End Sidebar scroll-->
 </aside>
