@extends('main_layout')
@section('css')
    <link rel="stylesheet" href="{{ url('css/form.css') }}">
@endsection

@section('content')
@section('title', 'Create Book')

<div class="main">
    <div class="name">
        <h4>Bookstore</h4>
    </div>
    <div>
        <x-bladewind::notification />

        <x-bladewind::card>
            <form action="{{ route('books.store') }}" method="post" class="create-book-form" enctype="multipart/form-data">
                @csrf
                <h1>Create Book</h1>

                <div class="mb-4">
                    <x-bladewind::input name="book_name" required="true" label="Book Name"
                        error_message="Please enter a book name" show_error_inline="true" />
                    @error('book_name')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <x-bladewind::input name="author" required="true" label="Author"
                        error_message="Please enter an author" show_error_inline="true" />
                    @error('author')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <label for="cover_file" class="mb-2">
                    <div style="display: flex; justify-content: center;"><span>Choose Book Cover</span></div>
                </label>
                <div class="mb-4">
                    <x-bladewind::filepicker name="book_cover" required="true" placeholder="Upload book cover"
                        accepted_file_types="image/*" error_message="Please upload a cover photo"
                        show_error_inline="true" :value="old('book_cover')"
                        onchange="handleFileChange(this)"/>
                    <input type="hidden" name="book_cover" id="book_cover" />
                    @error('book_cover')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-center">
                    <x-bladewind::button name="btn-save" type="primary" can_submit="true">
                        Add Book
                    </x-bladewind::button>
                    <x-bladewind::button name="btn-save" type="secondary" onclick="window.location.href='{{ route('books.index') }}'">
                        Back
                    </x-bladewind::button>
                </div>
            </form>
        </x-bladewind::card>
    </div>
</div>

<script>
    function handleFileChange(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('book_cover').value = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
