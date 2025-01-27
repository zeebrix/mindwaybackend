@extends('mw-1.layout.app')
@section('selected_menu', 'active')
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Update Sleep course</h5>
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

                    <form action="{{ url('/manage-admin/update-sleep-course', ['id' => $getSleepCourse->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="disabledSelect" class="form-label">Choose Course</label>
                            <select id="disabledSelect" class="form-select" name="category_id">
                                @foreach ($categoryList as $session)
                                    <option @if ($session->id == $getSleepCourse->category_id) selected @endif value="{{ $session->id }}">
                                        {{ $session->id }} -
                                        {{ $session->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Title </label>
                            <input type="text" class="form-control" value="{{ $getSleepCourse->title }}"
                                placeholder="Enter title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="">Description</label>
                            <input type="text" class="form-control" value="{{ $getSleepCourse->description }}"
                                placeholder="Enter description" name="description" required>
                        </div>


                        {{-- <div class="form-group">
                            <label for="">Duration:</label>
                            <input type="text" class="form-control" value="{{ $getSleepCourse->duration }}"
                                placeholder="Enter audio duration" name="duration" required>
                        </div>

                        <div class="form-group">
                            <label for="">Color</label>
                            <input type="text" class="form-control" value="{{ $getSleepCourse->color }}"
                                placeholder="Enter color code" name="color" required>
                        </div> --}}

                        <div class="form-group">
                            <label for="">image</label>
                            <input type="file" class="form-control" value="{{ $getSleepCourse->image }}"
                                placeholder="Enter image code" name="image">
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="">Old image:</label>
                            <img height="50px" width="50px" class="popup"
                                src="{{ asset('storage/course') }}/{{ $getSleepCourse->thumbnail }}" alt="">
                            </td>
                        </div>

                        {{-- <div class="form-group">
                            <label for="audio">Audio File:</label>
                            <input type="file" class="form-control" name="audio" id="audio">
                            <br>
                            <!-- Display current audio value if available -->
                            @if (isset($getSleepCourse->audio))
                                <audio controls="" style="vertical-align: middle"
                                    src="{{ asset('storage/') }}/{{ $getSleepCourse->audio }}" type="audio/mp3"
                                    controlslist="nodownload"></audio>
                            @endif

                            <!-- Display errors if there are any -->
                            @error('audio')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div> --}}
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
