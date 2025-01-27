@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Sleep course</h5>
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

                    <form action="{{ url('/manage-admin/sleep-course-add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="categorySelect" class="form-label">Choose Category</label>
                            <select id="categorySelect" class="form-select" name="category_id">
                                @foreach ($categoryList as $category)
                                    <option value="{{ $category->id }}">{{ $category->id }} - {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="titleId" class="form-label">Title</label>
                            <input type="text" class="form-control" id="titleId" aria-describedby="titleHelp"
                                name="title" placeholder="Enter title" required>
                        </div>

                        <div class="mb-3">
                            <label for="descriptionId" class="form-label">Description</label>
                            <input type="text" class="form-control" id="descriptionId"
                                aria-describedby="descriptionHelp" name="description" placeholder="Enter Description"
                                required>
                        </div>

                        {{-- <div class="mb-3">
                            <label for="colorId" class="form-label">Course Color</label>
                            <input type="text" class="form-control" id="colorId" aria-describedby="colorHelp"
                                name="color" placeholder="Enter favorite color" required>
                        </div>

                        <div class="mb-3">
                            <label for="durationId" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="durationId" aria-describedby="durationHelp"
                                name="duration" placeholder="Enter Duration" required>
                        </div> --}}

                        <div class="mb-3">
                            <label for="thumbnailId" class="form-label">Thumbnail</label>
                            <input type="file" class="form-control" id="thumbnailId" aria-describedby="thumbnailHelp"
                                name="thumbnail" placeholder="Enter thumbnail" required>
                        </div>

                        {{-- <div class="mb-3">
                            <label for="audioId" class="form-label">Audio</label>
                            <input type="file" class="form-control" id="audioId" aria-describedby="audioHelp"
                                name="audio" placeholder="Enter Audio" required>
                        </div> --}}

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
