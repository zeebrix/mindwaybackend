@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Course</h5>
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

                    <form action="{{ url('/manage-admin/course-add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="course_titleId" class="form-label">Course Title</label>
                            <input type="text" class="form-control" id="course_titleId"
                                aria-describedby="course_titleHelp" name="course_title" placeholder="Enter course title"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="course_descriptionId" class="form-label">Course Description</label>
                            <input type="text" class="form-control" id="course_descriptionId"
                                aria-describedby="course_descriptionHelp" name="course_description"
                                placeholder="Enter course Description" required>
                        </div>

                        <div class="mb-3">
                            <label for="course_durationId" class="form-label">Course Duration</label>
                            <input type="text" class="form-control" id="course_durationId"
                                aria-describedby="course_durationHelp" name="course_duration"
                                placeholder="Enter course duration" required>
                        </div>

                        <div class="mb-3">
                            <label for="course_thumbnailId" class="form-label">Course Thumbnail</label>
                            <input type="file" class="form-control" id="course_thumbnailId"
                                aria-describedby="course_thumbnailHelp" name="course_thumbnail"
                                placeholder="Enter course thumbnail" required>
                        </div>

                        <div class="mb-3">
                            <label for="favorite_colorId" class="form-label">Course Favorite Color</label>
                            <input type="text" class="form-control" id="favorite_colorId"
                                aria-describedby="favorite_colorHelp" name="favorite_color"
                                placeholder="Enter Color Code Ex:#000000" required>
                        </div>

                        {{-- <div class="mb-3">
                            <label for="audio_titleId" class="form-label">Audio title</label>
                            <input type="text" class="form-control" id="audio_titleId"
                                aria-describedby="audio_titleHelp" name="audio_title"
                                placeholder="Enter audio title" required>
                        </div> --}}

                        {{-- <div class="mb-3">
                            <label for="audioId" class="form-label">Audio</label>
                            <input type="file" class="form-control" id="audioId" aria-describedby="audioHelp"
                                name="audio">
                        </div> --}}
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
