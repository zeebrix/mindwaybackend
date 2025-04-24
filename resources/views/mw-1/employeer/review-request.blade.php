<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Request</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 w-100" style="max-width: 600px;">
            <div class="card-body">
                <h4 class="card-title mb-4 text-center">Session Request R#{{ $reqSession->id}} </h4>

                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                <h6><strong>Client Name:</strong> {{ $emp->name }}</h6>
                <h6><strong>Client Email:</strong> {{ $emp->email }}</h6>
                <h6><strong>Client ID:</strong> {{ $emp->id }}</h6>
                <h6><strong>Counsellor:</strong> {{ $counsellor_name }}</h6>
                <h6><strong>Reason(s) for the sessions:</strong> {{ $reqSession->reasons }}</h6>
                @if($reqSession->status == 'pending')
                <h6><strong>Date Requested:</strong> {{ $reqSession->request_date }}</h6>
                @elseif($reqSession->status == 'accepted')
                <h6><strong>Date Accepted:</strong> {{ $reqSession->accepted_date }}</h6>
                @else
                <h6><strong>Date Denied:</strong> {{ $reqSession->denied_date }}</h6>
                @endif
                
                <!-- Approve Sessions Form -->
                @if($reqSession->status == 'pending')

                <form action="{{ route('approvedsession') }}" method="POST" class="form-section">
                    @csrf
                    <input type="hidden" name="requestedId" id="requestedId" value="{{ $reqSession->id }}">

                    <hr>
                    <h6 class="mb-3"><strong>Recommended Request:</strong> How many additional sessions?</h6>

                    <div class="d-flex flex-wrap justify-content-start" style="gap: 10px;">
                        @for ($i = 1; $i <= 5; $i++)
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="request_session_count"
                                    id="session-count-{{ $i }}" value="{{ $i }}" autocomplete="off" {{ $i == $reqSession->request_days ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary rounded-pill px-4"
                                    for="session-count-{{ $i }}">{{ $i }}</label>
                            </div>
                        @endfor
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary w-100">Approve Sessions</button>
                        </div>
                    </div>
                </form>

                <!-- Deny Sessions Form -->
                <form action="{{ route('denysession') }}" method="POST" class="form-section">
                    @csrf
                    <input type="hidden" name="requestedId" id="requestedIdDeny" value="{{ $reqSession->id }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-danger w-100">Deny Sessions</button>
                        </div>
                    </div>
                </form>
                @endif


            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
