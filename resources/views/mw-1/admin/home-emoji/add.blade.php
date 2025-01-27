@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Home Emoji</h5>
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


                <form action="{{ url('/manage-admin/home-emoji-add' )}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                        <div class="mb-3">
                            <label for="nameId" class="form-label">Emoji name</label>
                            <input type="text" class="form-control" id="nameId" aria-describedby="nameHelp"
                                name="name" placeholder="Enter Emoji name" required>
                        </div>



                        <div class="mb-3">
                            <label for="home_emojiId" class="form-label">Home Emoji</label>
                            <input type="file" class="form-control" id="home_emojiId" aria-describedby="home_emojiHelp"
                                name="home_emoji" placeholder="Browse Home Emoji" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
