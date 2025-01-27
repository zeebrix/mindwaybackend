 <aside class="left-sidebar">
     <!-- Sidebar scroll-->
     <div>
         <div class="brand-logo d-flex align-items-center justify-content-between">
             <a href="{{ url('counseller/dashboard') }}" class="text-nowrap logo-img">
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
                     <span class="hide-menu">Manage</span>
                 </li>

                 <li class="sidebar-item">
                     {{-- @yield('selected_menu') --}}
                     <a class="sidebar-link " href="{{ url('counseller/dashboard') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-home"></i>
                         </span>
                         <span class="hide-menu">Home</span>
                     </a>
                 </li>


                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('counsellersesions') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-users"></i>
                         </span>
                         <span class="hide-menu">Sessions</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/counselleravailability') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-calendar"></i>
                         </span>
                         <span class="hide-menu">Availability</span>
                     </a>
                 </li>


                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('counsellerprofile') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-settings"></i>
                         </span>
                         <span class="hide-menu">Profile</span>
                     </a>
                 </li>
                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('counseller/setting') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-settings"></i>
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
