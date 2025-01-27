@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Sleep Screen</h5>
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


                <form action="{{ url('/manage-admin/sleep-screen-add' )}}" method="POST"  enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="audio_titleId" class="form-label">Audio title</label>
                            <input type="text" class="form-control" id="audio_titleId" aria-describedby="audio_titleHelp"
                                name="audio_title" placeholder="Enter Audio title" required>
                        </div>

                        {{-- <div class="mb-3">
                            <label for="SubtitleId" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="SubtitleId" aria-describedby="SubtitleHelp"
                                name="subtitle" placeholder="Enter Subtitle" required>
                        </div> --}}

                        <div class="mb-3">
                            <label for="durationId" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="durationId" aria-describedby="durationHelp"
                                name="duration" placeholder="Enter duration" required>
                        </div>
  <div class="mb-3">
                            <label for="sleep_audioId" class="form-label">Sleep Audio</label>
                            <input type="file" class="form-control" id="sleep_audioId" aria-describedby="sleep_audioHelp"
                                name="sleep_audio" placeholder="Enter Sleep Audio" required>
                        </div>
                        <div class="mb-3">
                            <label for="imageId" class="form-label">Sleep Image</label>
                            <input type="file" class="form-control" id="imageId" aria-describedby="imageHelp"
                                name="image" placeholder="Enter image" required>
                        </div>


                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
