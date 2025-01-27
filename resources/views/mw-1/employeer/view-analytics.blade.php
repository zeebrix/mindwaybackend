@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')

    <style>
        .text-with-line {
            display: flex;
            align-items: center;
            font-family: Arial, sans-serif;
            color: #777;
            /* Light gray text */
        }

        .text-with-line::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ccc;
            /* Light gray line */
            margin-left: 10px;
            /* Space between text and line */
        }

        /* For the Progress bar */
        .progress.custom-height {
            height: 50px;
            border-radius: 20px;
            /* For rounded corners */
            background-color: #f1f1f1;
            /* Light gray background */
            overflow: hidden;
        }

        .progress-bar.work {
            background-color: #EAEAEA;
            /* Gray color for "Work Related" */
            color: #000;
            /* Black text for contrast */
            font-weight: 700;
            /* Bolder font */
            font-size: 16px;
            /* Larger font size */
            display: flex;
            align-items: center;
            justify-content: center;
            /* Center text vertically and horizontally */
        }

        .progress-bar.personal {
            background-color: #F5F5F5;
            /* White color for "Personal Related" */
            color: #000;
            /* Blue text for contrast */
            font-weight: 700;
            /* Bolder font */
            font-size: 16px;
            /* Larger font size */
            display: flex;
            align-items: center;
            justify-content: center;
            /* Center text vertically and horizontally */
        }

        /* .nav-item .nav-link {
                padding-left: 25px;
                padding-right: 25px;
                border-radius: 25px;
                transition: all 0.3s ease;
                color: #000;
                font-weight: 500;
                background-color: #F5F9FF;
                border: 1px solid #F5F9FF;
            }

            .nav-item .nav-link:hover {
                background-color: #e0e0e0;
            }

            .active-tab .nav-link {
                background-color: #688EDC;
                color: #fff;
                font-weight: 700;
                border: none;
            } */
    </style>

    <div class="row">
        <div class="col-10 offset-1">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <img style="object-fit: contain;" height="46px" width="130px" class="popup"
                        src="{{ asset('storage/logo/' . $Program->logo) }}" alt="{{ $Program->company_name }} Logo">
                </div>
                <div>
                    @if ($is_trial)
                        <p><b style="color: #000000; font-weight:700px; font-size:15px">On Free Trial:</b> <span
                                style="color: #000000; font-size:15px">{{ $leftDays }} days left of trial</span></p>
                    @endif
                </div>
            </div>


            <div class="main-content">
                <h1><strong>{{ $user->company_name }} Analytics</strong></h1>
                <h4 style="color: #000000; font-weight:500; font-size:20px">See platform and session insights</h4>

                @if (isset($departments) && is_iterable($departments) && count($departments) > 0)
<nav class="navbar navbar-expand-lg navbar-light bg-white" style="overflow-x: hidden; width: 100%;">
    <div class="collapse navbar-collapse" id="navbarSupportedContent" style="max-width: 100%; overflow-x: auto;">
        <ul class="navbar-nav mr-auto tabs-container" style="
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                white-space: nowrap;
                padding-left: 0;
                margin-bottom: 0;
                list-style: none;
                max-width: 100%;
                scrollbar-width: thin;
                scrollbar-color: #ccc #f9f9f9;
            ">
            <!-- Active Tab -->
            <li class="nav-item {{ !request()->has('department') ? 'active-tab' : '' }}" style="margin-right: 10px; flex-shrink: 0;">
                <a class="nav-link" href="/manage-program/view-analytics" style="
                        padding-left: 25px;
                        padding-right: 25px;
                        border-radius: 25px;
                        transition: all 0.3s ease;
                        color: {{ !request()->has('department') ? '#fff' : '#000' }};
                        font-weight: {{ !request()->has('department') ? '700' : '500' }};
                        background-color: {{ !request()->has('department') ? '#688EDC' : '#F5F9FF' }};
                        border: {{ !request()->has('department') ? 'none' : '1px solid #F5F9FF' }};
                    ">All</a>
            </li>
            @foreach ($departments as $depart)
            <li class="nav-item {{ request()->get('department') == $depart->id ? 'active-tab' : '' }}" style="margin-right: 10px; flex-shrink: 0;">
                <a class="nav-link" href="/manage-program/view-analytics?department={{ $depart->id }}" style="
                        padding-left: 25px;
                        padding-right: 25px;
                        border-radius: 25px;
                        transition: all 0.3s ease;
                        color: {{ request()->get('department') == $depart->id ? '#fff' : '#000' }};
                        font-weight: {{ request()->get('department') == $depart->id ? '700' : '500' }};
                        background-color: {{ request()->get('department') == $depart->id ? '#688EDC' : '#F5F9FF' }};
                        border: {{ request()->get('department') == $depart->id ? 'none' : '1px solid #F5F9FF' }};
                    ">{{ $depart->name }}</a>
            </li>
            @endforeach
        </ul>
    </div>
