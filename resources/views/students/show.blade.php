@extends('layouts.app')

@php
    use Illuminate\Support\Carbon;
@endphp

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ $student->name }}</h1>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ $student->student_number }}
                        &middot; {{ $student->email }}
                        @if ($student->phone)
                            &middot; {{ $student->phone }}
                        @endif
                    </p>
                </div>

                @can('manage students')
                    <a href="{{ route('students.edit', $student) }}"
                       class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                        {{ __('Edit') }}
                    </a>
                @endcan
            </div>

            <div class="mt-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <h2 class="border-b border-gray-100 px-4 py-3 text-sm font-semibold text-gray-700">
                    {{ __('Registered courses') }}
                </h2>

                @if ($student->courses->isEmpty())
                    <p class="px-4 py-6 text-sm text-gray-500">{{ __('This student is not registered for any course yet.') }}</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">{{ __('Code') }}</th>
                                <th class="px-4 py-3">{{ __('Title') }}</th>
                                <th class="px-4 py-3">{{ __('Lecturer') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('Credits') }}</th>
                                <th class="px-4 py-3">{{ __('Registered') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($student->courses as $course)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $course->code }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:underline">
                                            {{ $course->title }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">{{ $course->lecturer?->name }}</td>
                                    <td class="px-4 py-3 text-center">{{ $course->credits }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                        {{ Carbon::parse($course->pivot->registered_at)->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('students.index') }}" class="text-sm text-gray-600 hover:underline">
                    &larr; {{ __('Back to students') }}
                </a>
            </div>
        </div>
    </div>
@endsection
