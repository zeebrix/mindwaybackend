@extends('mw-1.layout.app')
@section('selected_menu', 'active')
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Course</h5>
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

                <form action="{{ url('/manage-admin/update-course',['id' => $editCourse->id] )}}" method="POST"  enctype="multipart/form-data">
                    @csrf

                        <div class="mb-3">
                            <label for="course_titleId" class="form-label">Course Title</label>
                            <input type="text" class="form-control" id="course_titleId" aria-describedby="course_titleHelp"
                                name="course_title" placeholder="Enter course_title" required value="{{ $editCourse->course_title }}">
                        </div>

                        <div class="mb-3">
                            <label for="course_descriptionId" class="form-label">Course Description</label>
                            <input type="text" class="form-control" id="course_descriptionId" aria-describedby="course_descriptionHelp"
                                name="course_description" placeholder="Enter course_description" required value="{{ $editCourse->course_description }}">
                        </div>

                        <div class="mb-3">
                            <label for="course_durationId" class="form-label">Course Duration</label>
                            <input type="text" class="form-control" id="durationId" aria-describedby="course_durationHelp"
                                name="course_duration" placeholder="Enter Course duration" required value="{{ $editCourse->course_duration }}">
                        </div>

                        <div class="mb-3">
                            <label for="course_thumbnailId" class="form-label">Course Thumbnail</label>
                            <input type="file" class="form-control" id="course_thumbnailId" aria-describedby="course_thumbnailHelp"
                                name="course_thumbnail" placeholder="Enter course_thumbnail">
                            <br>
                            <label for="">Old image:</label>
                            <img height="50px" width="50px" class="popup"
                                src="{{ asset('storage/course') }}/{{ $editCourse->course_thumbnail }}" alt="">
                        </div>


     <div class="mb-3">
                            <label for="favorite_colorId" class="form-label">Course Favorite Color</label>
                            <input type="text" class="form-control" id="favorite_colorId"
                                aria-describedby="favorite_colorHelp" name="favorite_color"
                                placeholder="Enter Color Code Ex:#000000" required value="{{ $editCourse->color }}">
                        </div>
                        {{-- <div class="mb-3">
                            <label for="home_audioId" class="form-label">Course audio</label>
                            <input type="file" class="form-control" id="home_audioId" aria-describedby="home_audioHelp"
                                name="home_audio" placeholder="Enter Home Audio" required>
                            <br>
                            <label for="">Old Audio:</label>
                            <audio controls="" style="vertical-align: middle"
                                src="{{ asset('storage/') }}/{{ $editCourse->home_audio }}" type="audio/mp3"
                                controlslist="nodownload"></audio>

                        </div> --}}
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