</nav>
                @endif
                <div class="text-with-line mt-3 mb-2"><strong>Utilisation</strong></div>

                <div class="row">
                    <div class="col-4"> <!-- Flexibly adjusts width -->
                        <div class="card" style="height: 39px">
                            <div class="card-body d-flex justify-content-center align-items-center p-4"
                                style="padding: 8px 0px 8px 0px !important;">
                                <span style="font-weight:900;color:#818181;margin-right:15px;">Licenses </span> <span
                                    style="color: #000000; font-weight:500">{{ $Program->max_lic }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-8">
                        <div class="card" style="height: 39px">
                            <div style="padding: 8px 0px 8px 0px !important;"
                                class="card-body d-flex justify-content-center align-items-center p-4">
                                <span style="font-weight:900;color:#818181;margin-right:15px">Overall Adoption Rate</span>
                                <span style="color: #000000; font-weight:500">{{ $adoptionRate }} %</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- {{ ($totalCustomersCount / 1000) * 100 }} --}}
                <div class="card w-100" style="width:576px; height:127px;">
                    <div class="card-body p-4">
                        <h6>Overall Adoption</h6>
                        <h4><strong>{{ $totalCustomersCount }} Employees</strong></h4>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted" id="min-value">0</span>
                            <div class="progress w-100 mx-3">
                                <div class="progress-bar" role="progressbar" style="width: {{ $totalCustomersCount }}%;"
                                    aria-valuenow="{{ $totalCustomersCount }}" aria-valuemin="0" aria-valuemax="1000">
                                </div>
                            </div>
                            <span class="text-muted" id="max-value">{{ $Program->max_lic }}</span>
                        </div>
                    </div>
                </div>
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h3 style="display: inline;">Growth of Program</h3>
                        <p style="display: inline; margin-left:10px">Past 6 months</p>
                        <p class="mt-2">This shows the total users enrolled in the program</p>
                        <div class="chart-container">
                            <canvas id="growthProgramChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="text-with-line mt-3 mb-3"><strong>Sessions</strong></div>
                <div class="row">
                    <div class="col me-3"> <!-- Flexibly adjusts width -->
                        <div class="card">
                            <div class="card-body d-flex justify-content-center align-items-center p-4">
                                <b>Total Sessions {{ $totalSessions }}</b>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card">
                            <div class="card-body d-flex justify-content-center align-items-center p-4">
                                <b>Unique Session Users {{ $newUserCount }}</b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card w-100">
                    <div class="card-body p-4">
                        <h3 style="display: inline;">Growth of Sessions</h3>
                        <p style="display: inline;">Past 6 months</p>
                        <p class="mt-2">This shows session conducted</p>
                        <div class="chart-container">
                            <canvas id="sessionsChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card w-100">
                    <div class="card-body p-4">
                        <h3 style="display: inline;">Reason for Sessions</h3>
                        <br>
                        <br>
                        <div class="progress custom-height">
                            <div class="progress-bar work" role="progressbar"
                                style="width: {{ $percentageData['workReasonsPercentage'] }}%;" aria-valuenow="30"
                                aria-valuemin="0" aria-valuemax="100">
                                Work Related {{ ceil($percentageData['workReasonsPercentage']) }}%
                            </div>

                            <div class="progress-bar personal" role="progressbar"
                                style="width: {{ $percentageData['personRelatedPercentage'] }}%;" aria-valuenow="20"
                                aria-valuemin="0" aria-valuemax="100">
                                Personal Related {{ floor($percentageData['personRelatedPercentage']) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card w-100">
                    <div class="card-body p-4">
                        <h3>Breakdown of Sessions</h3>
                        <p class="mt-2">See the common threads to why work related-sessions have been conducted</p>
                        <div class="chart-container">
                            <canvas id="breakdownChart"></canvas>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    @endsection

    @section('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

        <script>
            const growthProgramCtx = document.getElementById('growthProgramChart').getContext('2d');

            // Data
            const growthData = @json($growthData);
            const labels = @json($labels);

            // Calculate the min and max values from the dataset
            const minValue = Math.min(...growthData);
            const maxValue = Math.max(...growthData);

            // Chart initialization
            new Chart(growthProgramCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Growth of Program',
                        data: growthData,
                        borderColor: '#688EDC',
                        fill: false,
                        tension: 0.3, // Makes the line smooth
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false, // Remove vertical grid lines
                            }
                        },
                        y: {
                            beginAtZero: false,
                            min: minValue,
                            max: maxValue,
                            ticks: {
                                callback: function(value) {
                                    if (value === minValue || value === maxValue) {
                                        return value;
                                    }
                                    return '';
                                }
                            },
                            grid: {
                                display: true, // Keep horizontal grid lines
                            },
                            title: {
                                display: true,
                                text: 'Program Count',
                            }
                        }
                    }
                }
            });


            // Sessions Chart
            // const sessionsCtx = document.getElementById('sessionsChart').getContext('2d');
            // new Chart(sessionsCtx, {
            //     type: 'line',
            //     data: {
            //         labels: @json($labelsSession),
            //         datasets: [{
            //             label: 'Growth of Sessions',
            //             data: @json($growthDataSession),
            //             borderColor: '#688EDC',
            //             fill: false
            //         }]
            //     },
            // });

            const sessionsCtx = document.getElementById('sessionsChart').getContext('2d');

            // Data
            const labelsSession = @json($labelsSession);
            const growthDataSession = @json($growthDataSession);

            // Calculate the min and max values from the dataset
            const minValueSession = Math.min(...growthDataSession);
            const maxValueSession = Math.max(...growthDataSession);

            // Chart initialization
            new Chart(sessionsCtx, {
                type: 'line',
                data: {
                    labels: labelsSession,
                    datasets: [{
                        label: 'Growth of Sessions',
                        data: growthDataSession,
                        borderColor: '#688EDC',
                        fill: false,
                        tension: 0.3, // Makes the line smooth
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false, // Remove vertical grid lines
                            }
                        },
                        y: {
                            beginAtZero: false,
                            min: minValueSession,
                            max: maxValueSession,
                            ticks: {
                                callback: function(value) {
                                    if (value === minValueSession || value === maxValueSession) {
                                        return value;
                                    }
                                    return '';
                                }
                            },
                            grid: {
                                display: true, // Keep horizontal grid lines
                            },
                            title: {
                                display: true,
                                text: 'Sessions Count',
                            }
                        }
                    }
                }
            });

            const breakdownCtx = document.getElementById('breakdownChart').getContext('2d');
            new Chart(breakdownCtx, {
                type: 'bar',
                data: {
                    labels: @json($sessionReasonLabel), // Sorted labels from backend
                    datasets: [{
                        label: 'Work-Related Sessions',
                        data: @json($sessionReasonData), // Sorted data from backend
                        backgroundColor: Array(@json(count($sessionReasonData))).fill('#9E9E9E'),
                        borderRadius: 20,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y', // Ensures the bars are displayed horizontally
                    plugins: {
                        legend: {
                            display: false // Hides the legend if not needed
                        },
                        datalabels: {
                            anchor: 'end', // Position the labels at the end of the bars
                            align: 'right', // Align the labels to the right
                            color: '#000', // Set the label color
                            font: {
                                size: 12, // Adjust the font size
                                weight: 'bold'
                            },
                            formatter: (value) => value > 0 ? value : '' // Show value only if it's greater than 0
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false // Hides the x-axis gridlines
                            },
                            ticks: {
                                display: false // Hides the x-axis tick marks
                            },
                            beginAtZero: true, // Ensure the bars start at 0
                            max: Math.max(...
                                @json($sessionReasonData)), // Dynamically set the maximum length based on data
                            // Add padding to the x-axis to prevent clipping of the largest bar
                            padding: 20
                        },
                        y: {
                            grid: {
                                display: false // Hides the y-axis gridlines
                            },
                            ticks: {
                                display: true, // Keeps the row labels visible
                                font: {
                                    size: 12 // Adjust font size for visibility
                                },
                                color: '#000', // Optional: Set label color
                                padding: 10 // Reduce padding for Y-axis to make space for labels
                            },
                            title: {
                                display: true, // Shows the left-axis title
                                text: '', // Custom title text
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                color: '#000'
                            }
                        }
                    },
                    layout: {
                        padding: {
                            left: 0, // Increase the left padding to prevent the largest bar from getting cut off
                            right: 40
                        }
                    }
                },
                plugins: [ChartDataLabels] // Add the DataLabels plugin
            });
        </script>
    @endsection
