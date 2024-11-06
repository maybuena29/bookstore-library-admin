@extends('main_layout')
@section('css')
    <link rel="stylesheet" href="{{ url('css/form.css') }}">
@endsection

@section('content')
@section('title', 'View Book')

<div class="main">
    <div class="name">
        <h4>Bookstore</h4>
    </div>
    <div>
        <x-bladewind::notification />

        <x-bladewind::card>
            <form method="post" class="create-book-form" enctype="multipart/form-data">
                @csrf
                <h1>View Book</h1>

                <div class="mb-4">
                    <x-bladewind::input name="book_name" label="Book Name" :value="old('book_name', $books->book_name)" readOnly/>
                    @error('book_name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4 w-full">
                    <x-bladewind::input name="author" label="Author" :value="old('author', $books->author)" readOnly/>
                    @error('author')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <label for="cover_file" class="mb-2">
                    <div style="display: flex; justify-content: center;"><span>Book Cover</span></div>
                </label>
                <div class="mb-4">
                    <img src="{{$books->book_cover? url($books->book_cover) : url('book_image_default.png')}}" alt="" value="{{ $books->book_cover? url($books->book_cover) : url('book_image_default.png') }}" style="max-width: 400px; max-height: 400px; display: block; margin: auto;" >
                    @error('book_cover')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-center">
                    <x-bladewind::button name="btn-save" type="secondary" onclick="window.location.href='{{ route('books.index') }}'">
                        Back
                    </x-bladewind::button>
                </div>
            </form>
        </x-bladewind::card>
    </div>
</div>
@endsection
