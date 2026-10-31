@extends('layouts.app')

@php
    $signedInUser = auth()->user();
@endphp

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">
                    @can('manage registrations')
                        {{ __('Registrations') }}
                    @else
                        {{ __('My Registrations') }}
                    @endcan
                </h1>

                @can('register for course')
                    <a href="{{ route('registrations.create') }}"
                       class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                        {{ __('Register for a course') }}
                    </a>
                @endcan
            </div>

            @if ($registrations->isEmpty())
                <p class="mt-4 bg-white px-4 py-6 text-center text-sm text-gray-500 shadow-sm sm:rounded-lg">
                    {{ __('Nothing registered yet.') }}
                </p>
            @else
                <div class="mt-4 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">{{ __('Student') }}</th>
                                <th class="px-4 py-3">{{ __('Course') }}</th>
                                <th class="px-4 py-3">{{ __('Lecturer') }}</th>
                                <th class="px-4 py-3">{{ __('Registered') }}</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($registrations as $registration)
                                @php
                                    $mayCancel = $signedInUser->can('manage registrations')
                                        || ($signedInUser->can('register for course')
                                            && $registration->student_id === $signedInUser->student?->getKey());
                                @endphp

                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-gray-900">{{ $registration->student->name }}</span>
                                        <span class="block text-xs text-gray-500">{{ $registration->student->student_number }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('courses.show', $registration->course) }}" class="text-indigo-600 hover:underline">
                                            {{ $registration->course->code }}
                                        </a>
                                        <span class="block text-xs text-gray-500">{{ $registration->course->title }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $registration->course->lecturer?->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                        {{ $registration->registered_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($mayCancel)
                                            <form method="POST" action="{{ route('registrations.destroy', $registration) }}" class="inline"
                                                  onsubmit="return confirm('Cancel this registration?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">{{ __('Cancel') }}</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $registrations->links() }}</div>
            @endif
        </div>
    </div>
@endsection
