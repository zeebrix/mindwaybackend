@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add account links</h5>
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
              <form action="{{ url('/manage-admin/links-add' )}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                        <div class="mb-3">
                            <label for="titleId" class="form-label">Title</label>
                            <input type="text" class="form-control" id="titleId" aria-describedby="titleHelp"
                                name="title" placeholder="Enter title" required>
                        </div>

                        <div class="mb-3">
                            <label for="SubtitleId" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="SubtitleId" aria-describedby="SubtitleHelp"
                                name="sub_title" placeholder="Enter Subtitle" required>
                        </div>

                        <div class="mb-3">
                            <label for="url_nameId" class="form-label">Url name</label>
                            <input type="text" class="form-control" id="url_nameId" aria-describedby="url_nameHelp"
                                name="url_name" placeholder="Enter Url name" required>
                        </div>

                        <div class="mb-3">
                            <label for="iconId" class="form-label">Icon</label>
                            <input type="file" class="form-control" id="iconId" aria-describedby="iconHelp"
                                name="icon" placeholder="Browse icon" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
