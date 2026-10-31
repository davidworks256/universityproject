@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('Add student') }}</h1>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Creating a student also creates a sign-in account with the student role and the password "password".') }}
            </p>

            <form method="POST" action="{{ route('students.store') }}" class="mt-6 bg-white p-6 shadow-sm sm:rounded-lg">
                @include('students.form', ['student' => null, 'submitLabel' => __('Create student')])
            </form>
        </div>
    </div>
@endsection
