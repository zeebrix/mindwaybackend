@if(Auth::guard('programs')->check())
@include('mw-1.layout.employeer-sidebar')
@elseif (Auth::guard('counselor')->check())
@include('mw-1.layout.counseller-sidebar')
@else
@if (auth()->check())
@include('mw-1.layout.admin-sidebar')
@endif
@endif
