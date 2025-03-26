<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item d-block d-xl-none">
        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
          <i class="ti ti-menu-2"></i>
        </a>
      </li>
      {{-- <li class="nav-item">
              <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                <i class="ti ti-bell-ringing"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
            </li> --}}
    </ul>
    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
        {{-- <a href="https://adminmart.com/product/modernize-free-bootstrap-admin-dashboard/" target="_blank" class="btn btn-primary">Download Free</a> --}}
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
            aria-expanded="false">
            @if(Auth::guard('programs')->check())
            @php
               $Program = Auth::guard('programs')->user();
            @endphp
            <img style="object-fit: contain;" src="{{ asset('storage/logo/' . $Program->logo) }}" alt="" width="35" height="35" class="rounded-circle">
              @elseif (Auth::guard('counselor')->check())
            @php
              $counselor = Auth::guard('counselor')->user();;
            @endphp

            @if ($counselor->avatar !== 'default.png')
            <img style="object-fit: contain;" src="{{ asset('storage/logo/' . $counselor->avatar) }}" alt="" width="35" height="35" class="rounded-circle">
            @else
              <img style="object-fit: contain;" src="{{ asset('/storage/logo/default.png') }}" alt="" width="35" height="35" class="rounded-circle">
            @endif
              @else
              @if (auth()->check())
              <img style="object-fit: contain;" src="{{ asset('/logo/logo.png') }}" alt="" width="35" height="35" class="rounded-circle">
              @endif
              @endif



          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
            <div class="message-body">
              {{-- <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-mail fs-6"></i>
                      <p class="mb-0 fs-3">My Account</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-list-check fs-6"></i>
                      <p class="mb-0 fs-3">My Task</p>
                    </a> --}}

              @if(Auth::guard('programs')->check())

              <a href="{{ route('program.logout') }}" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>

              {{-- @include('mw-1.layout.employeer-sidebar') --}}
              @elseif (Auth::guard('counselor')->check())

              <a href="{{ route('counsellor.logout') }}" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>

              {{-- @include('mw-1.layout.counseller-sidebar') --}}
              @else
              @if (auth()->check())
              <form action="{{ route('logoutadmin') }}" method="post">
                @csrf
                <button class="dropdown-item" type="submit"><i class="typcn typcn-eject text-primary"></i>Logout</button>
              </form>
              {{-- @include('mw-1.layout.admin-sidebar') --}}
              @endif
              @endif
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</header>
