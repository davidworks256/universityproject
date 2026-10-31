@extends('layouts.app')

@php
    $heading = match ($scope) {
        'assigned' => __('My Courses'),
        'available' => __('Available Courses'),
        default => __('Courses'),
    };
@endphp

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">{{ $heading }}</h1>

                @can('manage courses')
                    <a href="{{ route('courses.create') }}"
                       class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                        {{ __('Add course') }}
                    </a>
                @endcan
            </div>

            @if ($scope === 'available')
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('These are the courses you are not registered for yet.') }}
                    <a href="{{ route('registrations.create') }}" class="text-indigo-600 hover:underline">{{ __('Register for a course') }}</a>
                </p>
            @endif

            <div class="mt-4 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Code') }}</th>
                            <th class="px-4 py-3">{{ __('Title') }}</th>
                            <th class="px-4 py-3">{{ __('Lecturer') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('Credits') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('Students') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($courses as $course)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $course->code }}</td>
                                <td class="px-4 py-3">{{ $course->title }}</td>
                                <td class="px-4 py-3">{{ $course->lecturer?->name ?? __('Unassigned') }}</td>
                                <td class="px-4 py-3 text-center">{{ $course->credits }}</td>
                                <td class="px-4 py-3 text-center">{{ $course->students_count }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:underline">
                                        {{ __('View') }}
                                    </a>

                                    @can('manage courses')
                                        <a href="{{ route('courses.edit', $course) }}" class="ms-3 text-indigo-600 hover:underline">
                                            {{ __('Edit') }}
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">
                                    {{ $scope === 'available' ? __('You are registered for every course on offer.') : __('No courses yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $courses->links() }}</div>
        </div>
    </div>
@endsection
