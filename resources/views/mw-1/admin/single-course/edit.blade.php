@extends('mw-1.layout.app')
@section('selected_menu', 'active')
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Update Single Course</h5>
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


                    <form action="{{ url('/manage-admin/update-single-course', ['id' => $editHome->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="titleId" class="form-label">Title</label>
                            <input type="text" class="form-control" id="titleId" aria-describedby="titleHelp"
                                name="title" placeholder="Enter title" required value="{{ $editHome->title }}">
                        </div>

                        <div class="mb-3">
                            <label for="subtitleId" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="subtitleId" aria-describedby="subtitleHelp"
                                name="subtitle" placeholder="Enter Subtitle" required value="{{ $editHome->subtitle }}">
                        </div>


                        <div class="mb-3">
                            <label for="durationId" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="durationId" aria-describedby="durationHelp"
                                name="duration" placeholder="Enter duration" required value="{{ $editHome->duration }}">
                        </div>


                        <div class="mb-3">
                            <label for="colorId" class="form-label">Course Favorite Color</label>
                            <input type="text" class="form-control" id="colorId"
                                aria-describedby="colorHelp" name="favorite_color"
                                placeholder="Enter Color Code Ex:#000000" required value="{{ $editHome->color }}">
                        </div>

                        <div class="mb-3">
                            <label for="imageId" class="form-label">Image</label>
                            <input type="file" class="form-control" id="imageId" aria-describedby="imageHelp"
                                name="image" >
                            <br>
                            <label for="">Old image:</label>
                            <img height="50px" width="50px" class="popup"
                        src="{{ asset('storage/SingleCourse') }}/{{ $editHome->image }}"
                        alt="">
                        </div>

                        <div class="mb-3">
                            <label for="single_audioId" class="form-label">Single audio</label>
                            <input type="file" class="form-control" id="single_audioId" aria-describedby="single_audioHelp"
                                name="single_audio" placeholder="Enter Music Audio" >
                            <br>
                            <label for="">Old Audio:</label>
                             <audio controls="" style="vertical-align: middle" src="{{ asset('storage/') }}/{{ $editHome->single_audio }}" type="audio/mp3" controlslist="nodownload"></audio>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
