@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <style>
        .text-with-line {
            font-weight: 500;
            display: flex;
            align-items: center;
            font-family: Arial, sans-serif;
            color: #B3B3B3;
        }

        .text-with-line::after {
            content: '';
            flex: 1;
            color: #B3B3B3;
            border: 1px solid #ccc;
            margin-left: 10px;
        }
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

            <div class="align-items-center mb-2">
                <h2 style="font-weight: 700;">Welcome {{ session('loginUserName') }} 👋</h2>

                <h4 style="color: #000000; font-weight:500; font-size:20px">EAP Program for {{ $Program->company_name }}
                </h4>

                <p style="font-weight: 500;font-size:20px"></p>
            </div>
            <div class="text-with-line mt-3 mb-3"><strong>Utilisation</strong></div>

            <div class="row">
                <div class="col-4">
                    <div class="card">
                        <div class="card-body d-flex justify-content-center align-items-center p-4"
                            style="padding: 8px 0px 8px 0px !important;">
                            <span style="font-weight:600;color:#818181;margin-right:15px;">Licenses </span> <span
                                style="color: #000000; font-weight:500">{{ $Program->max_lic }}</span>
                        </div>
                    </div>
                </div>
@php
                    $adoptionRate = ($adoptedUsers / $allUsers->count()) * 100;
                    @endphp
                <div class="col-8">
                    <div class="card">
                        <div style="padding: 8px 0px 8px 0px !important;"
                            class="card-body d-flex justify-content-center align-items-center p-4">
                            <span style="font-weight:600;color:#818181;margin-right:15px">Overall Adoption Rate</span> <span
                                style="color: #000000; font-weight:500">{{number_format($adoptionRate, 2) }} %</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card w-100" style="width:576px; height:127px;">
                <div class="card-body p-4">
                    <h6>Overall Adoption</h6>
                    <h4><strong>{{ $adoptedUsers }} Employees</strong></h4>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted" id="min-value">0</span>
                        <div class="progress w-100 mx-3">
                            <div class="progress-bar" role="progressbar" style="width:{{ round($adoptionRate) }}%;"
                    aria-valuenow="{{ $adoptedUsers }}"
                    aria-valuemin="0"
                    aria-valuemax="{{ $allUsers->count() }}"></div>
                        </div>
                        <span class="text-muted" id="max-value">{{ $allUsers->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection
