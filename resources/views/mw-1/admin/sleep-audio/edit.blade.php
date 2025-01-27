@extends('mw-1.layout.app')
@section('selected_menu', 'active')
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Sleep Course</h5>
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

                    <form action="{{ url('/manage-admin/update-sleep_audio', ['id' => $editSleepAudio->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="disabledSelect" class="form-label">Choose Course</label>
                            <select id="disabledSelect" class="form-select">
                                @foreach ($courseList as $session)
                                    <option @if ($session->id == $editSleepAudio->session_id) selected @endif name="session_id"
                                        value="{{ $session->id }}">{{ $session->id }} -
                                        {{ $session->title ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Title </label>
                            <input type="text" class="form-control" value="{{ $editSleepAudio->title }}"
                                placeholder="Enter title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="">Description</label>
                            <input type="text" class="form-control" value="{{ $editSleepAudio->description }}"
                                placeholder="Enter description" name="description" required>
                        </div>


                        <div class="form-group">
                            <label for="">Duration:</label>
                            <input type="text" class="form-control" value="{{ $editSleepAudio->duration }}"
                                placeholder="Enter audio duration" name="duration" required>
                        </div>

                        <div class="form-group">
                            <label for="">Color</label>
                            <input type="text" class="form-control" value="{{ $editSleepAudio->color }}"
                                placeholder="Enter color code" name="color" required>
                        </div>

                        <div class="form-group">
                            <label for="">image</label>
                            <input type="file" class="form-control" value="{{ $editSleepAudio->image }}"
                                placeholder="Enter image code" name="image" >
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="">Old image:</label>
                            <img height="50px" width="50px" class="popup"
                                src="{{ asset('storage/') }}/{{ $editSleepAudio->image }}" alt="">
                        </div>

                        <div class="form-group">
                            <label for="audio">Audio File:</label>
                            <input type="file" class="form-control" name="audio" id="audio">
                            <br>
                            <!-- Display current audio value if available -->
                            @if (isset($editSleepAudio->audio))
                                <audio controls="" style="vertical-align: middle"
                                    src="{{ asset('storage/') }}/{{ $editSleepAudio->audio }}" type="audio/mp3"
                                    controlslist="nodownload"></audio>
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
