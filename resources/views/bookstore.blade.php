@extends('main_layout')
@section('css')
    <link rel="stylesheet" href="{{ url('css/books_layout.css') }}">
@endsection

@section('content')
@section('title', 'Bookstore')
<div class="main">
    <div class="add-btn-container">
        <x-bladewind::button class="add-btn" onclick="window.location.href='{{ route('books.create') }}'">Add Book</x-bladewind::button>
        <x-bladewind::button class="import-btn" onclick="showModal('import')">Import File</x-bladewind::button>
        <x-bladewind::button class="export-btn" onclick="window.location.href='{{ Route('export') }}'">Export File</x-bladewind::button>

        {{-- MODAL FOR IMPORT --}}
        <x-bladewind::modal title="Import" name="import" blur_size="medium" size="big" backdrop_can_close="false" ok_button_action="handleImport()" ok_button_label="Import">
            <form action="{{ Route('import') }}" method="post" id="import" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <x-bladewind::filepicker name="excel_file" required="true" placeholder="Upload import file"
                        accepted_file_types=".xlsx" error_message="Please upload an excel file"
                        show_error_inline="true"
                        onchange="handleFileChange(this)"/>
                    <input type="hidden" name="excel_file" id="excel_file" />
                    @error('excel_file')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </form>
        </x-bladewind::modal>
    </div>
    <div class="name">
        <h4>Bookstore</h4>
    </div>
    <div class="table-container">
        <x-bladewind::card class="card">
            <table class="data-table" id="data-table">
                <thead>
                    <tr>
                        <th>Book Name</th>
                        <th>Author</th>
                        <th>Book Cover</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($books as $book)
                        <tr>
                            <td>{{ $book->book_name }}</td>
                            <td>{{ $book->author }}</td>
                            <td class="center-image">
                                <img src="{{ $book->book_cover ? url($book->book_cover) : url('book_image_default.png') }}"
                                    alt="" />
                            </td>
                            <td class="action-td">
                                <div class="action-icons">
                                    <x-antdesign-eye class="view-btn" {{-- onclick="window.location.href='{{ Route('books.show', $book->book_id) }}'"  --}}
                                        onclick="showModal('view_{{ $book->book_id }}')" />
                                    <x-fas-edit class="edit-btn"
                                        onclick="window.location.href='{{ Route('books.edit', $book->book_id) }}'" />
                                    <x-eos-delete-forever class="delete-btn"
                                        onclick="showModal('delete_{{ $book->book_id }}')" />
                                    {{-- FORM FOR DELETE --}}
                                    <form id="delete-{{ $book->book_id }}"
                                        action="{{ Route('books.destroy', $book->book_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    {{-- MODAL FOR VIEWING --}}
                                    <x-bladewind::modal title="View Book" name="view_{{ $book->book_id }}"
                                        blur_size="medium" backdrop_can_close="false" size='large'
                                        cancel_button_label="">
                                        <form method="post" class="create-book-form" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mt-6">
                                                <x-bladewind::input name="book_name" label="Book Name" :value="old('book_name', $book->book_name)"
                                                    readOnly />
                                            </div>

                                            <div class="mb-4 w-full">
                                                <x-bladewind::input name="author" label="Author" :value="old('author', $book->author)"
                                                    readOnly />
                                            </div>

                                            <div class="mb-4 w-full">
                                                <x-bladewind::input name="created_at" label="Date Created" :value="old('author', \Carbon\Carbon::parse($book->created_at)->format('m-d-Y'))"
                                                    readOnly />
                                            </div>

                                            <div class="mb-4 w-full">
                                                <x-bladewind::input name="updated_at" label="Date Updated" :value="old('author', \Carbon\Carbon::parse($book->updated_at)->format('m-d-Y'))"
                                                    readOnly />
                                            </div>

                                            <label for="cover_file" class="mb-2">
                                                <div style="display: flex; justify-content: center;">
                                                    <span>Book Cover</span>
                                                </div>
                                            </label>
                                            <div class="mb-4 w-full">
                                                <img src="{{ $book->book_cover ? url($book->book_cover) : url('book_image_default.png') }}"
                                                    alt=""
                                                    value="{{ $book->book_cover ? url($book->book_cover) : url('book_image_default.png') }}"
                                                    style="width: 300px; height: auto; display: block; margin: auto;">
                                            </div>
                                        </form>
                                    </x-bladewind::modal>

                                    {{-- MODAL FOR DELETE --}}
                                    <x-bladewind::modal title="Archived" name="delete_{{ $book->book_id }}"
                                        blur_size="medium" type="warning" backdrop_can_close="false"
                                        ok_button_action="submitDeleteForm('{{ $book->book_id }}')">
                                        Are you sure you want to delete this data?
                                    </x-bladewind::modal>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-bladewind::card>
    </div>
</div>
<script>
    function submitDeleteForm(book_id) {
        const form = document.getElementById('delete-' + book_id);
        if (form) {
            form.submit();
        } else {
            console.error(`Form with ID delete-${book_id} not found.`);
        }
    }

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

    function handleImport() {
        const form = document.getElementById('import');
        if (form) {
            form.submit();
        } else {
            console.error(`Form with ID import not found.`);
        }
    }
</script>


@endsection
