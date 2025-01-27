<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Mindway</title>
  {{-- <link rel="shortcut icon" type="image/png" href="{{ asset('mw-1/assets/images/logos/favicon.ico') }}" /> --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" type="image/png" href="{{ asset('/images/favicon.ioc')}}" />
  <!-- Select2 CSS -->
  <link rel="stylesheet" href="{{ asset('mw-1/assets/css/styles.min.css') }}" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

<!-- Add this to your Blade layout -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

<!-- Add this to your Blade layout -->
<style>
  .sidebar-nav ul .sidebar-item.selected>.sidebar-link, .sidebar-nav ul .sidebar-item.selected>.sidebar-link.active, .sidebar-nav ul .sidebar-item>.sidebar-link.active
  {
    background-color: unset !important;
    color: #688EDC !important;
  }

  .mindway-btn-blue{
        border: unset !important;
    white-space: nowrap !important;
    background: #688EDC !important;
    color: #ffffff !important;
    border-radius: 20px !important;
    }

    .mindway-btn-blue {
      border: unset !important;
      white-space: nowrap !important;
      background: #688EDC !important;
      color: #ffffff !important;
      border-radius: 20px !important;
    }

    .theme-btn {
      border: unset !important;
      white-space: nowrap !important;
      background: #F5F9FF;
      color: #688EDC;
      border-radius: 20px !important;
    }

    .mindway-btn {
      border: unset !important;
      white-space: nowrap !important;
      background: #F5F9FF !important;
      color: #688EDC !important;
      border-radius: 20px !important;
    }

    a.sidebar-link:hover {
      text-decoration: none !important;
      /* Remove underline */
      color: #688edf !important;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    @include('mw-1.layout.sidebar')
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->

      @include('mw-1.layout.header')
      <!--  Header End -->
      <div class="container-fluid">
        <!--  Row 1 -->
        {{-- @include('mw-1.layout.d-content') --}}
        @yield('content')
      </div>

    </div>
  </div>
  <script src="{{ asset('mw-1/assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
</script>
  <script src="{{ asset('mw-1/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('mw-1/assets/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('mw-1/assets/js/app.min.js') }}"></script>
  {{-- <script src="{{ asset('mw-1/assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script> --}}
  <script src="{{ asset('mw-1/assets/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="{{ asset('mw-1/assets/js/dashboard.js') }}"></script>

  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  @yield('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const communicationMethodElement = document.querySelector('#communication_method');

            // Check if the element exists
            if (communicationMethodElement) {
                // Initialize Choices only if the element exists
                const specializations = new Choices(communicationMethodElement, {
                    removeItemButton: true, // Allow tags to be removed
                    placeholder: true,
                    placeholderValue: 'Select communication method...', // Placeholder text
                });
            } else {
                console.warn('Element with ID #communication_method not found.');
            }
        });

        //   const tagsInput = document.getElementById('tagsInput');
        //   const tagsList = document.getElementById('tagsList');
        //   const hiddenTags = document.getElementById('hiddenTags');
        //   let tags = [];

        //   tagsInput.addEventListener('keydown', function (e) {
        //     if (e.key === 'Enter' && tagsInput.value.trim() !== '') {
        //       e.preventDefault();

        //       const newTag = tagsInput.value.trim();
        //       if (!tags.includes(newTag)) {
        //         tags.push(newTag);

        //         // Add tag to the visible list
        //         const li = document.createElement('li');
        //         li.textContent = newTag;
        //         li.style.display = 'inline-block';
        //         li.style.padding = '5px 10px';
        //         li.style.margin = '5px';
        //         li.style.background = '#407bff';
        //         li.style.color = '#fff';
        //         li.style.borderRadius = '5px';
        //         li.style.cursor = 'pointer';

        //         li.addEventListener('click', function () {
        //           tags = tags.filter(tag => tag !== newTag);
        //           updateHiddenInput();
        //           li.remove();
        //         });

        //         tagsList.appendChild(li);

        //         // Clear input
        //         tagsInput.value = '';
        //         updateHiddenInput();
        //       }
        //     }
        //   });

        //   function updateHiddenInput() {
        //     hiddenTags.value = tags.join(',');
        //   }


        const tagsInput = document.getElementById('tagsInput');
        const tagsList = document.getElementById('tagsList');
        const hiddenTags = document.getElementById('hiddenTags');

        // Ensure the required elements exist before proceeding
        if (tagsInput && tagsList && hiddenTags) {
            let tags = @json($specialization ?? []);

            // Display tags already in the list
            tags.forEach(tag => {
                const li = document.createElement('li');
                li.textContent = tag;
                li.style.display = 'inline-block';
                li.style.padding = '5px 10px';
                li.style.margin = '5px';
                li.style.background = '#F5F9FF';
                li.style.color = '#688EDC';
                li.style.borderRadius = '5px';
                li.style.cursor = 'pointer';

                li.addEventListener('click', function() {
                    tags = tags.filter(t => t !== tag);
                    updateHiddenInput();
                    li.remove();
                });

                tagsList.appendChild(li);
            });

            tagsInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && tagsInput.value.trim() !== '') {
                    e.preventDefault();

                    const newTag = tagsInput.value.trim();
                    if (!tags.includes(newTag)) {
                        tags.push(newTag);

                        // Add tag to the visible list
                        const li = document.createElement('li');
                        li.textContent = newTag;
                        li.style.display = 'inline-block';
                        li.style.padding = '5px 10px';
                        li.style.margin = '5px';
                        li.style.background = '#F5F9FF';
                        li.style.color = '#688EDC';
                        li.style.borderRadius = '5px';
                        li.style.cursor = 'pointer';

                        li.addEventListener('click', function() {
                            tags = tags.filter(tag => tag !== newTag);
                            updateHiddenInput();
                            li.remove();
                        });

                        tagsList.appendChild(li);

                        // Clear input
                        tagsInput.value = '';
                        updateHiddenInput();
                    }
                }
            });

            function updateHiddenInput() {
                hiddenTags.value = tags.join(',');
            }
        }
    </script>
</body>

</html>
