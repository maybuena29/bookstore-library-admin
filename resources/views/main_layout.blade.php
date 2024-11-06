<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    @yield('css')

    {{-- FOR BLADEWIND UI --}}
    <link href="{{ url('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ url('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
    <script src="{{ url('vendor/bladewind/js/helpers.js') }}"></script>

    {{-- FOR DATA TABLE --}}
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

    {{-- CUSTOM CSS --}}
    <link rel="stylesheet" href="{{ url('css/main_container.css') }}">

    {{-- @livewireStyles --}}
    <x-bladewind::notification />
</head>

<body>
    {{-- FOR MAIN CONTENT --}}
    @yield('content')

    {{-- @livewireScripts --}}
    {{-- SCRIPT FOR DATA TABLE --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script>
        let table = new DataTable('#data-table');
    </script>

    <script>
        // FOR ALERT NOTIFICATION
        @if (Session::has('created'))
            showNotification('Success', '{{ Session::get('created') }}', 'success', 3, 'regular', 'notification');
        @endif

        @if (Session::has('updated'))
            showNotification('Updated', '{{ Session::get('updated') }}', 'info', 3, 'regular', 'notification');
        @endif

        @if (Session::has('deleted'))
            showNotification('Deleted', '{{ Session::get('deleted') }}', 'warning', 3, 'regular', 'notification');
        @endif

        @if ($errors->any())
            showNotification('Error', 'There is an error occured', 'error', 3, 'regular', 'notification');
        @endif

        @if (Session::has('error'))
            showNotification('Error', '{{ Session::get('error') }}', 'error', 3, 'regular', 'notification');
        @endif
    </script>
</body>

</html>
