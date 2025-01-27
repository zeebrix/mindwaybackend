@extends('mw-1.layout.app')
@section('selected_menu', 'active')
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Course Audio</h5>
            <div>
                <div>

                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session()->get('message') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    @endif

                    <form action="{{ url('/manage-admin/update-audio') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="disabledSelect" class="form-label">Choose Course</label>
                            <select id="disabledSelect" class="form-select" name="session_id">
                                @foreach ($sessionList as $session)
                                    <option @if ($session->id == $getAudio->session_id) selected @endif
                                        value="{{ $session->id }}">{{ $session->id }} -
                                        {{ $session->course_title ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Duration:</label>
                            <input type="text" class="form-control" value="{{ $getAudio->duration }}"
                                placeholder="Enter audio duration" name="duration" required>
                        </div>

                        <div class="form-group">
                            <label for="">Audio title:</label>
                            <input type="text" class="form-control" value="{{ $getAudio->audio_title }}"
                                placeholder="Enter audio title" name="audio_title" required>
                            <input type="hidden" name="audio_id" value="{{ $getAudio->id }}">
                        </div>

                        <div class="form-group">
                            <label for="audio">Audio File:</label>
                            <input type="file" class="form-control" name="audio" id="audio">

                            <!-- Display current audio value if available -->
                            @if (isset($getAudio->audio))
                                <p>Current Audio: {{ $getAudio->audio }}</p>
                            @endif

                            <!-- Display errors if there are any -->
                            @error('audio')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div> <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
