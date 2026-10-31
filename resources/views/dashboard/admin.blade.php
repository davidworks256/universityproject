@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('Administrator dashboard') }}</h1>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                    <span class="text-sm text-gray-500">{{ __('Students') }}</span>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $studentCount }}</p>
                </div>
                <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                    <span class="text-sm text-gray-500">{{ __('Courses') }}</span>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $courseCount }}</p>
                </div>
                <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                    <span class="text-sm text-gray-500">{{ __('Registrations') }}</span>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $registrationCount }}</p>
                </div>
                <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                    <span class="text-sm text-gray-500">{{ __('User accounts') }}</span>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $userCount }}</p>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <h2 class="border-b border-gray-100 px-4 py-3 text-sm font-semibold text-gray-700">
                    {{ __('Recent registrations') }}
                </h2>

                @if ($recentRegistrations->isEmpty())
                    <p class="px-4 py-6 text-sm text-gray-500">{{ __('No registrations yet.') }}</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($recentRegistrations as $registration)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $registration->student->name }}</td>
                                    <td class="px-4 py-3">{{ $registration->student->student_number }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('courses.show', $registration->course) }}" class="text-indigo-600 hover:underline">
                                            {{ $registration->course->code }}
                                        </a>
                                        <span class="text-xs text-gray-500">{{ $registration->course->title }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-gray-500">
                                        {{ $registration->registered_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
