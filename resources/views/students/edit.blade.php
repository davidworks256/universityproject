@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-xl font-semibold text-gray-900">
                {{ __('Edit student: :number', ['number' => $student->student_number]) }}
            </h1>

            <form method="POST" action="{{ route('students.update', $student) }}" class="mt-6 bg-white p-6 shadow-sm sm:rounded-lg">
                @method('PUT')
                @include('students.form', ['student' => $student, 'submitLabel' => __('Save changes')])
            </form>
        </div>
    </div>
@endsection
