@extends('mw-1.layout.app')
@section('selected_menu', 'active')
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Course Category</h5>
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

                <form action="{{ url('/manage-admin/update-category',['id'=>$editCategory->id] )}}" method="POST"  enctype="multipart/form-data">
                    @csrf

                        <div class="mb-3">
                            <label for="nameId" class="form-label">Course Category Name</label>
                            <input type="text" class="form-control" id="nameId" aria-describedby="nameHelp"
                                name="name" placeholder="Enter name" required value="{{ $editCategory->name }}">
                        </div>

                        <button type="submit" class="btn btn-primary mindway-btn">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
