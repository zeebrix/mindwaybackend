@extends('mw-1.layout.app')
@section('selected_menu', 'active')
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Update account links</h5>
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

                <form action="{{ url('/manage-admin/update-links',['id'=>$getUpdateLinks->id] )}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                        <div class="mb-3">
                            <label for="titleId" class="form-label">Title</label>
                            <input type="text" class="form-control" id="titleId" aria-describedby="titleHelp"
                                name="title" placeholder="Enter title" required value="{{ $getUpdateLinks->title }}">
                        </div>

                        <div class="mb-3">
                            <label for="SubtitleId" class="form-label">Subtitle</label>
                            <input type="text" class="form-control" id="SubtitleId" aria-describedby="SubtitleHelp"
                                name="sub_title" placeholder="Enter Subtitle" required value="{{ $getUpdateLinks->title }}">
                        </div>

                        <div class="mb-3">
                            <label for="url_nameId" class="form-label">Url name</label>
                            <input type="text" class="form-control" id="url_nameId" aria-describedby="url_nameHelp"
                                name="url_name" placeholder="Enter Url name" required value="{{ $getUpdateLinks->url_name }}">
                        </div>

                        <div class="mb-3">
                            <label for="iconId" class="form-label">icon</label>
                            <input type="file" class="form-control" id="iconId" aria-describedby="iconHelp"
                                name="icon" placeholder="Enter icon" >
                            <br>
                           <label for="">Old icon:</label>
                        <img height="50px" width="50px" class="popup"
                            src="{{ asset('storage/links') }}/{{ $getUpdateLinks->icon }}"
                            alt="No image upload">

                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
