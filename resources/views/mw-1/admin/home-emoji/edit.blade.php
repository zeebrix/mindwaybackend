@extends('mw-1.layout.app')
@section('selected_menu', 'active')
@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Update Home Emoji</h5>
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

                <form action="{{ url('/manage-admin/update-home-emoji',['id'=>$editEmoji->id] )}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                        <div class="mb-3">
                            <label for="Emoji nameId" class="form-label">Emoji name</label>
                            <input type="text" class="form-control" id="Emoji nameId" aria-describedby="Emoji nameHelp"
                                name="name" placeholder="Enter Emoji name" required value="{{ $editEmoji->name }}">
                        </div>

                        <div class="mb-3">
                            <label for="home_emojiId" class="form-label">Home Emoji</label>
                            <input type="file" class="form-control" id="home_emojiId" aria-describedby="home_emojiHelp"
                                name="home_emoji" placeholder="Enter Home Emoji" >
                            <br>
                           <label for="">Home Emoji</label>
                      <img height="50px" width="50px" class="popup"
                            src="{{ asset('storage/homeEmoji') }}/{{ $editEmoji->home_emoji }}"
                            alt="emoji image">

                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection
