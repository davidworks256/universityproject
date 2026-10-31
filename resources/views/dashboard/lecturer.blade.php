@extends('layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('Lecturer dashboard') }}</h1>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('The courses assigned to you and how many students are registered in each.') }}
            </p>

            <div class="mt-4 overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                @if ($courses->isEmpty())
                    <p class="px-4 py-6 text-sm text-gray-500">{{ __('No course has been assigned to you yet.') }}</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">{{ __('Code') }}</th>
                                <th class="px-4 py-3">{{ __('Title') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('Credits') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('Students') }}</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($courses as $course)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $course->code }}</td>
                                    <td class="px-4 py-3">{{ $course->title }}</td>
                                    <td class="px-4 py-3 text-center">{{ $course->credits }}</td>
                                    <td class="px-4 py-3 text-center">{{ $course->students_count }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:underline">
                                            {{ __('View students') }}
                                        </a>
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
