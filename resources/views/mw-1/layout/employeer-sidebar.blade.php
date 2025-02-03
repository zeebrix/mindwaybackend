 <aside class="left-sidebar">
     <!-- Sidebar scroll-->
     <div>
         <div class="brand-logo d-flex align-items-center justify-content-between">
             <a href="{{ url('/manage-program/view-dashboard') }}" class="text-nowrap logo-img">
                 <img src="{{ asset('/logo/logo.png') }}" width="180" alt="" />
             </a>
             <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                 <i class="ti ti-x fs-8"></i>
             </div>
         </div>
         <!-- Sidebar navigation-->
         <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
             <ul id="sidebarnav">
             @php
                        $Program = Auth::guard('programs')->user();
                        $ProgramLink = $Program->link ?? '';
                @endphp
                 <li class="nav-small-cap">
                     <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                     <span class="hide-menu">Manage</span>
                 </li>

                 <li class="sidebar-item">
                     {{-- @yield('selected_menu') --}}
                     <a class="sidebar-link " href="{{ url('/manage-program/view-dashboard') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-home"></i>
                         </span>
                         <span class="hide-menu">Home</span>
                     </a>
                 </li>

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-program/view-analytics') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-article"></i>
                         </span>
                         <span class="hide-menu">Analytics</span>
                     </a>
                 </li>

                 @if($Program->allow_employees == 1)
                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-program/view-employees') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-users"></i>
                         </span>
                         <span class="hide-menu">Employees</span>
                     </a>
                 </li>
                @endif

                 <li class="nav-small-cap">
                     <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                     <span class="hide-menu">Others</span>
                 </li>


                @if ($ProgramLink)
                    <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ $ProgramLink }}" target="_blank" aria-expanded="false">
                         <span>
                             <i class="ti ti-files"></i>
                         </span>
                         <span class="hide-menu">App Setup Guide</span>
                     </a>
                 </li>
                @endif

                 <li class="sidebar-item">
                     <a class="sidebar-link" href="{{ url('/manage-program/setting') }}" aria-expanded="false">
                         <span>
                             <i class="ti ti-settings"></i>
                         </span>
                         <span class="hide-menu">Settings</span>
                     </a>
                 </li>

             </ul>

         </nav>
         <!-- End Sidebar navigation -->
     </div>
     <!-- End Sidebar scroll-->
 </aside>
