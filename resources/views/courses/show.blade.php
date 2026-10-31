@extends('layouts.app')

@php
    use Illuminate\Support\Carbon;
@endphp

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h1>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ $course->code }} &middot; {{ $course->lecturer?->name ?? __('Unassigned') }}
                        &middot; {{ __(':count credits', ['count' => $course->credits]) }}
                    </p>
                </div>

                @can('manage courses')
                    <div class="flex items-center gap-3">
                        <a href="{{ route('courses.edit', $course) }}"
                           class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            {{ __('Edit') }}
                        </a>

                        <form method="POST" action="{{ route('courses.destroy', $course) }}"
                              onsubmit="return confirm('Delete this course?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-md border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                @endcan
            </div>

            @if ($course->description)
                <p class="mt-4 text-sm leading-relaxed text-gray-700">{{ $course->description }}</p>
            @endif

            <div class="mt-6 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <h2 class="border-b border-gray-100 px-4 py-3 text-sm font-semibold text-gray-700">
                    {{ __('Registered students') }}
                </h2>

                @can('view course students')
                    @if ($course->students->isEmpty())
                        <p class="px-4 py-6 text-sm text-gray-500">{{ __('No student has registered for this course yet.') }}</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Student number') }}</th>
                                    <th class="px-4 py-3">{{ __('Name') }}</th>
                                    <th class="px-4 py-3">{{ __('Email') }}</th>
                                    <th class="px-4 py-3">{{ __('Registered') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($course->students as $student)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $student->student_number }}</td>
                                        <td class="px-4 py-3">
                                            @can('manage students')
                                                <a href="{{ route('students.show', $student) }}" class="text-indigo-600 hover:underline">
                                                    {{ $student->name }}
                                                </a>
                                            @else
                                                {{ $student->name }}
                                            @endcan
                                        </td>
                                        <td class="px-4 py-3">{{ $student->email }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                            {{ Carbon::parse($student->pivot->registered_at)->format('d M Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @else
                    <p class="px-4 py-6 text-sm text-gray-500">
                        {{ __('Only an administrator or the lecturer assigned to this course can see the roster.') }}
                    </p>
                @endcan
            </div>

            <div class="mt-4">
                <a href="{{ route('courses.index') }}" class="text-sm text-gray-600 hover:underline">
                    &larr; {{ __('Back to courses') }}
                </a>
            </div>
        </div>
    </div>
@endsection
