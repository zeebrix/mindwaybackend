@extends('mw-1.layout.app')
@section('selected_menu', 'active')
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Update Music</h5>
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



                    <form action="{{ url('/manage-admin/update-music', ['id' => $editMusic->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="titleId" class="form-label">Title</label>
                            <input type="text" class="form-control" id="titleId" aria-describedby="titleHelp"
                                name="title" placeholder="Enter title" required value="{{ $editMusic->title }}">
                        </div>

                        <div class="mb-3">
                            <label for="subtitleId" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="subtitleId" aria-describedby="subtitleHelp"
                                name="subtitle" placeholder="Enter Subtitle" required value="{{ $editMusic->subtitle }}">
                        </div>


                        <div class="mb-3">
                            <label for="durationId" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="durationId" aria-describedby="durationHelp"
                                name="duration" placeholder="Enter duration" required
                                value="{{ $editMusic->duration }}">
                        </div>


                        {{-- <div class="mb-3">
                            <label for="course_durationId" class="form-label">Course Duration</label>
                            <input type="text" class="form-control" id="durationId"
                                aria-describedby="course_durationHelp" name="course_duration"
                                placeholder="Enter Course duration" required value="{{ $editMusic->course_duration }}">
                        </div> --}}

                        <div class="mb-3">
                            <label for="imageId" class="form-label">Image</label>
                            <input type="file" class="form-control" id="imageId" aria-describedby="imageHelp"
                                name="image" >
                            <br>
                            <label for="">Old image:</label>
                             <img height="50px" width="50px" class="popup"
                        src="{{ asset('storage/music') }}/{{ $editMusic->image }}"
                        alt="">
                        </div>

                        <div class="mb-3">
                            <label for="music_audioId" class="form-label">Music audio</label>
                            <input type="file" class="form-control" id="music_audioId" aria-describedby="music_audioHelp"
                                name="music_audio" placeholder="Enter Music Audio" >
                            <br>
                            <label for="">Old Audio:</label>
                              <audio controls="" style="vertical-align: middle" src="{{ asset('storage/') }}/{{ $editMusic->music_audio }}" type="audio/mp3" controlslist="nodownload"></audio>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
