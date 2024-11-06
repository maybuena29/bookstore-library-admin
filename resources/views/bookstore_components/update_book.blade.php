@extends('main_layout')
@section('css')
    <link rel="stylesheet" href="{{ url('css/form.css') }}">
@endsection

@section('content')
@section('title', 'Update Book')

<div class="main">
    <div class="name">
        <h4>Bookstore</h4>
    </div>
    <div>
        <x-bladewind::notification />

        <x-bladewind::card>
            <form action="{{ route('books.update', $books->book_id) }}" method="POST" class="create-book-form"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <h1>Update Book</h1>

                <div class="mb-4">
                    <x-bladewind::input name="book_name" label="Book Name" :value="old('book_name', $books->book_name)" />
                    @error('book_name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4 w-full">
                    <x-bladewind::input name="author" label="Author" :value="old('author', $books->author)" />
                    @error('author')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <label for="cover_file" class="mb-2">
                    <div style="display: flex; justify-content: center;"><span>Current Book Cover</span></div>
                </label>
                <div class="mb-4">
                    {{-- FOR VIEWING IMAGE IN UPDATE --}}
                    @if ($books->book_cover)
                        <div class="current-cover-preview mb-2">
                            <img src="{{ url($books->book_cover) }}" alt="Current Book Cover"
                                style="max-width: 400px; max-height: 400px; display: block; margin: auto;" />
                        </div>
                    @endif

                    <x-bladewind::filepicker name="book_cover" required="true" placeholder="Change book cover"
                        accepted_file_types="image/*" error_message="Please upload a cover photo"
                        show_error_inline="true" />
                    <input type="hidden" name="book_cover" id="book_cover" />
                    @error('book_cover')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-center">
                    <x-bladewind::button name="btn-save" type="submit" can_submit="true">
                        Update Book
                    </x-bladewind::button>
                    <x-bladewind::button name="btn-save" type="secondary"
                        onclick="window.location.href='{{ route('books.index') }}'">
                        Back
                    </x-bladewind::button>
                </div>
            </form>
        </x-bladewind.card>
    </div>
</div>
@endsection
