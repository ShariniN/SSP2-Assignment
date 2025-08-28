<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ElectroMart')</title>
    @vite('resources/css/app.css')
</head>
<body class="font-sans bg-gray-100 text-gray-900">

    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-blue-600">ElectroMart</div>
        <ul class="flex space-x-6 text-gray-700 font-semibold">
            @php
                $categories = ['Phones', 'Laptops', 'Audio', 'Wearables', 'Accessories'];
            @endphp
            @foreach($categories as $category)
                <li><a href="#" class="hover:text-blue-500">{{ $category }}</a></li>
            @endforeach
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-inner p-4 text-center text-gray-500 mt-10">
        &copy; {{ date('Y') }} ElectroMart. All rights reserved.
    </footer>

</body>
</html>
