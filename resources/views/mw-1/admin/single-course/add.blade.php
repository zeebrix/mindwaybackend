@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Single Course</h5>
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


                    <form action="{{ url('/manage-admin/single-course-add') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="titleId" class="form-label">Title</label>
                            <input type="text" class="form-control" id="titleId" aria-describedby="titleHelp"
                                name="title" placeholder="Enter title" required>
                        </div>

                        <div class="mb-3">
                            <label for="subtitleId" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="subtitleId" aria-describedby="subtitleHelp"
                                name="subtitle" placeholder="Enter subtitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="durationId" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="durationId" aria-describedby="durationHelp"
                                name="duration" placeholder="Enter duration" required>
                        </div>

                        <div class="mb-3">
                            <label for="favorite_colorId" class="form-label">Course Favorite Color</label>
                            <input type="text" class="form-control" id="favorite_colorId" aria-describedby="favorite_colorHelp"
                                name="favorite_color" placeholder="Enter Color Code Ex:#000000" required>
                        </div>


                        <div class="mb-3">
                            <label for="imageId" class="form-label">Image</label>
                            <input type="file" class="form-control" id="imageId" aria-describedby="imageHelp"
                                name="image" placeholder="Enter image" required>
                        </div>

                        <div class="mb-3">
                            <label for="single_audioId" class="form-label">Single Audio</label>
                            <input type="file" class="form-control" id="single_audioId" aria-describedby="single_audioHelp"
                                name="single_audio" placeholder="Enter Music Audio" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
