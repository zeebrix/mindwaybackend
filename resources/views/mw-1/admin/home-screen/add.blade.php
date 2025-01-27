@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Home Screen</h5>
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

                            <form action="{{ url('/manage-admin/home-add') }}" method="POST"
                                enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="titleId" class="form-label">Title</label>
                            <input type="text" class="form-control" id="titleId" aria-describedby="titleHelp"
                                name="title" placeholder="Enter Title" required>
                        </div>

                        <div class="mb-3">
                            <label for="SubtitleId" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="SubtitleId" aria-describedby="SubtitleHelp"
                                name="subtitle" placeholder="Enter Subtitle" required>
                        </div>

                        <div class="mb-3">
                            <label for="durationId" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="durationId" aria-describedby="durationHelp"
                                name="duration" placeholder="Enter duration" required>
                        </div>

                        <div class="mb-3">
                            <label for="imageId" class="form-label">image</label>
                            <input type="file" class="form-control" id="imageId" aria-describedby="imageHelp"
                                name="image" placeholder="Enter image" required>
                        </div>

                        <div class="mb-3">
                            <label for="home_audioId" class="form-label">Home Audio</label>
                            <input type="file" class="form-control" id="home_audioId" aria-describedby="home_audioHelp"
                                name="home_audio" placeholder="Enter Home Audio" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
