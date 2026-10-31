@extends('layouts.app')

@php
    use Illuminate\Support\Carbon;
@endphp

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('Student dashboard') }}</h1>

            @if ($student === null)
                <p class="mt-4 bg-white px-4 py-6 text-sm text-gray-500 shadow-sm sm:rounded-lg">
                    {{ __('This account has no student record yet, so an administrator must create one before you can register for courses.') }}
                </p>
            @else
                <p class="mt-1 text-sm text-gray-600">
                    {{ $student->name }} &middot; {{ $student->student_number }}
                </p>

                <div class="mt-4 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                        <h2 class="text-sm font-semibold text-gray-700">{{ __('My registrations') }}</h2>

                        <a href="{{ route('registrations.create') }}" class="text-sm text-indigo-600 hover:underline">
                            {{ __('Register for a course') }}
                        </a>
                    </div>

                    @if ($registrations->isEmpty())
                        <p class="px-4 py-6 text-sm text-gray-500">{{ __('You are not registered for any course yet.') }}</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($registrations as $course)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $course->code }}</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:underline">
                                                {{ $course->title }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">{{ $course->lecturer?->name }}</td>
                                        <td class="px-4 py-3 text-center">{{ $course->credits }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-500">
                                            {{ Carbon::parse($course->pivot->registered_at)->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="mt-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <h2 class="border-b border-gray-100 px-4 py-3 text-sm font-semibold text-gray-700">
                        {{ __('Available courses') }}
                    </h2>

                    @if ($availableCourses->isEmpty())
                        <p class="px-4 py-6 text-sm text-gray-500">{{ __('You are registered for every course on offer.') }}</p>
                    @else
                        <ul class="divide-y divide-gray-100 text-sm">
                            @foreach ($availableCourses as $course)
                                <li class="flex items-center justify-between px-4 py-3">
                                    <span>
                                        <span class="font-medium text-gray-900">{{ $course->code }}</span>
                                        &mdash; {{ $course->title }}
                                        <span class="block text-xs text-gray-500">
                                            {{ $course->lecturer?->name }}
                                            &middot; {{ __(':count credits', ['count' => $course->credits]) }}
                                        </span>
                                    </span>

                                    <a href="{{ route('registrations.create') }}" class="text-indigo-600 hover:underline">
                                        {{ __('Register') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
