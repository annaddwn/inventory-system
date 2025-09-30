<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventory System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @yield('content')

    @if(session('success'))
    <div id="alert-success" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded shadow-lg">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('alert-success').remove();
        }, 3000);
    </script>
    @endif

    @if(session('error'))
    <div id="alert-error" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded shadow-lg">
        {{ session('error') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('alert-error').remove();
        }, 3000);
    </script>
    @endif

    @yield('scripts')
</body>
</html>