<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Student Management System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex flex-col">
            <!-- Site Header -->
            <header class="bg-gray-800 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 text-sm flex justify-between">
                    <span class="font-semibold">{{ config('app.name', 'Student Management System') }}</span>
                    <span>{{ Auth::check() && Auth::user()->getRoleNames()->isNotEmpty() ? Auth::user()->getRoleNames()->implode(', ') : __('Not signed in') }}</span>
                </div>
            </header>

            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                    <div class="rounded-md bg-green-50 border border-green-300 p-4 text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                    <div class="rounded-md bg-red-50 border border-red-300 p-4 text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Validation Error Summary -->
            @if ($errors->any())
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                    <div class="rounded-md bg-red-50 border border-red-300 p-4 text-sm text-red-800">
                        {{ __('Please correct the following problems:') }}

                        <ul class="mt-2 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <!-- Site Footer -->
            <footer class="bg-white border-t border-gray-200 mt-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-sm text-gray-500">
                    &copy; {{ now()->year }} {{ config('app.name', 'Student Management System') }}. {{ __('All rights reserved.') }}
                </div>
            </footer>
        </div>
    </body>
</html>
