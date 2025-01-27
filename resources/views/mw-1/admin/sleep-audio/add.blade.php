@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Sleep Audio</h5>
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
                    <form action="{{ url('/manage-admin/sleep-audio-add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="courseSelect" class="form-label">Choose Course</label>
                            <select id="courseSelect" class="form-select" name="course_id">
                                @foreach ($courseList as $course)
                                    <option name="course_id" value="{{ $course->id }}">{{ $course->id }} - {{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="durationId" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="durationId" aria-describedby="durationHelp"
                                name="duration" placeholder="Enter duration" required>
                        </div>

                        {{-- <div class="mb-3">
                            <label for="audio_titleId" class="form-label">Audio Title</label>
                            <input type="text" class="form-control" id="audio_titleId" aria-describedby="audio_titleHelp"
                                name="audio_title" placeholder="Enter audio Title" required>
                        </div> --}}

                        <div class="mb-3">
                            <label for="audioId" class="form-label">Audio</label>
                            <input type="file" class="form-control" id="audioId" aria-describedby="audioHelp"
                                name="audio" placeholder="Enter Audio" required>
                        </div>

                        <div class="mb-3">
                            <label for="TitleId" class="form-label">Title</label>
                            <input type="text" class="form-control" id="TitleId" aria-describedby="TitleHelp"
                                name="title" placeholder="Enter Title" required>
                        </div>

                        <div class="mb-3">
                            <label for="imageId" class="form-label">Image</label>
                            <input type="file" class="form-control" id="imageId" aria-describedby="imageHelp"
                                name="image" placeholder="Enter image" required>
                        </div>

                        <div class="mb-3">
                            <label for="descriptionId" class="form-label">Description</label>
                            <input type="text" class="form-control" id="descriptionId" aria-describedby="descriptionHelp"
                                name="description" placeholder="Enter Description" required>
                        </div>


                        <div class="mb-3">
                            <label for="favorite_colorId" class="form-label">Course Favorite Color</label>
                            <input type="text" class="form-control" id="favorite_colorId" aria-describedby="favorite_colorHelp"
                                name="favorite_color" placeholder="Enter favorite color" required>
                        </div>



                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
